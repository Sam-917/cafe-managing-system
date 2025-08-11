# Cafe Management System

## Overview
The Cafe Management System is a comprehensive solution designed to streamline daily operations for modern cafes. This system handles online orders (dine-in, takeaway, and delivery), table reservations, and user registration through an intuitive interface.

## Disclaimer : 
This project is just a prototype for education purpose. It is not intended for production use. Furthermore, it does not include any security measures to protect user data and some feature will not fully working properly due to the complexity of the project.

## Features

### Order Management
- **Multi-channel ordering**: Dine-in, takeaway, and delivery options
- **Real-time order tracking**: Admin can monitor order status from placement to fulfillment  
- **Order history**: Admin can view past orders for customers 

### Reservation System
- **Table booking**: Customers can reserve tables online
- **Reservation calendar**: Visual interface for staff to manage bookings
- **Table Visualization**: Visual cafe layout interface featuring table mapping with each specifies each unique features such as capacity, shape, and location.  

### User Management
- **Customer registration**: Secure sign-up and profile management
- **Role-based access**: Different permissions for customers, and admin

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
   git clone https://github.com/Sam-917/cafe-managing-system.git
   
   # Navigate to project directory
   cd cafe-management-system

   # Open code & run in VS Code
   code . 
    
   ```

### Note : 
1. Make sure VS Code and XAMPP Control Panel is installed. After that, start Apache and mySQL module then you can import the database from database_setup.sql in the PhpMyAdmin localserver.

2. Currently, our dine-in ordering and reservation booking systems are temporarily unavailable due to ongoing development complexity and technical optimization requirement. We sincerely apologize for any inconvenience this may cause and appreciate your patience as we work diligently to enhance these features for an improved user experience.

3. To access the administrative interface, please use the following credentials:
   ```bash
       Username: Admin
       Password: 123
   ```
   The admin dashboard provides comprehensive access to sales statistic and analytics, database management with table controls and administrative feature including order, product and user management (edit feature)

   **Please note**: Adding order, product and user feature is unavailable :(
   
   
## Usage

### For Customers
1. Register or log in to your account
2. Browse the menu and place orders
3. Make table reservations
4. Track order status in real-time

### For Staff (Staff role is unavailable for now)
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
├── assets/             # Static files (CSS, JS, images)
│   ├── css/
│   ├── js/
│   └── img/
├── incudes/            # Configuration files and reusable
├── php/                # PHP controllers
├── sql_setup/          # Database models
├── admin/              # Admin Interface 
```

## Support

For any questions or issues, please contact:
- Email: someting983@gmail.com
- Phone: +60 17491129

---

# Preview 

## User Interface Preview

### Home page  

<img src="/assets/img/preview-prototype-1/home.png" alt="Home page" width="600">

### Menu page
<img src="/assets/img/preview-prototype-1/menu.png" alt="Menu page" width="600">

### Order page
<img src="/assets/img/preview-prototype-1/order.png" alt="Order page" width="600">

### Reservation page
<img src="/assets/img/preview-prototype-1/reservation.png" alt="Reservation page" width="600">



## Admin Interface Preview 

### Dashboard page 
<img src="/assets/img/preview-prototype-1/admin-2.png" alt="Dashboard page " width="600">

### Manage Product page
<img src="/assets/img/preview-prototype-1/admin-1.png" alt=" Manage Product page" width="600">
 
 
