<?php
session_start();
session_destroy();
header("Location: /complaint_management/login.php");
exit();
