# E-commerce API Documentation

## Authentication

All API endpoints require authentication using JWT tokens. Include the token in the Authorization header:

```
Authorization: Bearer <your_token>
```

## Rate Limiting

- General API: 60 requests per minute
- Authentication: 5 requests per minute
- Orders: 30 requests per minute

## Endpoints

### Authentication

#### Login
- **POST** `/api/v1/login`
- **Body:**
  ```json
  {
    "email": "user@example.com",
    "password": "password"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "data": {
      "token": "jwt_token",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com"
      }
    }
  }
  ```

#### Register
- **POST** `/api/v1/register`
- **Body:**
  ```json
  {
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password"
  }
  ```

### Products

#### List Products
- **GET** `/api/v1/products`
- **Query Parameters:**
  - `category_id` (optional)
  - `brand_id` (optional)
  - `search` (optional)
  - `sort_by` (optional, default: created_at)
  - `sort_direction` (optional, default: desc)
  - `per_page` (optional, default: 15)

#### Get Product
- **GET** `/api/v1/products/{id}`

#### Create Product (Admin)
- **POST** `/api/v1/products`
- **Body:**
  ```json
  {
    "name": "Product Name",
    "description": "Product Description",
    "price": 99.99,
    "stock": 100,
    "category_id": 1,
    "brand_id": 1,
    "sku": "PROD-001",
    "status": "active",
    "featured": false,
    "images": [file1, file2]
  }
  ```

### Orders

#### List Orders
- **GET** `/api/v1/orders`
- **Query Parameters:**
  - `status` (optional)
  - `date_from` (optional)
  - `date_to` (optional)
  - `sort_by` (optional, default: created_at)
  - `sort_direction` (optional, default: desc)
  - `per_page` (optional, default: 15)

#### Create Order
- **POST** `/api/v1/orders`
- **Body:**
  ```json
  {
    "shipping_address_id": 1,
    "billing_address_id": 1,
    "payment_method": "credit_card",
    "shipping_method": "standard",
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      }
    ],
    "notes": "Optional order notes"
  }
  ```

#### Cancel Order
- **POST** `/api/v1/orders/{id}/cancel`

### Cart

#### Get Cart
- **GET** `/api/v1/cart`

#### Add to Cart
- **POST** `/api/v1/cart`
- **Body:**
  ```json
  {
    "product_id": 1,
    "quantity": 2
  }
  ```

#### Update Cart Item
- **PUT** `/api/v1/cart/{id}`
- **Body:**
  ```json
  {
    "quantity": 3
  }
  ```

#### Remove from Cart
- **DELETE** `/api/v1/cart/{id}`

### Addresses

#### List Addresses
- **GET** `/api/v1/addresses`

#### Create Address
- **POST** `/api/v1/addresses`
- **Body:**
  ```json
  {
    "address_line1": "123 Main St",
    "address_line2": "Apt 4B",
    "city": "New York",
    "state": "NY",
    "postal_code": "10001",
    "country": "USA",
    "is_default": true
  }
  ```

## Error Responses

All error responses follow this format:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error details"]
  }
}
```

Common HTTP Status Codes:
- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 429: Too Many Requests
- 500: Server Error 