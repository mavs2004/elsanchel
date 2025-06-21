<?php
$con = mysqli_connect("localhost","root","","hotel") or die(mysql_error());
$chatTables = [
    "CREATE TABLE IF NOT EXISTS chat_users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        password VARCHAR(255),
        avatar VARCHAR(255),
        status ENUM('Online','Offline') DEFAULT 'Offline',
        last_activity DATETIME,
        is_admin BOOLEAN DEFAULT FALSE,
        UNIQUE KEY (email)
    )",
    
    "CREATE TABLE IF NOT EXISTS chat_messages (
        msg_id INT AUTO_INCREMENT PRIMARY KEY,
        incoming_msg_id INT NOT NULL,
        outgoing_msg_id INT NOT NULL,
        msg TEXT NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('unread','read') DEFAULT 'unread',
        FOREIGN KEY (incoming_msg_id) REFERENCES chat_users(user_id),
        FOREIGN KEY (outgoing_msg_id) REFERENCES chat_users(user_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS chat_attachments (
        attachment_id INT AUTO_INCREMENT PRIMARY KEY,
        message_id INT NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_type VARCHAR(100) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        file_size INT NOT NULL,
        FOREIGN KEY (message_id) REFERENCES chat_messages(msg_id) ON DELETE CASCADE
    )"
];

foreach ($chatTables as $query) {
    mysqli_query($con, $query);
}

// Create admin chat user if not exists
$checkAdmin = "SELECT * FROM chat_users WHERE email = 'admin@elsanchel.com'";
$result = mysqli_query($con, $checkAdmin);
if(mysqli_num_rows($result) == 0) {
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $insertAdmin = "INSERT INTO chat_users (username, email, password, is_admin, status) 
                   VALUES ('Admin', 'admin@elsanchel.com', '$password', TRUE, 'Online')";
    mysqli_query($con, $insertAdmin);
}
?>