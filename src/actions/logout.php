<?php
require_once "../helpers/session_manager.php";
startSecureSession();
requireCSRFToken();
$_SESSION = array();
session_destroy();
header("Location: /account.php");
exit();
?>