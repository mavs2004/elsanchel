<?php
function sendCancellationEmail($booking_id, $customer_email, $customer_name, $refund_amount, $reason = '') {
    $subject = "Booking #$booking_id Cancellation Confirmation";
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #1A73E8; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; background-color: #F8F9FA; }
            .footer { margin-top: 20px; font-size: 0.8em; text-align: center; color: #777; }
            .btn { display: inline-block; padding: 10px 15px; background-color: #1A73E8; color: white; text-decoration: none; border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Booking Cancellation</h2>
            </div>
            <div class="content">
                <p>Dear ' . htmlspecialchars($customer_name) . ',</p>
                <p>Your booking #' . $booking_id . ' has been successfully cancelled.</p>
                
                ' . ($refund_amount > 0 ? 
                    '<p>A refund of <strong>₱' . number_format($refund_amount, 2) . '</strong> will be processed within 5-7 business days.</p>' : 
                    '<p>No refund is available for this cancellation as per our policy.</p>') . '
                
                ' . (!empty($reason) ? '<p><strong>Reason for cancellation:</strong> ' . htmlspecialchars($reason) . '</p>' : '') . '
                
                <p>If you have any questions or need further assistance, please don\'t hesitate to contact our support team.</p>
                
                <p>Thank you for considering El Sanchel Staycation. We hope to serve you again in the future.</p>
                
                <p>Best regards,<br>The El Sanchel Team</p>
            </div>
            <div class="footer">
                <p>© ' . date('Y') . ' El Sanchel Staycation. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: El Sanchel Staycation <noreply@elsanchel.com>\r\n";
    
    mail($customer_email, $subject, $message, $headers);
}
?>