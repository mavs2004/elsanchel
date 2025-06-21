<?php
session_start();
session_destroy();
header("Location: el.php");
exit();
?>