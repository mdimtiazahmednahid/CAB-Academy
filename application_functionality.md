# CAB Academy - Application Functionality Overview

CAB Academy is a modern, comprehensive Learning Management System (LMS) and student portal built on the Laravel framework. It seamlessly integrates course management, student assessments, performance tracking, and a job portal into a single platform.

This document details the total functionality of the application, categorized by user roles and core modules.

---

## 1. User Roles & Access Control

The application uses a role-based access control system with two primary roles:

### **Student (Default)**
* Access to the Student Dashboard.
* Can browse and enroll in courses.
* Can take quizzes and track their own performance.
* Can browse active job postings and submit applications.
* Can manage their profile, upload a profile picture and cover photo, and set preferences.
* Receives system notifications and admin announcements.

### **Administrator**
* Full access to the Admin Dashboard.
* **Content Management:** Create, read, update, and delete Courses, Sections, Lessons, Materials, Quizzes, and Questions.
* **Job Board Management:** Post new jobs, edit existing ones, and manage the job board.
* **User Management:** View all users, edit user details, and delete users.
* **Payment Management:** Track student payments, update payment statuses, and configure available payment methods.
* **System Settings:** Configure site name, logo, frontend layout, and other global settings.
* **Trash / Recycle Bin:** Restore soft-deleted items (like courses and jobs) or permanently delete them.
* Send global announcements to all users.

---

## 2. Core Modules

### **Course Management**
The core LMS functionality allows structured learning content.
* **Courses:** The top-level container for learning content. Can be marked as `published` (visible to students) or draft. Paid courses integrate with the checkout system.
* **Sections:** Modules or chapters within a course to organize lessons.
* **Lessons:** Individual learning units within a section (text, video, etc.).
* **Materials:** Downloadable resources (PDFs, ZIP files) attached to a course for students to use.
* **Progress Tracking:** The system tracks which lessons a student has completed.

### **Quizzes & Assessments**
* **Quizzes:** Interactive tests tied to specific courses.
* **Questions:** Multiple questions can be added to a quiz.
* **Quiz Attempts:** The system records each attempt a student makes, calculating scores and logging performance for the gamification/performance engine.

### **Job Portal**
A built-in job board connecting students with career opportunities.
* **Job Posts:** Admins can create active job listings.
* **Job Applications:** Students can apply to jobs directly through the platform.
* The system tracks all applications submitted by the student.

### **Payments & Checkout**
* **Enrollment & Checkout:** Students can enroll in free courses or check out for paid courses.
* **Payment Tracking:** Records of all transactions.
* **Payment Methods:** Admins can define and toggle available payment methods.

### **Performance & Gamification (In Progress)**
* **Performance Dashboard:** Students can view their overall progress, quiz scores, and course completion rates.
* *(Upcoming)* Points, streaks, and levels based on student engagement and quiz performance.

### **User Profile & Onboarding**
* **Onboarding Flow:** New users are directed to an onboarding screen to set up their initial preferences.
* **Profile Management:** Users can update their personal information, passwords, and notification preferences.

### **Admin System & Settings**
* **Dynamic Settings:** Global settings (like site name, logo) are stored in the database and cached for performance.
* **Frontend Management:** Admins can customize aspects of the public-facing landing page.
* **Soft Deletes (Recycle Bin):** Critical entities like courses and jobs are soft-deleted first, allowing admins to restore them from a dedicated Trash interface to prevent accidental data loss.

---

## 3. Automated Workflows & Notifications

* **Notifications:** In-app notification system alerts users about course enrollments, quiz results, and job application statuses.
* **Announcements:** Admins can broadcast messages to all users (e.g., system updates, new courses).
* **Database Optimization:** Built-in route (`/reset-db`) and scripts to easily reset, optimize, and seed the database for development and testing.

---

## 4. Technical Architecture
* **Backend:** Laravel (PHP 8.2+)
* **Database:** Relational schema (MySQL/MariaDB) utilizing Eloquent ORM with robust relationships (e.g., Users `have many` Enrolled Courses, Courses `have many` Sections).
* **Frontend:** Blade templating with Tailwind CSS for responsive design. Uses Laravel Breeze for authentication scaffolding.
* **Routing:** Protected routes using middleware for `auth`, `verified`, and custom `IsAdmin` / `IsStrictAdmin` checks.
