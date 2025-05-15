<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$redirectUri = $_ENV['REDIRECT_URI'];

if (!isset($_GET['code'])) {
    die('Authorization code not found');
}

$code = $_GET['code'];

$tokenUrl = 'https://account-d.docusign.com/oauth/token';

$data = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirectUri,
];

// Use cURL to request token
$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response, true);

if (isset($token['access_token'])) {
    $accessToken = $token['access_token'];
    $refreshToken = $token['refresh_token'];

    $content = "ACCESS_TOKEN:\n$accessToken\n\nREFRESH_TOKEN:\n$refreshToken\n";

    echo "✅ Access Token: " . $accessToken . "<br>";
    echo "🔁 Refresh Token: " . $refreshToken . "<br>";
    
    file_put_contents("token.txt", $content);


} else {
    echo "❌ Failed to get access token:<br>";
    echo "<pre>" . print_r($token, true) . "</pre>";
}

require_once "template.php";
