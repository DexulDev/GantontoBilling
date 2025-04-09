# GantontoBilling
## NOT FINISHED
LaraBilling is a Laravel 12 lightweight and efficient invoicing system designed to streamline the process of generating and managing invoices.

## 🚀 Features
- **Invoice Generation** – Easily create invoices with customizable details.
- **Client Management** – Store and manage customer information efficiently.
- **Payment Tracking** – Keep records of paid and pending invoices.
- **Tax & Discounts Handling** – Automatically apply taxes and discounts.
- **PDF & Export Options** – Generate invoices as PDFs or structured data for accounting.
- **Secure & Scalable** – Designed with modern security standards and future expansion in mind.

## 🛠️ Tech Stack
- **Backend:** Laravel
- **Frontend:** Vue.js
- **Database:** MySQL but can handle another databases if you configure them.
- **Authentication:** OAuth
- **Deployment:** Railway App
- **Payment Processing:** Stripe

## 📌 Installation
1. Clone the repository:
   ```sh
   git clone https://github.com/DexulDev/gatontobilling.git
   cd gatontobilling
   ```
2. Install dependencies:
   ```sh
   composer install
   npm install 
   ```
3. Set up the environment:
   ```sh
   cp .env.example .env
   php artisan key:generate
   ```
4. Configure the database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

5. Configure Stripe API keys in the `.env` file:
   ```
   STRIPE_KEY=your_stripe_publishable_key
   STRIPE_SECRET=your_stripe_secret_key
   STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
   ```

6. Run migrations and seed the database:
   ```sh
   php artisan migrate --seed
   ```

7. Start the development server:
   ```sh
   php artisan serve
   ```
   If using a frontend, start it separately with:
   ```sh
   npm run dev
   ```

## 🔄 Using Stripe CLI for Local Testing

To test Stripe webhooks locally:

1. [Download and install the Stripe CLI](https://stripe.com/docs/stripe-cli)

2. Login to your Stripe account:
   ```sh
   stripe login
   ```

3. Start listening to webhook events:
   ```sh
   stripe listen --forward-to http://localhost:8000/stripe/webhook
   ```

4. The CLI will output a webhook signing secret. Copy this to your `.env` file:
   ```
   STRIPE_WEBHOOK_SECRET=whsec_your_signing_secret_from_cli
   ```

## 💡 Future Enhancements
- Multi-currency support
- Automated email invoicing

## 🛠️ Actually working on
- OAuth support

## 📜 License
This project is licensed under the MIT License.

🚀 Built with passion by [DexulDev](https://github.com/DexulDev) for Gatonto Solutions S.A. de C.V.
