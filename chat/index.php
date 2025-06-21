<?php
session_start();
include('../db.php');

// Redirect to login if not authenticated
if(!isset($_SESSION['chat_user_id'])) {
    // Create temporary guest user if not logged in
    $guest_id = session_id();
    $guest_name = "Guest_" . substr(md5(time()), 0, 6);
    
    $check_guest = "SELECT * FROM chat_users WHERE username = '$guest_name'";
    $result = mysqli_query($con, $check_guest);
    
    if(mysqli_num_rows($result) == 0) {
        $insert_guest = "INSERT INTO chat_users (username, status, last_activity) 
                         VALUES ('$guest_name', 'Online', NOW())";
        mysqli_query($con, $insert_guest);
        $guest_user_id = mysqli_insert_id($con);
    } else {
        $guest = mysqli_fetch_assoc($result);
        $guest_user_id = $guest['user_id'];
    }
    
    $_SESSION['chat_user_id'] = $guest_user_id;
    $_SESSION['chat_username'] = $guest_name;
    $_SESSION['chat_is_admin'] = 0;
}

$user_id = $_SESSION['chat_user_id'];
$is_admin = $_SESSION['chat_is_admin'];

// Update user activity
$update_activity = "UPDATE chat_users SET status = 'Online', last_activity = NOW() WHERE user_id = $user_id";
mysqli_query($con, $update_activity);

// Get admin users for chat
$admin_sql = "SELECT * FROM chat_users WHERE is_admin = TRUE AND status = 'Online' LIMIT 1";
$admin_result = mysqli_query($con, $admin_sql);
$admin = mysqli_fetch_assoc($admin_result);

// Get chat history
$chat_sql = "SELECT m.*, u.username, u.avatar 
             FROM chat_messages m
             JOIN chat_users u ON m.outgoing_msg_id = u.user_id
             WHERE (m.incoming_msg_id = $user_id OR m.outgoing_msg_id = $user_id)
             ORDER BY m.timestamp ASC";
$chat_result = mysqli_query($con, $chat_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat - El Sanchel Staycation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="user-info">
                <?php if($is_admin): ?>
                    <h3><i class="fas fa-headset"></i> Support Dashboard</h3>
                <?php else: ?>
                    <h3><i class="fas fa-comments"></i> Live Support</h3>
                <?php endif; ?>
            </div>
            <div class="chat-actions">
                <button id="refresh-chat" title="Refresh"><i class="fas fa-sync-alt"></i></button>
                <a href="php/logout.php" class="logout-btn" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
        
        <div class="chat-wrapper">
            <!-- Users list (for admin) -->
            <?php if($is_admin): ?>
                <div class="users-list">
                    <div class="search-box">
                        <input type="text" placeholder="Search users..." id="search-user">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    <div class="users" id="users-container">
                        <!-- Users will be loaded via AJAX -->
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Chat area -->
            <div class="chat-area">
                <div class="chat-messages" id="chat-messages">
                    <?php while($row = mysqli_fetch_assoc($chat_result)): ?>
                        <?php 
                        $message_class = ($row['outgoing_msg_id'] == $user_id) ? 'outgoing' : 'incoming';
                        $avatar = $row['avatar'] ? 'assets/avatars/'.$row['avatar'] : 'assets/default-avatar.jpg';
                        ?>
                        <div class="message <?php echo $message_class; ?>">
                            <div class="message-content">
                                <?php if($message_class == 'incoming'): ?>
                                    <img src="<?php echo $avatar; ?>" alt="<?php echo $row['username']; ?>" class="message-avatar">
                                <?php endif; ?>
                                <div class="message-text">
                                    <?php if($message_class == 'incoming'): ?>
                                        <div class="message-sender"><?php echo $row['username']; ?></div>
                                    <?php endif; ?>
                                    <p><?php echo $row['msg']; ?></p>
                                    <div class="message-time"><?php echo date('h:i A', strtotime($row['timestamp'])); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="typing-area">
                    <form id="chat-form" class="chat-form">
                        <input type="text" name="outgoing_id" value="<?php echo $user_id; ?>" hidden>
                        <input type="text" name="incoming_id" value="<?php echo $admin ? $admin['user_id'] : 1; ?>" hidden>
                        
                        <div class="chat-tools">
                            <label for="file-input" class="file-label">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" id="file-input" name="file" style="display: none;">
                            </label>
                            <button type="button" id="emoji-btn"><i class="far fa-smile"></i></button>
                        </div>
                        
                        <input type="text" name="message" class="input-field" id="message-input" 
                               placeholder="Type your message here..." autocomplete="off">
                        
                        <button type="submit" class="send-btn"><i class="fab fa-telegram-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Emoji Picker -->
    <div class="emoji-picker" id="emoji-picker">
        <div class="emoji-categories">
            <button data-category="smileys"><i class="far fa-smile"></i></button>
            <button data-category="animals"><i class="fas fa-paw"></i></button>
            <button data-category="food"><i class="fas fa-utensils"></i></button>
            <button data-category="travel"><i class="fas fa-plane"></i></button>
        </div>
        <div class="emoji-container" id="emoji-container">
            <!-- Emojis will be loaded via JavaScript -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>