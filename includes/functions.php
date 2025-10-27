<?php
// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to a page
function redirect($url)
{
    header("Location: $url");
    exit();
}

// Check if admin is logged in
function checkAdminLogin()
{
    if (!isset($_SESSION['admin_id'])) {
        redirect("../admin/login.php");
    }
}

// Check if user is logged in
function checkUserLogin()
{
    if (!isset($_SESSION['user_id'])) {
        redirect("../user/login.php");
    }
}

// Display success message
function displaySuccessMessage()
{
    if (isset($_SESSION['success_message'])) {
        echo "<div class='message success'>{$_SESSION['success_message']}</div>";
        unset($_SESSION['success_message']);
    }
}

// Display error message
function displayErrorMessage()
{
    if (isset($_SESSION['error_message'])) {
        echo "<div class='message error'>{$_SESSION['error_message']}</div>";
        unset($_SESSION['error_message']);
    }
}

// Sanitize input data
function sanitizeInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if email is valid
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hash password
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}
