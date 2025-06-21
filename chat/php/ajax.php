<?php
session_start();
include('../../db.php');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if(isset($_SESSION['chat_user_id'])) {
    $user_id = $_SESSION['chat_user_id'];
    $is_admin = $_SESSION['chat_is_admin'];
    
    // Update last activity
    mysqli_query($con, "UPDATE chat_users SET last_activity = NOW() WHERE user_id = $user_id");
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch($action) {
            case 'send_message':
                $outgoing_id = $_POST['outgoing_id'];
                $incoming_id = $_POST['incoming_id'];
                $message = mysqli_real_escape_string($con, $_POST['message']);
                
                if(!empty($message)) {
                    $sql = "INSERT INTO chat_messages (incoming_msg_id, outgoing_msg_id, msg) 
                            VALUES ('$incoming_id', '$outgoing_id', '$message')";
                    
                    if(mysqli_query($con, $sql)) {
                        $response = ['status' => 'success'];
                    } else {
                        $response['message'] = 'Database error';
                    }
                } else {
                    $response['message'] = 'Message is empty';
                }
                break;
                
            case 'get_messages':
                $incoming_id = $_POST['incoming_id'];
                $output = "";
                
                $sql = "SELECT m.*, u.username, u.avatar 
                        FROM chat_messages m
                        JOIN chat_users u ON m.outgoing_msg_id = u.user_id
                        WHERE (m.outgoing_msg_id = $user_id AND m.incoming_msg_id = $incoming_id)
                        OR (m.outgoing_msg_id = $incoming_id AND m.incoming_msg_id = $user_id)
                        ORDER BY m.msg_id ASC";
                
                $query = mysqli_query($con, $sql);
                if(mysqli_num_rows($query) > 0) {
                    while($row = mysqli_fetch_assoc($query)) {
                        if($row['outgoing_msg_id'] == $user_id) {
                            $output .= '<div class="message outgoing">
                                        <div class="message-content">
                                            <div class="message-text">
                                                <p>'.$row['msg'].'</p>
                                                <div class="message-time">'.date('h:i A', strtotime($row['timestamp'])).'</div>
                                            </div>
                                        </div>
                                      </div>';
                        } else {
                            $avatar = $row['avatar'] ? 'assets/avatars/'.$row['avatar'] : 'assets/default-avatar.jpg';
                            $output .= '<div class="message incoming">
                                        <div class="message-content">
                                            <img src="'.$avatar.'" alt="'.$row['username'].'" class="message-avatar">
                                            <div class="message-text">
                                                <div class="message-sender">'.$row['username'].'</div>
                                                <p>'.$row['msg'].'</p>
                                                <div class="message-time">'.date('h:i A', strtotime($row['timestamp'])).'</div>
                                            </div>
                                        </div>
                                      </div>';
                        }
                    }
                    
                    // Mark messages as read
                    mysqli_query($con, "UPDATE chat_messages SET status = 'read' 
                                      WHERE incoming_msg_id = $user_id AND outgoing_msg_id = $incoming_id");
                    
                    $response = ['status' => 'success', 'html' => $output];
                } else {
                    $response = ['status' => 'empty'];
                }
                break;
                
            case 'get_users':
                if($is_admin) {
                    $search = isset($_POST['search']) ? mysqli_real_escape_string($con, $_POST['search']) : '';
                    $where = $search ? "WHERE username LIKE '%$search%' OR email LIKE '%$search%'" : "";
                    
                    $sql = "SELECT * FROM chat_users WHERE user_id != $user_id AND is_admin = FALSE $where ORDER BY status DESC, last_activity DESC";
                    $query = mysqli_query($con, $sql);
                    $output = "";
                    
                    if(mysqli_num_rows($query) > 0) {
                        while($row = mysqli_fetch_assoc($query)) {
                            $active = ($row['user_id'] == ($_POST['selected_user'] ?? 0)) ? 'active' : '';
                            $status = $row['status'] == 'Online' ? 'online' : 'offline';
                            $unread = '';
                            
                            // Check for unread messages
                            $unread_sql = "SELECT COUNT(*) as unread FROM chat_messages 
                                          WHERE incoming_msg_id = $user_id AND outgoing_msg_id = {$row['user_id']} AND status = 'unread'";
                            $unread_result = mysqli_query($con, $unread_sql);
                            $unread_data = mysqli_fetch_assoc($unread_result);
                            
                            if($unread_data['unread'] > 0) {
                                $unread = '<span class="unread-count">'.$unread_data['unread'].'</span>';
                            }
                            
                            $avatar = $row['avatar'] ? 'assets/avatars/'.$row['avatar'] : 'assets/default-avatar.jpg';
                            $output .= '<div class="user '.$active.'" data-userid="'.$row['user_id'].'">
                                        <div class="user-info">
                                            <img src="'.$avatar.'" alt="'.$row['username'].'">
                                            <div>
                                                <span>'.$row['username'].'</span>
                                                <p class="status '.$status.'">'.$row['status'].'</p>
                                            </div>
                                        </div>
                                        '.$unread.'
                                      </div>';
                        }
                    } else {
                        $output = '<div class="no-users">No users found</div>';
                    }
                    
                    $response = ['status' => 'success', 'html' => $output];
                }
                break;
                
            case 'upload_file':
                if(isset($_FILES['file'])) {
                    $file = $_FILES['file'];
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];
                    
                    if(in_array($file['type'], $allowed_types) && $file['size'] < 5000000) { // 5MB max
                        $file_name = time() . '_' . basename($file['name']);
                        $target_path = "../assets/uploads/" . $file_name;
                        
                        if(move_uploaded_file($file['tmp_name'], $target_path)) {
                            $response = [
                                'status' => 'success',
                                'file_name' => $file['name'],
                                'file_path' => $target_path
                            ];
                        } else {
                            $response['message'] = 'File upload failed';
                        }
                    } else {
                        $response['message'] = 'Invalid file type or size too large';
                    }
                }
                break;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>