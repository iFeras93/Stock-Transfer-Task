# Laravel Stock Transfer Module

A comprehensive Laravel application for managing stock transfers between warehouses with clean architecture, design patterns, and role-based permissions.

## 🚀 Features

- **Complete Stock Transfer Workflow**: From creation to completion with proper status transitions
- **Role**: I make a simple role by added ``role`` column to ``users`` table
- **Activity Logging**: Track all actions and status changes with full audit trail
- **Clean Architecture**: Services, Observers, Events, and Listeners for maintainable code
- **API-First Design**: RESTful API with comprehensive validation and error handling
- **Authentication**: Laravel Sanctum for secure API access

### Not implemented (take time to finalize it):
- **Frontend Interface**
- **Role-Based Permissions**: for complex role/permission system, will take more time to implement the idea  

## 📋 Requirements

- PHP 8.1+
- Laravel 10+
- MySQL 8.0+ / PostgreSQL 13+
- Composer

## ⚡ Installation

### 1. Clone and Setup
```bash
git clone <repository-url>
cd stock-transfer-module
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Database Configuration
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_transfer
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations and Seeders
```bash
php artisan migrate
php artisan db:seed
```

### 5. Start the Server
```bash
php artisan serve
```

## 🔐 Authentication

The application uses Laravel Sanctum for API authentication.

### Test Users
After running seeders, you can use these test accounts:

| Email | Password | Role | Access |
|-------|----------|------|--------|
| admin@example.com | password | Admin | All warehouses |
| manager1@example.com | password | Warehouse Manager | Main Warehouse |
| manager2@example.com | password | Warehouse Manager | Secondary Warehouse, Distribution Center |
| shipping@example.com | password | Shipping Integration | System access |

## 📚 API Documentation

### Authentication Endpoints

#### Register User
```http
POST /api/v1/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com"
    },
    "access_token": "1|abc123...",
    "token_type": "Bearer"
}
```

#### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

#### Get Current User
```http
GET /api/v1/user
Authorization: Bearer {token}
```

### Stock Transfer Endpoints

All stock transfer endpoints require authentication via Bearer token.

#### List Stock Transfers
```http
GET /api/v1/stock_transfers/index?page=1&per_page=15&status=new&warehouse_from_id=1
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)
- `status` (optional): Filter by status
- `warehouse_from_id` (optional): Filter by source warehouse
- `warehouse_to_id` (optional): Filter by destination warehouse

#### Get Status Filter Counts
```http
GET /api/v1/stock_transfers/statusFilter
Authorization: Bearer {token}
```

**Response:**
```json
{
    "statuses": [
        {
            "value": "new",
            "label": "New",
            "color": "blue",
            "count": 5
        }
    ],
    "total": 25
}
```

#### Create Stock Transfer
```http
POST /api/v1/stock_transfers/store
Authorization: Bearer {token}
Content-Type: application/json

{
    "warehouse_from_id": 1,
    "warehouse_to_id": 2,
    "delivery_integration_id": 1,
    "notes": "Monthly inventory transfer",
    "products": [
        {
            "product_id": 1,
            "quantity": 10
        },
        {
            "product_id": 2,
            "quantity": 5
        }
    ]
}
```

#### Change Transfer Status
```http
POST /api/v1/stock_transfers/{id}/change_status
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "preparing",
    "notes": "Ready for preparation"
}
```

#### Get Transfer Details
```http
GET /api/v1/stock_transfers/{id}/info_details
Authorization: Bearer {token}
```

#### Cancel or Return Transfer
```http
POST /api/v1/stock_transfers/{id}/cancel_or_return
Authorization: Bearer {token}
Content-Type: application/json

{
    "action": "cancel",
    "notes": "Inventory not available"
}
```

## 🏗️ Architecture

### Design Patterns Used

- **Service Layer Pattern**: Business logic separation in `StockTransferService`
- **Observer Pattern**: Model event handling with `StockTransferObserver`
- **Event/Listener Pattern**: Activity logging and notifications
- **Repository Pattern**: Data access through Eloquent models
- **Factory Pattern**: For creating consistent data structures

### Key Components

#### Models
- `StockTransfer`: Main transfer entity
- `StockTransferProduct`: Transfer line items
- `StockTransferActivity`: Activity logging
- `User`: Extended with warehouse and role permissions
- `Role`: User roles
- `Warehouse`: Warehouse management
- `Product`: Product catalog

#### Services
- `StockTransferService`: Core business logic for transfers

#### Events
- `StockTransferCreated`: Fired when transfer is created
- `StockTransferStatusChanged`: Fired when status changes

#### Observers
- `StockTransferObserver`: Handles model events

#### Resources
- `StockTransferResource`: API response formatting
- `StockTransferProductResource`: Product line formatting
- `StockTransferActivityResource`: Activity log formatting

#### Requests
- `StoreStockTransferRequest`: Transfer creation validation
- `ChangeStatusRequest`: Status change validation
- `RegisterRequest`: User registration validation
- `LoginRequest`: Login validation

## 🧪 Testing with Postman

### Import Collection

1. Download the Postman collection from the repository
2. Import into Postman
3. Set up environment variables:
   - `baseUrl`: `http://localhost:8000`
   - `apiPrefix`: `api/v1`
   - `auth_token`: Bearer token from login response

### Test Flow

1. **Register/Login**: Get authentication token
2. **Create Transfer**: Create a new stock transfer
3. **List Transfers**: View all transfers with filters
4. **Change Status**: Update transfer status
5. **View Details**: Get complete transfer information
6. **Cancel/Return**: Cancel or return a transfer

### Security Considerations

- Use HTTPS in production
- Set proper CORS policies
- Configure rate limiting
- Regular security updates
- Database connection security
- Token expiration policies

## 📝 API Response Examples

### Successful Transfer Creation
```json
{
    "message": "Stock transfer created successfully",
    "data": {
        "id": 1,
        "warehouse_from": {
            "id": 1,
            "name": "Main Warehouse"
        },
        "warehouse_to": {
            "id": 2,
            "name": "Secondary Warehouse"
        },
        "status": {
            "value": "new",
            "label": "New",
            "color": "blue"
        },
        "products": [
            {
                "id": 1,
                "product": {
                    "id": 1,
                    "name": "Laptop Computer",
                    "sku": "LAPTOP-001"
                },
                "quantity": 10
            }
        ],
        "next_allowed_statuses": ["preparing", "cancelled"],
        "created_at": "2024-01-01T10:00:00.000000Z"
    }
}
```

### Error Response
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "warehouse_to_id": [
            "The destination warehouse must be different from the source warehouse."
        ],
        "products.0.quantity": [
            "Product quantity must be at least 1."
        ]
    }
}
```

----

**Built with ❤️ By [Feras Alshaer](https://iferas93.com)**
