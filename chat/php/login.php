<?php
session_start();
include('../../db.php');

if(isset($_SESSION['chat_user_id'])) {
    header("location: ../index.php");
    exit();
}

$error = '';
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    
    $sql = "SELECT * FROM chat_users WHERE email = '$email'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row['password'])) {
            $_SESSION['chat_user_id'] = $row['user_id'];
            $_SESSION['chat_username'] = $row['username'];
            $_SESSION['chat_is_admin'] = $row['is_admin'];
            
            // Update status
            $update = "UPDATE chat_users SET status = 'Online', last_activity = NOW() WHERE user_id = {$row['user_id']}";
            mysqli_query($con, $update);
            
            header("location: ../index.php");
            exit();
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "Email not found";
    }
}

// Auto-login for registered customers
if(isset($_SESSION['customer_id']) && !isset($_SESSION['chat_user_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $sql = "SELECT * FROM customers WHERE id = '$customer_id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        $email = $customer['email'];
        
        // Check if chat user exists
        $chat_sql = "SELECT * FROM chat_users WHERE email = '$email'";
        $chat_result = mysqli_query($con, $chat_sql);
        
        if(mysqli_num_rows($chat_result) > 0) {
            $row = mysqli_fetch_assoc($chat_result);
            $_SESSION['chat_user_id'] = $row['user_id'];
            $_SESSION['chat_username'] = $row['username'];
            $_SESSION['chat_is_admin'] = $row['is_admin'];
            
            $update = "UPDATE chat_users SET status = 'Online', last_activity = NOW() WHERE user_id = {$row['user_id']}";
            mysqli_query($con, $update);
            
            header("location: ../index.php");
            exit();
        } else {
            // Auto-create chat user for customer
            $username = $customer['name'];
            $password = password_hash('welcome123', PASSWORD_DEFAULT);
            $insert = "INSERT INTO chat_users (username, email, password, status, last_activity) 
                      VALUES ('$username', '$email', '$password', 'Online', NOW())";
            
            if(mysqli_query($con, $insert)) {
                $user_id = mysqli_insert_id($con);
                $_SESSION['chat_user_id'] = $user_id;
                $_SESSION['chat_username'] = $username;
                $_SESSION['chat_is_admin'] = 0;
                
                header("location: ../index.php");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Login - El Sanchel Staycation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="chat-auth">
    <div class="chat-login-container">
        <div class="chat-login-box">
            <div class="text-center mb-4">
                <img src="../../images/logo.png" alt="El Sanchel Logo" class="chat-logo">
                <h3>Chat Login</h3>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            
            <div class="mt-3 text-center">
                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
                <p>or <a href="../index.php">Continue as guest</a></p>
            </div>
        </div>
    </div>
</body>
</html>