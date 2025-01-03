# S-Forum: Laravel Project with Stripe Payment Integration

S-Forum is a platform that allows users to create and share blogs across various categories with the community. This project is a RESTful API built with Laravel that integrates Stripe payment functionality.

## Prerequisites

- **PHP**: 8.2.12
- **Laravel**: 9.52.17
- **Composer**
- **MySQL**

---

## Setup Instructions

Follow these steps to set up and run the project:

### Step 1: Configure Environment Variables

Ensure the `.env` file is properly set up with all necessary configurations. If any variables are missing, update them accordingly:

- **Database Configuration**: Provide your database connection details (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
- **Stripe Keys**: Add your Stripe API keys (`STRIPE_SECRET_KEY` and `STRIPE_PUBLISHABLE_KEY`).
- **Mail**: Add your `MAIL_USERNAME` and `MAIL_PASSWORD`
- **Passport Client Secret**: Will be configured in Step 4.

### Step 2: Install Dependencies

Run the following command to install all required PHP dependencies:

```bash
composer install
```

### Step 3: Set Up the Database

Run the migrations and seeders:

```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

### Step 4: Set Up Passport

Generate a personal access client for Laravel Passport:

```bash
php artisan passport:client --personal
```

- When prompted to enter a name for the client, input a name or leave it blank.
- Copy the generated secret key and add it to the `.env` file:

```env
PASSPORT_CLIENT_SECRET=your_generated_key_here
```

### Step 5: Run the Project

Start the Laravel development server and optimize the application:

```bash
php artisan serve
php artisan optimize
```

---

## Additional Notes

- Ensure you have the correct permissions set for storage and bootstrap/cache directories:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```
- For production, consider using a web server like Nginx or Apache and configure the `.env` file accordingly.

---

## Support

For issues or questions, please open an issue in this repository or contact the developer directly.

