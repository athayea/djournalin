# Djournalin

Djournalin is a web application built with Laravel designed to help you manage your daily tasks, activities, and journal entries efficiently.

## Features

- **User Authentication:** Secure registration and login system.
- **Dashboard Overview:** A centralized hub to view your daily tasks and activities.
- **Task Management:** Create, read, update, and delete tasks easily.
- **Responsive Design:** A clean, user-friendly interface that works on different devices.

## Tech Stack

- **Backend:** [Laravel](https://laravel.com/) (PHP)
- **Frontend:** Blade Templates, HTML, CSS, JavaScript
- **Database:** MySQL / SQLite

## Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- A database server (MySQL, PostgreSQL, or SQLite)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/athayea/djournalin.git
   cd djournalin
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup:**
   Copy the example `.env` file and configure your database and other environment variables:
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations:**
   Run the database migrations to create the required tables:
   ```bash
   php artisan migrate
   ```

7. **Serve the Application:**
   Start the local development server:
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000` in your browser to view the application.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
