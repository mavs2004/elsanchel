<?php  
session_start();  
if(!isset($_SESSION["user"]))
{
 header("location:index.php");
}
ob_start();
?> 

<?php
include('db.php');
$rsql ="select id from room";
$rre=mysqli_query($con,$rsql);
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delete Room - El Sanchel Staycation</title>
    
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
        
        .room-card {
            border-left: 4px solid;
            transition: all 0.3s;
        }
        
        .room-card:hover {
            transform: translateY(-3px);
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
                <a href="room.php" class="nav-link">
                    <i class="fas fa-plus-circle"></i> Add Room
                </a>
                <a href="roomdel.php" class="nav-link active">
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
                        <h1 class="h3 mb-0 text-gray-800">Delete Room</h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Delete Room</h5>
                            </div>
                            <div class="card-body">
                                <form name="form" method="post">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Select the Room ID</label>
                                        <select name="id" class="form-control" required>
                                            <option value selected></option>
                                            <?php
                                            while($rrow=mysqli_fetch_array($rre))
                                            {
                                                $value = $rrow['id'];
                                                echo '<option value="'.$value.'">'.$value.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <input type="submit" name="del" value="Delete Room" class="btn btn-danger">
                                </form>
                                <?php
                                if(isset($_POST['del']))
                                {
                                    $did = $_POST['id'];
                                    
                                    $sql ="DELETE FROM `room` WHERE id = '$did'" ;
                                    if(mysqli_query($con,$sql))
                                    {
                                        echo '<div class="alert alert-success mt-3">Room deleted successfully</div>';
                                        header("Refresh:2; url=roomdel.php");
                                    } else {
                                        echo '<div class="alert alert-danger mt-3">Sorry! There was an error</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <?php
                    include ('db.php');
                    $sql = "select * from room";
                    $re = mysqli_query($con,$sql);
                    
                    while($row= mysqli_fetch_array($re))
                    {
                        $id = $row['type'];
                        if($id == "MAUI GALAXY") {
                            $bg = "bg-color-blue";
                            $footer = "back-footer-blue";
                        } else if ($id == "MIAMI GAMING") {
                            $bg = "bg-color-green";
                            $footer = "back-footer-green";
                        } else if($id =="COZZY ROOM") {
                            $bg = "bg-color-brown";
                            $footer = "back-footer-brown";
                        } else {
                            $bg = "bg-color-red";
                            $footer = "back-footer-red";
                        }
                        
                        echo "<div class='col-md-3 mb-4'>
                            <div class='card room-card $bg h-100'>
                                <div class='card-body text-center'>
                                    <i class='fas fa-bed fa-3x mb-3'></i>
                                    <h4>".$row['bedding']."</h4>
                                </div>
                                <div class='card-footer $footer text-center'>
                                    ".$row['type']."
                                </div>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>