<?php  
session_start();  
if(!isset($_SESSION["user"]))
{
 header("location:index.php");
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Room - El Sanchel Staycation</title>
    
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
        
        .room-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-left: 4px solid;
        }
        
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .bg-color-blue {
            background-color: #E3F2FD;
            border-left-color: #1A73E8;
        }
        
        .bg-color-green {
            background-color: #E8F5E9;
            border-left-color: #34A853;
        }
        
        .bg-color-brown {
            background-color: #EFEBE9;
            border-left-color: #6D4C41;
        }
        
        .bg-color-red {
            background-color: #FFEBEE;
            border-left-color: #EA4335;
        }
        
        .back-footer-blue {
            background-color: #1A73E8;
            color: white;
        }
        
        .back-footer-green {
            background-color: #34A853;
            color: white;
        }
        
        .back-footer-brown {
            background-color: #6D4C41;
            color: white;
        }
        
        .back-footer-red {
            background-color: #EA4335;
            color: white;
        }
        
        .status-free {
            color: #34A853;
            font-weight: bold;
        }
        
        .status-occupied {
            color: #EA4335;
            font-weight: bold;
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
                <a href="settings.php" class="nav-link">
                    <i class="fas fa-bed"></i> Room Status
                </a>
                <a href="room.php" class="nav-link active">
                    <i class="fas fa-plus-circle"></i> Add Room
                </a>
                <a href="roomdel.php" class="nav-link">
                    <i class="fas fa-trash-alt"></i> Delete Room
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
                        <h1 class="h3 mb-0 text-gray-800">Add New Room</h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">Room Details</h6>
                            </div>
                            <div class="card-body">
                                <form name="form" method="post">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Type Of Room *</label>
                                        <select name="troom" class="form-control" required>
                                            <option value selected></option>
                                            <option value="MAUI GALAXY">GAMING ROOM V1 (MAUI GALAXY)(MIAMI TOWER 15TH FLOOR)</option>
                                            <option value="MIAMI GAMING">GAMING ROOM V2 (MIAMI GAMING)(SANTORINI TOWER 9TH FLOOOR)</option>
                                            <option value="COZZY ROOM">COZZY ROOM V1 (MIAMI TOWER 19TH FLOOR)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label class="form-label">Bedding Type *</label>
                                        <select name="bed" class="form-control" required>
                                            <option value selected></option>
                                            <option value="Single">Single</option>
                                            <option value="Double">Double</option>
                                            <option value="Triple">Triple</option>
                                            <option value="Quad">Quad</option>
                                            <option value="None">None</option>
                                        </select>
                                    </div>
                                    
                                    <input type="submit" name="add" value="Add New Room" class="btn btn-primary">
                                </form>
                                
                                <?php
                                include('db.php');
                                if(isset($_POST['add']))
                                {
                                    $room = $_POST['troom'];
                                    $bed = $_POST['bed'];
                                    $place = 'Free';
                                    
                                    // Check if room already exists
                                    $check = "SELECT * FROM room WHERE type = '$room' AND bedding = '$bed'";
                                    $rs = mysqli_query($con,$check);
                                    
                                    if(mysqli_num_rows($rs) > 0) {
                                        echo '<div class="alert alert-danger mt-3">Room with this type and bedding already exists</div>';
                                    }
                                    else {
                                        $sql = "INSERT INTO `room`(`type`, `bedding`, `place`) VALUES ('$room','$bed','$place')";
                                        if(mysqli_query($con,$sql)) {
                                            echo '<div class="alert alert-success mt-3">New Room Added Successfully</div>';
                                            echo '<script>window.location="room.php"</script>';
                                        } else {
                                            echo '<div class="alert alert-danger mt-3">Error adding room: '.mysqli_error($con).'</div>';
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">Recent Rooms</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Room ID</th>
                                                <th>Room Type</th>
                                                <th>Bedding</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM room ORDER BY id DESC LIMIT 10";
                                            $re = mysqli_query($con,$sql);
                                            while($row = mysqli_fetch_array($re)) {
                                                $status_class = ($row['place'] == 'Free') ? 'status-free' : 'status-occupied';
                                                echo "<tr>
                                                    <td>".$row['id']."</td>
                                                    <td>".$row['type']."</td>
                                                    <td>".$row['bedding']."</td>
                                                    <td class='$status_class'>".$row['place']."</td>
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
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>