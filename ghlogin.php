<?php
session_start();

$client_id = '64db9b34101d94173ad9';
$client_secret = '9c3f64aca4e175bfafc49c95e7bc930a1ca4af50';
$redirect_uri = 'http://localhost:3000/ghlogin.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $token_url = "https://github.com/login/oauth/access_token?client_id=$client_id&client_secret=$client_secret&code=$code&redirect_uri=$redirect_uri";
    $response = file_get_contents($token_url);
    parse_str($response, $data);

    if (isset($data['access_token'])) {
        $access_token = $data['access_token'];
        $user_info_url = 'https://api.github.com/user';
        $options = [
            'http' => [
                'header' => "Authorization: token $access_token"
            ]
        ];
        $context = stream_context_create($options);
        $user_info = json_decode(file_get_contents($user_info_url, false, $context), true);

        $_SESSION['user_email'] = $user_info['email'];
        header('Location: uname.php');
        exit();
    } else {
        echo 'Error in obtaining access token.';
    }
} else {
    echo 'Error: Code parameter is missing.';
}
?>
