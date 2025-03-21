<?php

// Use Sandbox Client ID and Secret
$clientId = 'SLGStore-importx2-SBX-45d865283-1f8c4a64';  // Sandbox Client ID
$clientSecret = 'SBX-8c60150861b0-2dd5-4e5d-ad11-28ea';  // Client Secret (same for sandbox)
$redirectUri = 'SLG_Store_di_St-SLGStore-import-vllrci';   // Make sure this matches your registered URI
// $redirectUri = 'https://chatgpt.com';  // Update with your correct HTTPS URL


// Check if the authorization code is received
if (isset($_GET['code'])) {
    $authorizationCode = $_GET['code'];

    // Debugging: Display the received authorization code
    echo "<p>Received Authorization Code: " . $authorizationCode . "</p>";

    // Prepare POST data to exchange the authorization code for an access token
    $postFields = [
        'grant_type' => 'authorization_code',  
        'code' => $authorizationCode,          
        'redirect_uri' => $redirectUri         
    ];

    $postFieldsString = http_build_query($postFields);

    // Initialize cURL for the token request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.ebay.com/identity/v1/oauth2/token');  // Use sandbox token URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFieldsString);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    // Get the response from the token request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    } else {
        // Decode the JSON response to extract the access token
        $tokenData = json_decode($response, true);

        if (isset($tokenData['access_token'])) {
            $accessToken = $tokenData['access_token'];
            echo "<p>Access Token: " . $accessToken . "</p>";

            // Fetch product data with the access token
            fetchEbayProductData($accessToken);
        } else {
            echo "<p>Error: " . $response . "</p>";
        }
    }

    curl_close($ch);
} else {
    // Define the necessary API scopes for sandbox
    $scope = 'https://api.ebay.com/oauth/api_scope/commerce.catalog.readonly';  // Define the necessary API scopes
    $responseType = 'code';  
    $state = bin2hex(random_bytes(16));  // Generate a random state

    // Generate the sandbox authorization URL
    $authUrl = 'https://auth.sandbox.ebay.com/oauth2/authorize';  // Use sandbox authorization URL
    $authUrl .= "?client_id=$clientId&response_type=$responseType&redirect_uri=$redirectUri&scope=$scope&state=$state";

    // Debugging: Display the generated authorization URL
    echo($authUrl);

    // Redirect user to eBay Sandbox authorization URL
    header('Location: ' . $authUrl);
    exit;
}

// Function to fetch product data using the access token
function fetchEbayProductData($accessToken) {
    // Use sandbox product search URL
    $url = 'https://api.sandbox.ebay.com/commerce/catalog/v1_beta/product_summary/search?gtin=0813917020203';  // Use sandbox endpoint

    // Initialize cURL to fetch product data
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,  // Pass the access token as Bearer
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    } else {
        // Decode and display product data
        $productData = json_decode($response, true);
        echo "<pre>";
        print_r($productData);
        echo "</pre>";
    }

    curl_close($ch);
}
?>
