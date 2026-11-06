---
name: zero-redundancy-ux
description: >-
  Enforces zero-redundancy, minimalist content hygiene, and sleek visual architecture across Academy of Commercial & Supply Chain (ACS).
  Strictly forbids repetitive micro-badges, redundant subheadings, AI marketing slope, verbose announcement bars,
  and duplicate explanatory labels. Use when reviewing views, authoring page layouts, writing copy, building
  components, or auditing the platform for lean, high-luxury visual excellence.
---

# Zero-Redundancy UX — Strict Edition

## 1. Core Philosophy

**Less content. More meaning.**

Academy of Commercial & Supply Chain (ACS) must communicate through clarity, hierarchy, and purposeful design — not through excessive text, repeated labels, marketing slogans, or unnecessary explanations.

Every element must justify its existence.

If removing an element does not reduce functionality, clarity, or essential context, remove it.

The default decision is **DELETE, not ADD.**

## 2. Aggressive Text Reduction

### The One-Message Rule

- One section communicates one primary message.
- One heading identifies that message.
- One short supporting sentence is sufficient when explanation is necessary.
- Never explain something that is already self-explanatory.
- Never repeat the same information in a heading, subtitle, badge, and paragraph.

### Strict Copy Limits

| Element             | Preferred limit            |
| ------------------- | -------------------------- |
| Section heading     | 1–4 words                  |
| Section description | 8–15 words                 |
| Card title          | 3–8 words                  |
| Card description    | 10–18 words                |
| Button label        | 1–3 words                  |
| Badge               | 1–2 words                  |
| Announcement        | Essential information only |
| Hero subtitle       | One concise sentence       |

These are maximum design guidelines, not mandatory text quotas. Use fewer words whenever possible.

### Delete These Automatically

- Unnecessary introductory paragraphs.
- Repeated explanations of platform features.
- Generic marketing statements.
- Empty adjectives such as exceptional, extraordinary, world-class, unparalleled, and transformative.
- Sentences that merely announce what the following section already shows.
- Repeated calls to action.
- Decorative labels that add no information.
- Long explanations of obvious interface elements.

## 3. Information Hierarchy

Every section should follow this priority:

1. Primary message.
2. Essential supporting information.
3. Relevant action.

Nothing else by default.

Example:

**Bad:**

- Badge: Professional Learning
- Heading: Our Professional Courses
- Subtitle: Explore our professional learning opportunities.
- Paragraph: We offer a wide range of courses designed to help learners develop professional skills.

**Good:**

**Professional Courses**

Explore courses designed for practical career development.

Do not add a badge or another explanatory paragraph.

## 4. Component-Level Minimalism

### Course Cards

Display only information that helps someone choose a course:

- Course image.
- Course title.
- Price, if applicable.
- One essential course detail.
- Instructor, if relevant.
- One clear action.

Do not repeat course categories, course descriptions, instructor details, and duration across multiple UI elements.

Use widescreen imagery (16:10), compact layouts, and controlled card heights.

### Instructor Cards

Show:

- Photo.
- Name.
- Relevant designation.

Avoid lengthy biographies, decorative verification labels, and repeated credentials.

### Statistics

Show the number and its meaning.

Example:

- 25+ Courses
- 1,200+ Learners

Do not add explanatory paragraphs underneath obvious statistics.

### Buttons

Use direct actions:

- Explore Courses
- Enroll Now
- Learn More
- Contact Us

Never use lengthy sentence-style buttons.

## 5. Section Elimination Rules

Before adding a new section, ask:

1. Does this information already exist elsewhere?
2. Does the user actually need it here?
3. Does it help the user make a decision?
4. Can an existing section communicate it?
5. Can the information be removed entirely?

If an existing section already communicates the message, do not create another section for it.

Merge overlapping sections. Remove duplicate content. Prefer restructuring over adding.

## 6. Visual Restraint

- Avoid excessive badges, pills, icons, decorative dividers, and labels.
- Never place a redundant badge above a heading.
- Avoid oversized content blocks.
- Maintain consistent spacing and visual rhythm.
- Use subtle pearl, alabaster, and slate backgrounds.
- Keep course imagery widescreen.
- Keep instructor avatars compact and unobtrusive.
- Use whitespace to establish hierarchy instead of adding more labels.

## 7. Functional Preservation

Visual simplification must never break:

- Existing business logic.
- Blade and Laravel functionality.
- Responsive behavior.
- Accessibility.
- SEO and semantic HTML.
- Existing Cypress selectors and compatibility hooks.
- Existing integrations and user workflows.

Preserve required selectors such as `.aspect-square`, `.btn-ipdp-primary`, and `#home-category` when existing tests or functionality depend on them. Their presence does not require visually square images.

## 8. Mandatory Editing Protocol

Whenever modifying an existing page:

**Step 1 — Audit**
Identify repeated messages, unnecessary paragraphs, redundant labels, and oversized components.

**Step 2 — Eliminate**
Remove unnecessary content before introducing new content.

**Step 3 — Consolidate**
Merge overlapping sections, descriptions, and actions.

**Step 4 — Refine**
Improve typography, spacing, image proportions, contrast, and hierarchy.

**Step 5 — Validate**
Check responsive layouts, accessibility, existing functionality, PHPUnit, and Cypress.

Do not rewrite an entire page unnecessarily when targeted removal solves the problem.

## 9. Final Zero-Redundancy Test

Before completing any change, ask:

- Can any sentence be removed?
- Can any paragraph become one sentence?
- Can any two sections become one?
- Does any label repeat the heading?
- Does any card contain unnecessary information?
- Are there multiple elements communicating the same message?
- Is the page visually or textually overwhelming?
- Does every element help the user understand, decide, or act?

If the answer indicates unnecessary content, remove it.

## 10. Absolute Rule

**Never add content to make a page look complete.**

Do not fill whitespace with paragraphs, badges, statistics, slogans, or decorative descriptions.

Whitespace is intentional.

A page is complete when the user's objective is achieved — not when every available space is occupied.

**Academy of Commercial & Supply Chain (ACS) Design Principle: Maximum clarity. Minimum content. Zero redundancy.**
