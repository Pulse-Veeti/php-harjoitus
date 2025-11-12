<?php
require_once "session_manager.php";
function csrfField(){
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
function csrfToken(){
    return generateCSRFToken();
}
?>