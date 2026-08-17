# Developer Next Steps & Bug Fixes 🐛🚀

This document serves as a checklist for the next development session to ensure we don't forget where we left off. 

## 1. 🐞 Broken / Incomplete Features to Fix First
- [ ] **Login/Auth Flow**: 
  - Ensure the registration defaults make sense and redirect to the correct onboarding/dashboard.
  - Check the Breeze layouts (`resources/views/layouts/navigation.blade.php`, etc.) to display the newly added `profile_picture` instead of default avatars.
- [ ] **Settings Integration**:
  - The `SettingsController` works, but verify that `\App\Models\Setting::getVal()` is properly caching and retrieving the `site_name` and `site_logo` without hitting the DB on every view load.
  - Verify that the updated logo correctly replaces the default logo across all front-facing and admin views.
- [ ] **Profile Polish**:
  - Check if the uploaded profile and cover photos render perfectly on all responsive breakpoints (mobile, tablet, desktop).
- [ ] **Data Integrity (Cascading)**:
  - If a User is deleted, ensure their enrolled courses, quiz attempts, and created job posts (if instructor/admin) are either safely deleted or handled (migrations might need `onDelete('cascade')`).

## 2. 🚀 Next Phase Features (To Be Built)
- [ ] **Gamification Engine**:
  - Implement **Points**: Award points based on quiz score (e.g., `score * 10`).
  - Implement **Streaks**: Track consecutive days a user logs in and completes a lesson/quiz.
  - Implement **Levels**: Create a leveling system based on total points accumulated (e.g., Level 1: 0-100 pts, Level 2: 101-500 pts).
  - Add Gamification UI to the Student Dashboard (progress bars, badges, leaderboard).
- [ ] **Payment Gateway Integration**:
  - Integrate **Stripe** (or PayPal) for course enrollments.
  - Add a checkout flow when a student clicks "Enroll" on a paid course.
  - Implement a webhooks handler to automatically grant access upon successful payment.

## 3. 🎨 Front-End Polish
- [ ] **Course Catalog**: Ensure the user-facing catalog page (`/catalog`) sorts the courses beautifully (Latest first, Recommended).
- [ ] **Landing Page**: Hook up dynamic data (recent courses, job posts) to the landing page if not already done.

---
**Note to AI Assistant for next session:** 
Read this file first, pick up from Section 1 (Broken Features to Fix), and then proceed to Section 2 (Gamification & Payments).
