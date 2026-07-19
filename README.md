# Ticketing System

A clean, Laravel-based ticketing system built with Laravel 12, Livewire Flux/Volt, Tailwind CSS, and Vite.

## 🚀 Project Overview

This project delivers a simple support ticket workflow with two user roles:

- **Admin**
  - Dashboard overview
  - User management
  - Reports section
  - Settings page
- **User**
  - Dashboard view of personal tickets
  - Create new support tickets
  - View ticket details

The system is designed for fast development, modern UI, and an easy deployment flow.

## ✨ Key Features

- Laravel 12 application structure
- Role-based access control (`admin` and `user`)
- Ticket creation and tracking
- User management interface for admins
- Livewire-powered settings pages using Volt
- Tailwind CSS styling with Vite asset pipeline
- Fast local development with `npm run dev`

## 🧱 Built With

- PHP `^8.2`
- Laravel `^12.0`
- Livewire Flux `^2.0`
- Livewire Volt `^1.6.7`
- Tailwind CSS `^4.0.7`
- Vite `^6.0`
- Axios for frontend HTTP handling

## 🛠️ Installation

1. Clone the repository

```bash
git clone <repository-url> ticketing-system
cd ticketing-system
```

2. Install PHP dependencies

```bash
composer install
```

3. Install JavaScript dependencies

```bash
npm install
```

4. Copy the environment file and generate an app key

```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in `.env`

6. Run migrations and seeders

```bash
php artisan migrate --seed
```

## ▶️ Running Locally

Start the Laravel server and Vite development tools:

```bash
php artisan serve
npm run dev
```

If you want a combined development workflow, use:

```bash
composer dev
```

## 📦 Database Schema

The app includes a `tickets` table with the following fields:

- `id`
- `user_id`
- `subject`
- `description`
- `status` (`open` by default)
- `created_at`
- `updated_at`

A `Ticket` model is available with relationships to the user and assigned user.

## 🧪 Testing

Run the test suite with Pest or PHPUnit:

```bash
./vendor/bin/pest
```

Or using Artisan:

```bash
php artisan test
```

## 📁 Routes Overview

- `/` - Public landing page
- `/admin/dashboard` - Admin dashboard
- `/admin/users` - Admin user management
- `/admin/reports` - Admin reports
- `/admin/settings` - Admin settings
- `/userDashboard` - User dashboard
- `/userDashboard/tickets` - Ticket list and create flow

## 💡 Notes

- The app uses middleware to protect admin and user routes.
- Settings pages are implemented with Livewire Volt routes.
- Ticket creation is validated to ensure `subject` and `description` are provided.

## 🤝 Contributing

Contributions are welcome. Please open an issue or submit a pull request with improvements.

---

Thank you for exploring the Ticketing System project!