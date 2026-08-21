# Next Day Development Plan: The "Student Experience" Phase 🚀

This document outlines the development roadmap for the next session. With the **Admin Panel fully polished**, **Offline Payments verified**, and the **Public Landing/Auth UI redesigned**, the next logical phase is to build out the core interactive experience for the actual users (Students).

## Phase 1: Core Student Dashboard
Now that students can register through our beautiful new auth pages, they need a place to land.
- [ ] **Dashboard Layout**: Build a custom, student-focused dashboard (replacing the default Breeze dashboard).
- [ ] **Enrolled Courses Widget**: Display a grid of courses the student has access to (verified via the offline payment system).
- [ ] **Job Applications Widget**: Show the status of jobs they've applied for through our new internal application system.

## Phase 2: The Course Player & Learning Experience
The most important part of the platform—where the learning happens.
- [ ] **Course Player UI**: Build a split-screen or sidebar UI where the video/material sits on the right, and the curriculum (Sections/Lessons) sits on the left.
- [ ] **Progress Tracking**: Implement logic to mark lessons as "Completed" (`lesson_user` pivot table).
- [ ] **Quizzes & Assessments**: Build the UI for taking quizzes at the end of sections and storing the results in the `QuizAttempt` models.

## Phase 3: The Gamification Engine 🎮
We have the foundational `awardXp` method on the `User` model, but it needs to be integrated into the actual platform flow.
- [ ] **Event Hooks**: Automatically award XP when a student finishes a lesson (e.g., +10 XP) or passes a quiz (e.g., +50 XP).
- [ ] **Streaks Logic Validation**: Ensure the login streaks correctly update based on daily activity.
- [ ] **Gamification UI**: Add a "Level & XP Bar" to the top navigation of the Student Dashboard, and create a Leaderboard page to drive engagement.

## Phase 4: Course Catalog & Discovery
- [ ] **Public Course Catalog (`/courses`)**: Build a beautiful frontend page listing all published courses.
- [ ] **Course Details Page**: Build a sales page for individual courses showing the curriculum, price, instructor, and an "Enroll Now" / "Pay Now" button that routes to the offline payment instructions page.

---

### 📝 Notes for the Next Session:
- We have the deployment scripts ready for InfinityFree (`public/migrate.php`), so ensure that any new features are thoroughly tested locally before the next big code push.
- Keep the aesthetic consistent with the new **glassmorphism** and **premium gradients** we established in the `guest.blade.php` redesign.
