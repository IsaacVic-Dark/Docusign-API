<?php
require 'vendor/autoload.php';

use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Api\AuthApi;

// https://account-d.docusign.com/oauth/auth?response_type=code&scope=signature%20impersonation&client_id=6d1cbafa-13c3-402d-a826-02aa3c9ab0ea&redirect_uri=https://www.example.com

$privateKey = file_get_contents('key/private.pem');
$integrationKey = "6d1cbafa-13c3-402d-a826-02aa3c9ab0ea";             
$userId = "c40c2ca8-ac27-4803-9b9a-5cd063372a54";                
$authServer = "account-d.docusign.com"; // Use "account.docusign.com" for production

$config = new Configuration();
$config->setHost("https://{$authServer}");
$apiClient = new ApiClient($config);

if (!openssl_pkey_get_private($privateKey)) {
    die("❌ Invalid private key format.");
}

$response = $apiClient->requestJWTUserToken(
    $integrationKey,
    $userId,
    $privateKey,
    ['signature', 'impersonation'],    
    3600    
);

$accessToken = $response[0]['access_token'];
echo "Access Token: " . $accessToken;

