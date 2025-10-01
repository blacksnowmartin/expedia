<?php
// setup.php - Database setup and configuration script
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expedia Flights - Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .step-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .step-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .step-number {
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <!-- Header -->
        <div class="setup-header text-center">
            <i class="fas fa-cog fa-3x mb-3"></i>
            <h2>Expedia Flights Setup</h2>
            <p class="lead">Complete system setup and configuration</p>
        </div>
        
        <!-- Setup Steps -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-list-ol me-2"></i>Setup Steps</h4>
                    </div>
                    <div class="card-body">
                        <!-- Step 1 -->
                        <div class="step-card">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">1</div>
                                <h5 class="mb-0">XAMPP Installation</h5>
                            </div>
                            <p>Ensure XAMPP is installed and running:</p>
                            <ul>
                                <li>Download XAMPP from <a href="https://www.apachefriends.org/" target="_blank">apachefriends.org</a></li>
                                <li>Start Apache and MySQL services</li>
                                <li>Open phpMyAdmin at <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
                            </ul>
                        </div>
                        
                        <!-- Step 2 -->
                        <div class="step-card">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">2</div>
                                <h5 class="mb-0">Database Creation</h5>
                            </div>
                            <p>Create the database and import schema:</p>
                            <ol>
                                <li>Open phpMyAdmin</li>
                                <li>Create new database named <code>expedia_flights</code></li>
                                <li>Import the file <code>database/database-optimized.sql</code></li>
                                <li>Verify all tables are created successfully</li>
                            </ol>
                        </div>
                        
                        <!-- Step 3 -->
                        <div class="step-card">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">3</div>
                                <h5 class="mb-0">File Configuration</h5>
                            </div>
                            <p>Verify all files are in place:</p>
                            <ul>
                                <li><code>index.html</code> - Main search page</li>
                                <li><code>search_results_enhanced.php</code> - Search results</li>
                                <li><code>book_flight_enhanced.php</code> - Booking form</li>
                                <li><code>confirm_booking_enhanced.php</code> - Confirmation</li>
                                <li><code>admin_dashboard.php</code> - Admin panel</li>
                                <li><code>db_connect.php</code> - Database connection</li>
                            </ul>
                        </div>
                        
                        <!-- Step 4 -->
                        <div class="step-card">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number">4</div>
                                <h5 class="mb-0">Test System</h5>
                            </div>
                            <p>Test the system functionality:</p>
                            <ol>
                                <li>Visit <a href="index.html" target="_blank">index.html</a> to test search</li>
                                <li>Try searching for flights (use airport IDs: 1=JFK, 2=LAX, 3=YYZ)</li>
                                <li>Test booking process</li>
                                <li>Access admin dashboard at <a href="admin_dashboard.php" target="_blank">admin_dashboard.php</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Info -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Quick Info</h5>
                    </div>
                    <div class="card-body">
                        <h6>Sample Airport IDs:</h6>
                        <ul class="list-unstyled">
                            <li><strong>1</strong> - JFK (New York)</li>
                            <li><strong>2</strong> - LAX (Los Angeles)</li>
                            <li><strong>3</strong> - YYZ (Toronto)</li>
                            <li><strong>11</strong> - YYZ (Toronto)</li>
                            <li><strong>15</strong> - LHR (London)</li>
                        </ul>
                        
                        <h6>Admin Access:</h6>
                        <p>Password: <code>admin123</code></p>
                        
                        <h6>Sample Search:</h6>
                        <ul>
                            <li>From: 1 (JFK)</li>
                            <li>To: 2 (LAX)</li>
                            <li>Date: 2025-10-01</li>
                            <li>Passengers: 1</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-database me-2"></i>Database Status</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Test database connection
                        try {
                            $conn = new mysqli("localhost", "root", "", "expedia_flights");
                            if ($conn->connect_error) {
                                echo '<div class="alert alert-danger">Database connection failed</div>';
                            } else {
                                echo '<div class="alert alert-success">Database connected successfully</div>';
                                
                                // Check if tables exist
                                $result = $conn->query("SHOW TABLES");
                                $table_count = $result->num_rows;
                                echo "<p><strong>Tables found:</strong> $table_count</p>";
                                
                                if ($table_count > 0) {
                                    echo '<div class="alert alert-success">Database schema loaded</div>';
                                } else {
                                    echo '<div class="alert alert-warning">No tables found. Please import the database schema.</div>';
                                }
                            }
                            $conn->close();
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-rocket me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="index.html" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Test Search
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="admin_dashboard.php" class="btn btn-success w-100">
                                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="http://localhost/phpmyadmin" target="_blank" class="btn btn-info w-100">
                                    <i class="fas fa-database me-2"></i>phpMyAdmin
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="README_ENHANCED.md" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-book me-2"></i>Documentation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>