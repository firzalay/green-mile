<p align="center">
  <img src="public/images/greenmile_logo.png" alt="GreenMile Logo" width="180" />
</p>

<h1 align="center">GreenMile</h1>

<p align="center">
  <strong>Eco-Friendly Running Event Platform</strong><br />
  A platform for organizing, joining, and tracking sustainable running events.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
</p>

---

## 📖 About GreenMile

**GreenMile** is a web-based platform that connects eco-conscious runners with sustainable running events. The platform supports three types of users:

- **Super Admin** — Manages the platform, approves organizers, and oversees all events.
- **Organizer** — Creates and manages eco-friendly running events, defines checkpoints, and tracks participants.
- **Participant** — Registers for events, scans QR codes at checkpoints, earns points, and redeems rewards.

### ✨ Key Features

- 🏃 **Event Management** — Create, publish, and manage running events with routes and checkpoints.
- 📍 **QR Checkpoint Scanning** — Participants scan QR codes at each checkpoint to log progress.
- 🏆 **Leaderboard & Points** — Track rankings and earn points for completing eco-friendly milestones.
- 🎁 **Reward Redemption** — Redeem earned points for eco-friendly rewards.
- 👥 **Multi-Role Access** — Separate dashboards and permissions for admins, organizers, and participants.
- 📱 **PWA Support** — Installable as a Progressive Web App on mobile devices.

---

## 🛠️ Tech Stack

| Layer       | Technology                              |
|-------------|----------------------------------------|
| Backend     | Laravel 13.x (PHP 8.3+)               |
| Frontend    | Blade Templates + TailwindCSS 4.x      |
| Bundler     | Vite 8.x                              |
| Database    | MySQL 8.x                             |
| QR Code     | `linkxtr/laravel-qrcode`              |
| Testing     | Pest 4.x + PHPUnit 12.x              |

---

## ✅ Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.3 with extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`
- **Composer** >= 2.x — [getcomposer.org](https://getcomposer.org)
- **Node.js** >= 20.x & **npm** >= 10.x — [nodejs.org](https://nodejs.org)
- **MySQL** >= 8.x (or MariaDB >= 10.6)
- **Git** — [git-scm.com](https://git-scm.com)

---

## 🚀 Installation

Follow these steps to get GreenMile running on your local machine.

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/greenmile.git
cd greenmile
```

> Replace `your-username/greenmile` with the actual repository URL.

---

### 2. Install PHP Dependencies

```bash
composer install
```

---

### 3. Set Up Environment File

```bash
cp .env.example .env
```

Then open `.env` and update the database credentials:

```env
APP_NAME=GreenMile
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=greenmile       # Your database name
DB_USERNAME=root            # Your MySQL username
DB_PASSWORD=                # Your MySQL password
```

---

### 4. Generate Application Key

```bash
php artisan key:generate
```

---

### 5. Create the Database

Create a new MySQL database named `greenmile` (or whatever you set in `.env`):

```sql
CREATE DATABASE greenmile CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 6. Run Migrations & Seeders

```bash
php artisan migrate --seed
```

This will create all the database tables and seed them with sample data including default user accounts.

---

### 7. Install Node.js Dependencies & Build Assets

```bash
npm install
npm run build
```

---

### 8. Start the Development Server

```bash
composer run dev
```

This starts Laravel's development server, the queue worker, and the Vite dev server concurrently. The app will be available at **http://localhost:8000**.

Alternatively, you can run them separately:

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Vite (for hot-reloading)
npm run dev

# Terminal 3 — Queue worker
php artisan queue:listen --tries=1
```

---

## 🔐 Default Accounts

After running `php artisan migrate --seed`, the following accounts are available:

| Role         | Email                      | Password   |
|--------------|----------------------------|------------|
| Super Admin  | `superadmin@example.com`   | `password` |
| Organizer    | `organizer@example.com`    | `password` |
| Organizer 2  | `organizer2@example.com`   | `password` |
| Organizer 3  | `organizer3@example.com`   | `password` |
| Participant  | *(registered via sign-up)* | —          |

> ⚠️ **Important:** Change these credentials before deploying to production.

---

## 🧪 Running Tests

```bash
php artisan test --compact
```

To run a specific test file or filter by name:

```bash
php artisan test --compact --filter=EventTest
```

---

## 📁 Project Structure

```
greenmile/
├── app/
│   ├── Http/Controllers/   # Route controllers
│   ├── Models/             # Eloquent models
│   └── ...
├── database/
│   ├── migrations/         # Database migrations
│   ├── factories/          # Model factories
│   └── seeders/            # Database seeders
├── public/
│   └── images/             # Public assets (logos, icons)
├── resources/
│   ├── views/              # Blade templates
│   └── css/                # Application CSS
├── routes/
│   └── web.php             # Web routes
└── tests/                  # Pest/PHPUnit tests
```
---

## 🤝 Contributing

1. Fork the repository
2. Create a new branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

<p align="center">Made with 💚 for a greener planet.</p>
