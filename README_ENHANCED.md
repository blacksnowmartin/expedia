# Expedia Flights Booking System - Enhanced

A comprehensive flight booking system built with PHP, MySQL, and Bootstrap. This enhanced version includes advanced features, better security, and a modern user interface.

## 🚀 Features

### User Features
- **Modern Search Interface**: Beautiful, responsive flight search with filters
- **Advanced Flight Search**: Filter by price, class, and airline
- **Comprehensive Booking System**: Multi-passenger booking with detailed forms
- **Real-time Flight Information**: Live flight status and schedules
- **Secure Payment Processing**: Multiple payment methods support
- **Booking Confirmation**: Detailed confirmation with QR codes
- **Responsive Design**: Mobile-first approach with Bootstrap 5

### Admin Features
- **Admin Dashboard**: Comprehensive management interface
- **Flight Management**: Add, edit, and monitor flights
- **Booking Analytics**: Real-time statistics and reports
- **Performance Monitoring**: Airline performance tracking
- **User Management**: Booking and passenger management

### Technical Features
- **Optimized Database**: Enhanced schema with proper indexing
- **Stored Procedures**: Efficient database operations
- **Security**: Input validation and SQL injection prevention
- **Error Handling**: Comprehensive error management
- **Scalability**: Designed for high-volume operations

## 📋 Prerequisites

- **XAMPP** (Apache, MySQL, PHP 7.4+)
- **Web Browser** (Chrome, Firefox, Safari, Edge)
- **MySQL** 5.7+ or 8.0+

## 🛠️ Installation

### 1. Setup XAMPP
1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL services
3. Open phpMyAdmin (http://localhost/phpmyadmin)

### 2. Database Setup
1. Create a new database named `expedia_flights`
2. Import the optimized database schema:
   ```sql
   -- Run the contents of database/database-optimized.sql
   ```

### 3. Project Setup
1. Clone or download this project
2. Place the project folder in `C:\xampp\htdocs\expedia`
3. Ensure the following files are in the project root:
   - `index.html`
   - `search_results_enhanced.php`
   - `book_flight_enhanced.php`
   - `confirm_booking_enhanced.php`
   - `admin_dashboard.php`
   - `db_connect.php`

### 4. Database Configuration
Update `db_connect.php` if needed:
```php
private $servername = "localhost";
private $username = "root";
private $password = "";
private $dbname = "expedia_flights";
```

## 🎯 Usage

### For Users
1. **Search Flights**: Visit `http://localhost/expedia/`
2. **Enter Search Criteria**: Select departure/destination airports, date, and passengers
3. **View Results**: Browse available flights with filters
4. **Book Flight**: Complete passenger details and payment
5. **Confirmation**: Receive booking confirmation with details

### For Administrators
1. **Access Dashboard**: Visit `http://localhost/expedia/admin_dashboard.php`
2. **Login**: Use password `admin123`
3. **Monitor System**: View statistics, bookings, and performance
4. **Manage Flights**: Add new flights and update schedules

## 📊 Database Schema

### Core Tables
- **Countries**: Country information with codes
- **Cities**: City data with timezone support
- **Airports**: Airport details with coordinates
- **Airlines**: Airline information and codes
- **Flights**: Flight schedules and status
- **FlightClasses**: Available classes and pricing
- **FlightBookings**: Booking records
- **FlightBookingPassengers**: Passenger details

### Enhanced Features
- **Optimized Indexes**: Improved query performance
- **Foreign Key Constraints**: Data integrity
- **Stored Procedures**: Efficient operations
- **Comprehensive Sample Data**: 30+ flights with realistic data

## 🔧 Configuration

### Airport Codes (Sample Data)
- **JFK**: John F. Kennedy International (New York)
- **LAX**: Los Angeles International
- **YYZ**: Toronto Pearson International
- **LHR**: London Heathrow
- **CDG**: Charles de Gaulle (Paris)
- **FRA**: Frankfurt Airport
- **NRT**: Narita International (Tokyo)

### Sample Airlines
- American Airlines (AA)
- Delta Air Lines (DL)
- United Airlines (UA)
- Air Canada (AC)
- British Airways (BA)
- Lufthansa (LH)

## 🚀 Advanced Features

### Search Enhancements
- **Price Filtering**: Set maximum price limits
- **Class Filtering**: Filter by Economy, Business, First Class
- **Real-time Results**: Live flight availability
- **Responsive Design**: Mobile-optimized interface

### Booking System
- **Multi-passenger Support**: Book for multiple passengers
- **Document Management**: Passport, ID, and license support
- **Special Requests**: Dietary and accessibility needs
- **Payment Integration**: Multiple payment methods

### Admin Dashboard
- **Real-time Statistics**: Live flight and booking data
- **Performance Analytics**: Airline performance metrics
- **Booking Management**: View and manage all bookings
- **System Monitoring**: Health and status monitoring

## 🔒 Security Features

- **Input Validation**: All user inputs are sanitized
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: HTML entity encoding
- **Session Management**: Secure admin authentication
- **Error Handling**: Comprehensive error management

## 📱 Mobile Support

- **Responsive Design**: Works on all device sizes
- **Touch-friendly Interface**: Optimized for mobile devices
- **Fast Loading**: Optimized for mobile networks
- **Progressive Enhancement**: Works without JavaScript

## 🎨 UI/UX Features

- **Modern Design**: Clean, professional interface
- **Bootstrap 5**: Latest framework with components
- **Font Awesome Icons**: Professional iconography
- **Gradient Backgrounds**: Modern visual appeal
- **Card-based Layout**: Organized information display
- **Interactive Elements**: Hover effects and animations

## 📈 Performance Optimizations

- **Database Indexing**: Optimized query performance
- **Stored Procedures**: Efficient database operations
- **Connection Pooling**: Optimized database connections
- **Caching**: Reduced database load
- **Compressed Assets**: Faster page loading

## 🧪 Testing

### Test Scenarios
1. **Search Functionality**: Test various search criteria
2. **Booking Process**: Complete booking flow
3. **Admin Dashboard**: Test all admin features
4. **Mobile Responsiveness**: Test on different devices
5. **Error Handling**: Test error scenarios

### Sample Test Data
- **Flights**: 30+ flights across multiple dates
- **Airlines**: 20+ airlines with realistic data
- **Airports**: 25+ airports worldwide
- **Bookings**: Sample booking records

## 🔧 Troubleshooting

### Common Issues
1. **Database Connection**: Check MySQL service and credentials
2. **File Permissions**: Ensure proper file permissions
3. **PHP Version**: Ensure PHP 7.4+ is installed
4. **Browser Compatibility**: Use modern browsers

### Error Messages
- **Connection Failed**: Check database configuration
- **No Flights Found**: Verify sample data is loaded
- **Booking Failed**: Check form validation and database

## 📚 API Documentation

### Stored Procedures
- `SP_SearchFlights`: Enhanced flight search with filters
- `SP_CreateBooking`: Secure booking creation
- `SP_AddPassenger`: Add passenger to booking
- `SP_GetBookingDetails`: Retrieve booking information
- `SP_GetFlightStats`: Get system statistics

### Database Functions
- **Input Validation**: Sanitize and validate all inputs
- **Transaction Management**: Ensure data consistency
- **Error Handling**: Comprehensive error management
- **Performance Monitoring**: Track system performance

## 🚀 Future Enhancements

### Planned Features
- **Email Notifications**: Automated booking confirmations
- **SMS Integration**: Text message notifications
- **API Integration**: Real-time flight data
- **Mobile App**: Native mobile application
- **Advanced Analytics**: Detailed reporting system
- **Multi-language Support**: Internationalization
- **Payment Gateway**: Real payment processing
- **Loyalty Program**: Customer rewards system

## 📞 Support

For technical support or questions:
- **Documentation**: Check this README file
- **Database Issues**: Verify MySQL configuration
- **PHP Errors**: Check error logs in XAMPP
- **Browser Issues**: Clear cache and cookies

## 📄 License

This project is for educational purposes. Please ensure compliance with all applicable laws and regulations when using in production environments.

## 🎯 Success Metrics

### Performance Targets
- **Page Load Time**: < 2 seconds
- **Database Queries**: < 100ms average
- **Search Results**: < 500ms response time
- **Booking Process**: < 30 seconds completion
- **Mobile Performance**: 90+ Lighthouse score

### User Experience
- **Intuitive Navigation**: Easy-to-use interface
- **Responsive Design**: Works on all devices
- **Fast Search**: Quick flight results
- **Secure Booking**: Safe transaction process
- **Clear Confirmation**: Detailed booking details

---

**Note**: This is an enhanced version of the Expedia Flights Booking System with significant improvements in functionality, security, and user experience. The system is designed for educational purposes and demonstrates modern web development practices.