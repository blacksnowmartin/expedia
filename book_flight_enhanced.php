<?php
// book_flight_enhanced.php - Enhanced booking system with passenger details
include 'db_connect.php';

// Security: Validate and sanitize inputs
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flightclassid = intval($_POST['flightclassid']);
    $num_passengers = intval($_POST['num_passengers']);
    
    // Get flight and class details
    $stmt = $conn->prepare("
        SELECT f.flightid, f.flightno, a.airlinename, a.airlinecode,
               dep_air.airportcode AS dep_code, dep_air.airportname AS dep_name,
               dest_air.airportcode AS dest_code, dest_air.airportname AS dest_name,
               f.departuretime, f.arrivaltime, f.duration,
               fc.bookingclass, fc.unitprice, c.currencycode
        FROM Flights f
        JOIN Airlines a ON f.airlineid = a.airlineid
        JOIN Airports dep_air ON f.departureairportid = dep_air.airportid
        JOIN Airports dest_air ON f.destinationairportid = dest_air.airportid
        JOIN FlightClasses fc ON f.flightid = fc.flightid
        JOIN Currencies c ON fc.currencyid = c.currencyid
        WHERE fc.flightclassid = ?
    ");
    $stmt->bind_param("i", $flightclassid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        die("Flight class not found.");
    }
    
    $flight = $result->fetch_assoc();
    $total_price = $flight['unitprice'] * $num_passengers;
    $stmt->close();
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Flight Booking</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            .booking-summary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 15px;
                padding: 2rem;
                margin-bottom: 2rem;
            }
            .passenger-form {
                background: white;
                border-radius: 15px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                padding: 2rem;
                margin-bottom: 2rem;
            }
            .form-section {
                border: 1px solid #e0e0e0;
                border-radius: 10px;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            .price-breakdown {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 1.5rem;
            }
        </style>
    </head>
    <body>
        <div class="container mt-4">
            <!-- Booking Summary -->
            <div class="booking-summary">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3><i class="fas fa-plane me-2"></i>Flight Booking Summary</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo $flight['airlinename']; ?> <?php echo $flight['flightno']; ?></h5>
                                <p class="mb-1">
                                    <strong><?php echo $flight['dep_code']; ?></strong> - <?php echo $flight['dep_name']; ?>
                                </p>
                                <p class="mb-1">
                                    <strong><?php echo $flight['dest_code']; ?></strong> - <?php echo $flight['dest_name']; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Departure:</strong> <?php echo date('M d, Y H:i', strtotime($flight['departuretime'])); ?>
                                </p>
                                <p class="mb-1">
                                    <strong>Arrival:</strong> <?php echo date('M d, Y H:i', strtotime($flight['arrivaltime'])); ?>
                                </p>
                                <p class="mb-1">
                                    <strong>Duration:</strong> <?php echo floor($flight['duration']/60); ?>h <?php echo $flight['duration']%60; ?>m
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <h4><?php echo $flight['bookingclass']; ?></h4>
                        <h2>$<?php echo number_format($total_price, 2); ?></h2>
                        <small>For <?php echo $num_passengers; ?> passenger(s)</small>
                    </div>
                </div>
            </div>
            
            <!-- Passenger Details Form -->
            <div class="passenger-form">
                <h4 class="mb-4">
                    <i class="fas fa-users me-2"></i>Passenger Details
                </h4>
                
                <form action="confirm_booking.php" method="POST">
                    <input type="hidden" name="flightclassid" value="<?php echo $flightclassid; ?>">
                    <input type="hidden" name="num_passengers" value="<?php echo $num_passengers; ?>">
                    <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                    
                    <?php for ($i = 1; $i <= $num_passengers; $i++): ?>
                        <div class="form-section">
                            <h5>Passenger <?php echo $i; ?></h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="firstname_<?php echo $i; ?>" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="firstname_<?php echo $i; ?>" 
                                           name="firstname_<?php echo $i; ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="middlename_<?php echo $i; ?>" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middlename_<?php echo $i; ?>" 
                                           name="middlename_<?php echo $i; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="lastname_<?php echo $i; ?>" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="lastname_<?php echo $i; ?>" 
                                           name="lastname_<?php echo $i; ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="gender_<?php echo $i; ?>" class="form-label">Gender *</label>
                                    <select class="form-select" id="gender_<?php echo $i; ?>" name="gender_<?php echo $i; ?>" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="dateofbirth_<?php echo $i; ?>" class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" id="dateofbirth_<?php echo $i; ?>" 
                                           name="dateofbirth_<?php echo $i; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="document_type_<?php echo $i; ?>" class="form-label">Document Type *</label>
                                    <select class="form-select" id="document_type_<?php echo $i; ?>" name="document_type_<?php echo $i; ?>" required>
                                        <option value="">Select Document</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Driver License">Driver License</option>
                                        <option value="National ID">National ID</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="document_number_<?php echo $i; ?>" class="form-label">Document Number *</label>
                                    <input type="text" class="form-control" id="document_number_<?php echo $i; ?>" 
                                           name="document_number_<?php echo $i; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="document_issue_<?php echo $i; ?>" class="form-label">Document Issue Date</label>
                                    <input type="date" class="form-control" id="document_issue_<?php echo $i; ?>" 
                                           name="document_issue_<?php echo $i; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="document_expiry_<?php echo $i; ?>" class="form-label">Document Expiry Date</label>
                                    <input type="date" class="form-control" id="document_expiry_<?php echo $i; ?>" 
                                           name="document_expiry_<?php echo $i; ?>">
                                </div>
                                <div class="col-12">
                                    <label for="special_requests_<?php echo $i; ?>" class="form-label">Special Requests</label>
                                    <textarea class="form-control" id="special_requests_<?php echo $i; ?>" 
                                              name="special_requests_<?php echo $i; ?>" rows="2" 
                                              placeholder="Any special requests or dietary requirements..."></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                    
                    <!-- Payment Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method *</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="1">Credit Card</option>
                                    <option value="2">Debit Card</option>
                                    <option value="3">PayPal</option>
                                    <option value="4">Apple Pay</option>
                                    <option value="5">Google Pay</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="booking_type" class="form-label">Booking Type *</label>
                                <select class="form-select" id="booking_type" name="booking_type" required>
                                    <option value="">Select Booking Type</option>
                                    <option value="1">One Way</option>
                                    <option value="2">Round Trip</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Price Breakdown -->
                    <div class="price-breakdown">
                        <h5><i class="fas fa-calculator me-2"></i>Price Breakdown</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p class="mb-1"><?php echo $flight['bookingclass']; ?> Class x <?php echo $num_passengers; ?> passenger(s)</p>
                                <p class="mb-1">Base Price: $<?php echo number_format($flight['unitprice'], 2); ?> per passenger</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <h4>Total: $<?php echo number_format($total_price, 2); ?> <?php echo $flight['currencycode']; ?></h4>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms_conditions" name="terms_conditions" required>
                        <label class="form-check-label" for="terms_conditions">
                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-credit-card me-2"></i>Confirm Booking
                        </button>
                        <a href="search_results_enhanced.php" class="btn btn-outline-secondary btn-lg ms-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Results
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Terms and Conditions Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Terms and Conditions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h6>Booking Terms</h6>
                        <ul>
                            <li>All bookings are subject to availability</li>
                            <li>Prices are subject to change until booking is confirmed</li>
                            <li>Cancellation policies vary by airline and fare type</li>
                            <li>Passenger names must match travel documents exactly</li>
                            <li>Check-in requirements vary by airline</li>
                        </ul>
                        <h6>Refund Policy</h6>
                        <ul>
                            <li>Refunds are subject to airline policies</li>
                            <li>Processing fees may apply</li>
                            <li>Refund processing time: 7-14 business days</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Privacy Policy Modal -->
        <div class="modal fade" id="privacyModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Privacy Policy</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h6>Data Collection</h6>
                        <p>We collect personal information necessary for flight booking and travel services.</p>
                        <h6>Data Usage</h6>
                        <p>Your information is used to process bookings, send confirmations, and provide customer service.</p>
                        <h6>Data Protection</h6>
                        <p>We implement industry-standard security measures to protect your personal information.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
} else {
    header("Location: index.html");
    exit();
}
?>