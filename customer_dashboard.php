<?php
session_start();
include('db.php');

if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'];
$customer_email = $_SESSION['customer_email'];
// Get customer bookings
$sql = "SELECT r.*, p.room_rent, p.bed_rent, p.fintot, p.refund_status, p.refund_amount 
        FROM roombook r 
        LEFT JOIN payment p ON r.id = p.id 
        WHERE r.Email = ? 
        ORDER BY cin DESC";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $customer_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
// Handle cancellation request
if(isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $reason = $_POST['reason'] ?? 'No reason provided';
    
    // Update booking status
    $update_sql = "UPDATE roombook SET stat='Cancelled', cancelled_at=NOW(), cancellation_reason=? WHERE id=? AND Email=?";
    $stmt = mysqli_prepare($con, $update_sql);
    mysqli_stmt_bind_param($stmt, "sis", $reason, $booking_id, $customer_email);
    mysqli_stmt_execute($stmt);
    
    // Calculate refund (example: 80% refund if cancelled more than 48 hours before check-in)
    $booking_sql = "SELECT r.cin, p.fintot FROM roombook r JOIN payment p ON r.id=p.id WHERE r.id=?";
    $stmt = mysqli_prepare($con, $booking_sql);
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);
    
    $refund_amount = 0;
    $refund_status = 'pending';
    $checkin = new DateTime($booking['cin']);
    $now = new DateTime();
    $diff = $now->diff($checkin);
    
    if($diff->days > 2) { // More than 48 hours
        $refund_amount = $booking['fintot'] * 0.8;
        $refund_status = 'pending';
    } elseif($diff->days > 0) { // Less than 48 hours
        $refund_amount = $booking['fintot'] * 0.5;
        $refund_status = 'pending';
    } else { // Same day
        $refund_amount = 0;
        $refund_status = 'denied';
    }
    
    // Update payment record
    $update_payment = "UPDATE payment SET refund_amount=?, refund_status=? WHERE id=?";
    $stmt = mysqli_prepare($con, $update_payment);
    mysqli_stmt_bind_param($stmt, "dsi", $refund_amount, $refund_status, $booking_id);
    mysqli_stmt_execute($stmt);
    
    $_SESSION['success_message'] = "Booking #$booking_id has been cancelled. Refund status: " . 
                                  ($refund_amount > 0 ? "₱" . number_format($refund_amount, 2) . " pending" : "No refund");
    header("Location: customer_dashboard.php");
    exit();
}

// Get customer bookings
$bookings = [];
$sql = "SELECT r.*, p.room_rent, p.bed_rent, p.fintot, p.refund_status, p.refund_amount 
        FROM roombook r 
        LEFT JOIN payment p ON r.id = p.id 
        WHERE r.Email = ? 
        ORDER BY r.cin DESC";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $customer_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - El Sanchel Staycation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #1A73E8;
            --secondary: #34A853;
            --danger: #EA4335;
            --neutral: #F8F9FA;
            --text: #202124;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
        }
        
        .booking-card {
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .booking-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .booking-header {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 12px 16px;
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .btn-cancel {
            background-color: var(--danger);
            color: white;
            border: none;
        }
        
        .btn-cancel:hover {
            background-color: #c5221f;
            color: white;
        }
        
        .refund-info {
            background-color: var(--neutral);
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .cancellation-modal .modal-header {
            background-color: var(--danger);
            color: white;
        }
        
        @media (max-width: 768px) {
            .booking-actions {
                margin-top: 15px;
            }
            
            .booking-card {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">My Profile</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_email); ?></p>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">My Bookings</h5>
                            <a href="admin/reservation.php" class="btn btn-light btn-sm">
                                <i class="bi bi-plus-lg"></i> New Booking
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(empty($bookings)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #adb5bd;"></i>
                                <h5 class="mt-3">No bookings found</h5>
                                <p>You haven't made any bookings yet.</p>
                                <a href="admin/reservation.php" class="btn btn-primary">Book Now</a>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach($bookings as $booking): ?>
                                    <div class="list-group-item booking-card p-0 mb-3">
                                        <div class="booking-header d-flex justify-content-between align-items-center" 
                                             style="background-color: <?php echo $booking['stat'] == 'Cancelled' ? '#F8F9FA' : '#E8F0FE'; ?>">
                                            <div>
                                                <strong>Booking #<?php echo $booking['id']; ?></strong>
                                                <span class="status-badge ms-2" 
                                                      style="background-color: <?php 
                                                          echo $booking['stat'] == 'Confirm' ? '#E6F4EA' : 
                                                               ($booking['stat'] == 'Cancelled' ? '#FCE8E6' : '#FEF7E0'); 
                                                      ?>; 
                                                      color: <?php 
                                                          echo $booking['stat'] == 'Confirm' ? '#137333' : 
                                                               ($booking['stat'] == 'Cancelled' ? '#D93025' : '#E37400'); 
                                                      ?>;">
                                                    <?php echo $booking['stat']; ?>
                                                </span>
                                            </div>
                                            <div>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($booking['stat'] == 'Cancelled' ? $booking['cancelled_at'] : $booking['cin'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="p-3">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h5><?php echo $booking['TRoom']; ?></h5>
                                                    <div class="d-flex flex-wrap gap-3 mb-2">
                                                        <div>
                                                            <i class="bi bi-calendar-check"></i> 
                                                            <strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($booking['cin'])); ?>
                                                        </div>
                                                        <div>
                                                            <i class="bi bi-calendar-x"></i> 
                                                            <strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($booking['cout'])); ?>
                                                        </div>
                                                        <div>
                                                            <i class="bi bi-people"></i> 
                                                            <strong>Guests:</strong> <?php echo $booking['guest']; ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php if($booking['stat'] == 'Cancelled'): ?>
                                                        <div class="refund-info mt-2">
                                                            <i class="bi bi-info-circle"></i>
                                                            <?php if($booking['refund_status'] == 'processed'): ?>
                                                                Refund of ₱<?php echo number_format($booking['refund_amount'], 2); ?> processed
                                                            <?php elseif($booking['refund_status'] == 'pending'): ?>
                                                                Refund of ₱<?php echo number_format($booking['refund_amount'], 2); ?> pending
                                                            <?php else: ?>
                                                                No refund available for this cancellation
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <?php if(!empty($booking['cancellation_reason'])): ?>
                                                            <div class="mt-2">
                                                                <strong>Cancellation reason:</strong> 
                                                                <?php echo htmlspecialchars($booking['cancellation_reason']); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="col-md-4 booking-actions">
                                                    <div class="d-flex flex-column gap-2">
                                                        <?php if($booking['stat'] == 'Confirm' || $booking['stat'] == 'Conform'): ?>
                                                            <a href="view_invoice.php?booking_id=<?php echo $booking['id']; ?>" 
                                                               class="btn btn-outline-primary btn-sm">
                                                                <i class="bi bi-receipt"></i> View Invoice
                                                            </a>
                                                            
                                                            <?php 
                                                            $checkin = new DateTime($booking['cin']);
                                                            $now = new DateTime();
                                                            $can_cancel = $now < $checkin;
                                                            ?>
                                                            
                                                            <?php if($can_cancel): ?>
                                                                <button type="button" class="btn btn-cancel btn-sm" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#cancelModal"
                                                                        data-booking-id="<?php echo $booking['id']; ?>">
                                                                    <i class="bi bi-x-circle"></i> Cancel Booking
                                                                </button>
                                                            <?php else: ?>
                                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                                    <i class="bi bi-clock"></i> Stay completed
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Modal -->
    <div class="modal fade cancellation-modal" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Booking</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="customer_dashboard.php">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="modalBookingId" value="">
                        <p>Are you sure you want to cancel this booking?</p>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Cancellation Policy:</strong> 
                            <ul class="mb-0 mt-2">
                                <li>More than 48 hours before check-in: 80% refund</li>
                                <li>Less than 48 hours before check-in: 50% refund</li>
                                <li>Same day cancellation: No refund</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cancellationReason" class="form-label">Reason for cancellation (optional)</label>
                            <textarea class="form-control" id="cancellationReason" name="reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="cancel_booking" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Set booking ID in modal when cancel button is clicked
        document.addEventListener('DOMContentLoaded', function() {
            var cancelModal = document.getElementById('cancelModal');
            cancelModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var bookingId = button.getAttribute('data-booking-id');
                var modalBookingId = document.getElementById('modalBookingId');
                modalBookingId.value = bookingId;
            });
        });
    </script>
</body>
</html>