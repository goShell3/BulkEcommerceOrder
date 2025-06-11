# E-commerce API

A robust e-commerce API built with Laravel, featuring authentication, product management, order processing, and more.

## Features

- 🔐 Secure Authentication with Laravel Sanctum
- 🛍️ Product Management
- 🏷️ Category & Brand Management
- 🛒 Shopping Cart
- 📦 Order Processing
- 🔄 Return Request System
- 💼 B2B Features
- 🚚 Shipping Management
- 💳 Payment Gateway Integration
- 👥 User Management
- 🎯 Role-based Access Control

## Requirements

- PHP >= 8.1
- MySQL >= 8.0
- Composer
- Laravel >= 10.0

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd <project-directory>
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations and seeders:
```bash
php artisan migrate --seed
```

7. Create admin user and role:
```bash
php artisan tinker
```
Then run:
```php
// Create admin role
$role = \App\Models\Role::firstOrCreate(['slug' => 'admin', 'name' => 'Administrator']);

// Create admin user
$user = \App\Models\User::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin User',
        'password' => bcrypt('admin123'),
        'phone' => '1234567890',
        'address' => 'Admin Address'
    ]
);

// Attach admin role to user
$user->roles()->sync([$role->id]);
```

8. Start the development server:
```bash
php artisan serve
```

## Testing the API

### 1. Authentication

#### Login as Admin
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "admin123"
}
```

Response:
```json
{
    "token": "8|t9mWqMouqM3IMYMVO1RfXi7viWSr0EfGGystusne6972bc87",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com"
    }
}
```

Save the token for subsequent requests.

### 2. Shipping Management

#### Create Shipping Carrier
```http
POST /api/v1/shipping-carriers
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "name": "DHL Express",
    "code": "dhl",
    "description": "Fast international shipping",
    "is_active": true,
    "tracking_url": "https://www.dhl.com/tracking",
    "api_credentials": {
        "api_key": "test_key",
        "account_number": "123456"
    }
}
```

#### Create Shipping Method
```http
POST /api/v1/shipping-methods
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "name": "Express Delivery",
    "code": "express",
    "carrier_id": 1,
    "description": "Fast delivery within 24 hours",
    "base_price": 15.99,
    "is_active": true,
    "estimated_delivery_time": "24 hours",
    "weight_limit": 20,
    "dimension_limit": {
        "length": 100,
        "width": 50,
        "height": 50
    }
}
```

### 3. Payment Gateway Configuration

#### Create Payment Gateway
```http
POST /api/v1/payment-gateways
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "name": "Stripe",
    "code": "stripe",
    "description": "Credit card payments via Stripe",
    "is_active": true,
    "credentials": {
        "publishable_key": "pk_test_123",
        "secret_key": "sk_test_123",
        "webhook_secret": "whsec_123"
    },
    "test_mode": true
}
```

## Testing Tools

1. Use Postman or similar API testing tool
2. Always include these headers:
   ```
   Accept: application/json
   Content-Type: application/json
   Authorization: Bearer {your-token}
   ```
3. For protected routes, make sure to:
   - Login first to get the token
   - Include the token in the Authorization header
   - Use the correct HTTP method (GET, POST, PUT, DELETE)

## Common Operations

### Shipping Carriers
- List: GET `/api/v1/shipping-carriers`
- Create: POST `/api/v1/shipping-carriers`
- Get: GET `/api/v1/shipping-carriers/{id}`
- Update: PUT `/api/v1/shipping-carriers/{id}`
- Delete: DELETE `/api/v1/shipping-carriers/{id}`

### Shipping Methods
- List: GET `/api/v1/shipping-methods`
- Create: POST `/api/v1/shipping-methods`
- Get: GET `/api/v1/shipping-methods/{id}`
- Update: PUT `/api/v1/shipping-methods/{id}`
- Delete: DELETE `/api/v1/shipping-methods/{id}`

### Payment Gateways
- List: GET `/api/v1/payment-gateways`
- Create: POST `/api/v1/payment-gateways`
- Get: GET `/api/v1/payment-gateways/{id}`
- Update: PUT `/api/v1/payment-gateways/{id}`
- Delete: DELETE `/api/v1/payment-gateways/{id}`

## Troubleshooting

1. If you get "Unauthenticated" error:
   - Make sure you're logged in
   - Check if the token is valid
   - Verify the Authorization header format

2. If you get "Role not found" error:
   - Verify the admin role exists
   - Check if the user has the admin role assigned

3. If you get database errors:
   - Make sure all migrations are run
   - Check database connection settings
   - Verify table structure

## API Documentation

### Authentication

#### Login
```