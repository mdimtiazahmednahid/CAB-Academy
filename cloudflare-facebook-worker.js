/**
 * ACS Academy — Cloudflare Worker for Meta/Facebook Open Graph Scraping on InfinityFree
 *
 * Works BOTH:
 *  1) Directly on `https://acsacademy.bd/*` (via Cloudflare Workers Routes), AND
 *  2) Immediately on `https://fb.neurasoftware.workers.dev/*`
 *
 * Automatically resolves each course's exact uploaded thumbnail across both
 * `acsacademy.bd` and `cabacademy.bd` storage paths (as well as base64 data URIs).
 */

const PRIMARY_DOMAIN = 'acsacademy.bd';
const LEGACY_DOMAIN = 'cabacademy.bd';
const PRIMARY_ORIGIN = `https://${PRIMARY_DOMAIN}`;
const LEGACY_ORIGIN = `https://${LEGACY_DOMAIN}`;

const SOCIAL_BOT_REGEX = /(facebookexternalhit|Facebot|meta-externalagent|FacebookBot|WhatsApp|Twitterbot|LinkedInBot|TelegramBot|Discordbot|Slackbot|Pinterest|Googlebot|bingbot)/i;
const ASSET_PATH_REGEX = /^\/(storage|uploads|images|build|public)\//i;

const CHROME_UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36';

// Cache solved InfinityFree __test cookies per origin hostname
const cachedTestCookies = {
  [PRIMARY_DOMAIN]: '',
  [LEGACY_DOMAIN]: '',
};

export default {
  async fetch(request, env, ctx) {
    const reqUrl = new URL(request.url);
    const ua = request.headers.get('user-agent') || '';
    const isSocialBot = SOCIAL_BOT_REGEX.test(ua);
    const isWorkersDev = reqUrl.hostname.endsWith('.workers.dev') || reqUrl.hostname === 'fb.acsacademy.bd';
    const isAssetPath = ASSET_PATH_REGEX.test(reqUrl.pathname);

    // 1. Always guarantee 200 OK for /robots.txt so Meta Sharing Debugger never gets 403
    if (reqUrl.pathname === '/robots.txt') {
      const robotsTxt = [
        'User-agent: facebookexternalhit',
        'User-agent: facebookexternalhit/1.1',
        'User-agent: Facebot',
        'User-agent: meta-externalagent',
        'User-agent: meta-externalagent/1.1',
        'User-agent: FacebookBot',
        'User-agent: WhatsApp',
        'User-agent: Twitterbot',
        'User-agent: LinkedInBot',
        'User-agent: TelegramBot',
        'Allow: /',
        '',
        'User-agent: *',
        'Disallow: /admin/',
        'Disallow: /dashboard',
        'Disallow: /login',
        'Disallow: /register',
        'Allow: /',
        `Sitemap: ${PRIMARY_ORIGIN}/sitemap.xml`,
      ].join('\n');

      return new Response(robotsTxt, {
        status: 200,
        headers: {
          'Content-Type': 'text/plain; charset=utf-8',
          'Cache-Control': 'public, max-age=3600',
        },
      });
    }

    // 2. Dynamic Base64 Course Thumbnail Endpoint (if a course cover is stored as data:image/...;base64,...)
    if (reqUrl.pathname.startsWith('/__og_b64/')) {
      return serveBase64CourseImage(reqUrl);
    }

    // 3. Image / static asset request: resolve exact course thumbnail across acsacademy.bd and cabacademy.bd
    if (isAssetPath) {
      return fetchAssetSolvingChallenge(reqUrl);
    }

    // 4. If human visitor clicks the Facebook preview card via https://fb.neurasoftware.workers.dev/c/{id},
    //    redirect them straight to https://acsacademy.bd/courses/{id}?enroll=true (opens Enroll Now drawer!)
    if (isWorkersDev && !isSocialBot && !reqUrl.searchParams.has('debug_og') && !reqUrl.searchParams.has('preview_debug')) {
      const shortMatch = reqUrl.pathname.match(/^\/c\/(\d+)/);
      if (shortMatch) {
        return Response.redirect(`${PRIMARY_ORIGIN}/courses/${shortMatch[1]}?enroll=true`, 302);
      }
      const targetUrl = `${PRIMARY_ORIGIN}${reqUrl.pathname}${reqUrl.search}`;
      return Response.redirect(targetUrl, 302);
    }

    // 5. If human visitor accesses https://acsacademy.bd directly, pass straight through
    if (!isWorkersDev && !isSocialBot && !reqUrl.searchParams.has('debug_og')) {
      return fetch(request);
    }

    // 6. HTML page for social crawlers (or ?debug_og=1)
    return fetchHtmlSolvingChallenge(reqUrl, isWorkersDev);
  },
};

/**
 * Build ordered candidate URLs across both acsacademy.bd and cabacademy.bd
 * so courses uploaded on either domain resolve their exact thumbnail.
 */
function buildCandidateAssetUrls(rawPath) {
  const cleanPath = decodeURIComponent(rawPath);
  const subPath = cleanPath
    .replace(/^\/(public\/)?(storage\/app\/public|storage|uploads)\//i, '')
    .replace(/^\/+/, '');

  if (!subPath || cleanPath.startsWith('/images/') || cleanPath.startsWith('/public/images/')) {
    const imgSub = cleanPath.replace(/^\/(public\/)?images\//i, '');
    return [
      `${PRIMARY_ORIGIN}/images/${imgSub}`,
      `${PRIMARY_ORIGIN}/public/images/${imgSub}`,
      `${LEGACY_ORIGIN}/images/${imgSub}`,
    ];
  }

  return [
    // Primary domain (acsacademy.bd) — try Laravel /storage/ stream route & direct upload folders first
    `${PRIMARY_ORIGIN}/storage/${subPath}`,
    `${PRIMARY_ORIGIN}/public/uploads/${subPath}`,
    `${PRIMARY_ORIGIN}/uploads/${subPath}`,
    `${PRIMARY_ORIGIN}/public/storage/${subPath}`,
    // Legacy domain (cabacademy.bd) — in case course cover was uploaded before switching domains
    `${LEGACY_ORIGIN}/storage/${subPath}`,
    `${LEGACY_ORIGIN}/public/uploads/${subPath}`,
    `${LEGACY_ORIGIN}/uploads/${subPath}`,
    `${LEGACY_ORIGIN}/public/storage/${subPath}`,
    // Direct storage/app/public fallback
    `${PRIMARY_ORIGIN}/storage/app/public/${subPath}`,
    `${LEGACY_ORIGIN}/storage/app/public/${subPath}`,
  ];
}

async function fetchAssetSolvingChallenge(reqUrl) {
  const candidateUrls = buildCandidateAssetUrls(reqUrl.pathname);

  for (const targetUrl of candidateUrls) {
    const resp = await fetchWithInfinityFreeCookie(targetUrl, true);
    if (!resp) continue;

    const ct = (resp.headers.get('content-type') || '').toLowerCase();

    if (resp.ok && !ct.includes('text/html')) {
      const buf = await resp.arrayBuffer();
      // Verify it is a real non-empty binary image (> 200 bytes)
      if (buf.byteLength > 200) {
        const ext = reqUrl.pathname.split('.').pop().toLowerCase();
        const inferredMime =
          ext === 'png' ? 'image/png' :
          ext === 'webp' ? 'image/webp' :
          ext === 'gif' ? 'image/gif' :
          'image/jpeg';

        return new Response(buf, {
          status: 200,
          headers: {
            'Content-Type': ct.startsWith('image/') ? ct : inferredMime,
            'Content-Length': String(buf.byteLength),
            'Cache-Control': 'public, max-age=86400',
            'Access-Control-Allow-Origin': '*',
            'X-Resolved-Asset-Url': targetUrl,
          },
        });
      }
    }
  }

  // Fallback to 1200x630 executive banner only if the course has no uploaded cover on either domain
  const unsplashFallback = 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&h=630&q=85';
  const fallbackResp = await fetch(unsplashFallback);
  return new Response(fallbackResp.body, {
    status: 200,
    headers: {
      'Content-Type': 'image/jpeg',
      'Cache-Control': 'no-cache, no-store, must-revalidate',
      'Access-Control-Allow-Origin': '*',
      'X-Resolved-Asset-Url': 'fallback-1200x630',
    },
  });
}

/**
 * If a course cover in HTML is a `data:image/...;base64,...` URI, decode and stream it as binary image/jpeg
 * so Facebook's Open Graph scraper receives a standard HTTP 200 binary image.
 */
async function serveBase64CourseImage(reqUrl) {
  const coursePath = reqUrl.pathname.replace(/^\/__og_b64/, '') || '/';
  const resp = await fetchWithInfinityFreeCookie(`${PRIMARY_ORIGIN}${coursePath}`, false);
  if (resp && resp.ok) {
    const html = await resp.text();
    const b64Match = html.match(/data:image\/([a-zA-Z0-9+]+);base64,([A-Za-z0-9+/=]+)/);
    if (b64Match) {
      const subType = b64Match[1].toLowerCase();
      const b64Data = b64Match[2];
      const binaryStr = atob(b64Data);
      const bytes = new Uint8Array(binaryStr.length);
      for (let i = 0; i < binaryStr.length; i++) {
        bytes[i] = binaryStr.charCodeAt(i);
      }
      return new Response(bytes.buffer, {
        status: 200,
        headers: {
          'Content-Type': `image/${subType === 'jpg' ? 'jpeg' : subType}`,
          'Content-Length': String(bytes.byteLength),
          'Cache-Control': 'public, max-age=86400',
          'Access-Control-Allow-Origin': '*',
        },
      });
    }
  }
  return fetchAssetSolvingChallenge(new URL(`${PRIMARY_ORIGIN}/images/og-banner.jpg`));
}

/**
 * Fetch HTML page for social crawlers, solving InfinityFree's aes.js challenge
 * and ensuring the exact course thumbnail URL is served with cache-busting.
 */
async function fetchHtmlSolvingChallenge(reqUrl, isWorkersDev) {
  const originUrl = new URL(`${PRIMARY_ORIGIN}${reqUrl.pathname}${reqUrl.search}`);
  originUrl.searchParams.delete('fbclid');
  originUrl.searchParams.delete('debug_og');
  originUrl.searchParams.delete('preview_debug');

  let resp = await fetchWithInfinityFreeCookie(originUrl.toString(), false);
  let html = resp ? await resp.text() : '';

  // Strip ?i=1 from canonical and og:url if present
  html = html.replace(/\?i=1/g, '');

  const activeHostOrigin = `https://${reqUrl.host}`;
  const cacheBust = Math.floor(Date.now() / 30000);

  // Extract raw og:image from HTML
  const ogImgMatch = html.match(/<meta\s+property=["']og:image["']\s+content=["']([^"']+)["']/i);
  let rawOgImage = ogImgMatch ? ogImgMatch[1] : '';

  // Also check if there is a course hero <img src="..."> in the body in case og:image fell back to default
  const bodyImgMatch = html.match(/<img[^>]+src=["']([^"']*\/(?:storage|uploads)\/course_covers\/[^"']+|data:image\/[^"']+)["']/i);
  if (bodyImgMatch && (!rawOgImage || rawOgImage.includes('images.unsplash.com'))) {
    rawOgImage = bodyImgMatch[1];
  }

  let resolvedWorkerOgUrl = '';
  if (rawOgImage.startsWith('data:image/')) {
    resolvedWorkerOgUrl = `${activeHostOrigin}/__og_b64${reqUrl.pathname}?v=${cacheBust}`;
  } else if (rawOgImage) {
    try {
      const parsedImg = new URL(rawOgImage, PRIMARY_ORIGIN);
      if (
        parsedImg.hostname.includes('acsacademy.bd') ||
        parsedImg.hostname.includes('cabacademy.bd') ||
        parsedImg.hostname === 'localhost' ||
        parsedImg.hostname === '127.0.0.1'
      ) {
        resolvedWorkerOgUrl = `${activeHostOrigin}${parsedImg.pathname}?v=${cacheBust}`;
      }
    } catch (e) {
      if (rawOgImage.startsWith('/')) {
        resolvedWorkerOgUrl = `${activeHostOrigin}${rawOgImage}?v=${cacheBust}`;
      }
    }
  }

  // Optional diagnostic mode (?debug_og=1) to inspect raw Open Graph tags and asset resolution
  if (reqUrl.searchParams.has('debug_og')) {
    const ogTitleMatch = html.match(/<meta\s+property=["']og:title["']\s+content=["']([^"']*)["']/i);
    const probeResults = [];

    if (resolvedWorkerOgUrl && !resolvedWorkerOgUrl.includes('/__og_b64/')) {
      const imgUrlObj = new URL(resolvedWorkerOgUrl);
      for (const candUrl of buildCandidateAssetUrls(imgUrlObj.pathname)) {
        const r = await fetchWithInfinityFreeCookie(candUrl, true);
        if (!r) {
          probeResults.push({ url: candUrl, status: '404_or_redirected_to_error_page' });
          continue;
        }
        const cType = r.headers.get('content-type') || '';
        const b = await r.arrayBuffer();
        probeResults.push({
          url: candUrl,
          finalUrl: r.url,
          status: r.status,
          contentType: cType,
          bytes: b.byteLength,
        });
      }
    }

    return new Response(
      JSON.stringify(
        {
          originUrl: originUrl.toString(),
          ogTitle: ogTitleMatch ? ogTitleMatch[1] : null,
          rawOgImage,
          resolvedWorkerOgUrl,
          probeResults,
        },
        null,
        2
      ),
      {
        status: 200,
        headers: { 'Content-Type': 'application/json; charset=utf-8' },
      }
    );
  }

  // Replace og:image, og:image:secure_url, and twitter:image with the resolved Worker URL + cache-buster
  if (resolvedWorkerOgUrl) {
    html = html
      .replace(/(<meta\s+property=["']og:image["']\s+content=["'])[^"']*(["'])/gi, `$1${resolvedWorkerOgUrl}$2`)
      .replace(/(<meta\s+property=["']og:image:secure_url["']\s+content=["'])[^"']*(["'])/gi, `$1${resolvedWorkerOgUrl}$2`)
      .replace(/(<meta\s+name=["']twitter:image["']\s+content=["'])[^"']*(["'])/gi, `$1${resolvedWorkerOgUrl}$2`);
  }

  if (isWorkersDev) {
    html = html
      .replace(/https?:\/\/(?:www\.)?(?:acsacademy|cabacademy)\.bd\/(storage|uploads|images|public)\//gi, `${activeHostOrigin}/$1/`)
      .replace(/(<meta\s+property=["']og:url["']\s+content=["'])https?:\/\/(?:www\.)?(?:acsacademy|cabacademy)\.bd/gi, `$1${activeHostOrigin}`)
      .replace(/(<link\s+rel=["']canonical["']\s+href=["'])https?:\/\/(?:www\.)?(?:acsacademy|cabacademy)\.bd/gi, `$1${activeHostOrigin}`);
  }

  return new Response(html, {
    status: 200,
    headers: {
      'Content-Type': 'text/html; charset=utf-8',
      'Cache-Control': 'no-cache, no-store, must-revalidate',
    },
  });
}

/**
 * Perform an HTTP GET to an InfinityFree origin (acsacademy.bd or cabacademy.bd),
 * automatically solving the /aes.js challenge and skipping 404 redirects to errors.infinityfree.net.
 */
async function fetchWithInfinityFreeCookie(urlStr, isBinary) {
  const targetUrlObj = new URL(urlStr);
  const domain = targetUrlObj.hostname;
  const originBase = `${targetUrlObj.protocol}//${domain}`;

  const headers = new Headers();
  headers.set('User-Agent', CHROME_UA);
  headers.set(
    'Accept',
    isBinary
      ? 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8'
      : 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8'
  );
  headers.set('Accept-Language', 'en-US,en;q=0.9');
  headers.set('Referer', `${originBase}/`);

  if (cachedTestCookies[domain]) {
    headers.set('Cookie', `__test=${cachedTestCookies[domain]}`);
  }

  let response = await fetch(urlStr, {
    method: 'GET',
    headers,
    redirect: 'follow',
  });

  if (response.url && (response.url.includes('errors.infinityfree.net') || response.url.includes('/errors/'))) {
    return null;
  }

  const ct = (response.headers.get('content-type') || '').toLowerCase();
  if (!ct.includes('text/html')) {
    return response;
  }

  const bodyText = await response.text();
  const match = bodyText.match(/toNumbers\("([0-9a-fA-F]{32})"\).*?toNumbers\("([0-9a-fA-F]{32})"\).*?toNumbers\("([0-9a-fA-F]{32})"\)/s);

  if (match) {
    const keyHex = match[1];
    const ivHex = match[2];
    const cipherHex = match[3];

    const testCookieVal = await decryptInfinityFreeTestCookie(keyHex, ivHex, cipherHex);
    if (testCookieVal) {
      cachedTestCookies[domain] = testCookieVal;
      headers.set('Cookie', `__test=${testCookieVal}`);

      const retryUrl = new URL(urlStr);
      retryUrl.searchParams.set('i', '1');

      const retryResp = await fetch(retryUrl.toString(), {
        method: 'GET',
        headers,
        redirect: 'follow',
      });

      if (retryResp.url && (retryResp.url.includes('errors.infinityfree.net') || retryResp.url.includes('/errors/'))) {
        return null;
      }

      return retryResp;
    }
  }

  return new Response(bodyText, {
    status: response.status,
    headers: response.headers,
  });
}

function hexToBytes(hex) {
  const bytes = new Uint8Array(hex.length / 2);
  for (let i = 0; i < bytes.length; i++) {
    bytes[i] = parseInt(hex.substr(i * 2, 2), 16);
  }
  return bytes;
}

function bytesToHex(bytes) {
  return Array.from(bytes)
    .map((b) => b.toString(16).padStart(2, '0'))
    .join('');
}

async function decryptInfinityFreeTestCookie(keyHex, ivHex, cipherHex) {
  try {
    const keyBytes = hexToBytes(keyHex);
    const ivBytes = hexToBytes(ivHex);
    const cipherBytes = hexToBytes(cipherHex);

    const cryptoKey = await crypto.subtle.importKey(
      'raw',
      keyBytes,
      { name: 'AES-CBC' },
      false,
      ['encrypt', 'decrypt']
    );

    const padBlock = new Uint8Array(16).fill(16);
    const encPad = await crypto.subtle.encrypt(
      { name: 'AES-CBC', iv: cipherBytes },
      cryptoKey,
      padBlock
    );
    const encPadBytes = new Uint8Array(encPad);

    const combinedCipher = new Uint8Array(32);
    combinedCipher.set(cipherBytes, 0);
    combinedCipher.set(encPadBytes.slice(0, 16), 16);

    const decrypted = await crypto.subtle.decrypt(
      { name: 'AES-CBC', iv: ivBytes },
      cryptoKey,
      combinedCipher
    );

    return bytesToHex(new Uint8Array(decrypted).slice(0, 16));
  } catch (e) {
    return null;
  }
}
