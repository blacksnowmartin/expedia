<?php
// admin_dashboard.php - Admin dashboard for managing flights and bookings
include 'db_connect.php';

// Simple authentication (in production, use proper authentication)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['admin_password']) && $_POST['admin_password'] === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-center">Admin Login</h4>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="admin_password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Get statistics
$stats = [];
$result = $conn->query("CALL SP_GetFlightStats()");
if ($result) {
    $stats = $result->fetch_assoc();
    $result->close();
}

// Get recent bookings
$recent_bookings = [];
$result = $conn->query("
    SELECT fb.bookingid, fb.bookingdate, fb.total_amount, c.currencycode, fb.booking_status,
           f.flightno, a.airlinename, f.departuretime
    FROM FlightBookings fb
    JOIN Flights f ON fb.flightid = f.flightid
    JOIN Airlines a ON f.airlineid = a.airlineid
    JOIN Currencies c ON fb.currencyid = c.currencyid
    ORDER BY fb.bookingdate DESC
    LIMIT 10
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_bookings[] = $row;
    }
    $result->close();
}

// Get flight performance
$flight_performance = [];
$result = $conn->query("
    SELECT a.airlinename, COUNT(f.flightid) as total_flights,
           COUNT(CASE WHEN f.status = 'On Time' THEN 1 END) as on_time,
           COUNT(CASE WHEN f.status = 'Delayed' THEN 1 END) as delayed,
           COUNT(CASE WHEN f.status = 'Cancelled' THEN 1 END) as cancelled
    FROM Airlines a
    LEFT JOIN Flights f ON a.airlineid = f.airlineid
    GROUP BY a.airlineid, a.airlinename
    ORDER BY total_flights DESC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $flight_performance[] = $row;
    }
    $result->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .stat-card.danger {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
                    <p class="mb-0">Flight booking system management</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="index.html" class="btn btn-light me-2">
                        <i class="fas fa-home me-1"></i>View Site
                    </a>
                    <a href="?logout=1" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4><?php echo $stats['total_flights'] ?? 0; ?></h4>
                            <p class="mb-0">Total Flights</p>
                        </div>
                        <i class="fas fa-plane fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4><?php echo $stats['on_time_flights'] ?? 0; ?></h4>
                            <p class="mb-0">On Time</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4><?php echo $stats['delayed_flights'] ?? 0; ?></h4>
                            <p class="mb-0">Delayed</p>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4><?php echo $stats['cancelled_flights'] ?? 0; ?></h4>
                            <p class="mb-0">Cancelled</p>
                        </div>
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Recent Bookings -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-receipt me-2"></i>Recent Bookings</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Flight</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_bookings as $booking): ?>
                                        <tr>
                                            <td>#<?php echo str_pad($booking['bookingid'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td>
                                                <strong><?php echo $booking['airlinename']; ?></strong><br>
                                                <small class="text-muted"><?php echo $booking['flightno']; ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($booking['bookingdate'])); ?></td>
                                            <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $booking['booking_status'] == 'Confirmed' ? 'success' : 'warning'; ?>">
                                                    <?php echo $booking['booking_status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Flight Performance -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>Airline Performance</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($flight_performance as $airline): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong><?php echo $airline['airlinename']; ?></strong>
                                    <span class="badge bg-primary"><?php echo $airline['total_flights']; ?> flights</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo $airline['total_flights'] > 0 ? ($airline['on_time'] / $airline['total_flights']) * 100 : 0; ?>%"></div>
                                    <div class="progress-bar bg-warning" style="width: <?php echo $airline['total_flights'] > 0 ? ($airline['delayed'] / $airline['total_flights']) * 100 : 0; ?>%"></div>
                                    <div class="progress-bar bg-danger" style="width: <?php echo $airline['total_flights'] > 0 ? ($airline['cancelled'] / $airline['total_flights']) * 100 : 0; ?>%"></div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-success">On Time: <?php echo $airline['on_time']; ?></small>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-warning">Delayed: <?php echo $airline['delayed']; ?></small>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-danger">Cancelled: <?php echo $airline['cancelled']; ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-tools me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="?action=view_flights" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-plane me-2"></i>View All Flights
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="?action=view_bookings" class="btn btn-outline-success w-100">
                                    <i class="fas fa-receipt me-2"></i>View All Bookings
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="?action=add_flight" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-plus me-2"></i>Add New Flight
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="?action=reports" class="btn btn-outline-info w-100">
                                    <i class="fas fa-chart-line me-2"></i>Generate Reports
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
<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_dashboard.php");
    exit();
}
?>