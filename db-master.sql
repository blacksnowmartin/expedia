-- Create Database
CREATE DATABASE expedia_flight_booking;
USE expedia_flight_booking;

-- Airline Table
CREATE TABLE Airline (
    airlineid INT PRIMARY KEY,
    airlinename VARCHAR(50),
    airlinelogo VARCHAR(255)
);

-- Country Table
CREATE TABLE Country (
    countryid INT PRIMARY KEY,
    countryname VARCHAR(50)
);

-- City Table
CREATE TABLE City (
    cityid INT PRIMARY KEY,
    cityname VARCHAR(50),
    countryid INT,
    FOREIGN KEY (countryid) REFERENCES Country(countryid)
);

-- Airports Table
CREATE TABLE Airports (
    airportid INT PRIMARY KEY,
    airportcode VARCHAR(10),
    airportname VARCHAR(50),
    cityid INT,
    FOREIGN KEY (cityid) REFERENCES City(cityid)
);

-- Flights Table
CREATE TABLE Flights (
    flightid INT PRIMARY KEY,
    airlineid INT,
    flightno VARCHAR(10),
    departurecity VARCHAR(50),
    destinationcity VARCHAR(50),
    departuretime DATETIME,
    duration VARCHAR(10),
    departureairportid INT,
    FOREIGN KEY (airlineid) REFERENCES Airline(airlineid),
    FOREIGN KEY (departureairportid) REFERENCES Airports(airportid)
);

-- Bookingclass Table
CREATE TABLE Bookingclass (
    classid INT PRIMARY KEY,
    classname VARCHAR(50)
);

-- Bookingtype Table
CREATE TABLE Bookingtype (
    typeid INT PRIMARY KEY,
    typename VARCHAR(50)
);

-- Paymentmethods Table
CREATE TABLE Paymentmethods (
    methodid INT PRIMARY KEY,
    methodname VARCHAR(50)
);

-- Currency Table (added for completeness)
CREATE TABLE Currency (
    currencyid INT PRIMARY KEY,
    currencyname VARCHAR(50)
);

-- Flightclasses Table
CREATE TABLE Flightclasses (
    flightclassid INT PRIMARY KEY,
    flightid INT,
    bookingclassid INT,
    noofseats INT,
    unitprice DECIMAL(10,2),
    currencyid INT,
    FOREIGN KEY (flightid) REFERENCES Flights(flightid),
    FOREIGN KEY (bookingclassid) REFERENCES Bookingclass(classid),
    FOREIGN KEY (currencyid) REFERENCES Currency(currencyid)
);

-- Flightbooking Table
CREATE TABLE Flightbooking (
    bookingid INT PRIMARY KEY,
    flightid INT,
    bookingdate DATETIME,
    paymentmethodid INT,
    bookingtypeid INT,
    FOREIGN KEY (flightid) REFERENCES Flights(flightid),
    FOREIGN KEY (paymentmethodid) REFERENCES Paymentmethods(methodid),
    FOREIGN KEY (bookingtypeid) REFERENCES Bookingtype(typeid)
);

-- Flightbookingclasses Table
CREATE TABLE Flightbookingclasses (
    bookingclassid INT PRIMARY KEY,
    bookingid INT,
    bookingclassrefid INT,
    noofseats INT,
    unitprice DECIMAL(10,2),
    currencyid INT,
    FOREIGN KEY (bookingid) REFERENCES Flightbooking(bookingid),
    FOREIGN KEY (bookingclassrefid) REFERENCES Bookingclass(classid),
    FOREIGN KEY (currencyid) REFERENCES Currency(currencyid)
);

-- Traveldocuments Table
CREATE TABLE Traveldocuments (
    documentid INT PRIMARY KEY,
    documentname VARCHAR(50),
    documenttype VARCHAR(50)
);

-- Flightbookingpassengers Table
CREATE TABLE Flightbookingpassengers (
    passengerbookingid INT PRIMARY KEY,
    bookingclassid INT,
    documentid INT,
    documentname VARCHAR(50),
    firstname VARCHAR(50),
    middlename VARCHAR(50),
    lastname VARCHAR(50),
    gender VARCHAR(10),
    dateofbirth DATE,
    FOREIGN KEY (bookingclassid) REFERENCES Flightbookingclasses(bookingclassid),
    FOREIGN KEY (documentid) REFERENCES Traveldocuments(documentid)
);
-- Blacksnow Martin Kitonga SCNI/01292/2021
