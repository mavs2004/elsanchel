<?php
session_start();
include('db.php');

$response = ['success' => false, 'message' => ''];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM customers WHERE email = '$email'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        
        if(password_verify($password, $customer['password'])) {
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];
            $_SESSION['customer_email'] = $customer['email'];
            
            $response['success'] = true;
        } else {
            $response['message'] = 'Invalid email or password!';
        }
    } else {
        $response['message'] = 'Invalid email or password!';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>