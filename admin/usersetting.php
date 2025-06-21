<?php  
session_start();  
if(!isset($_SESSION["user"]))
{
 header("location:index.php");
}

ob_start();
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Management - El Sanchel Staycation</title>
    
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
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .modal-content {
            border-radius: 10px;
        }
        
        .modal-header {
            background-color: var(--primary);
            color: white;
            border-radius: 10px 10px 0 0;
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
                    <i class="fas fa-person"></i>Admin Dashboard
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
                        <h1 class="h3 mb-0 text-gray-800">Admin Accounts Management</h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">Administrator Accounts</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                include ('db.php');
                                $sql = "SELECT * FROM `login`";
                                $re = mysqli_query($con,$sql);
                                ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>User ID</th>
                                                <th>Username</th>
                                                <th>Password</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while($row = mysqli_fetch_array($re)) {
                                                $id = $row['id'];
                                                $us = $row['usname'];
                                                $ps = $row['pass'];
                                                
                                                echo "<tr>
                                                    <td>".$id."</td>
                                                    <td>".$us."</td>
                                                    <td>".$ps."</td>
                                                    <td>
                                                        <button class='btn btn-sm btn-primary me-2' data-bs-toggle='modal' data-bs-target='#editModal".$id."'>
                                                            <i class='fas fa-edit'></i> Edit
                                                        </button>
                                                        <a href='usersettingdel.php?eid=".$id."' class='btn btn-sm btn-danger'>
                                                            <i class='fas fa-trash'></i> Delete
                                                        </a>
                                                    </td>
                                                </tr>";
                                                
                                                // Edit Modal for each row
                                                echo '
                                                <div class="modal fade" id="editModal'.$id.'" tabindex="-1" aria-labelledby="editModalLabel'.$id.'" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title" id="editModalLabel'.$id.'">Edit Admin Account</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="post">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id" value="'.$id.'">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Username</label>
                                                                    <input type="text" class="form-control" name="usname" value="'.$us.'">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Password</label>
                                                                    <input type="text" class="form-control" name="pasd" value="'.$ps.'">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" name="up" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus-circle"></i> Add New Admin
                                </button>
                                
                                <!-- Add New Admin Modal -->
                                <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="addModalLabel">Add New Admin</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="post">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Username</label>
                                                    <input type="text" class="form-control" name="newus" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Password</label>
                                                    <input type="text" class="form-control" name="newps" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="in" class="btn btn-primary">Add Admin</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                if(isset($_POST['in'])) {
                                    $newus = $_POST['newus'];
                                    $newps = $_POST['newps'];
                                    
                                    $newsql = "INSERT INTO login (usname, pass) VALUES ('$newus', '$newps')";
                                    if(mysqli_query($con,$newsql)) {
                                        echo '<div class="alert alert-success mt-3">Admin account added successfully</div>';
                                        echo '<script>setTimeout(function(){ window.location="usersetting.php"; }, 1000);</script>';
                                    } else {
                                        echo '<div class="alert alert-danger mt-3">Error adding admin: '.mysqli_error($con).'</div>';
                                    }
                                }
                                
                                if(isset($_POST['up'])) {
                                    $id = $_POST['id'];
                                    $usname = $_POST['usname'];
                                    $passwr = $_POST['pasd'];
                                    
                                    $upsql = "UPDATE `login` SET `usname`='$usname', `pass`='$passwr' WHERE id = '$id'";
                                    if(mysqli_query($con,$upsql)) {
                                        echo '<div class="alert alert-success mt-3">Admin account updated successfully</div>';
                                        echo '<script>setTimeout(function(){ window.location="usersetting.php"; }, 1000);</script>';
                                    } else {
                                        echo '<div class="alert alert-danger mt-3">Error updating admin: '.mysqli_error($con).'</div>';
                                    }
                                }
                                ob_end_flush();
                                ?>
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