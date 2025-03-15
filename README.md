# GatontoBilling

GatontoBilling is a lightweight and efficient invoicing system designed to simplify invoice creation and management. Built with scalability and security in mind, it provides businesses with an intuitive platform to generate, store, and track invoices seamlessly.

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
- **Deployment:** Laravel Forge

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
4. Configure the database in the `.env` file and run migrations:
   ```sh
   php artisan migrate --seed
   ```
5. Start the development server:
   ```sh
   php artisan serve
   ```
   If using a frontend, start it separately with:
   ```sh
   npm run dev
   ```

## 💡 Future Enhancements
- Multi-currency support
- Automated email invoicing
- Integration with accounting software

## 📜 License
This project is licensed under the MIT License.

---
🚀 Built with passion by [DexulDev](https://github.com/DexulDev) & Gatonto Solutions S.A. de C.V.
