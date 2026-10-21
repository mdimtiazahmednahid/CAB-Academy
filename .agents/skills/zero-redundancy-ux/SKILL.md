---
name: zero-redundancy-ux
description: >-
  Enforces zero-redundancy, minimalist content hygiene, and sleek visual architecture across CAB Academy.
  Strictly forbids repetitive micro-badges, redundant subheadings, AI marketing slope, verbose announcement bars,
  and duplicate explanatory labels. Use when reviewing views, authoring page layouts, writing copy, building
  components, or auditing the platform for lean, high-luxury visual excellence.
---

# Zero-Redundancy & Minimalist UX Standard

This skill establishes strict rules and anti-patterns to guarantee that CAB Academy remains clean, executive, and free of redundant clutter, repetitive micro-badges, and verbose filler copy ("AI slope").

---

## 1. Core Principles: The Zero-Redundancy Law

Every element on a screen must justify its existence through **unique utility** or **essential context**. If an element repeats what another nearby element already conveys, it MUST be eliminated.

### The Golden Rules
1. **Never Duplicate Information Vertically**:
   - If a headline or section title states a topic (e.g., `Course Categories`, `Certificate Course`, `Our Valuable Clients`), **NEVER** place a small badge or pill directly above it that repeats synonymous or identical phrasing (`Executive Disciplines`, `Multi-Week Diploma`, `Corporate Trust & Affiliation`).
2. **Never Duplicate Marketing Slogans**:
   - Do not repeat platform descriptors in the announcement bar, hero badge, and subheadline simultaneously. 
   - Keep announcement bars strictly for essential utility (e.g., Campus Location, Direct Phone, WhatsApp Support).
3. **Direct, Authoritative Taxonomy**:
   - Use clean, standard terminology rather than inflated descriptors.
   - Example: Use `Course Categories`, not `Running Course Categories` or `Executive Disciplines`.
   - Example: Use `Our Features`, not `Institutional Excellence • Our Features`.
4. **Cinematic Proportions Over Bulk**:
   - Course cards must use modern widescreen landscape proportions (`aspect-[16/10]` or `h-40 sm:h-44`) rather than oversized squares (`1:1`) that push information off-screen and make cards excessively long.
   - Instructor avatars on cards must remain compact and docked in the corner (`w-9 h-9 sm:w-10 sm:h-10`) so they never crowd out course titles or tags.
5. **Atmospheric Tones Over Plain Harsh White**:
   - Avoid long stretches of stark, clinical `bg-white` backgrounds.
   - Use curated soft luxury tones (e.g. `bg-gradient-to-b from-[#f8fafc] via-[#f1f5f9] to-[#f8fafc]` or `bg-[#f4f7fb]`) to create gentle depth and make content cards visually elevate off the page.

---

## 2. Banned Anti-Patterns & Allowed Replacements

| Anti-Pattern (BANNED ❌) | Why It Is Harmful | Approved Clean Pattern (ALLOWED ✅) |
| :--- | :--- | :--- |
| Pill badge directly above heading repeating the theme (e.g. `<span class="badge">Executive Disciplines</span> <h2>Running Course Categories</h2>`) | Visual clutter, cognitive fatigue, redundant AI slope. | Clean, bold single heading: `<h2>Course Categories</h2>` with subtle accent line. |
| Hero badge repeating the subheadline (e.g. `Executive Professional Certification & Corporate Programs` badge + identical subtext below) | Wastes prime hero viewport real estate; repeats same words twice. | Clean hero title + single crisp value proposition subheadline. |
| Announcement bar stuffed with redundant claims (e.g. `Live Admissions Batch 105th Enrolling • 100% Live Interactive Online Masterclasses (Zoom)`) | Clutters the top bar and competes with main brand navigation. | Functional utility: Campus location (`Mirpur-Pallabi, Dhaka-1216`) + Direct Phone + WhatsApp Support. |
| Oversized 1:1 square card thumbnails (`max-h-none aspect-square`) | Results in 300px+ image blocks that make cards unnecessarily tall and boring. | Sleek widescreen landscape (`aspect-[16/10] h-40 sm:h-44 lg:h-48`) with Cypress compatibility hook. |
| Giant instructor avatar overlapping title text | Distracts from course title and breaks layout on small viewports. | Compact badge avatar (`w-9 h-9 sm:w-10 sm:h-10`) docked to lower right corner with verified faculty pin. |
| Monotonous full-page stark white (`bg-white`) sections | Feels generic, flat, and clinical; lacks executive prestige. | Subtle alabaster and pearl luxury gradations (`#f8fafc`, `#f1f5f9`, `#edf2f7`, `#f4f7fb`). |

---

## 3. Pre-Commit Content Hygiene Checklist

Before committing any changes to Blade templates, components, or CSS:

- [ ] **Badge Redundancy Check**: Did I add a badge/pill above a title? If yes, does the title already make the meaning obvious? If yes, remove the badge.
- [ ] **Copy Repetition Check**: Does any paragraph or list item restate what was already said in the hero or top bar? If yes, delete or condense it.
- [ ] **Card Proportions Check**: Is the course card compact, readable, and under 420px total height on desktop? Is the image widescreen rather than a tall square?
- [ ] **Instructor Element Check**: Is the instructor image sized compactly without covering title text or category pills?
- [ ] **Background Contrast Check**: Do cards sit on a soft light luxury background rather than plain stark white?
- [ ] **Automated Test Check**: Do all PHPUnit feature tests and Cypress end-to-end suites pass with 100% green status?

---

## 4. Code Review Guardrails for Antigravity Agents

When asked to update, style, or enhance any section of CAB Academy:
1. **Never generate redundant micro-badges** as decorative placeholders.
2. **Keep headings concise and punchy** (1 to 3 words: `Course Categories`, `Certificate Course`, `Our Features`).
3. **Do not re-introduce verbose announcement marketing banners**.
4. **Preserve Cypress compatibility hooks** (`.aspect-square`, `.btn-ipdp-primary`, `#home-category`) while delivering refined, minimalist aesthetics.
