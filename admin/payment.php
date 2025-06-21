<?php  
session_start();  
if(!isset($_SESSION["user"])) {
    header("location:index.php");
}

include('db.php');

// Fetch payment data for chart
$monthly_payments = [];
$payment_sql = "SELECT 
    DATE_FORMAT(cin, '%Y-%m') as month, 
    SUM(fintot) as total_payment 
    FROM payment 
    GROUP BY DATE_FORMAT(cin, '%Y-%m') 
    ORDER BY month";
$payment_result = mysqli_query($con, $payment_sql);
while($row = mysqli_fetch_assoc($payment_result)) {
    $monthly_payments[] = $row;
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Details - El Sanchel Staycation</title>
    
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
                <a href="home.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
             
                <a href="payment.php" class="nav-link active">
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
                    <div class="col-md-12">
                        <div class="chart-container h-100">
                            <h5 class="mb-4">Monthly Payments</h5>
                            <canvas id="monthlyPaymentsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Payment Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Payment Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Room Type</th>
                                                <th>Bed Type</th>
                                                <th>Check In</th>
                                                <th>Check Out</th>
                                                <th>No. of Guests</th>
                                                <th>Room Rent</th>
                                                <th>Bed Rent</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM payment";
                                            $re = mysqli_query($con, $sql);
                                            while($row = mysqli_fetch_assoc($re)) {
                                                echo "<tr>
                                                    <td>{$row['title']} {$row['fname']} {$row['lname']}</td>
                                                    <td>{$row['troom']}</td>
                                                    <td>{$row['tbed']}</td>
                                                    <td>{$row['cin']}</td>
                                                    <td>{$row['cout']}</td>
                                                    <td>{$row['guest']}</td>
                                                    <td>{$row['ttot']}</td>
                                                    <td>{$row['btot']}</td>
                                                    <td>{$row['fintot']}</td>
                                                    <td>
                                                        <a href='print.php?pid={$row['id']}' class='btn btn-primary btn-sm'>
                                                            <i class='fas fa-print'></i> Print
                                                        </a>
                                                    </td>
                                                </tr>";
                                            }
                                            ?>
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
        // Monthly payments chart
        const monthlyCtx = document.getElementById('monthlyPaymentsChart').getContext('2d');
        const monthlyPaymentsChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    foreach($monthly_payments as $payment) {
                        echo "'" . date('M Y', strtotime($payment['month'] . '-01')) . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Total Payments',
                    data: [
                        <?php 
                        foreach($monthly_payments as $payment) {
                            echo $payment['total_payment'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: 'rgba(26, 115, 232, 0.2)',
                    borderColor: '#1A73E8',
                    borderWidth: 2,
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
                                return context.dataset.label + ': $' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>