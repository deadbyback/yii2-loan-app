# Loan Application System

REST API for loan applications management with admin panel. 
Inspired by test task.

## Requirements

- Docker
- Docker Compose

## Quick Start

```bash
git clone 
cd loan-app
docker-compose up -d
```

The system will automatically:
- Install dependencies
- Initialize application
- Apply migrations
- Setup RBAC
- Generate API documentation

## Access Points

- API: http://localhost:8080
- Admin Panel: http://localhost:8081/admin
- API Documentation: http://localhost:8080/swagger

## Default Admin Credentials

- Email: admin@example.com
- Password: admin123

## Architecture Overview

- Backend: Yii2 Framework (PHP 8.3)
- Database: PostgreSQL 16
- API Documentation: Swagger/OpenAPI
- Authentication: JWT

### Key Design Decisions

1. Clean Architecture:
    - MVC (Model-View-Controller) for base architecture:
      - View for UI
      - Controller should be as thin as possible, 
      responsible only for routing and linking the interface to business logic services
      - Model Layer should store all the business logic of the application, 
      ideally storing DTO, ActiveRecord and other logic in different model layers 
      adhering to the single responsibility principle
    - Service Layer for business logic
    - Repository Pattern for data access
    - Form Models for request validation
    - Singleton (via DI container)

2. Security:
    - JWT-based API authentication
    - RBAC for access control
    - Secure file upload handling

3. Features:
    - User registration and authentication
    - Loan application management
    - Document upload system
    - Yearly random debt clearing
    - Admin panel for management

## API Endpoints

### Authentication
- POST /auth/signup - Register new user
- POST /auth/login - User login
- POST /auth/logout - User logout

### Loans
- POST /loan/create - Create loan application
- GET /loan/list - Get user's loans
- GET /loan/{id} - Get loan details
- POST /loan/{id}/status - Update loan status

### Documents
- POST /document/upload - Upload document
- GET /document/list - Get user's documents

## License

MIT