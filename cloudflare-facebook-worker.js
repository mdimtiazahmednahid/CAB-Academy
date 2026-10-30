/**
 * ACS Academy — Cloudflare Worker for Meta/Facebook Open Graph Scraping on InfinityFree
 *
 * WHY THIS IS NEEDED ON INFINITYFREE FREE HOSTING:
 * InfinityFree's server-level Nginx firewall blocks non-browser user agents (such as
 * `facebookexternalhit/1.1`, `Facebot`, `meta-externalagent`, `WhatsApp`, `LinkedInBot`)
 * with HTTP 403 Forbidden or an `/aes.js` JavaScript cookie challenge (`__test` cookie)
 * BEFORE the request ever reaches `.htaccess`, `robots.txt`, or Laravel.
 *
 * HOW THIS WORKER SOLVES IT (100% FREE ON CLOUDFLARE):
 * 1. For normal human visitors: passes requests straight through untouched.
 * 2. For `/robots.txt`: serves a clean 200 OK `robots.txt` that explicitly allowlists
 *    `facebookexternalhit`, `Facebot`, `meta-externalagent`, and all social crawlers.
 * 3. For social media crawlers (`facebookexternalhit`, `Facebot`, `meta-externalagent`,
 *    `WhatsApp`, `Twitterbot`, `LinkedInBot`, `TelegramBot`):
 *    - Spoofs a standard Desktop Chrome User-Agent when contacting the InfinityFree origin.
 *    - Automatically solves InfinityFree's `aes.js` (`slowAES.decrypt`) challenge in <1ms
 *      to obtain the `__test` verification cookie.
 *    - Re-fetches the real Laravel HTML page or course cover image (`/storage/*`, `/uploads/*`)
 *      with the `__test` cookie and returns HTTP 200 OK to Facebook Sharing Debugger.
 */

const SOCIAL_BOT_REGEX = /(facebookexternalhit|Facebot|meta-externalagent|FacebookBot|WhatsApp|Twitterbot|LinkedInBot|TelegramBot|Discordbot|Slackbot|Pinterest)/i;

const CHROME_UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36';

export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);
    const ua = request.headers.get('user-agent') || '';
    const isSocialBot = SOCIAL_BOT_REGEX.test(ua);

    // 1. Always guarantee 200 OK for /robots.txt so Meta Sharing Debugger never gets 403
    if (url.pathname === '/robots.txt') {
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
        `Sitemap: ${url.origin}/sitemap.xml`,
      ].join('\n');

      return new Response(robotsTxt, {
        status: 200,
        headers: {
          'Content-Type': 'text/plain; charset=utf-8',
          'Cache-Control': 'public, max-age=3600',
        },
      });
    }

    // 2. Pass normal human browser traffic straight through
    if (!isSocialBot) {
      return fetch(request);
    }

    // 3. Social crawler traffic: fetch from origin using Chrome UA and solve aes.js if challenged
    return fetchSolvingInfinityFreeChallenge(url, request);
  },
};

async function fetchSolvingInfinityFreeChallenge(url, originalRequest) {
  const cleanUrl = new URL(url.toString());
  cleanUrl.searchParams.delete('fbclid');

  const headers = new Headers();
  headers.set('User-Agent', CHROME_UA);
  headers.set('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8');
  headers.set('Accept-Language', 'en-US,en;q=0.9');

  let response = await fetch(cleanUrl.toString(), {
    method: 'GET',
    headers,
    redirect: 'follow',
  });

  const contentType = response.headers.get('content-type') || '';
  if (!contentType.includes('text/html')) {
    // Binary asset (e.g., course cover image JPEG/PNG/WebP)
    return new Response(response.body, {
      status: 200,
      headers: response.headers,
    });
  }

  const html = await response.text();

  // Check if InfinityFree returned the /aes.js challenge page
  const match = html.match(/toNumbers\("([0-9a-fA-F]{32})"\).*?toNumbers\("([0-9a-fA-F]{32})"\).*?toNumbers\("([0-9a-fA-F]{32})"\)/s);
  if (match) {
    const keyHex = match[1];
    const ivHex = match[2];
    const cipherHex = match[3];

    const testCookieVal = await decryptInfinityFreeTestCookie(keyHex, ivHex, cipherHex);
    if (testCookieVal) {
      headers.set('Cookie', `__test=${testCookieVal}`);
      const retryUrl = new URL(cleanUrl.toString());
      retryUrl.searchParams.set('i', '1');

      const retryResp = await fetch(retryUrl.toString(), {
        method: 'GET',
        headers,
        redirect: 'follow',
      });

      const retryType = retryResp.headers.get('content-type') || '';
      if (!retryType.includes('text/html')) {
        return new Response(retryResp.body, {
          status: 200,
          headers: retryResp.headers,
        });
      }

      let finalHtml = await retryResp.text();
      // Strip ?i=1 from canonical and og:url if present
      finalHtml = finalHtml.replace(/\?i=1/g, '');

      return new Response(finalHtml, {
        status: 200,
        headers: {
          'Content-Type': 'text/html; charset=utf-8',
          'Cache-Control': 'public, max-age=600',
        },
      });
    }
  }

  return new Response(html, {
    status: 200,
    headers: {
      'Content-Type': 'text/html; charset=utf-8',
      'Cache-Control': 'public, max-age=600',
    },
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

/**
 * Raw 16-byte block AES-128-CBC decryption matching slowAES.decrypt(c, 2, a, b)
 * using Web Crypto API by computing a valid PKCS#7 padded 2nd block.
 */
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

    // Construct a 2nd block of 16 bytes of 0x10 (valid PKCS#7 full-block padding)
    // Encrypting 16 bytes of 0x10 with IV = cipherBytes yields a 2-block ciphertext
    // whose first block is the decrypted target!
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

