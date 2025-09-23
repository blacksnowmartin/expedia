<?php
// book_flight.php - Handle booking form
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flightclassid = $_POST['flightclassid'];
    $flightid = $_POST['flightid'];
    $num_pass = $_POST['num_passengers'];
    // Fetch selected class details (simple query for mock)
    $query = "SELECT unitprice, currencyid FROM FlightClasses WHERE flightclassid = $flightclassid";
    $result = $conn->query($query);
    $class = $result->fetch_assoc();
    $unitprice = $class['unitprice'];
    $currencyid = $class['currencyid'];

    // Form for details
    echo '<div class="container mt-5">';
    echo '<h2>Enter Booking Details</h2>';
    echo '<form action="confirm_booking.php" method="POST">';
    echo '<input type="hidden" name="flightid" value="' . $flightid . '">';
    echo '<input type="hidden" name="num_passengers" value="' . $num_pass . '">';
    echo '<input type="hidden" name="unitprice" value="' . $unitprice . '">';
    echo '<input type="hidden" name="currencyid" value="' . $currencyid . '">';

    // Booking type (hardcode one way for simplicity)
    echo '<input type="hidden" name="bookingtypeid" value="1">';  // 1 = One Way

    // Payment method
    echo '<div class="mb-3">';
    echo '<label for="paymentmethodid">Payment Method</label>';
    echo '<select class="form-control" name="paymentmethodid" required>';
    echo '<option value="1">Credit Card</option>';
    echo '<option value="2">PayPal</option>';
    echo '</select>';
    echo '</div>';

    // Passenger details loop
    for ($i = 1; $i <= $num_pass; $i++) {
        echo '<h4>Passenger ' . $i . '</h4>';
        echo '<div class="row">';
        echo '<div class="col-md-4"><input type="text" class="form-control" name="firstname[]" placeholder="First Name" required></div>';
        echo '<div class="col-md-4"><input type="text" class="form-control" name="middlename[]" placeholder="Middle Name"></div>';
        echo '<div class="col-md-4"><input type="text" class="form-control" name="lastname[]" placeholder="Last Name" required></div>';
        echo '</div>';
        echo '<div class="row mt-2">';
        echo '<div class="col-md-4"><input type="date" class="form-control" name="dateofbirth[]" placeholder="Date of Birth" required></div>';
        echo '<div class="col-md-4"><select class="form-control" name="gender[]" required><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>';
        echo '<div class="col-md-4"><input type="text" class="form-control" name="iddocumentno[]" placeholder="Passport No" required></div>';
        echo '</div>';
        echo '<div class="row mt-2">';
        echo '<div class="col-md-6"><input type="date" class="form-control" name="documentissue[]" placeholder="Issue Date"></div>';
        echo '<div class="col-md-6"><input type="date" class="form-control" name="documentexpires[]" placeholder="Expiry Date"></div>';
        echo '</div>';
        // Hardcode documentname to Passport
        echo '<input type="hidden" name="documentname[]" value="Passport">';
    }

    echo '<button type="submit" class="btn btn-primary mt-3">Confirm Booking</button>';
    echo '</form>';
    echo '</div>';
}
$conn->close();
?>