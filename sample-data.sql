-- Insert sample data for Country
INSERT INTO Country (countryid, countryname) VALUES
(1, 'Kenya'),
(2, 'Tanzania'),
(3, 'Uganda');

-- Insert sample data for City
INSERT INTO City (cityid, cityname, countryid) VALUES
(1, 'Nairobi', 1),
(2, 'Mombasa', 1),
(3, 'Dar es Salaam', 2),
(4, 'Kampala', 3),
(5, 'Entebbe', 3);

-- Insert sample data for Airports
INSERT INTO Airports (airportid, airportcode, airportname, cityid) VALUES
(1, 'NBO', 'Jomo Kenyatta International Airport', 1),
(2, 'MBA', 'Moi International Airport', 2),
(3, 'DAR', 'Julius Nyerere International Airport', 3),
(4, 'EBB', 'Entebbe International Airport', 4);

-- Insert sample data for Airline
INSERT INTO Airline (airlineid, airlinename, airlinelogo) VALUES
(1, 'Kenya Airways', 'kenya_logo.png'),
(2, 'Precision Air', 'precision_logo.png'),
(3, 'Uganda Airlines', 'uganda_logo.png');

-- Insert sample data for Currency
INSERT INTO Currency (currencyid, currencyname) VALUES
(1, 'KES'),
(2, 'USD'),
(3, 'TZS');

-- Insert sample data for Flights
INSERT INTO Flights (flightid, airlineid, flightno, departurecity, destinationcity, departuretime, duration, departureairportid) VALUES
(1, 1, 'KQ402', 'Nairobi', 'Mombasa', '2025-08-12 08:00:00', '1h00m', 1),
(2, 2, 'PW725', 'Dar es Salaam', 'Nairobi', '2025-08-12 10:00:00', '2h15m', 3),
(3, 3, 'UR320', 'Entebbe', 'Nairobi', '2025-08-12 06:30:00', '1h30m', 4);

-- Insert sample data for Bookingclass
INSERT INTO Bookingclass (classid, classname) VALUES
(1, 'Economy'),
(2, 'Business'),
(3, 'First Class');

-- Insert sample data for Bookingtype
INSERT INTO Bookingtype (typeid, typename) VALUES
(1, 'One-way'),
(2, 'Round Trip');

-- Insert sample data for Paymentmethods
INSERT INTO Paymentmethods (methodid, methodname) VALUES
(1, 'Credit Card'),
(2, 'Mobile Money'),
(3, 'Cash');

-- Insert sample data for Flightclasses
INSERT INTO Flightclasses (flightclassid, flightid, bookingclassid, noofseats, unitprice, currencyid) VALUES
(1, 1, 1, 100, 7500.00, 1),
(2, 1, 2, 20, 15000.00, 1),
(3, 2, 1, 80, 200.00, 2),
(4, 3, 1, 90, 120000.00, 3);

-- Insert sample data for Flightbooking
INSERT INTO Flightbooking (bookingid, flightid, bookingdate, paymentmethodid, bookingtypeid) VALUES
(1, 1, '2025-08-10', 2, 1),
(2, 1, '2025-08-11', 1, 2),
(3, 2, '2025-08-10', 3, 1);

-- Insert sample data for Flightbookingclasses
INSERT INTO Flightbookingclasses (bookingclassid, bookingid, bookingclassrefid, noofseats, unitprice, currencyid) VALUES
(1, 1, 1, 2, 7500.00, 1),
(2, 2, 2, 1, 15000.00, 1),
(3, 3, 1, 3, 200.00, 2);

-- Insert sample data for Traveldocuments
INSERT INTO Traveldocuments (documentid, documentname, documenttype) VALUES
(1, 'Passport', 'International'),
(2, 'National ID', 'Domestic');

-- Insert sample data for Flightbookingpassengers
INSERT INTO Flightbookingpassengers (passengerbookingid, bookingclassid, documentid, documentname, firstname, middlename, lastname, gender, dateofbirth) VALUES
(1, 1, 1, 'Passport', 'John', 'M', 'Doe', 'Male', '1990-05-10'),
(2, 1, 1, 'Passport', 'Mary', 'A', 'Smith', 'Female', '1985-09-20'),
(3, 2, 2, 'National ID', 'James', 'K', 'Otieno', 'Male', '1995-02-14'),
(4, 3, 1, 'Passport', 'Alice', 'N', 'Kamau', 'Female', '1992-07-18'),
(5, 3, 1, 'Passport', 'Peter', 'O', 'Mutiso', 'Male', '1988-11-02'),
(6, 3, 2, 'National ID', 'Jane', 'W', 'Wambui', 'Female', '1997-03-05');
