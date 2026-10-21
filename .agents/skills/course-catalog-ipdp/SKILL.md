---
name: course-catalog-ipdp
description: >-
  Provides comprehensive specifications and guidelines for implementing, styling, and
  maintaining the IPDP-style (ipdpbd.com) course catalog, category pages, course cards,
  and single course overview layout within CAB Academy. Use this skill whenever building or
  modifying course listing pages, category taxonomies, course card UI, instructor overlays,
  course accordions, or replicating the IPDP visual structure.
---

# Course Catalog & Category Design (IPDP Style)

This skill guides the implementation and maintenance of the course catalog and category structures inspired by `ipdpbd.com` (such as `https://ipdpbd.com/export-import/`), tailored to integrate seamlessly with CAB Academy's LMS and student portal features.

---

## 1. Overview of the IPDP Structure

The IPDP course catalog layout consists of 5 core architectural blocks:

1. **Top Navigation & Brand Header**:
   - Sticky/semi-transparent navigation with Academy logo, main links (`Home`, `Courses`, `Day Long Courses`, `Certificate Courses`, `Teachers/Instructors`, `About Us`, `Contact`), and authenticated user buttons (`Sign In` or `Dashboard`).

2. **Category Hero & Breadcrumb Banner**:
   - Displays the current category title (e.g., `Export – Import`, `Supply Chain Management`, `Accounting`).
   - Breadcrumb navigation: `Home / Courses / {Category Name}`.
   - Clean, light background with subtle contrast.

3. **Signature Course Grid (The Core Element)**:
   - 3 columns on desktop (`grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6`), 2 on tablet, 1 on mobile.
   - **Course Card Anatomy**:
     - **Thumbnail Image**: 1:1 aspect ratio square container with subtle hover zoom effect (`group-hover:scale-105 transition-transform duration-500`).
     - **Floating Instructor Avatar**: Circular teacher badge (80x80px rounded-full with border and shadow) positioned prominently overlapping the thumbnail, linking to the teacher profile.
     - **Floating / Prominent Price Tag**: BDT formatted price badge (e.g., `৳9,000.00` or `Free`), positioned cleanly.
     - **Category Tag**: Clickable pill badge displaying the course category (e.g., `Export – Import`).
     - **Course Title**: Clamped to 2 lines (`line-clamp-2`), bold heading linking to the course overview.
     - **Short Excerpt / Target Audience**: 2-line brief synopsis explaining who the course is for and what will be gained.
     - **Course Meta Bar**:
       - Left: Clock/Calendar icon with course start date or duration (e.g., `<svg class="w-4 h-4">` + `23 October, 2026`).
       - Right: Primary CTA button (`Enroll Now` / `View Details`).
   - **Pagination**: Numbered pagination (`1`, `2`, `Next >`).

4. **"Running Course Categories" Carousel / Grid Section**:
   - Secondary section placed directly below the course grid.
   - Section heading: `Running Course Categories`.
   - Category cards with category thumbnail image and name, allowing users to switch between disciplines (e.g., `Accounting`, `Civil Construction`, `Export - Import`, `Fire Safety`, `HR`, `Taxation`, `Law`, `Leadership`).

5. **Course Single Page (Overview & Sales Page)**:
   - Header with course title, price in BDT (`Price TK: 9,000.00`), course schedule, format (Online / In-person Banani Campus).
   - Instructor spotlight card (circular avatar, name, designation, brief credentials).
   - Curriculum Accordions: Expandable sections and lessons with video / reading icons.
   - Target Audience & Methodology tabs.
   - Sticky / Floating "Enroll Now" bar (especially for mobile screens with a fixed bottom bar).

---

## 2. Integration with CAB Academy Features

When replicating this structure, preserve and enhance CAB Academy's core capabilities:
- **Guest-Friendly Public Browsing**: Public visitors must be able to explore all categories and course overviews without forced login.
- **Offline & Direct Enrollment Flow**: Clicking `Enroll Now` routes authenticated students directly to the offline payment (bKash/Nagad/Rocket/Bank) instructions or immediate enrollment if free, and prompts guests to quickly log in/register with an intended redirect.
- **Student Dashboard Synergy**: Enrolled courses appear in the student's personal dashboard (`/dashboard`) with progress bars and lesson player (`/courses/{course}/lessons/{lesson}`).
- **Gamification & Quizzes**: Quizzes and XP rewards are linked seamlessly to completed sections.

---

## 3. Reference Documentation

For detailed HTML/Blade component snippets and layout models:
- [IPDP Layout Architecture](./references/ipdp_layout_architecture.md)
- [Card Component Specification](./references/card_component_spec.md)
