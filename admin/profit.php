<?php  
session_start();  
if(!isset($_SESSION["user"])) {
    header("location:index.php");
}

include('db.php');

// Get monthly booking data
$monthly_bookings = [];
$booking_sql = "SELECT 
    DATE_FORMAT(cin, '%Y-%m') as month, 
    COUNT(*) as bookings 
    FROM roombook 
    WHERE cin >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    AND stat = 'Confirm'
    GROUP BY DATE_FORMAT(cin, '%Y-%m') 
    ORDER BY month";
$booking_result = mysqli_query($con, $booking_sql);
while($row = mysqli_fetch_assoc($booking_result)) {
    $monthly_bookings[] = $row;
}

// Get room type distribution for confirmed bookings
$room_types = [];
$room_sql = "SELECT TRoom, COUNT(*) as count FROM roombook WHERE stat = 'Confirm' GROUP BY TRoom";
$room_result = mysqli_query($con, $room_sql);
while($row = mysqli_fetch_assoc($room_result)) {
    $room_types[] = $row;
}

// Get revenue data
$revenue_sql = "SELECT 
    DATE_FORMAT(p.cin, '%Y-%m') as month,
    SUM(p.fintot) as revenue
    FROM payment p
    JOIN roombook r ON p.id = r.id
    WHERE p.cin >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    AND r.stat = 'Confirm'
    GROUP BY DATE_FORMAT(p.cin, '%Y-%m')
    ORDER BY month";
$revenue_result = mysqli_query($con, $revenue_sql);
$monthly_revenue = [];
while($row = mysqli_fetch_assoc($revenue_result)) {
    $monthly_revenue[] = $row;
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Analytics - El Sanchel Staycation</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #1A73E8;
            --secondary: #34A853;
            --danger: #EA4335;
            --warning: #FBBC05;
            --dark: #202124;
            --light: #F8F9FA;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: var(--dark);
        }
        
        .sidebar {
            background: linear-gradient(135deg, #1A73E8 0%, #0d47a1 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: var(--dark);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            background-color: white;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            height: 100%;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
        }
        
        .progress {
            height: 24px;
            border-radius: 12px;
        }
        
        .progress-bar {
            border-radius: 12px;
            font-weight: 500;
        }
        
        .stat-card {
            border-left: 4px solid;
            padding: 15px;
            border-radius: 8px;
            background: white;
        }
        
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar flex-shrink-0 p-3">
            <div class="sidebar-heading text-white p-3">
                <h4>El Sanchel Admin</h4>
                <hr class="my-2 bg-white">
            </div>
            <div class="list-group list-group-flush">
                <a href="home.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
              
                <a href="payment.php" class="nav-link">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
                <a href="profit.php" class="nav-link active">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div class="flex-grow-1">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid px-4">
                    <button class="btn btn-link" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-flex align-items-center ms-auto">
                        <span class="me-3">Welcome, <?php echo $_SESSION["user"]; ?></span>
                        <div class="dropdown">
                            <a class="btn btn-light rounded-circle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="usersetting.php"><i class="fas fa-user-cog me-2"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid p-4">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h2 class="h4 fw-bold">Booking Analytics</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Analytics</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stat-card border-left-warning">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="stat-label">Total Bookings</h6>
                                    <?php
                                    $total_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM roombook WHERE stat='Confirm'"))['count'];
                                    ?>
                                    <h2 class="stat-number text-warning"><?php echo $total_bookings; ?></h2>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-calendar-check text-warning fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="stat-card border-left-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="stat-label">Total Revenue</h6>
                                    <?php
                                    $total_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(fintot) as total FROM payment"))['total'];
                                    ?>
                                    <h2 class="stat-number text-success">₱<?php echo number_format($total_revenue, 2); ?></h2>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-money-bill-wave text-success fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="stat-card border-left-primary">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="stat-label">Avg. Booking Value</h6>
                                    <?php
                                    $avg_value = $total_bookings > 0 ? $total_revenue / $total_bookings : 0;
                                    ?>
                                    <h2 class="stat-number text-primary">₱<?php echo number_format($avg_value, 2); ?></h2>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-chart-pie text-primary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="chart-container">
                            <h5 class="mb-4 fw-bold">Monthly Bookings & Revenue</h5>
                            <canvas id="monthlyBookingsChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="chart-container">
                            <h5 class="mb-4 fw-bold">Room Type Distribution</h5>
                            <canvas id="roomTypeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Revenue Trends -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="chart-container">
                            <h5 class="mb-4 fw-bold">Revenue Trends</h5>
                            <canvas id="revenueChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Booking Trends Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Booking Trends</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="bookingsTable">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Total Bookings</th>
                                                <th>Confirmed</th>
                                                <th>Cancelled</th>
                                                <th>Conversion Rate</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $trends_sql = "SELECT 
                                            DATE_FORMAT(r.cin, '%Y-%m') as month, -- Specify 'r.cin' to avoid ambiguity
                                            COUNT(*) as total,
                                            SUM(CASE WHEN r.stat='Confirm' THEN 1 ELSE 0 END) as confirmed,
                                            SUM(CASE WHEN r.stat='Cancelled' THEN 1 ELSE 0 END) as cancelled,
                                            SUM(CASE WHEN r.stat='Confirm' THEN p.fintot ELSE 0 END) as revenue
                                            FROM roombook r
                                            LEFT JOIN payment p ON r.id = p.id
                                            WHERE r.cin >= DATE_SUB(NOW(), INTERVAL 12 MONTH) -- Specify 'r.cin'
                                            GROUP BY DATE_FORMAT(r.cin, '%Y-%m') -- Specify 'r.cin'
                                            ORDER BY month DESC";
                                            $trends_result = mysqli_query($con, $trends_sql);
                                            while($row = mysqli_fetch_assoc($trends_result)):
                                                $conversion_rate = $row['total'] > 0 ? round(($row['confirmed'] / $row['total']) * 100, 2) : 0;
                                            ?>
                                            <tr>
                                                <td><?php echo date('F Y', strtotime($row['month'] . '-01')); ?></td>
                                                <td><?php echo $row['total']; ?></td>
                                                <td><?php echo $row['confirmed']; ?></td>
                                                <td><?php echo $row['cancelled']; ?></td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                             style="width: <?php echo $conversion_rate; ?>%" 
                                                             aria-valuenow="<?php echo $conversion_rate; ?>" 
                                                             aria-valuemin="0" aria-valuemax="100">
                                                            <?php echo $conversion_rate; ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>₱<?php echo number_format($row['revenue'] ?? 0, 2); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('wrapper').classList.toggle('toggled');
        });
        
        // Monthly bookings chart
        const monthlyCtx = document.getElementById('monthlyBookingsChart').getContext('2d');
        const monthlyBookingsChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php 
                    foreach($monthly_bookings as $booking) {
                        echo "'" . date('M Y', strtotime($booking['month'] . '-01')) . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Bookings',
                    data: [
                        <?php 
                        foreach($monthly_bookings as $booking) {
                            echo $booking['bookings'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: '#1A73E8',
                    borderColor: '#1A73E8',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Room type distribution chart
        const roomTypeCtx = document.getElementById('roomTypeChart').getContext('2d');
        const roomTypeChart = new Chart(roomTypeCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php
                    foreach($room_types as $type) {
                        echo "'" . $type['TRoom'] . "',";
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                        foreach($room_types as $type) {
                            echo $type['count'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        '#1A73E8',
                        '#34A853',
                        '#FBBC05',
                        '#EA4335',
                        '#9B59B6',
                        '#34495E'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Revenue chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    foreach($monthly_revenue as $rev) {
                        echo "'" . date('M Y', strtotime($rev['month'] . '-01')) . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Revenue (₱)',
                    data: [
                        <?php 
                        foreach($monthly_revenue as $rev) {
                            echo $rev['revenue'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: 'rgba(26, 115, 232, 0.1)',
                    borderColor: '#1A73E8',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Initialize DataTable
        $(document).ready(function() {
            $('#bookingsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                responsive: true
            });
        });
    </script>
</body>
</html>