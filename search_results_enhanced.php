<?php
// search_results_enhanced.php - Enhanced search results with filters and better UI
include 'db_connect.php';

// Security: Validate and sanitize inputs
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dep_airport = intval($_POST['departure_airport']);
    $dest_airport = intval($_POST['destination_airport']);
    $dep_date = sanitize_input($_POST['departure_date']);
    $num_pass = intval($_POST['num_passengers']);
    $max_price = isset($_POST['max_price']) ? floatval($_POST['max_price']) : null;
    $preferred_class = isset($_POST['preferred_class']) ? sanitize_input($_POST['preferred_class']) : null;
    
    // Validate inputs
    if ($dep_airport <= 0 || $dest_airport <= 0 || $num_pass <= 0) {
        die("Invalid input parameters.");
    }
    
    if ($dep_airport == $dest_airport) {
        die("Departure and destination airports cannot be the same.");
    }
    
    try {
        // Call enhanced stored procedure
        $stmt = $conn->prepare("CALL SP_SearchFlights(?, ?, ?, ?, ?)");
        $stmt->bind_param("iisds", $dep_airport, $dest_airport, $dep_date, $max_price, $preferred_class);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Get airport names for display
        $airport_stmt = $conn->prepare("SELECT airportcode, airportname FROM Airports WHERE airportid IN (?, ?)");
        $airport_stmt->bind_param("ii", $dep_airport, $dest_airport);
        $airport_stmt->execute();
        $airport_result = $airport_stmt->get_result();
        $airports = [];
        while ($row = $airport_result->fetch_assoc()) {
            $airports[$row['airportcode']] = $row['airportname'];
        }
        $airport_stmt->close();
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Flight Search Results</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .flight-card {
                    border: 1px solid #e0e0e0;
                    border-radius: 10px;
                    margin-bottom: 15px;
                    transition: all 0.3s ease;
                    background: white;
                }
                .flight-card:hover {
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    transform: translateY(-2px);
                }
                .price-highlight {
                    font-size: 1.5rem;
                    font-weight: bold;
                    color: #28a745;
                }
                .flight-time {
                    font-size: 1.1rem;
                    font-weight: 600;
                }
                .airline-logo {
                    width: 40px;
                    height: 40px;
                    background: #667eea;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: bold;
                }
                .filter-section {
                    background: #f8f9fa;
                    border-radius: 10px;
                    padding: 20px;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class="container mt-4">
                <!-- Search Summary -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-search me-2"></i>Search Results
                                </h4>
                                <p class="card-text">
                                    <strong>Route:</strong> 
                                    <?php 
                                    $dep_codes = array_keys($airports);
                                    echo $dep_codes[0] . ' → ' . $dep_codes[1];
                                    ?>
                                    <br>
                                    <strong>Date:</strong> <?php echo date('M d, Y', strtotime($dep_date)); ?>
                                    <br>
                                    <strong>Passengers:</strong> <?php echo $num_pass; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="filter-section">
                    <h5><i class="fas fa-filter me-2"></i>Refine Your Search</h5>
                    <form method="POST" class="row g-3">
                        <input type="hidden" name="departure_airport" value="<?php echo $dep_airport; ?>">
                        <input type="hidden" name="destination_airport" value="<?php echo $dest_airport; ?>">
                        <input type="hidden" name="departure_date" value="<?php echo $dep_date; ?>">
                        <input type="hidden" name="num_passengers" value="<?php echo $num_pass; ?>">
                        
                        <div class="col-md-3">
                            <label for="max_price" class="form-label">Max Price ($)</label>
                            <input type="number" class="form-control" id="max_price" name="max_price" 
                                   value="<?php echo $max_price; ?>" placeholder="No limit">
                        </div>
                        <div class="col-md-3">
                            <label for="preferred_class" class="form-label">Class</label>
                            <select class="form-select" id="preferred_class" name="preferred_class">
                                <option value="">Any Class</option>
                                <option value="Economy" <?php echo $preferred_class == 'Economy' ? 'selected' : ''; ?>>Economy</option>
                                <option value="Business" <?php echo $preferred_class == 'Business' ? 'selected' : ''; ?>>Business</option>
                                <option value="First Class" <?php echo $preferred_class == 'First Class' ? 'selected' : ''; ?>>First Class</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Apply Filters
                            </button>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <a href="index.html" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>New Search
                            </a>
                        </div>
                    </form>
                </div>
                
                <?php if ($result->num_rows > 0): ?>
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="fas fa-plane me-2"></i>Found <?php echo $result->num_rows; ?> flights
                            </h5>
                            
                            <form action="book_flight.php" method="POST">
                                <input type="hidden" name="num_passengers" value="<?php echo $num_pass; ?>">
                                
                                <?php 
                                $flight_groups = [];
                                while ($row = $result->fetch_assoc()) {
                                    $flight_groups[$row['flightid']][] = $row;
                                }
                                
                                foreach ($flight_groups as $flight_id => $classes): 
                                    $flight = $classes[0]; // Get flight details from first class
                                ?>
                                    <div class="flight-card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                    <div class="airline-logo">
                                                        <?php echo substr($flight['airlinecode'], 0, 2); ?>
                                                    </div>
                                                    <div class="mt-2">
                                                        <strong><?php echo $flight['airlinename']; ?></strong><br>
                                                        <small class="text-muted"><?php echo $flight['flightno']; ?></small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="flight-time">
                                                        <?php echo date('H:i', strtotime($flight['departuretime'])); ?>
                                                    </div>
                                                    <div class="text-muted">
                                                        <?php echo $flight['dep_code'] . ' - ' . $flight['dep_name']; ?>
                                                    </div>
                                                    <div class="small text-muted">
                                                        <?php echo date('M d', strtotime($flight['departuretime'])); ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2 text-center">
                                                    <div class="text-muted">
                                                        <i class="fas fa-clock"></i> <?php echo floor($flight['duration']/60); ?>h <?php echo $flight['duration']%60; ?>m
                                                    </div>
                                                    <div class="small text-muted">
                                                        <?php echo $flight['aircraft_type']; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="flight-time">
                                                        <?php echo date('H:i', strtotime($flight['arrivaltime'])); ?>
                                                    </div>
                                                    <div class="text-muted">
                                                        <?php echo $flight['dest_code'] . ' - ' . $flight['dest_name']; ?>
                                                    </div>
                                                    <div class="small text-muted">
                                                        <?php echo date('M d', strtotime($flight['arrivaltime'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6>Available Classes:</h6>
                                                    <div class="row">
                                                        <?php foreach ($classes as $class): ?>
                                                            <div class="col-md-4 mb-2">
                                                                <div class="card border <?php echo $class['bookingclass'] == 'Business' ? 'border-warning' : ($class['bookingclass'] == 'First Class' ? 'border-danger' : 'border-primary'); ?>">
                                                                    <div class="card-body p-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" 
                                                                                   name="flightclassid" 
                                                                                   value="<?php echo $class['flightclassid']; ?>" 
                                                                                   id="class_<?php echo $class['flightclassid']; ?>" required>
                                                                            <label class="form-check-label w-100" for="class_<?php echo $class['flightclassid']; ?>">
                                                                                <div class="d-flex justify-content-between align-items-center">
                                                                                    <div>
                                                                                        <strong><?php echo $class['bookingclass']; ?></strong>
                                                                                        <br>
                                                                                        <small class="text-muted">
                                                                                            <?php echo $class['noofseats']; ?> seats available
                                                                                        </small>
                                                                                    </div>
                                                                                    <div class="price-highlight">
                                                                                        $<?php echo number_format($class['unitprice'], 0); ?>
                                                                                    </div>
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-credit-card me-2"></i>Proceed to Booking
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>No flights found</h4>
                        <p class="text-muted">Try adjusting your search criteria or dates.</p>
                        <a href="index.html" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>New Search
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        echo '<div class="container mt-5"><div class="alert alert-danger">An error occurred while searching for flights. Please try again.</div></div>';
    }
} else {
    header("Location: index.html");
    exit();
}
?>