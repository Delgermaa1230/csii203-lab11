<?php
$client_id = '64db9b34101d94173ad9';
$redirect_uri = 'http://localhost:3000/ghlogin.php';

echo "<a href='https://github.com/login/oauth/authorize?client_id=$client_id&redirect_uri=$redirect_uri'>Login with GitHub</a>";
?>

