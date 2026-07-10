<div align="center">
  <h1>Restaurant Management System</h1>
  <p>A full-featured Restaurant Management System built on Laravel 12. Designed to handle secure, role-based workflows across daily restaurant operations.</p>

  <p>
    <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
    <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" />
    <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/PHPUnit-green?style=for-the-badge" alt="PHPUnit" />
    <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap" />
    <img src="https://img.shields.io/badge/AdminLTE-blue?style=for-the-badge" alt="AdminLTE" />
    <img src="https://img.shields.io/badge/Version-1.0-blue?style=for-the-badge" alt="Version 1.0" />
    <img src="https://img.shields.io/badge/License-None-lightgrey?style=for-the-badge" alt="License: None" />
  </p>
</div>

<br>

| Project | Value |
|---------|------|
| **Framework** | Laravel 12 |
| **PHP** | 8.2 |
| **Database** | MySQL |
| **Roles** | 4 |
| **Automated Tests** | 61 |
| **Assertions** | 155 |
| **Main Modules** | 15+ |

<br>

## Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Key Highlights](#key-highlights)
- [Screenshots](#screenshots)
- [Technology Stack](#technology-stack)
- [User Roles](#user-roles)
- [Installation](#installation)
- [Demo Accounts](#demo-accounts)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Security](#security)
- [Future Improvements](#future-improvements)
- [Author](#author)

---

## About the Project

The Restaurant Management System provides a streamlined platform to digitize the entire operational flow of a modern restaurant. 

The application facilitates a natural workflow: it begins with managing customer **Reservations**, which transition into active **Orders** once guests are seated. These orders are instantly transmitted to the **Kitchen** through a dedicated display system, allowing chefs to manage their preparation queue in real time. Once the meal concludes, the workflow moves to **Billing** for invoice generation, discount application, and payment processing. Finally, all operations feed into comprehensive **Reports**, giving management actionable insights on revenue and staff performance.

Every step of this process is strictly isolated by a robust role-based access control system, ensuring staff members only interact with the tools necessary for their specific responsibilities.

---

## Features

### 🔐 Authentication & Authorization
- Authentication
- RBAC
- Policies
- Permission-based UI
- Dynamic Sidebar

### 🍽 Menu Management
- Sections
- Categories
- Subcategories
- Items
- Tags
- Image Upload
- Availability
- Offers

### 📦 Restaurant Operations
- Tables
- Reservations
- Orders
- Kitchen Display
- Order Status

### 💳 Billing & POS
- Invoice Generation
- Payments
- Taxes
- Discounts
- Service Charges

### 📊 Reporting & Analytics
- Dashboard KPIs
- Charts
- CSV Export
- Print Reports
- Revenue Analysis
- Sales Analysis
- Menu Analytics
- Staff Performance

### ⚙ Administration
- Users
- Roles
- Permissions
- Restaurant Settings

---

## ⭐ Key Highlights

- ✔ Role-Based Access Control
- ✔ Service Layer Architecture
- ✔ Automated Testing
- ✔ Dashboard Analytics
- ✔ Kitchen Display System
- ✔ CSV Export
- ✔ Print-ready Reports
- ✔ Image Upload
- ✔ Policy-based Authorization
- ✔ Responsive Admin Interface

---

## Screenshots

### Dashboard
![Dashboard](docs/images/dashboard.PNG)

---

### Login
![Login](docs/images/login.PNG)

---

### Users
![Users](docs/images/users.PNG)

---

### Menu
![Menu](docs/images/menu.PNG)

---

### Tables
![Tables](docs/images/tables.PNG)

---

### Reservations
![Reservations](docs/images/reservations.PNG)

---

### Orders
![Orders](docs/images/orders.PNG)

---

### Kitchen
![Kitchen](docs/images/kitchen.PNG)

---

### Billing
![Billing](docs/images/billing.PNG)

---

### Reports
![Reports](docs/images/reports.PNG)

---

### Settings
![Settings](docs/images/settings.PNG)

---

## Technology Stack

| Technology | Description |
|------------|-------------|
| **Laravel 12** | Core PHP Framework |
| **PHP 8.2** | Programming Language |
| **MySQL** | Relational Database |
| **Blade** | Templating Engine |
| **Bootstrap 4** | CSS Framework |
| **AdminLTE 3** | Admin Dashboard Template |
| **Chart.js** | Data Visualization |
| **Spatie Laravel Permission** | Role-Based Access Control |
| **Intervention Image** | Image Handling |
| **PHPUnit** | Automated Testing |

---

## User Roles

1. **Admin**
   Unrestricted access to the entire platform. Manages users, system settings, global menu configurations, and analytical reports.
2. **Waiter**
   Handles dining room operations. Manages table occupancy, creates and updates customer orders, and oversees reservations.
3. **Cashier**
   Manages financial transactions. Generates invoices from completed orders, processes cash payments, and accesses specific billing reports.
4. **Kitchen Staff**
   Operates strictly within the Kitchen Display System. Views incoming food orders, updates individual item statuses, and notifies staff when dishes are ready.

---

## Installation

```bash
git clone https://github.com/hallem4sure/Restaurant_System.git
cd Restaurant_System

cp .env.example .env

composer install

php artisan key:generate

php artisan migrate --seed

php artisan storage:link

php artisan serve
```

---

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@restaurant.com` | `12345678` |
| **Waiter** | `waiter@restaurant.com` | `12345678` |
| **Cashier** | `cashier@restaurant.com` | `12345678` |
| **Kitchen** | `kitchen@restaurant.com` | `12345678` |

---

## Testing

The application is thoroughly covered by an automated test suite to ensure reliability across all workflows and role permissions.

- **61 Tests**
- **155 Assertions**
- **All Passing**

```bash
php artisan test
```

---

## Project Structure

```text
app/
    Http/
    Models/
    Policies/
    Services/
resources/
    views/
routes/
database/
tests/
```

---

## Security

The platform maintains a highly secure environment through multiple integrated layers:
- **Authentication**: Secure session management for all user logins.
- **Policies**: Fine-grained authorization classes paired with Eloquent models to protect CRUD operations.
- **RBAC**: Spatie-powered role and permission verification directly on application routes.
- **Validation**: Strict `FormRequest` validation classes prevent malformed data injection.
- **CSRF**: Automatic Cross-Site Request Forgery protection on all mutating requests.
- **Password Hashing**: Secure bcrypt password encryption.
- **UI Guards**: Blade directives dynamically hide inaccessible actions and links to prevent unauthorized interactions.

---

## Future Improvements

- Inventory Management
- REST API
- Mobile Application
- QR Menu
- Online Payments

---

## Author

**Abdelhalim Abdelrahim Abdelrhman Abdellah**  
GitHub: [https://github.com/hallem4sure](https://github.com/hallem4sure)

---

*Built with Laravel 12 for learning, portfolio, and restaurant management scenarios.*  
*If you found this project interesting, consider giving it a ⭐ on GitHub.*
