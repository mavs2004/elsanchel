<?php
session_start();
include('db.php');

if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
    $customer_id = $_SESSION['customer_id'];
    $rating = mysqli_real_escape_string($con, $_POST['rating']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
    
    // Get room type from booking
    $room_sql = "SELECT TRoom FROM roombook WHERE id = '$booking_id'";
    $room_result = mysqli_query($con, $room_sql);
    $room_row = mysqli_fetch_assoc($room_result);
    $room_type = $room_row['TRoom'];
    
    // Insert review
    $sql = "INSERT INTO reviews (booking_id, customer_id, room_type, rating, comment) 
            VALUES ('$booking_id', '$customer_id', '$room_type', '$rating', '$comment')";
    
    if(mysqli_query($con, $sql)) {
        $_SESSION['review_success'] = "Thank you for your review!";
    } else {
        $_SESSION['review_error'] = "Error submitting review. Please try again.";
    }
    
    header("Location: customer_dashboard.php");
    exit();
}
?>