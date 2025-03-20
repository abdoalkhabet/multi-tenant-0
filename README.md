# Multi-Tenant E-Commerce API

## Project Overview
This is a multi-tenant e-commerce API built with Laravel and Passport for authentication. The application allows multiple tenants (store owners) to manage their own products and orders within a single system. Each tenant has isolated data, ensuring that users can only interact with products and orders associated with their tenant.

The API supports:
- User registration, login/logout
- Product management
- Order processing
- API token-based authentication using Laravel Passport
- Multi-tenancy data isolation
- API testing with PestPHP

## Features
- **Multi-Tenancy:** Each tenant has a separate set of products and orders.
- **Authentication:** User registration, login, and logout with Laravel Passport token-based authentication.
- **Product Management:** CRUD operations for products.
- **Order Management:** View, place, and cancel orders with stock validation.
- **Testing:** API tests with PestPHP.

## Prerequisites
Ensure you have the following installed:
- PHP (>= 8.0)
- Composer (latest version)
- MySQL or SQLite (for testing)
- Git (to clone the repository)

## Setup Instructions
### 1. Clone the Repository
```bash
git clone [https://github.com/abdoalkhabet/multi-tenant-0.git]
cd multi-tenant-ecommerce
```
### 2. Install Dependencies
```bash
composer install
```
### 3. Configure Environment
Copy the `.env.example` file and update database credentials:
```bash
cp .env.example .env
```
Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_tenant_ecommerce
DB_USERNAME=root
DB_PASSWORD=
```
### 4. Generate Application Key
```bash
php artisan key:generate
```
### 5. Run Migrations
```bash
php artisan migrate
```
### 6. Install and Configure Passport
```bash
php artisan passport:install
```
After installation, update `.env` with the generated `PASSPORT_PERSONAL_ACCESS_CLIENT_ID` and `PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET`.

### 7. Seed the Database (Optional)
```bash
php artisan db:seed
```
### 8. Start the Server
```bash
php artisan serve
```
The API will be available at `http://localhost:8000`.

## Running the Application
Use Postman or cURL to access the API.

### Example: Register a User
```bash
curl -X POST http://localhost:8000/api/register \
-H "Content-Type: application/json" \
-d '{"name": "Test User", "email": "test@example.com", "password": "password", "password_confirmation": "password", "tenant_name": "Test Tenant", "owner_name": "Owner Name"}'
```

## API Endpoints

### **Authentication**
| Method | Endpoint       | Description | Middleware        |
|--------|--------------|-------------|------------------|
| POST   | /api/register | Register new tenant owner | throttle:register |
| POST   | /api/login    | Login and get token | throttle:login |
| POST   | /api/logout   | Logout and revoke token | auth:api |

### **Products**
| Method | Endpoint           | Description | Middleware |
|--------|-------------------|-------------|------------|
| GET    | /api/products      | List all products | auth:api |
| POST   | /api/products      | Create a new product | auth:api |
| PUT    | /api/products/{id} | Update a product | auth:api |
| DELETE | /api/products/{id} | Delete a product | auth:api |

### **Orders**
| Method | Endpoint          | Description | Middleware |
|--------|------------------|-------------|------------|
| GET    | /api/orders      | List all orders | auth:api |
| POST   | /api/orders      | Place an order | auth:api |
| DELETE | /api/orders/{id} | Cancel an order | auth:api |

## Testing
This project includes API tests written with PestPHP.

### 1. Install PestPHP
```bash
composer require pestphp/pest --dev
php artisan pest:install
```

### 2. Run Tests
```bash
php artisan test
```
Or using Pest:
```bash
./vendor/bin/pest
```
### Test Coverage
- **AuthTest:** Registration, login, logout.
- **ProductTest:** CRUD operations for products.
- **OrderTest:** Order placement, stock validation, cancellation.

## Project Structure
- **Controllers:** `app/Http/Controllers/API/`
  - `AuthController.php` (Authentication)
  - `ProductController.php` (Product CRUD)
  - `OrderController.php` (Order Management)
- **Routes:** `routes/api.php`
- **Tests:** `tests/Feature/`
- **Models:** `User`, `Tenant`, `Product`, `Order` with relationships.

## Deployment Guide
For deploying to a live server:
1. Upload project files.
2. Configure `.env` for production database and Passport settings.
3. Run migrations and Passport setup:
   ```bash
   php artisan migrate --force
   php artisan passport:install --force
   ```
4. Set up a queue system if needed.
5. Use a process manager like Supervisor for queue workers.


## License
This project is licensed under the MIT License.

## Contact
For support, reach out via `abdoalkhabet556@gmail.com` or open an issue on GitHub.

