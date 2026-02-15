<?php
session_start();
session_destroy();
header("Location: /HALLEASE/user/login.php");
exit();
?>
