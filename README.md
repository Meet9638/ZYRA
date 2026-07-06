
# ZYRA — AI-Powered Fashion E-Commerce Platform

ZYRA is a full-stack fashion e-commerce web application built as a final-year BCA capstone project. It combines a standard e-commerce shopping experience with an AI-based size recommendation engine to help customers pick the right fit, along with secure OTP authentication and integrated online payments.

**Status:** Academic project (Final Year, BCA) — Feb 2026 – Apr 2026

---

## Features

- **User Authentication** — OTP-based registration and login for secure access
- **AI Size Recommendation Engine** — Suggests the right size to customers based on product and fit data
- **Product Catalog & Browsing** — Browse fashion products with categories and details
- **Cart & Order Management** — Add to cart, place orders, and track order status
- **Payment Integration** — Razorpay integration for secure, real-time online payments
- **Admin Panel** — Manage products, orders, and store data

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11, PHP 8.3 |
| Frontend | Blade Templates, CSS |
| Database | MySQL |
| Payments | Razorpay API |
| Auth | OTP-based Authentication |

---

## Team

This project was built by a team of 3 as a final-year academic project:

- **Meet Parmar** — Backend Developer (application logic, database, API integrations, authentication, AI size recommendation integration, payment gateway)
- **[Teammate Name]** — UI/UX & Flow Design
- **[Teammate Name]** — UI/UX & Flow Design

---

## Getting Started

### Prerequisites
- PHP 8.3+
- Composer
- MySQL
- Node.js & npm

### Installation

```bash
# Clone the repository
git clone https://github.com/Meet9638/ZYRA.git
cd ZYRA

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file and configure it
cp .env.example .env
# Update DB_* and RAZORPAY_* values in .env

# Generate application key
php artisan key:generate

# Run migrations (and seeders, if available)
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

The app should now be running at `http://127.0.0.1:8000`.

---

## Demo Credentials

For evaluation/testing purposes, a seeded admin account is available:

| Field | Value |
|---|---|
| Email | `admin@zyra.com` |
| Password | `zyra@123` |
| Role | Super Admin |

> ⚠️ These are demo credentials for academic evaluation only and should never be used in a production environment.

---

## Project Structure

```
ZYRA/
├── app/            # Application logic (models, controllers, services)
├── bootstrap/      # Framework bootstrap files
├── config/         # Configuration files
├── database/       # Migrations, seeders, factories
├── public/         # Publicly accessible entry point & assets
├── resources/      # Blade views, CSS, JS source
├── routes/         # Web & API route definitions
├── storage/        # Logs, cache, uploaded files
├── tests/          # Automated tests
└── artisan         # Laravel CLI tool
```

---

## Acknowledgements

Built as part of the Bachelor of Computer Applications (BCA) final-year curriculum at LJ Institute of Computer Application, Ahmedabad.

