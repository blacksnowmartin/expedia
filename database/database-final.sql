-- database.sql
-- Run this in MySQL via XAMPP phpMyAdmin or command line to set up the database.

-- Create the database
CREATE DATABASE IF NOT EXISTS expedia_flights;
USE expedia_flights;

-- Create tables based on refined ERD and data dictionary
-- Refinements: Used INT for IDs (no precision needed), added Currency table, changed Flights to use airport IDs for FK integrity,
-- Made bookingclass in Flightclasses a VARCHAR for simplicity, added some constraints.

CREATE TABLE Countries (
    countryid INT AUTO_INCREMENT PRIMARY KEY,
    countryname VARCHAR(50) NOT NULL
);

CREATE TABLE Cities (
    cityid INT AUTO_INCREMENT PRIMARY KEY,
    cityname VARCHAR(100) NOT NULL,
    countryid INT NOT NULL,
    FOREIGN KEY (countryid) REFERENCES Countries(countryid)
);

CREATE TABLE Airports (
    airportid INT AUTO_INCREMENT PRIMARY KEY,
    airportcode VARCHAR(10) NOT NULL,
    airportname VARCHAR(100) NOT NULL,
    cityid INT NOT NULL,
    FOREIGN KEY (cityid) REFERENCES Cities(cityid)
);

CREATE TABLE Airlines (
    airlineid INT AUTO_INCREMENT PRIMARY KEY,
    airlinename VARCHAR(50) NOT NULL
);

CREATE TABLE Currencies (
    currencyid INT AUTO_INCREMENT PRIMARY KEY,
    currencycode VARCHAR(3) NOT NULL,
    currencyname VARCHAR(50) NOT NULL
);

CREATE TABLE BookingTypes (
    typeid INT AUTO_INCREMENT PRIMARY KEY,
    typename VARCHAR(50) NOT NULL  -- e.g., 'One Way', 'Round Trip'
);

CREATE TABLE PaymentMethods (
    methodid INT AUTO_INCREMENT PRIMARY KEY,
    methodname VARCHAR(50) NOT NULL  -- e.g., 'Credit Card', 'PayPal'
);

CREATE TABLE BookingClasses (
    classid INT AUTO_INCREMENT PRIMARY KEY,
    classname VARCHAR(50) NOT NULL  -- e.g., 'Economy', 'Business'
);

CREATE TABLE Flights (
    flightid INT AUTO_INCREMENT PRIMARY KEY,
    airlineid INT NOT NULL,
    flightno VARCHAR(20) NOT NULL,
    departureairportid INT NOT NULL,
    destinationairportid INT NOT NULL,
    departuretime DATETIME NOT NULL,
    duration INT NOT NULL,  -- in minutes
    FOREIGN KEY (airlineid) REFERENCES Airlines(airlineid),
    FOREIGN KEY (departureairportid) REFERENCES Airports(airportid),
    FOREIGN KEY (destinationairportid) REFERENCES Airports(airportid)
);

CREATE TABLE FlightClasses (
    flightclassid INT AUTO_INCREMENT PRIMARY KEY,
    flightid INT NOT NULL,
    bookingclass VARCHAR(50) NOT NULL,  -- e.g., 'Economy' (could make FK to BookingClasses if refined further)
    noofseats INT NOT NULL,
    unitprice DECIMAL(10,2) NOT NULL,
    currencyid INT NOT NULL,
    FOREIGN KEY (flightid) REFERENCES Flights(flightid),
    FOREIGN KEY (currencyid) REFERENCES Currencies(currencyid)
);

CREATE TABLE TravelDocuments (
    documentid INT AUTO_INCREMENT PRIMARY KEY,
    documentname VARCHAR(50) NOT NULL,  -- e.g., 'Passport'
    documentissue DATE,
    documentexpires DATE
);

CREATE TABLE FlightBookings (
    bookingid INT AUTO_INCREMENT PRIMARY KEY,
    flightid INT NOT NULL,
    bookingdate DATE NOT NULL,
    paymentmethodid INT NOT NULL,
    bookingtypeid INT NOT NULL,
    FOREIGN KEY (flightid) REFERENCES Flights(flightid),
    FOREIGN KEY (paymentmethodid) REFERENCES PaymentMethods(methodid),
    FOREIGN KEY (bookingtypeid) REFERENCES BookingTypes(typeid)
);

CREATE TABLE FlightBookingClasses (
    bookingclassid INT AUTO_INCREMENT PRIMARY KEY,
    bookingid INT NOT NULL,
    noofseats INT NOT NULL,
    unitprice DECIMAL(10,2) NOT NULL,
    currencyid INT NOT NULL,
    FOREIGN KEY (bookingid) REFERENCES FlightBookings(bookingid),
    FOREIGN KEY (currencyid) REFERENCES Currencies(currencyid)
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
    FOREIGN KEY (bookingclassid) REFERENCES FlightBookingClasses(bookingclassid),
    FOREIGN KEY (documentid) REFERENCES TravelDocuments(documentid)
);

-- Stored Procedures
-- SP to search flights (simple, for one-way, by airports and date)
DELIMITER //
CREATE PROCEDURE SP_SearchFlights (
    IN dep_airport_id INT,
    IN dest_airport_id INT,
    IN dep_date DATE
)
BEGIN
    SELECT 
        f.flightid, f.flightno, a.airlinename,
        dep_air.airportcode AS dep_code, dest_air.airportcode AS dest_code,
        f.departuretime, f.duration,
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
    AND fc.noofseats > 0;
END //
DELIMITER ;

-- SP to create a new booking (simplified for one-way, single class, multiple passengers)
DELIMITER //
CREATE PROCEDURE SP_CreateBooking (
    IN flight_id INT,
    IN booking_date DATE,
    IN payment_method_id INT,
    IN booking_type_id INT,
    IN num_seats INT,
    IN unit_price DECIMAL(10,2),
    IN currency_id INT,
    OUT new_booking_id INT
)
BEGIN
    INSERT INTO FlightBookings (flightid, bookingdate, paymentmethodid, bookingtypeid)
    VALUES (flight_id, booking_date, payment_method_id, booking_type_id);
    
    SET new_booking_id = LAST_INSERT_ID();
    
    INSERT INTO FlightBookingClasses (bookingid, noofseats, unitprice, currencyid)
    VALUES (new_booking_id, num_seats, unit_price, currency_id);
    
    -- Update available seats (simplified, assumes single class update)
    UPDATE FlightClasses
    SET noofseats = noofseats - num_seats
    WHERE flightid = flight_id AND unitprice = unit_price;  -- Match by price or adjust as needed
END //
DELIMITER ;

-- SP to add passenger to booking class
DELIMITER //
CREATE PROCEDURE SP_AddPassenger (
    IN booking_class_id INT,
    IN doc_name VARCHAR(50),
    IN doc_issue DATE,
    IN doc_expires DATE,
    IN id_doc_no VARCHAR(50),
    IN first_name VARCHAR(50),
    IN middle_name VARCHAR(50),
    IN last_name VARCHAR(50),
    IN gen_der VARCHAR(10),
    IN dob DATE
)
BEGIN
    INSERT INTO TravelDocuments (documentname, documentissue, documentexpires)
    VALUES (doc_name, doc_issue, doc_expires);
    
    SET @new_doc_id = LAST_INSERT_ID();
    
    INSERT INTO FlightBookingPassengers (bookingclassid, documentid, iddocumentno, firstname, middlename, lastname, gender, dateofbirth)
    VALUES (booking_class_id, @new_doc_id, id_doc_no, first_name, middle_name, last_name, gen_der, dob);
END //
DELIMITER ;

-- Sample Data Population
-- Currencies
INSERT INTO Currencies (currencycode, currencyname) VALUES ('USD', 'US Dollar'), ('EUR', 'Euro');

-- Countries
INSERT INTO Countries (countryname) VALUES ('United States'), ('Canada');

-- Cities
INSERT INTO Cities (cityname, countryid) VALUES ('New York', 1), ('Los Angeles', 1), ('Toronto', 2);

-- Airports
INSERT INTO Airports (airportcode, airportname, cityid) VALUES ('JFK', 'John F. Kennedy International', 1), ('LAX', 'Los Angeles International', 2), ('YYZ', 'Toronto Pearson International', 3);

-- Airlines
INSERT INTO Airlines (airlinename) VALUES ('Delta'), ('Air Canada');

-- BookingTypes
INSERT INTO BookingTypes (typename) VALUES ('One Way'), ('Round Trip');

-- PaymentMethods
INSERT INTO PaymentMethods (methodname) VALUES ('Credit Card'), ('PayPal');

-- BookingClasses (not used directly, but for reference)
INSERT INTO BookingClasses (classname) VALUES ('Economy'), ('Business');

-- Flights (future dates post Sep 23, 2025)
INSERT INTO Flights (airlineid, flightno, departureairportid, destinationairportid, departuretime, duration)
VALUES (1, 'DL123', 1, 2, '2025-10-01 08:00:00', 300),  -- JFK to LAX, 5 hours
       (2, 'AC456', 3, 1, '2025-10-02 10:00:00', 180);  -- YYZ to JFK, 3 hours

-- FlightClasses
INSERT INTO FlightClasses (flightid, bookingclass, noofseats, unitprice, currencyid)
VALUES (1, 'Economy', 150, 250.00, 1), (1, 'Business', 20, 800.00, 1),
       (2, 'Economy', 200, 150.00, 1), (2, 'Business', 30, 500.00, 1);