<?php  
session_start();  
if(!isset($_SESSION["user"])) {
    header("location:index.php");
}

include('db.php');

// Get counts for dashboard
$new_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM roombook WHERE stat='Not Confirm'"))['count'];
$confirmed_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM roombook WHERE stat='Confirm'"))['count'];
$cancelled_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM roombook WHERE stat='Cancelled'"))['count'];

// Get monthly booking data for chart
$monthly_bookings = [];
$booking_sql = "SELECT 
    DATE_FORMAT(cin, '%Y-%m') as month, 
    COUNT(*) as bookings 
    FROM roombook 
    WHERE cin >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(cin, '%Y-%m') 
    ORDER BY month";
$booking_result = mysqli_query($con, $booking_sql);
while($row = mysqli_fetch_assoc($booking_result)) {
    $monthly_bookings[] = $row;
}

// Get room type distribution for chart
$room_types = [];
$room_sql = "SELECT TRoom, COUNT(*) as count FROM roombook GROUP BY TRoom";
$room_result = mysqli_query($con, $room_sql);
while($row = mysqli_fetch_assoc($room_result)) {
    $room_types[] = $row;
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - El Sanchel Staycation</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            background-color: #f5f5f5;
            color: var(--dark);
        }
        
        .sidebar {
            background: linear-gradient(135deg, #1A73E8 0%, #0d47a1 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
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
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .card-stat {
            border-left: 4px solid;
        }
        
        .card-stat.new {
            border-left-color: var(--warning);
        }
        
        .card-stat.confirmed {
            border-left-color: var(--secondary);
        }
        
        .card-stat.cancelled {
            border-left-color: var(--danger);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
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
        
        .badge-new {
            background-color: var(--warning);
            color: var(--dark);
        }
        
        .badge-confirmed {
            background-color: var(--secondary);
            color: white;
        }
        
        .badge-cancelled {
            background-color: var(--danger);
            color: white;
        }
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
            background-color: #f5f5f5;
            color: var(--dark);
        }
        
        .sidebar {
            background: linear-gradient(135deg, #1A73E8 0%, #0d47a1 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
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
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar flex-shrink-0">
            <div class="sidebar-heading text-white p-3">
                <h4>El Sanchel Admin</h4>
            </div>
            <div class="list-group list-group-flush">
                <a href="home.php" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                
                <a href="payment.php" class="nav-link">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
                <a href="profit.php" class="nav-link">
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
                <div class="container-fluid">
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
                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="chart-container h-100">
                            <h5 class="mb-4">Monthly Bookings</h5>
                            <canvas id="monthlyBookingsChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="chart-container h-100">
                            <h5 class="mb-4">Room Type Distribution</h5>
                            <canvas id="roomTypeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- New Bookings Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Recent Bookings</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Room Type</th>
                                                <th>Check In</th>
                                                <th>Check Out</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $recent_sql = "SELECT * FROM roombook ORDER BY id DESC LIMIT 5";
                                            $recent_result = mysqli_query($con, $recent_sql);
                                            while($row = mysqli_fetch_assoc($recent_result)): 
                                                $status_class = '';
                                                if($row['stat'] == 'Confirm') $status_class = 'badge-confirmed';
                                                elseif($row['stat'] == 'Not Confirm') $status_class = 'badge-new';
                                                elseif($row['stat'] == 'Cancelled') $status_class = 'badge-cancelled';
                                            ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['FName'] . ' ' . $row['LName']; ?></td>
                                                <td><?php echo $row['TRoom']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($row['cin'])); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($row['cout'])); ?></td>
                                                <td><span class="badge <?php echo $status_class; ?>"><?php echo $row['stat']; ?></span></td>
                                                <td>
                                                    <a href="roombook.php?rid=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
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
    
    <script>
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
    </script>
</body>
</html>