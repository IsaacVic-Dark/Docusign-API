<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$redirectUri = $_ENV['REDIRECT_URI'];
$scope = 'signature';

$url = "https://account-d.docusign.com/oauth/auth?" . http_build_query([
    'response_type' => 'code',
    'scope' => $scope,
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
]);

header("Location: $url");
exit;
