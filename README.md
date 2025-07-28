# Vogueify

Vogueify is a modern fashion e-commerce platform built with **Laravel 12**, **Inertia.js**, and **Vue.js**. It offers users a seamless shopping experience with features like user authentication, product search, wishlist management, and responsive design. Whether you're browsing the latest trends or adding items to your wishlist, Vogueify ensures an intuitive and engaging experience for all users.

## Features

-   **User Authentication**: Users can sign up, log in, and log out securely.
-   **User Authorization**: Different access levels for users, allowing personalized experiences.
-   **Wishlist**: Users can add and manage their favorite items in a wishlist.
-   **Product Search**: A dynamic search feature to easily find products based on keywords and categories.
-   **Responsive Design**: The app is fully responsive, offering an optimal browsing experience across all devices (desktop, tablet, mobile).

## Installation & Setup

Follow the steps below to set up the Vogueify app locally:

### 1. Clone the Repository

```bash
git clone https://github.com/Mentorg/vogueify.git
cd vogueify
```

### 2. Install Dependencies

Install PHP and JavaScript dependencies using Composer and NPM.

```
composer install
npm install
```

### 3. Set Up Environment File

Copy the `.env.example` file to create your local `.env` file:

```
cp .env.example .env
```

### 4. Generate Application Key

Run the following command to generate your application's key:

```
php artisan key:generate
```

### 5. Configure Stripe

To enable Stripe payments, follow these steps:

#### a. Create a Stripe Account

If you haven't already, [sign up at Stripe](https://stripe.com) and log in to your dashboard.

#### b. Add Stripe Keys to `.env`

Add the following environment variables to your `.env` file:

```
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
```

#### c. Install Stripe CLI (For Local Development)

If you're working locally and need to test webhooks:

1. [Install the Stripe CLI](https://stripe.com/docs/stripe-cli)
2. Authenticate your Stripe account:

```
stripe login
```

3. Forward webhook events to your Laravel app:

```
stripe listen --forward-to http://localhost:8000/webhook/stripe
```

4. Copy the `whsec_...` token from the CLI output and paste it into your `.env` as `STRIPE_WEBHOOK_SECRET`.

---

### 6. Run Migrations

Create the necessary database tables and run the seeders:

```
php artisan migrate --seed
```

### 7. Start the Development Server

You can now start the local development server:

```
php artisan serve
```

The app will be accessible at http://localhost:8000.

### 8. Run the Frontend Development Server

For frontend hot-reloading and asset compilation, run:

```
npm run dev
```

## Conclusion

Vogueify is a powerful, flexible, and user-friendly fashion app designed to offer a seamless shopping experience. With features like authentication, authorization, wishlists, and responsive design, Vogueify is built to scale and adapt to modern web standards. We hope this app provides you with a great starting point for any fashion e-commerce project.

## License

This project is licenced under the [MIT License](https://opensource.org/license/mit).
