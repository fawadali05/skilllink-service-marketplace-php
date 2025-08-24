<<<<<<< HEAD
# skilllink-service-marketplace-php
=======

# SkillLink — Service Marketplace (PHP + Bootstrap)

A multi-role marketplace where **consumers** hire **local providers** (plumbers, electricians, welders, painters, carpenters, etc.). Includes **admin panel**, provider approval, bookings, and reviews.

## ✨ Features
- Bootstrap 5 UI, responsive & modern
- Roles: Consumer, Provider, Admin
- Auth (secure passwords), sessions
- Provider approval workflow (admin)
- Browse/filter by category & city
- Booking requests (accept/decline/complete)
- Reviews after completion
- Category manager (admin)
- MySQL with PDO

## 🧪 Quick Start (XAMPP/WAMP/Laragon)
1. Create a database named **`servicemarket`**.
2. Copy the project folder to your web root (e.g., `htdocs/service-marketplace-php`).
3. Open `http://localhost/service-marketplace-php/setup.php` to auto-create tables + seed data.
4. Login as admin: **admin@example.com / admin123**.
5. Update DB settings in `includes/config.php` if needed.

## 📁 Structure
```
/assets        CSS/JS
/includes      config + layout
/admin         admin panel (dashboard, categories, bookings)
/helpers       small POST handlers
```
## 🔐 Notes
- Provider registration = **pending** until admin approves.
- Consumer can cancel **pending** bookings.
- Provider can **accept/complete** jobs.
- After completion, consumer can leave a **review**.

## 📦 Tech
- PHP 8+, MySQL 5.7+/MariaDB, Bootstrap 5
- No external frameworks — easy to present for FYP.

## 🛠️ Customization
- Change site title in `includes/config.php` (`APP_NAME`).
- Add/rename categories in **Admin → Categories**.
- Extend DB schema if needed (pricing, location coords, chat, payouts, etc.).
>>>>>>> 18bbbc6 (Initial commit)
