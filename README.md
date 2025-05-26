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

7. Start the development server:
```bash
php artisan serve
```

## API Documentation

### Authentication

#### Login
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

Response:
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com"
    },
    "token": "1|abcdef...",
    "token_type": "Bearer"
}
```

#### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

#### Get User Profile
```http
GET /api/v1/me
Authorization: Bearer {token}
```

### Products

#### List Products
```http
GET /api/v1/products
```

#### Get Product Details
```http
GET /api/v1/products/{id}
```

### Categories

#### List Categories
```http
GET /api/v1/categories
```

#### Get Category Details
```http
GET /api/v1/categories/{id}
```

### Brands

#### List Brands
```http
GET /api/v1/brands
```

#### Get Brand Details
```http
GET /api/v1/brands/{id}
```

### Cart Management

#### View Cart
```http
GET /api/v1/cart
Authorization: Bearer {token}
```

#### Add to Cart
```http
POST /api/v1/cart
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 2
}
```

### Orders

#### Create Order
```http
POST /api/v1/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "shipping_address_id": 1,
    "payment_method": "credit_card"
}
```

#### List Orders
```http
GET /api/v1/orders
Authorization: Bearer {token}
```

### Return Requests

#### Create Return Request
```http
POST /api/v1/return-requests
Authorization: Bearer {token}
Content-Type: application/json

{
    "order_id": 1,
    "reason": "damaged",
    "description": "Product arrived damaged"
}
```

## Role-based Access

The API implements role-based access control with the following roles:

- **Customer**: Basic user access
- **B2B**: Business customer access
- **Admin**: Full administrative access

## Error Handling

The API uses standard HTTP status codes and returns errors in the following format:

```json
{
    "message": "Error message",
    "errors": {
        "field": ["Error details"]
    }
}
```

## Security

- All API endpoints (except public ones) require authentication
- Passwords are hashed using Laravel's secure hashing
- CSRF protection is enabled
- Rate limiting is implemented
- Input validation is enforced

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
composer run lint
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
