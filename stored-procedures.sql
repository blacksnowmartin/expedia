-- Stored Procedures for Expedia Flight Booking Database

DELIMITER //

-- 1. Get All Airlines
CREATE PROCEDURE GetAllAirlines()
BEGIN
    SELECT airlineid, airlinename, airlinelogo
    FROM Airline;
END //

-- 2. Get All Flights Between Two Cities
CREATE PROCEDURE GetFlightsByCities(
    IN departure VARCHAR(50),
    IN destination VARCHAR(50)
)
BEGIN
    SELECT flightno, departurecity, destinationcity, departuretime, duration
    FROM Flights
    WHERE departurecity = departure
      AND destinationcity = destination;
END //

-- 3. Get Flight Details by Flight Number
CREATE PROCEDURE GetFlightDetails(
    IN flight_number VARCHAR(10)
)
BEGIN
    SELECT f.flightno, f.departurecity, f.destinationcity, f.departuretime, f.duration,
           a.airlinename, ap.airportname AS departure_airport
    FROM Flights f
    JOIN Airline a ON f.airlineid = a.airlineid
    JOIN Airports ap ON f.departureairportid = ap.airportid
    WHERE f.flightno = flight_number;
END //

-- 4. Get All Bookings for a Specific Passenger
CREATE PROCEDURE GetBookingsByPassenger(
    IN fname VARCHAR(50),
    IN lname VARCHAR(50)
)
BEGIN
    SELECT fb.bookingid, fb.bookingdate, f.flightno, f.departurecity, f.destinationcity
    FROM Flightbookingpassengers p
    JOIN Flightbookingclasses fbc ON p.bookingclassid = fbc.bookingclassid
    JOIN Flightbooking fb ON fbc.bookingid = fb.bookingid
    JOIN Flights f ON fb.flightid = f.flightid
    WHERE p.firstname = fname AND p.lastname = lname;
END //

-- 5. Count Passengers in a Flight
CREATE PROCEDURE CountPassengersInFlight(
    IN flight_number VARCHAR(10)
)
BEGIN
    SELECT f.flightno, COUNT(*) AS passenger_count
    FROM Flightbookingpassengers p
    JOIN Flightbookingclasses fbc ON p.bookingclassid = fbc.bookingclassid
    JOIN Flightbooking fb ON fbc.bookingid = fb.bookingid
    JOIN Flights f ON fb.flightid = f.flightid
    WHERE f.flightno = flight_number
    GROUP BY f.flightno;
END //

-- 6. List Available Seats for a Flight
CREATE PROCEDURE GetAvailableSeats(
    IN flight_number VARCHAR(10)
)
BEGIN
    SELECT fc.flightclassid, fc.noofseats, fc.unitprice, c.currencyname
    FROM Flightclasses fc
    JOIN Flights f ON fc.flightid = f.flightid
    JOIN Currency c ON fc.currencyid = c.currencyid
    WHERE f.flightno = flight_number;
END //

-- 7. Get All Airports in a Country
CREATE PROCEDURE GetAirportsByCountry(
    IN country_name VARCHAR(50)
)
BEGIN
    SELECT ap.airportname, ap.airportcode, c.cityname
    FROM Airports ap
    JOIN City c ON ap.cityid = c.cityid
    JOIN Country co ON c.countryid = co.countryid
    WHERE co.countryname = country_name;
END //

-- 8. Get Total Revenue for a Flight
CREATE PROCEDURE GetRevenueByFlight(
    IN flight_number VARCHAR(10)
)
BEGIN
    SELECT f.flightno, SUM(fbc.noofseats * fbc.unitprice) AS total_revenue
    FROM Flightbookingclasses fbc
    JOIN Flightbooking fb ON fbc.bookingid = fb.bookingid
    JOIN Flights f ON fb.flightid = f.flightid
    WHERE f.flightno = flight_number
    GROUP BY f.flightno;
END //

-- 9. Get Passengers by Booking ID
CREATE PROCEDURE GetPassengersByBooking(
    IN booking_id INT
)
BEGIN
    SELECT firstname, middlename, lastname, gender, dateofbirth
    FROM Flightbookingpassengers
    WHERE bookingclassid IN (
        SELECT bookingclassid FROM Flightbookingclasses WHERE bookingid = booking_id
    );
END //

-- 10. Get Bookings by Date Range
CREATE PROCEDURE GetBookingsByDateRange(
    IN start_date DATE,
    IN end_date DATE
)
BEGIN
    SELECT bookingid, bookingdate, flightid
    FROM Flightbooking
    WHERE bookingdate BETWEEN start_date AND end_date;
END //

-- 11. Get Flights by Airline
CREATE PROCEDURE GetFlightsByAirline(
    IN airline_name VARCHAR(50)
)
BEGIN
    SELECT f.flightno, f.departurecity, f.destinationcity, f.departuretime
    FROM Flights f
    JOIN Airline a ON f.airlineid = a.airlineid
    WHERE a.airlinename = airline_name;
END //

DELIMITER ;
