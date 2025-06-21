<?php
session_start();
include('db.php');

if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_email = $_SESSION['customer_email'];

// Get customer invoices
$invoices = [];
$sql = "SELECT p.*, r.room_rent, r.bed_rent, r.fintot 
        FROM payment p 
        JOIN roombook r ON p.id = r.id 
        WHERE r.Email = '$customer_email' 
        ORDER BY p.cout DESC";
$result = mysqli_query($con, $sql);
if($result) {
    $invoices = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Invoices - El Sanchel Staycation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation (same as el.php) -->
    
    <div class="container py-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">My Invoices</h5>
            </div>
            <div class="card-body">
                <?php if(empty($invoices)): ?>
                    <p>You don't have any invoices yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Booking ID</th>
                                    <th>Room Type</th>
                                    <th>Check-In</th>
                                    <th>Check-Out</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($invoices as $invoice): ?>
                                    <tr>
                                        <td><?php echo $invoice['id']; ?></td>
                                        <td><?php echo $invoice['id']; ?></td>
                                        <td><?php echo $invoice['troom']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($invoice['cin'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($invoice['cout'])); ?></td>
                                        <td>₱<?php echo number_format($invoice['fintot'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-success">Paid</span>
                                        </td>
                                        <td>
                                            <a href="view_invoice.php?booking_id=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                            <a href="print_invoice.php?pid=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-secondary">Download</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer (same as el.php) -->
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>