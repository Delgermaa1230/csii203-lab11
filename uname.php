<?php
session_start();

$client_id = '64db9b34101d94173ad9';
$client_secret = 'YOUR_CLIENT_SECRET'; // Replace with your actual client secret

if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];

    $allowed_emails = array('alloweduser@example.com', 'anotherallowed@example.com');

    if (in_array($user_email, $allowed_emails)) {
        echo "Welcome, $user_email! Your special information is here.";
    } else {
        echo 'Sorry, you are not allowed to access this information.';
    }
} else {
    echo 'Error: User not authenticated.';
}
?>
