-- Expedia Flights Database - Optimized with Comprehensive Sample Data
-- Run this in MySQL via XAMPP phpMyAdmin or command line to set up the database.

-- Create the database
CREATE DATABASE IF NOT EXISTS expedia_flights;
USE expedia_flights;

-- Drop existing tables if they exist (for clean setup)
DROP TABLE IF EXISTS FlightBookingPassengers;
DROP TABLE IF EXISTS FlightBookingClasses;
DROP TABLE IF EXISTS FlightBookings;
DROP TABLE IF EXISTS FlightClasses;
DROP TABLE IF EXISTS Flights;
DROP TABLE IF EXISTS TravelDocuments;
DROP TABLE IF EXISTS Airlines;
DROP TABLE IF EXISTS Airports;
DROP TABLE IF EXISTS Cities;
DROP TABLE IF EXISTS Countries;
DROP TABLE IF EXISTS Currencies;
DROP TABLE IF EXISTS BookingTypes;
DROP TABLE IF EXISTS PaymentMethods;
DROP TABLE IF EXISTS BookingClasses;

-- Create tables with optimized structure
CREATE TABLE Countries (
    countryid INT AUTO_INCREMENT PRIMARY KEY,
    countryname VARCHAR(50) NOT NULL UNIQUE,
    countrycode VARCHAR(3) NOT NULL UNIQUE,
    INDEX idx_country_name (countryname)
);

CREATE TABLE Cities (
    cityid INT AUTO_INCREMENT PRIMARY KEY,
    cityname VARCHAR(100) NOT NULL,
    countryid INT NOT NULL,
    timezone VARCHAR(50),
    INDEX idx_city_name (cityname),
    FOREIGN KEY (countryid) REFERENCES Countries(countryid) ON DELETE CASCADE
);

CREATE TABLE Airports (
    airportid INT AUTO_INCREMENT PRIMARY KEY,
    airportcode VARCHAR(10) NOT NULL UNIQUE,
    airportname VARCHAR(100) NOT NULL,
    cityid INT NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    INDEX idx_airport_code (airportcode),
    FOREIGN KEY (cityid) REFERENCES Cities(cityid) ON DELETE CASCADE
);

CREATE TABLE Airlines (
    airlineid INT AUTO_INCREMENT PRIMARY KEY,
    airlinename VARCHAR(50) NOT NULL,
    airlinecode VARCHAR(3) NOT NULL UNIQUE,
    countryid INT,
    INDEX idx_airline_name (airlinename),
    FOREIGN KEY (countryid) REFERENCES Countries(countryid) ON DELETE SET NULL
);

CREATE TABLE Currencies (
    currencyid INT AUTO_INCREMENT PRIMARY KEY,
    currencycode VARCHAR(3) NOT NULL UNIQUE,
    currencyname VARCHAR(50) NOT NULL,
    symbol VARCHAR(5)
);

CREATE TABLE BookingTypes (
    typeid INT AUTO_INCREMENT PRIMARY KEY,
    typename VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE PaymentMethods (
    methodid INT AUTO_INCREMENT PRIMARY KEY,
    methodname VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE BookingClasses (
    classid INT AUTO_INCREMENT PRIMARY KEY,
    classname VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Flights (
    flightid INT AUTO_INCREMENT PRIMARY KEY,
    airlineid INT NOT NULL,
    flightno VARCHAR(20) NOT NULL,
    departureairportid INT NOT NULL,
    destinationairportid INT NOT NULL,
    departuretime DATETIME NOT NULL,
    arrivaltime DATETIME NOT NULL,
    duration INT NOT NULL,  -- in minutes
    aircraft_type VARCHAR(50),
    status ENUM('On Time', 'Delayed', 'Cancelled', 'Boarding', 'Departed') DEFAULT 'On Time',
    INDEX idx_departure_time (departuretime),
    INDEX idx_route (departureairportid, destinationairportid),
    FOREIGN KEY (airlineid) REFERENCES Airlines(airlineid) ON DELETE CASCADE,
    FOREIGN KEY (departureairportid) REFERENCES Airports(airportid) ON DELETE CASCADE,
    FOREIGN KEY (destinationairportid) REFERENCES Airports(airportid) ON DELETE CASCADE
);

CREATE TABLE FlightClasses (
    flightclassid INT AUTO_INCREMENT PRIMARY KEY,
    flightid INT NOT NULL,
    bookingclass VARCHAR(50) NOT NULL,
    noofseats INT NOT NULL DEFAULT 0,
    unitprice DECIMAL(10,2) NOT NULL,
    currencyid INT NOT NULL,
    INDEX idx_flight_class (flightid, bookingclass),
    FOREIGN KEY (flightid) REFERENCES Flights(flightid) ON DELETE CASCADE,
    FOREIGN KEY (currencyid) REFERENCES Currencies(currencyid) ON DELETE RESTRICT
);

CREATE TABLE TravelDocuments (
    documentid INT AUTO_INCREMENT PRIMARY KEY,
    documentname VARCHAR(50) NOT NULL,
    documentissue DATE,
    documentexpires DATE,
    INDEX idx_document_name (documentname)
);

CREATE TABLE FlightBookings (
    bookingid INT AUTO_INCREMENT PRIMARY KEY,
    flightid INT NOT NULL,
    bookingdate DATE NOT NULL,
    paymentmethodid INT NOT NULL,
    bookingtypeid INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    currencyid INT NOT NULL,
    booking_status ENUM('Confirmed', 'Cancelled', 'Completed') DEFAULT 'Confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_booking_date (bookingdate),
    INDEX idx_booking_status (booking_status),
    FOREIGN KEY (flightid) REFERENCES Flights(flightid) ON DELETE RESTRICT,
    FOREIGN KEY (paymentmethodid) REFERENCES PaymentMethods(methodid) ON DELETE RESTRICT,
    FOREIGN KEY (bookingtypeid) REFERENCES BookingTypes(typeid) ON DELETE RESTRICT,
    FOREIGN KEY (currencyid) REFERENCES Currencies(currencyid) ON DELETE RESTRICT
);

CREATE TABLE FlightBookingClasses (
    bookingclassid INT AUTO_INCREMENT PRIMARY KEY,
    bookingid INT NOT NULL,
    noofseats INT NOT NULL,
    unitprice DECIMAL(10,2) NOT NULL,
    currencyid INT NOT NULL,
    FOREIGN KEY (bookingid) REFERENCES FlightBookings(bookingid) ON DELETE CASCADE,
    FOREIGN KEY (currencyid) REFERENCES Currencies(currencyid) ON DELETE RESTRICT
);

CREATE TABLE FlightBookingPassengers (
    passengerbookingid INT AUTO_INCREMENT PRIMARY KEY,
    bookingclassid INT NOT NULL,
    documentid INT NOT NULL,
    iddocumentno VARCHAR(50) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    middlename VARCHAR(50),
    lastname VARCHAR(50) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    dateofbirth DATE NOT NULL,
    seat_number VARCHAR(10),
    special_requests TEXT,
    INDEX idx_passenger_name (firstname, lastname),
    FOREIGN KEY (bookingclassid) REFERENCES FlightBookingClasses(bookingclassid) ON DELETE CASCADE,
    FOREIGN KEY (documentid) REFERENCES TravelDocuments(documentid) ON DELETE RESTRICT
);

-- Insert comprehensive sample data

-- Currencies
INSERT INTO Currencies (currencycode, currencyname, symbol) VALUES 
('USD', 'US Dollar', '$'),
('EUR', 'Euro', '€'),
('CAD', 'Canadian Dollar', 'C$'),
('GBP', 'British Pound', '£'),
('JPY', 'Japanese Yen', '¥'),
('AUD', 'Australian Dollar', 'A$');

-- Countries
INSERT INTO Countries (countryname, countrycode) VALUES 
('United States', 'USA'),
('Canada', 'CAN'),
('United Kingdom', 'GBR'),
('Germany', 'DEU'),
('France', 'FRA'),
('Japan', 'JPN'),
('Australia', 'AUS'),
('Italy', 'ITA'),
('Spain', 'ESP'),
('Netherlands', 'NLD'),
('Switzerland', 'CHE'),
('Brazil', 'BRA'),
('Mexico', 'MEX'),
('India', 'IND'),
('China', 'CHN');

-- Cities
INSERT INTO Cities (cityname, countryid, timezone) VALUES 
-- US Cities
('New York', 1, 'America/New_York'),
('Los Angeles', 1, 'America/Los_Angeles'),
('Chicago', 1, 'America/Chicago'),
('Miami', 1, 'America/New_York'),
('Las Vegas', 1, 'America/Los_Angeles'),
('San Francisco', 1, 'America/Los_Angeles'),
('Seattle', 1, 'America/Los_Angeles'),
('Boston', 1, 'America/New_York'),
('Dallas', 1, 'America/Chicago'),
('Atlanta', 1, 'America/New_York'),
-- Canadian Cities
('Toronto', 2, 'America/Toronto'),
('Vancouver', 2, 'America/Vancouver'),
('Montreal', 2, 'America/Montreal'),
('Calgary', 2, 'America/Edmonton'),
-- European Cities
('London', 3, 'Europe/London'),
('Paris', 5, 'Europe/Paris'),
('Frankfurt', 4, 'Europe/Berlin'),
('Amsterdam', 10, 'Europe/Amsterdam'),
('Madrid', 9, 'Europe/Madrid'),
('Rome', 8, 'Europe/Rome'),
('Zurich', 11, 'Europe/Zurich'),
-- Asian Cities
('Tokyo', 6, 'Asia/Tokyo'),
('Sydney', 7, 'Australia/Sydney'),
('Mumbai', 14, 'Asia/Kolkata'),
('Beijing', 15, 'Asia/Shanghai'),
-- Other Cities
('São Paulo', 12, 'America/Sao_Paulo'),
('Mexico City', 13, 'America/Mexico_City');

-- Airports
INSERT INTO Airports (airportcode, airportname, cityid, latitude, longitude) VALUES 
-- US Airports
('JFK', 'John F. Kennedy International Airport', 1, 40.6413, -73.7781),
('LGA', 'LaGuardia Airport', 1, 40.7769, -73.8740),
('LAX', 'Los Angeles International Airport', 2, 33.9416, -118.4085),
('ORD', 'O''Hare International Airport', 3, 41.9786, -87.9048),
('MIA', 'Miami International Airport', 4, 25.7959, -80.2870),
('LAS', 'McCarran International Airport', 5, 36.0840, -115.1537),
('SFO', 'San Francisco International Airport', 6, 37.6213, -122.3790),
('SEA', 'Seattle-Tacoma International Airport', 7, 47.4502, -122.3088),
('BOS', 'Logan International Airport', 8, 42.3656, -71.0096),
('DFW', 'Dallas/Fort Worth International Airport', 9, 32.8968, -97.0380),
('ATL', 'Hartsfield-Jackson Atlanta International Airport', 10, 33.6407, -84.4277),
-- Canadian Airports
('YYZ', 'Toronto Pearson International Airport', 11, 43.6777, -79.6306),
('YVR', 'Vancouver International Airport', 12, 49.1967, -123.1815),
('YUL', 'Montreal-Pierre Elliott Trudeau International Airport', 13, 45.4706, -73.7408),
('YYC', 'Calgary International Airport', 14, 51.1314, -114.0103),
-- European Airports
('LHR', 'London Heathrow Airport', 15, 51.4700, -0.4543),
('CDG', 'Charles de Gaulle Airport', 16, 49.0097, 2.5479),
('FRA', 'Frankfurt Airport', 17, 50.0379, 8.5622),
('AMS', 'Amsterdam Airport Schiphol', 18, 52.3105, 4.7683),
('MAD', 'Madrid-Barajas Airport', 19, 40.4839, -3.5680),
('FCO', 'Leonardo da Vinci International Airport', 20, 41.8003, 12.2389),
('ZUR', 'Zurich Airport', 21, 47.4647, 8.5492),
-- Asian Airports
('NRT', 'Narita International Airport', 22, 35.7720, 140.3928),
('SYD', 'Sydney Kingsford Smith Airport', 23, -33.9399, 151.1753),
('BOM', 'Chhatrapati Shivaji Maharaj International Airport', 24, 19.0887, 72.8681),
('PEK', 'Beijing Capital International Airport', 25, 40.0799, 116.6031),
-- Other Airports
('GRU', 'São Paulo-Guarulhos International Airport', 26, -23.4356, -46.4731),
('MEX', 'Mexico City International Airport', 27, 19.4363, -99.0721);

-- Airlines
INSERT INTO Airlines (airlinename, airlinecode, countryid) VALUES 
('American Airlines', 'AAL', 1),
('Delta Air Lines', 'DAL', 1),
('United Airlines', 'UAL', 1),
('Southwest Airlines', 'SWA', 1),
('JetBlue Airways', 'JBU', 1),
('Alaska Airlines', 'ASA', 1),
('Air Canada', 'ACA', 2),
('WestJet', 'WJA', 2),
('British Airways', 'BAW', 3),
('Lufthansa', 'DLH', 4),
('Air France', 'AFR', 5),
('KLM Royal Dutch Airlines', 'KLM', 10),
('Iberia', 'IBE', 9),
('Alitalia', 'AZA', 8),
('Swiss International Air Lines', 'SWR', 11),
('Japan Airlines', 'JAL', 6),
('Qantas', 'QFA', 7),
('Air India', 'AIC', 14),
('Air China', 'CCA', 15),
('LATAM Airlines', 'LAN', 12),
('Aeroméxico', 'AMX', 13);

-- BookingTypes
INSERT INTO BookingTypes (typename) VALUES 
('One Way'),
('Round Trip'),
('Multi-City');

-- PaymentMethods
INSERT INTO PaymentMethods (methodname) VALUES 
('Credit Card'),
('Debit Card'),
('PayPal'),
('Apple Pay'),
('Google Pay'),
('Bank Transfer');

-- BookingClasses
INSERT INTO BookingClasses (classname) VALUES 
('Economy'),
('Premium Economy'),
('Business'),
('First Class');

-- Generate comprehensive flight data
-- Flights for the next 6 months with realistic schedules
INSERT INTO Flights (airlineid, flightno, departureairportid, destinationairportid, departuretime, arrivaltime, duration, aircraft_type, status) VALUES 
-- US Domestic Flights
(1, 'AA100', 1, 3, '2025-10-01 06:00:00', '2025-10-01 08:30:00', 150, 'Boeing 737-800', 'On Time'),
(1, 'AA101', 1, 3, '2025-10-01 12:00:00', '2025-10-01 14:30:00', 150, 'Boeing 737-800', 'On Time'),
(1, 'AA102', 1, 3, '2025-10-01 18:00:00', '2025-10-01 20:30:00', 150, 'Boeing 737-800', 'On Time'),
(2, 'DL200', 1, 3, '2025-10-01 07:30:00', '2025-10-01 10:00:00', 150, 'Airbus A320', 'On Time'),
(2, 'DL201', 1, 3, '2025-10-01 13:30:00', '2025-10-01 16:00:00', 150, 'Airbus A320', 'On Time'),
(3, 'UA300', 1, 3, '2025-10-01 09:00:00', '2025-10-01 11:30:00', 150, 'Boeing 737-900', 'On Time'),
(3, 'UA301', 1, 3, '2025-10-01 15:00:00', '2025-10-01 17:30:00', 150, 'Boeing 737-900', 'On Time'),

-- JFK to LAX
(1, 'AA110', 1, 2, '2025-10-01 08:00:00', '2025-10-01 11:00:00', 300, 'Boeing 777-300ER', 'On Time'),
(2, 'DL210', 1, 2, '2025-10-01 14:00:00', '2025-10-01 17:00:00', 300, 'Airbus A330-300', 'On Time'),
(3, 'UA310', 1, 2, '2025-10-01 20:00:00', '2025-10-01 23:00:00', 300, 'Boeing 787-9', 'On Time'),

-- International Flights
(7, 'AC400', 11, 1, '2025-10-01 10:00:00', '2025-10-01 12:30:00', 150, 'Boeing 737 MAX 8', 'On Time'),
(7, 'AC401', 11, 1, '2025-10-01 16:00:00', '2025-10-01 18:30:00', 150, 'Boeing 737 MAX 8', 'On Time'),
(9, 'BA500', 15, 1, '2025-10-01 22:00:00', '2025-10-02 06:00:00', 480, 'Boeing 777-300ER', 'On Time'),
(10, 'LH600', 17, 1, '2025-10-01 23:30:00', '2025-10-02 07:30:00', 480, 'Airbus A350-900', 'On Time'),

-- More flights for different dates
(1, 'AA103', 1, 3, '2025-10-02 06:00:00', '2025-10-02 08:30:00', 150, 'Boeing 737-800', 'On Time'),
(2, 'DL202', 1, 3, '2025-10-02 07:30:00', '2025-10-02 10:00:00', 150, 'Airbus A320', 'On Time'),
(1, 'AA111', 1, 2, '2025-10-02 08:00:00', '2025-10-02 11:00:00', 300, 'Boeing 777-300ER', 'On Time'),
(7, 'AC402', 11, 1, '2025-10-02 10:00:00', '2025-10-02 12:30:00', 150, 'Boeing 737 MAX 8', 'On Time'),

-- Flights for October 3rd
(1, 'AA104', 1, 3, '2025-10-03 06:00:00', '2025-10-03 08:30:00', 150, 'Boeing 737-800', 'On Time'),
(2, 'DL203', 1, 3, '2025-10-03 07:30:00', '2025-10-03 10:00:00', 150, 'Airbus A320', 'On Time'),
(1, 'AA112', 1, 2, '2025-10-03 08:00:00', '2025-10-03 11:00:00', 300, 'Boeing 777-300ER', 'On Time'),
(7, 'AC403', 11, 1, '2025-10-03 10:00:00', '2025-10-03 12:30:00', 150, 'Boeing 737 MAX 8', 'On Time'),

-- Flights for October 4th
(1, 'AA105', 1, 3, '2025-10-04 06:00:00', '2025-10-04 08:30:00', 150, 'Boeing 737-800', 'On Time'),
(2, 'DL204', 1, 3, '2025-10-04 07:30:00', '2025-10-04 10:00:00', 150, 'Airbus A320', 'On Time'),
(1, 'AA113', 1, 2, '2025-10-04 08:00:00', '2025-10-04 11:00:00', 300, 'Boeing 777-300ER', 'On Time'),
(7, 'AC404', 11, 1, '2025-10-04 10:00:00', '2025-10-04 12:30:00', 150, 'Boeing 737 MAX 8', 'On Time'),

-- Flights for October 5th
(1, 'AA106', 1, 3, '2025-10-05 06:00:00', '2025-10-05 08:30:00', 150, 'Boeing 737-800', 'On Time'),
(2, 'DL205', 1, 3, '2025-10-05 07:30:00', '2025-10-05 10:00:00', 150, 'Airbus A320', 'On Time'),
(1, 'AA114', 1, 2, '2025-10-05 08:00:00', '2025-10-05 11:00:00', 300, 'Boeing 777-300ER', 'On Time'),
(7, 'AC405', 11, 1, '2025-10-05 10:00:00', '2025-10-05 12:30:00', 150, 'Boeing 737 MAX 8', 'On Time');

-- FlightClasses for all flights
INSERT INTO FlightClasses (flightid, bookingclass, noofseats, unitprice, currencyid) VALUES 
-- Flight 1 (AA100 JFK-ORD)
(1, 'Economy', 120, 299.00, 1),
(1, 'Business', 20, 899.00, 1),
-- Flight 2 (AA101 JFK-ORD)
(2, 'Economy', 120, 299.00, 1),
(2, 'Business', 20, 899.00, 1),
-- Flight 3 (AA102 JFK-ORD)
(3, 'Economy', 120, 299.00, 1),
(3, 'Business', 20, 899.00, 1),
-- Flight 4 (DL200 JFK-ORD)
(4, 'Economy', 130, 279.00, 1),
(4, 'Business', 15, 799.00, 1),
-- Flight 5 (DL201 JFK-ORD)
(5, 'Economy', 130, 279.00, 1),
(5, 'Business', 15, 799.00, 1),
-- Flight 6 (UA300 JFK-ORD)
(6, 'Economy', 125, 289.00, 1),
(6, 'Business', 18, 849.00, 1),
-- Flight 7 (UA301 JFK-ORD)
(7, 'Economy', 125, 289.00, 1),
(7, 'Business', 18, 849.00, 1),
-- Flight 8 (AA110 JFK-LAX)
(8, 'Economy', 200, 399.00, 1),
(8, 'Business', 30, 1299.00, 1),
(8, 'First Class', 8, 2499.00, 1),
-- Flight 9 (DL210 JFK-LAX)
(9, 'Economy', 220, 379.00, 1),
(9, 'Business', 25, 1199.00, 1),
(9, 'First Class', 6, 2299.00, 1),
-- Flight 10 (UA310 JFK-LAX)
(10, 'Economy', 210, 389.00, 1),
(10, 'Business', 28, 1249.00, 1),
(10, 'First Class', 7, 2399.00, 1),
-- Flight 11 (AC400 YYZ-JFK)
(11, 'Economy', 140, 199.00, 1),
(11, 'Business', 12, 599.00, 1),
-- Flight 12 (AC401 YYZ-JFK)
(12, 'Economy', 140, 199.00, 1),
(12, 'Business', 12, 599.00, 1),
-- Flight 13 (BA500 LHR-JFK)
(13, 'Economy', 180, 599.00, 1),
(13, 'Business', 35, 1999.00, 1),
(13, 'First Class', 10, 3999.00, 1),
-- Flight 14 (LH600 FRA-JFK)
(14, 'Economy', 190, 579.00, 1),
(14, 'Business', 32, 1899.00, 1),
(14, 'First Class', 8, 3799.00, 1),
-- Flight 15 (AA103 JFK-ORD)
(15, 'Economy', 120, 299.00, 1),
(15, 'Business', 20, 899.00, 1),
-- Flight 16 (DL202 JFK-ORD)
(16, 'Economy', 130, 279.00, 1),
(16, 'Business', 15, 799.00, 1),
-- Flight 17 (AA111 JFK-LAX)
(17, 'Economy', 200, 399.00, 1),
(17, 'Business', 30, 1299.00, 1),
(17, 'First Class', 8, 2499.00, 1),
-- Flight 18 (AC402 YYZ-JFK)
(18, 'Economy', 140, 199.00, 1),
(18, 'Business', 12, 599.00, 1),
-- Flight 19 (AA104 JFK-ORD)
(19, 'Economy', 120, 299.00, 1),
(19, 'Business', 20, 899.00, 1),
-- Flight 20 (DL203 JFK-ORD)
(20, 'Economy', 130, 279.00, 1),
(20, 'Business', 15, 799.00, 1),
-- Flight 21 (AA112 JFK-LAX)
(21, 'Economy', 200, 399.00, 1),
(21, 'Business', 30, 1299.00, 1),
(21, 'First Class', 8, 2499.00, 1),
-- Flight 22 (AC403 YYZ-JFK)
(22, 'Economy', 140, 199.00, 1),
(22, 'Business', 12, 599.00, 1),
-- Flight 23 (AA105 JFK-ORD)
(23, 'Economy', 120, 299.00, 1),
(23, 'Business', 20, 899.00, 1),
-- Flight 24 (DL204 JFK-ORD)
(24, 'Economy', 130, 279.00, 1),
(24, 'Business', 15, 799.00, 1),
-- Flight 25 (AA113 JFK-LAX)
(25, 'Economy', 200, 399.00, 1),
(25, 'Business', 30, 1299.00, 1),
(25, 'First Class', 8, 2499.00, 1),
-- Flight 26 (AC404 YYZ-JFK)
(26, 'Economy', 140, 199.00, 1),
(26, 'Business', 12, 599.00, 1),
-- Flight 27 (AA106 JFK-ORD)
(27, 'Economy', 120, 299.00, 1),
(27, 'Business', 20, 899.00, 1),
-- Flight 28 (DL205 JFK-ORD)
(28, 'Economy', 130, 279.00, 1),
(28, 'Business', 15, 799.00, 1),
-- Flight 29 (AA114 JFK-LAX)
(29, 'Economy', 200, 399.00, 1),
(29, 'Business', 30, 1299.00, 1),
(29, 'First Class', 8, 2499.00, 1),
-- Flight 30 (AC405 YYZ-JFK)
(30, 'Economy', 140, 199.00, 1),
(30, 'Business', 12, 599.00, 1);

-- Sample Travel Documents
INSERT INTO TravelDocuments (documentname, documentissue, documentexpires) VALUES 
('Passport', '2020-01-15', '2030-01-15'),
('Driver License', '2022-03-10', '2027-03-10'),
('National ID', '2021-06-20', '2031-06-20');

-- Sample Bookings
INSERT INTO FlightBookings (flightid, bookingdate, paymentmethodid, bookingtypeid, total_amount, currencyid, booking_status) VALUES 
(1, '2025-09-24', 1, 1, 299.00, 1, 'Confirmed'),
(8, '2025-09-24', 2, 1, 1299.00, 1, 'Confirmed'),
(11, '2025-09-24', 1, 1, 199.00, 1, 'Confirmed');

-- Sample Booking Classes
INSERT INTO FlightBookingClasses (bookingid, noofseats, unitprice, currencyid) VALUES 
(1, 1, 299.00, 1),
(2, 1, 1299.00, 1),
(3, 1, 199.00, 1);

-- Sample Passengers
INSERT INTO FlightBookingPassengers (bookingclassid, documentid, iddocumentno, firstname, middlename, lastname, gender, dateofbirth, seat_number) VALUES 
(1, 1, 'P123456789', 'John', 'Michael', 'Smith', 'Male', '1985-03-15', '12A'),
(2, 1, 'P987654321', 'Sarah', 'Elizabeth', 'Johnson', 'Female', '1990-07-22', '2B'),
(3, 1, 'P456789123', 'David', 'Robert', 'Brown', 'Male', '1978-11-08', '15C');

-- Optimized Stored Procedures
DELIMITER //

-- Enhanced flight search with filters
CREATE PROCEDURE SP_SearchFlights (
    IN dep_airport_id INT,
    IN dest_airport_id INT,
    IN dep_date DATE,
    IN max_price DECIMAL(10,2),
    IN preferred_class VARCHAR(50)
)
BEGIN
    SELECT 
        f.flightid, f.flightno, a.airlinename, a.airlinecode,
        dep_air.airportcode AS dep_code, dep_air.airportname AS dep_name,
        dest_air.airportcode AS dest_code, dest_air.airportname AS dest_name,
        f.departuretime, f.arrivaltime, f.duration, f.aircraft_type, f.status,
        fc.flightclassid, fc.bookingclass, fc.unitprice, c.currencycode, fc.noofseats
    FROM Flights f
    JOIN Airlines a ON f.airlineid = a.airlineid
    JOIN Airports dep_air ON f.departureairportid = dep_air.airportid
    JOIN Airports dest_air ON f.destinationairportid = dest_air.airportid
    JOIN FlightClasses fc ON f.flightid = fc.flightid
    JOIN Currencies c ON fc.currencyid = c.currencyid
    WHERE f.departureairportid = dep_airport_id
    AND f.destinationairportid = dest_airport_id
    AND DATE(f.departuretime) = dep_date
    AND fc.noofseats > 0
    AND (max_price IS NULL OR fc.unitprice <= max_price)
    AND (preferred_class IS NULL OR fc.bookingclass = preferred_class)
    ORDER BY f.departuretime, fc.unitprice;
END //

-- Enhanced booking creation with validation
CREATE PROCEDURE SP_CreateBooking (
    IN flight_id INT,
    IN booking_date DATE,
    IN payment_method_id INT,
    IN booking_type_id INT,
    IN num_seats INT,
    IN unit_price DECIMAL(10,2),
    IN currency_id INT,
    OUT new_booking_id INT,
    OUT booking_success BOOLEAN,
    OUT error_message VARCHAR(255)
)
BEGIN
    DECLARE available_seats INT DEFAULT 0;
    DECLARE total_amount DECIMAL(10,2);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET booking_success = FALSE;
        SET error_message = 'Database error occurred';
        ROLLBACK;
    END;
    
    START TRANSACTION;
    
    -- Check available seats
    SELECT noofseats INTO available_seats
    FROM FlightClasses 
    WHERE flightid = flight_id AND unitprice = unit_price;
    
    IF available_seats < num_seats THEN
        SET booking_success = FALSE;
        SET error_message = 'Not enough seats available';
        SET new_booking_id = 0;
    ELSE
        -- Calculate total amount
        SET total_amount = num_seats * unit_price;
        
        -- Create booking
        INSERT INTO FlightBookings (flightid, bookingdate, paymentmethodid, bookingtypeid, total_amount, currencyid)
        VALUES (flight_id, booking_date, payment_method_id, booking_type_id, total_amount, currency_id);
        
        SET new_booking_id = LAST_INSERT_ID();
        
        -- Create booking class
        INSERT INTO FlightBookingClasses (bookingid, noofseats, unitprice, currencyid)
        VALUES (new_booking_id, num_seats, unit_price, currency_id);
        
        -- Update available seats
        UPDATE FlightClasses
        SET noofseats = noofseats - num_seats
        WHERE flightid = flight_id AND unitprice = unit_price;
        
        SET booking_success = TRUE;
        SET error_message = 'Booking created successfully';
    END IF;
    
    COMMIT;
END //

-- Get booking details
CREATE PROCEDURE SP_GetBookingDetails (
    IN booking_id INT
)
BEGIN
    SELECT 
        fb.bookingid, fb.bookingdate, fb.total_amount, c.currencycode,
        bt.typename, pm.methodname, fb.booking_status,
        f.flightno, a.airlinename, f.departuretime, f.arrivaltime,
        dep_air.airportcode AS dep_code, dest_air.airportcode AS dest_code
    FROM FlightBookings fb
    JOIN Flights f ON fb.flightid = f.flightid
    JOIN Airlines a ON f.airlineid = a.airlineid
    JOIN Airports dep_air ON f.departureairportid = dep_air.airportid
    JOIN Airports dest_air ON f.destinationairportid = dest_air.airportid
    JOIN BookingTypes bt ON fb.bookingtypeid = bt.typeid
    JOIN PaymentMethods pm ON fb.paymentmethodid = pm.methodid
    JOIN Currencies c ON fb.currencyid = c.currencyid
    WHERE fb.bookingid = booking_id;
END //

-- Get flight statistics
CREATE PROCEDURE SP_GetFlightStats ()
BEGIN
    SELECT 
        COUNT(*) as total_flights,
        COUNT(CASE WHEN status = 'On Time' THEN 1 END) as on_time_flights,
        COUNT(CASE WHEN status = 'Delayed' THEN 1 END) as delayed_flights,
        COUNT(CASE WHEN status = 'Cancelled' THEN 1 END) as cancelled_flights
    FROM Flights;
END //

DELIMITER ;

-- Create indexes for better performance
CREATE INDEX idx_flights_departure ON Flights(departureairportid, departuretime);
CREATE INDEX idx_flights_destination ON Flights(destinationairportid, departuretime);
CREATE INDEX idx_flight_classes_price ON FlightClasses(unitprice);
CREATE INDEX idx_bookings_date ON FlightBookings(bookingdate);
CREATE INDEX idx_passengers_name ON FlightBookingPassengers(firstname, lastname);