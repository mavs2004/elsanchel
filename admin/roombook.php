<?php  
session_start();  
if(!isset($_SESSION["user"]))
{
 header("location:index.php");
}
?> 

<?php
if(!isset($_GET["rid"]))
{
    header("location:index.php");
}
else {
    $curdate=date("Y/m/d");
    include ('db.php');
    $id = $_GET['rid'];
    
    $sql ="Select * from roombook where id = '$id'";
    $re = mysqli_query($con,$sql);
    while($row=mysqli_fetch_array($re))
    {
        $title = $row['Title'];
        $fname = $row['FName'];
        $lname = $row['LName'];
        $email = $row['Email'];
        $nat = $row['National'];
        $country = $row['Country'];
        $Phone = $row['Phone'];
        $troom = $row['TRoom'];
        $guest = $row['guest'];
        $bed = $row['Bed'];
        $guest = $row['guest'];
        $cin = $row['cin'];
        $cout = $row['cout'];
        $sta = $row['stat'];
        $days = $row['nodays'];
        $ttot = 0;
        $mepr = 0;
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Details - El Sanchel Staycation</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        
        .card-header {
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
        
        .table th {
            background-color: var(--primary);
            color: white;
        }
        
        .info-panel {
            border-left: 4px solid var(--primary);
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
                <a href="roombook.php" class="nav-link active">
                    <i class="fas fa-calendar-check"></i> Room Booking
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
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h1 class="h3 mb-0 text-gray-800">Booking Details <small><?php echo $curdate; ?></small></h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card info-panel">
                            <div class="card-header">
                                <h5 class="mb-0">Booking Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>Name</th>
                                            <td><?php echo $title.' '.$fname.' '.$lname; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?php echo $email; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nationality</th>
                                            <td><?php echo $nat; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Country</th>
                                            <td><?php echo $country; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Phone No</th>
                                            <td><?php echo $Phone; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Room Type</th>
                                            <td><?php echo $troom; ?></td>
                                        </tr>
                                        <tr>
                                            <th>No of Guests</th>
                                            <td><?php echo $guest; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Bedding</th>
                                            <td><?php echo $bed; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Check-in Date</th>
                                            <td><?php echo date('M d, Y', strtotime($cin)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Check-out Date</th>
                                            <td><?php echo date('M d, Y', strtotime($cout)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>No of Days</th>
                                            <td><?php echo $days; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <?php 
                                                $status_class = '';
                                                if($sta == 'Confirm') $status_class = 'badge-confirmed';
                                                elseif($sta == 'Not Confirm') $status_class = 'badge-new';
                                                elseif($sta == 'Cancelled') $status_class = 'badge-cancelled';
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo $sta; ?></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <form method="post">
                                    <div class="form-group">
                                        <label>Select the Confirmation</label>
                                        <select name="conf" class="form-control">
                                            <option value selected></option>
                                            <option value="Confirm">Confirm</option>
                                        </select>
                                    </div>
                                    <input type="submit" name="co" value="Confirm" class="btn btn-success">
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Available Room Details</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <?php
                                    $room_types = array("MAUI GALAXY", "MIAMI GAMING", "COZZY ROOM");
                                    
                                    foreach ($room_types as $type) {
                                        $total_sql = "SELECT COUNT(*) FROM room WHERE type = '$type'";
                                        $total_result = mysqli_query($con, $total_sql);
                                        $total_row = mysqli_fetch_array($total_result);
                                        $total_rooms = $total_row[0];
                                        
                                        $booked_sql = "SELECT COUNT(*) FROM payment WHERE troom = '$type'";
                                        $booked_result = mysqli_query($con, $booked_sql);
                                        $booked_row = mysqli_fetch_array($booked_result);
                                        $booked_rooms = $booked_row[0];
                                        
                                        $available = $total_rooms - $booked_rooms;
                                        
                                        echo "<tr>
                                            <td><strong>$type</strong></td>
                                            <td>Total: $total_rooms</td>
                                        </tr>";
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
if(isset($_POST['co']))
{    
    $st = $_POST['conf'];
    
    if($st=="Confirm")
    {
        // First, calculate the room rates
        $type_of_room = 0;
        $checkin_day = date('N', strtotime($cin)); // Get day of week (1=Monday to 7=Sunday)
        
        // Base room prices
        if($troom == "MAUI GALAXY" || $troom == "MIAMI GAMING" || $troom == "COZZY ROOM") {
            if($checkin_day >= 1 && $checkin_day <= 4) { // Monday to Thursday
                $type_of_room = 2499;
            } elseif($checkin_day == 5 || $checkin_day == 7) { // Friday or Sunday
                $type_of_room = 2999;
            } else { // Saturday or holiday
                $type_of_room = 3199;
            }
        }
        
        // Calculate bed pricing
        if($bed == "Single") {
            $type_of_bed = $type_of_room * 0.01;
        } else if($bed == "Double") {
            $type_of_bed = $type_of_room * 0.02;
        } else if($bed == "Triple") {
            $type_of_bed = $type_of_room * 0.03;
        } else if($bed == "Quad") {
            $type_of_bed = $type_of_room * 0.04;
        } else {
            $type_of_bed = 0;
        }
        
        // Calculate totals
        $ttot = $type_of_room * $days;
        $btot = $type_of_bed * $days;
        $fintot = $ttot + $btot;
        
        // Update roombook table with calculated values
        $urb = "UPDATE `roombook` SET `stat`='$st', `room_rent`='$type_of_room', `bed_rent`='$type_of_bed', `fintot`='$fintot' WHERE id = '$id'";
        
        if(mysqli_query($con,$urb))
        {    
            // Insert into payment table with all calculated values
            $psql = "INSERT INTO `payment`(`id`, `title`, `fname`, `lname`, `troom`, `tbed`, `guest`, `cin`, `cout`, `ttot`, `btot`, `fintot`, `noofdays`, `room_rent`, `bed_rent`) 
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = mysqli_prepare($con, $psql);
            mysqli_stmt_bind_param($stmt, "isssssissdddddd", $id, $title, $fname, $lname, $troom, $bed, $guest, $cin, $cout, $ttot, $btot, $fintot, $days, $type_of_room, $type_of_bed);
            mysqli_stmt_execute($stmt);
    
            // Mark room as occupied
            $notfree = "NotFree";
            $rpsql = "UPDATE `room` SET `place`='$notfree', `cusid`='$id' WHERE bedding ='$bed' AND type='$troom' AND place='Free' LIMIT 1";
            mysqli_query($con,$rpsql);
    
            echo "<script>alert('Booking Confirmed'); window.location='roombook.php';</script>";
        }
    }
}
?>