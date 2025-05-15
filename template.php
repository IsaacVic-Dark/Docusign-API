<?php

// callback.php to get the access token
include 'callback.php';

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$accountId = $_ENV['ACCOUNT_ID'];

// List templates

$url = "https://demo.docusign.net/restapi/v2.1/accounts/$accountId/templates";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Accept: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

file_put_contents('raw_response.json', $response);

$data = json_decode($response, true);

if (isset($data['envelopeTemplates'])) {
    $output = ""; // Initialize an empty string

    foreach ($data['envelopeTemplates'] as $template) {
        $output .= "📝 Template: " . $template['name'] . " (ID: " . $template['templateId'] . ")\n";
    }

    file_put_contents("templates.txt", $output);
} else {
    file_put_contents("templates.txt", "❌ No templates found or invalid response.");
}

// Create a template 
// $templateData = [
//     "description" => "Non-Disclosure Agreement template created via API",
//     "name" => "Non-Disclosure Agreement Temp",
//     "emailSubject" => "Please sign this document",
//     "documents" => [
//         [
//             "documentBase64" => base64_encode(file_get_contents("ndaTemp.pdf")), // Make sure the file exists
//             "documentId" => "2",
//             "fileExtension" => "pdf",
//             "name" => "NDA Temp"
//         ]
//     ],
//     "recipients" => [
//         "signers" => [
//             [
//                 "email" => "isaacvwarui@gmail.com",
//                 "name" => "Isaac Warui",
//                 "recipientId" => "1",
//                 "roleName" => "Signer",
//                 "routingOrder" => "1",
//                 "tabs" => [
//                     "signHereTabs" => [
//                         [
//                             "anchorString" => "/sig1/", // Add this placeholder in your PDF
//                             "anchorYOffset" => "0",
//                             "anchorUnits" => "pixels",
//                             "anchorXOffset" => "0"
//                         ]
//                     ]
//                 ]
//             ]
//         ]
//     ],
//     "status" => "created" // Do NOT set to "sent" for templates
// ];

// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     "Authorization: Bearer $accessToken",
//     "Accept: application/json",
//     "Content-Type: application/json"
// ]);
// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($templateData));

// $resTemp = curl_exec($ch);
// $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// curl_close($ch);

// echo "<h3>📦 Create Template Response ($httpCode)</h3><pre>";


// Delete a template
$templateId = '7736b074-3cb1-4e34-a423-9c078f25b26c';

$urlTempID = "https://demo.docusign.net/restapi/v2.1/accounts/$accountId/templates/$templateId";

curl_setopt($ch, CURLOPT_URL, $urlTempID);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Accept: application/json"
]);

$resTempID = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$curlErr = curl_error($ch);

curl_close($ch);

// ✅ Debug Output
if ($curlErr) {
    echo "❌ cURL error: $curlErr";
} elseif ($httpCode === 202) {
    echo "✅ Template deleted successfully.";
} else {
    echo "❌ Failed to delete template. HTTP Code: $httpCode<br>";

    $errorDetails = json_decode($resTempID, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($errorDetails['errorCode'])) {
        echo "🔎 Error Code: " . $errorDetails['errorCode'] . "<br>";
        echo "📄 Message: " . $errorDetails['message'] . "<br>";
    } else {
        echo "<pre>Raw response:\n";
        print_r($resTempID);
        echo "</pre>";
    }
}


// if ($httpCode === 202) {
//     echo "✅ Template deleted successfully (deactivated).";
// } else {
//     echo "❌ Failed to delete template. Response code: $httpCode";
//     echo "<pre>" . print_r($resTempID, true) . "</pre>";
// }