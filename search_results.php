<?php
// search_results.php - Handle search and display results
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dep_airport = $_POST['departure_airport'];
    $dest_airport = $_POST['destination_airport'];
    $dep_date = $_POST['departure_date'];
    $num_pass = $_POST['num_passengers'];

    // Call stored procedure
    $stmt = $conn->prepare("CALL SP_SearchFlights(?, ?, ?)");
    $stmt->bind_param("iis", $dep_airport, $dest_airport, $dep_date);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<div class="container mt-5">';
    echo '<h2>Search Results</h2>';
    if ($result->num_rows > 0) {
        echo '<form action="book_flight.php" method="POST">';
        echo '<input type="hidden" name="num_passengers" value="' . $num_pass . '">';
        echo '<table class="table table-striped">';
        echo '<thead><tr><th>Flight No</th><th>Airline</th><th>Dep</th><th>Dest</th><th>Time</th><th>Duration (min)</th><th>Class</th><th>Price</th><th>Select</th></tr></thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['flightno'] . '</td>';
            echo '<td>' . $row['airlinename'] . '</td>';
            echo '<td>' . $row['dep_code'] . '</td>';
            echo '<td>' . $row['dest_code'] . '</td>';
            echo '<td>' . $row['departuretime'] . '</td>';
            echo '<td>' . $row['duration'] . '</td>';
            echo '<td>' . $row['bookingclass'] . '</td>';
            echo '<td>' . $row['unitprice'] . ' ' . $row['currencycode'] . '</td>';
            echo '<td><input type="radio" name="flightclassid" value="' . $row['flightclassid'] . '" required></td>';
            echo '</tr>';
            // Hidden fields
            echo '<input type="hidden" name="flightid" value="' . $row['flightid'] . '">';
        }
        echo '</tbody></table>';
        echo '<button type="submit" class="btn btn-success">Proceed to Booking</button>';
        echo '</form>';
    } else {
        echo '<p>No flights found.</p>';
    }
    echo '</div>';
    $stmt->close();
}
$conn->close();
?>