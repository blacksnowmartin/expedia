<?php
// confirm_booking_enhanced.php - Enhanced booking confirmation system
include 'db_connect.php';

// Security: Validate and sanitize inputs
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Start transaction
        $conn->autocommit(FALSE);
        
        // Get booking parameters
        $flightclassid = intval($_POST['flightclassid']);
        $num_passengers = intval($_POST['num_passengers']);
        $total_price = floatval($_POST['total_price']);
        $payment_method = intval($_POST['payment_method']);
        $booking_type = intval($_POST['booking_type']);
        
        // Validate terms acceptance
        if (!isset($_POST['terms_conditions'])) {
            throw new Exception("You must accept the terms and conditions.");
        }
        
        // Get flight details
        $stmt = $conn->prepare("
            SELECT f.flightid, fc.unitprice, fc.currencyid
            FROM Flights f
            JOIN FlightClasses fc ON f.flightid = fc.flightid
            WHERE fc.flightclassid = ?
        ");
        $stmt->bind_param("i", $flightclassid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception("Flight class not found.");
        }
        
        $flight_data = $result->fetch_assoc();
        $stmt->close();
        
        // Create booking using stored procedure
        $stmt = $conn->prepare("CALL SP_CreateBooking(?, ?, ?, ?, ?, ?, ?, @booking_id, @success, @error_msg)");
        $booking_date = date('Y-m-d');
        $stmt->bind_param("isiiids", 
            $flight_data['flightid'], 
            $booking_date, 
            $payment_method, 
            $booking_type, 
            $num_passengers, 
            $flight_data['unitprice'], 
            $flight_data['currencyid']
        );
        $stmt->execute();
        $stmt->close();
        
        // Get the output parameters
        $result = $conn->query("SELECT @booking_id, @success, @error_msg");
        $output = $result->fetch_assoc();
        
        if (!$output['@success']) {
            throw new Exception($output['@error_msg']);
        }
        
        $booking_id = $output['@booking_id'];
        
        // Get booking class ID for passengers
        $stmt = $conn->prepare("SELECT bookingclassid FROM FlightBookingClasses WHERE bookingid = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking_class = $result->fetch_assoc();
        $booking_class_id = $booking_class['bookingclassid'];
        $stmt->close();
        
        // Add passengers
        for ($i = 1; $i <= $num_passengers; $i++) {
            $firstname = sanitize_input($_POST["firstname_$i"]);
            $middlename = sanitize_input($_POST["middlename_$i"]);
            $lastname = sanitize_input($_POST["lastname_$i"]);
            $gender = sanitize_input($_POST["gender_$i"]);
            $dateofbirth = sanitize_input($_POST["dateofbirth_$i"]);
            $document_type = sanitize_input($_POST["document_type_$i"]);
            $document_number = sanitize_input($_POST["document_number_$i"]);
            $document_issue = !empty($_POST["document_issue_$i"]) ? sanitize_input($_POST["document_issue_$i"]) : null;
            $document_expiry = !empty($_POST["document_expiry_$i"]) ? sanitize_input($_POST["document_expiry_$i"]) : null;
            $special_requests = sanitize_input($_POST["special_requests_$i"]);
            
            // Validate required fields
            if (empty($firstname) || empty($lastname) || empty($gender) || empty($dateofbirth) || 
                empty($document_type) || empty($document_number)) {
                throw new Exception("All required passenger fields must be filled.");
            }
            
            // Add passenger using stored procedure
            $stmt = $conn->prepare("CALL SP_AddPassenger(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssssss", 
                $booking_class_id,
                $document_type,
                $document_issue,
                $document_expiry,
                $document_number,
                $firstname,
                $middlename,
                $lastname,
                $gender,
                $dateofbirth
            );
            $stmt->execute();
            $stmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        $conn->autocommit(TRUE);
        
        // Get booking details for confirmation
        $stmt = $conn->prepare("CALL SP_GetBookingDetails(?)");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking_details = $result->fetch_assoc();
        $stmt->close();
        
        // Get passenger details
        $stmt = $conn->prepare("
            SELECT fbp.firstname, fbp.middlename, fbp.lastname, fbp.gender, fbp.dateofbirth,
                   td.documentname, fbp.iddocumentno
            FROM FlightBookingPassengers fbp
            JOIN TravelDocuments td ON fbp.documentid = td.documentid
            JOIN FlightBookingClasses fbc ON fbp.bookingclassid = fbc.bookingclassid
            WHERE fbc.bookingid = ?
        ");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $passengers = [];
        while ($row = $result->fetch_assoc()) {
            $passengers[] = $row;
        }
        $stmt->close();
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Booking Confirmation</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .confirmation-header {
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                    border-radius: 15px;
                    padding: 2rem;
                    margin-bottom: 2rem;
                    text-align: center;
                }
                .booking-details {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    padding: 2rem;
                    margin-bottom: 2rem;
                }
                .passenger-card {
                    background: #f8f9fa;
                    border-radius: 10px;
                    padding: 1.5rem;
                    margin-bottom: 1rem;
                }
                .qr-code {
                    background: white;
                    border: 2px dashed #dee2e6;
                    border-radius: 10px;
                    padding: 1rem;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="container mt-4">
                <!-- Confirmation Header -->
                <div class="confirmation-header">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h2>Booking Confirmed!</h2>
                    <p class="lead">Your flight has been successfully booked</p>
                    <h4>Booking Reference: <strong><?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></strong></h4>
                </div>
                
                <!-- Booking Details -->
                <div class="booking-details">
                    <h4 class="mb-4">
                        <i class="fas fa-plane me-2"></i>Flight Details
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><?php echo $booking_details['airlinename']; ?> <?php echo $booking_details['flightno']; ?></h5>
                                    <p class="mb-1">
                                        <strong><?php echo $booking_details['dep_code']; ?></strong> - 
                                        <?php echo date('M d, Y H:i', strtotime($booking_details['departuretime'])); ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong><?php echo $booking_details['dest_code']; ?></strong> - 
                                        <?php echo date('M d, Y H:i', strtotime($booking_details['arrivaltime'])); ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Booking Date:</strong> <?php echo date('M d, Y', strtotime($booking_details['bookingdate'])); ?></p>
                                    <p class="mb-1"><strong>Payment Method:</strong> <?php echo $booking_details['methodname']; ?></p>
                                    <p class="mb-1"><strong>Booking Type:</strong> <?php echo $booking_details['typename']; ?></p>
                                    <p class="mb-1"><strong>Status:</strong> 
                                        <span class="badge bg-success"><?php echo $booking_details['booking_status']; ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="qr-code">
                                <i class="fas fa-qrcode fa-2x text-muted mb-2"></i>
                                <p class="mb-0">Booking QR Code</p>
                                <small class="text-muted">Show at check-in</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Passenger Details -->
                <div class="booking-details">
                    <h4 class="mb-4">
                        <i class="fas fa-users me-2"></i>Passenger Details
                    </h4>
                    
                    <?php foreach ($passengers as $index => $passenger): ?>
                        <div class="passenger-card">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6>Passenger <?php echo $index + 1; ?></h6>
                                    <p class="mb-1">
                                        <strong><?php echo $passenger['firstname']; ?> 
                                        <?php if (!empty($passenger['middlename'])) echo $passenger['middlename'] . ' '; ?>
                                        <?php echo $passenger['lastname']; ?></strong>
                                    </p>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <?php echo $passenger['gender']; ?> • 
                                            Born: <?php echo date('M d, Y', strtotime($passenger['dateofbirth'])); ?>
                                        </small>
                                    </p>
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            <?php echo $passenger['documentname']; ?>: <?php echo $passenger['iddocumentno']; ?>
                                        </small>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-primary">Confirmed</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Price Summary -->
                <div class="booking-details">
                    <h4 class="mb-4">
                        <i class="fas fa-receipt me-2"></i>Price Summary
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-1">Flight booking for <?php echo count($passengers); ?> passenger(s)</p>
                            <p class="mb-1">Base price per passenger</p>
                            <hr>
                            <h5>Total Amount</h5>
                        </div>
                        <div class="col-md-4 text-end">
                            <p class="mb-1">$<?php echo number_format($booking_details['total_amount'] / count($passengers), 2); ?></p>
                            <p class="mb-1">$<?php echo number_format($booking_details['total_amount'], 2); ?> <?php echo $booking_details['currencycode']; ?></p>
                            <hr>
                            <h4>$<?php echo number_format($booking_details['total_amount'], 2); ?> <?php echo $booking_details['currencycode']; ?></h4>
                        </div>
                    </div>
                </div>
                
                <!-- Important Information -->
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle me-2"></i>Important Information</h5>
                    <ul class="mb-0">
                        <li>Please arrive at the airport at least 2 hours before departure for international flights</li>
                        <li>Check-in opens 24 hours before departure</li>
                        <li>Bring a valid photo ID and travel documents</li>
                        <li>Contact the airline directly for any special assistance needs</li>
                        <li>Check flight status before traveling to the airport</li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="text-center mb-5">
                    <button class="btn btn-primary btn-lg me-3" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Confirmation
                    </button>
                    <button class="btn btn-success btn-lg me-3" onclick="sendEmailConfirmation()">
                        <i class="fas fa-envelope me-2"></i>Email Confirmation
                    </button>
                    <a href="index.html" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-home me-2"></i>New Search
                    </a>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                function sendEmailConfirmation() {
                    alert('Email confirmation feature would be implemented here. In a real system, this would send a confirmation email to the passenger.');
                }
            </script>
        </body>
        </html>
        <?php
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->autocommit(TRUE);
        
        error_log("Booking error: " . $e->getMessage());
        echo '<div class="container mt-5">
                <div class="alert alert-danger">
                    <h4>Booking Failed</h4>
                    <p>' . $e->getMessage() . '</p>
                    <a href="index.html" class="btn btn-primary">Try Again</a>
                </div>
              </div>';
    }
} else {
    header("Location: index.html");
    exit();
}
?>