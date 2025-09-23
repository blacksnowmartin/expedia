<?php
// confirm_booking.php - Process booking using stored procs
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flightid = $_POST['flightid'];
    $bookingdate = date('Y-m-d');  // Current date
    $paymentmethodid = $_POST['paymentmethodid'];
    $bookingtypeid = $_POST['bookingtypeid'];
    $num_passengers = $_POST['num_passengers'];
    $unitprice = $_POST['unitprice'];
    $currencyid = $_POST['currencyid'];

    // Call SP_CreateBooking
    $stmt = $conn->prepare("CALL SP_CreateBooking(?, ?, ?, ?, ?, ?, ?, @new_booking_id)");
    $stmt->bind_param("isiiidi", $flightid, $bookingdate, $paymentmethodid, $bookingtypeid, $num_passengers, $unitprice, $currencyid);
    $stmt->execute();

    // Get output param
    $result = $conn->query("SELECT @new_booking_id AS bookingid");
    $row = $result->fetch_assoc();
    $bookingid = $row['bookingid'];

    // Get the bookingclassid (latest for this booking)
    $query = "SELECT bookingclassid FROM FlightBookingClasses WHERE bookingid = $bookingid";
    $result = $conn->query($query);
    $class_row = $result->fetch_assoc();
    $bookingclassid = $class_row['bookingclassid'];

    // Add passengers
    for ($i = 0; $i < $num_passengers; $i++) {
        $doc_name = $_POST['documentname'][$i];
        $doc_issue = $_POST['documentissue'][$i] ?: NULL;
        $doc_expires = $_POST['documentexpires'][$i] ?: NULL;
        $id_doc_no = $_POST['iddocumentno'][$i];
        $first_name = $_POST['firstname'][$i];
        $middle_name = $_POST['middlename'][$i];
        $last_name = $_POST['lastname'][$i];
        $gender = $_POST['gender'][$i];
        $dob = $_POST['dateofbirth'][$i];

        $stmt_pass = $conn->prepare("CALL SP_AddPassenger(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_pass->bind_param("isssssssss", $bookingclassid, $doc_name, $doc_issue, $doc_expires, $id_doc_no, $first_name, $middle_name, $last_name, $gender, $dob);
        $stmt_pass->execute();
        $stmt_pass->close();
    }

    echo '<div class="container mt-5">';
    echo '<h2>Booking Confirmed!</h2>';
    echo '<p>Your booking ID is: ' . $bookingid . '</p>';
    echo '<a href="index.html" class="btn btn-secondary">Back to Search</a>';
    echo '</div>';

    $stmt->close();
}
$conn->close();
?>