# Cafe Management System

## Overview
The Cafe Management System is a comprehensive solution designed to streamline daily operations for modern cafes. This system handles online orders (dine-in, takeaway, and delivery), table reservations, and user registration through an intuitive interface.

## DISCLAIMER : 
This project is just a prototype for education purpose. It is not intended for production use. Furthermore, it does not include any security measures to protect user data and some feature will not fully working properly due to the complexity of the project.

## Features

### Order Management
- **Multi-channel ordering**: Dine-in, takeaway, and delivery options
- **Real-time order tracking**: Monitor order status from placement to fulfillment
- **Order history**: View past orders for customers and staff

### Reservation System
- **Table booking**: Customers can reserve tables online
- **Reservation calendar**: Visual interface for staff to manage bookings
- **Automated reminders**: Notifications for upcoming reservations

### User Management
- **Customer registration**: Secure sign-up and profile management
- **Role-based access**: Different permissions for customers, staff, and admin

## Technology Stack

### Frontend
- **HTML5**: Semantic markup for accessibility
- **CSS3**: Modern styling and animations
- **Tailwind CSS**: Utility-first framework for responsive design
- **Bootstrap**: Additional UI components and layout tools
- **JavaScript**: Interactive elements and client-side validation

### Backend
- **PHP**: Server-side logic and business rules
- **JavaScript (Node.js optional)**: Additional backend functionality
- **MySQL**: Relational database for structured data storage

### Database
- **SQL Database**: Secure and efficient data management
- **Tables for**: Users, menu items, orders, reservations, payments

## Installation

1. **Prerequisites**:
   - Web server (Apache, Nginx)
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Composer (for dependency management)

2. **Setup**:
   ```bash
   # Clone the repository
   git clone https://github.com/yourusername/cafe-management-system.git
   
   # Navigate to project directory
   cd cafe-management-system
   
   # Install dependencies
   composer install
   
   # Set up database
   mysql -u root -p < database/schema.sql
   
   # Configure environment variables
   cp .env.example .env
   ```

3. **Configuration**:
   - Update database credentials in `.env` file
   - Configure mail settings for notifications
   - Set up payment gateway keys

## Usage

### For Customers
1. Register or log in to your account
2. Browse the menu and place orders
3. Make table reservations
4. Track order status in real-time

### For Staff 
1. Access the admin dashboard
2. Manage incoming orders
3. Update reservation status
4. Generate reports (unfinished feature due to time constraints)

### For Administrators (Admin)
1. Manage user accounts and permissions
2. Update menu items and pricing
3. Configure system settings (unfinished feature due to time constraints)
4. View analytics and business insights

## File Structure

```
cafe-management-system/
├── assets/            # Static files (CSS, JS, images)
│   ├── css/
│   ├── js/
│   └── images/
├── incudes/            # Configuration files
├── php/       # PHP controllers
├── sql_setup/            # Database models
├── admin/              # Composer dependencies
```

## Support

For any questions or issues, please contact:
- Email: someting983@gmail.com
- Phone: +60 17491129

---

## Preview 


## User Interface Preview

# Home page 
<img src="/assets/img/preview_prototype-1/home.png" alt="Home page" width="600">

# Menu page
<img src="/assets/img/preview_prototype-1/menu.png" alt="Menu page" width="600">

# Order page
<img src="/assets/img/preview_prototype-1/order.png" alt="Order page" width="600">

# Reservation page
<img src="/assets/img/preview_prototype-1/reservation.png" alt="Reservation page" width="600">



## Admin Interface Preview 

# Dashboard page 
<img src="/assets/img/preview_prototype-1/admin-2.png" alt="Dashboard page " width="600">

# Manage Product page
<img src="/assets/img/preview_prototype-1/admin-1.png" alt=" Manage Product page" width="600">
 

**Enjoy managing your cafe operations efficiently with our comprehensive system!** ☕