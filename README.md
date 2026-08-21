# CAB Academy

CAB Academy is a modern Learning Management System (LMS) and student portal built with Laravel. It provides a comprehensive platform for managing courses, student enrollments, quizzes, performance tracking, and a job portal.

## Features

- **User Roles:** Distinct dashboards and permissions for Admins and Students.
- **Course Management:** Create, update, and manage courses, sections, lessons, and course materials.
- **Quizzes & Assessments:** Interactive quizzes tied to courses to test student knowledge.
- **Performance Tracking:** Monitor student progress and performance across courses.
- **Job Portal:** Integrated job board allowing students to browse and apply for jobs directly.
- **Payments & Checkout:** Integrated payment processing, payment methods, and enrollment management.
- **Admin Dashboard:** Full administrative control over users, content, site settings, and site frontend.
- **Recycle Bin (Trash):** Safe soft-delete system with restore functionality for courses, jobs, and other entities.

## Tech Stack

- **Framework:** Laravel
- **Language:** PHP 8.2+
- **Database:** MySQL / MariaDB
- **Frontend:** Blade Templating

## Installation & Setup (Local)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd CAB-Academy
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Environment Setup**
   Copy the `.env.example` file and configure your environment variables:
   ```bash
   cp .env.example .env
   ```
   Update the database credentials in your `.env` file.

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations & Seeders**
   This will create the database structure and populate it with default data (like an initial admin account).
   ```bash
   php artisan migrate --seed
   ```

6. **Storage Link**
   Make sure to link your storage directory so uploaded files (like course thumbnails) are accessible:
   ```bash
   php artisan storage:link
   ```

7. **Run the Development Server**
   ```bash
   php artisan serve
   ```
   Access the application at `http://localhost:8000`.

## Shared Hosting Deployment (e.g. InfinityFree)

If you are deploying to a shared host without terminal access (like InfinityFree):
1. Upload your files to the server.
2. Update your `.env` file with the server's database credentials.
3. If you encounter the `1071 Specified key was too long` error, it has already been patched in `AppServiceProvider.php` and the `jobs` migration.
4. Use the temporary utility scripts placed in the `public/` directory (e.g., `https://cabacademy.bd/migrate-fresh.php`) to setup and seed the database.
5. **Warning:** Delete those utility scripts from your server immediately after your database is set up to protect it from being reset by others!

## License
Proprietary / Closed Source.
