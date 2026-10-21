# IPDP Layout Architecture Reference

This reference document outlines the complete page layout hierarchy for replicating the `ipdpbd.com` course catalog and category structures.

---

## 1. Page Section Hierarchy

```text
┌─────────────────────────────────────────────────────────────┐
│ 1. Header & Navigation (Logo, Nav Dropdowns, Sign In / CTA) │
├─────────────────────────────────────────────────────────────┤
│ 2. Category Hero Banner (Breadcrumbs, Category Name)        │
├─────────────────────────────────────────────────────────────┤
│ 3. Main Content Grid:                                       │
│    - Filter / Sort Bar (Newest, Price, Recommended)         │
│    - Course Card Grid (3-cols desktop, 2-cols tablet, 1 mob)│
│    - Standard Pagination (Page 1, 2, Next)                  │
├─────────────────────────────────────────────────────────────┤
│ 4. "Running Course Categories" Grid                         │
│    - Thumbnail + Category Title Tiles                       │
├─────────────────────────────────────────────────────────────┤
│ 5. Footer (About, Quick Links, Contacts, Legal & Copyright) │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Category Routing & Taxonomy Scheme

To match `ipdpbd.com/export-import/`, the routing should support both direct clean slugs and canonical paths:

- Canonical Category URL: `/category/{slug}` or direct `/{slug}` (matching existing or configured categories like `export-import`, `supply-chain-management`, `accounting`).
- Canonical All Courses URL: `/courses` or `/catalog`.
- Single Course URL: `/courses/{slug_or_id}`.
- Instructor Profile URL: `/instructors/{id}` or `/teachers/{id}`.

### Breadcrumb Rules
- Root: `Home` (`route('home')`)
- Middle: `Courses` (`route('catalog.index')`)
- Leaf: Current Category (`$categoryName`)

---

## 3. Responsive Breakpoints

- **Desktop (>= 1024px)**:
  - 3 or 4 columns for courses.
  - Full horizontal navigation with dropdowns.
- **Tablet (640px - 1023px)**:
  - 2 columns for courses.
  - Collapsible navigation menu.
- **Mobile (< 640px)**:
  - 1 column for courses.
  - Sticky bottom action bar on course detail pages (`fixed bottom-3 left-1/2 -translate-x-1/2 w-[90%] z-50`).
