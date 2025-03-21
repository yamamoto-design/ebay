<?php

$client_id = 'SLGStore-importx2-PRD-c5d80d3bd-1c9bcf16';  
$client_secret = 'PRD-5d80d3bd1383-f11b-40a0-ae13-0ed2';  
$auth = base64_encode("$client_id:$client_secret");

function getOAuthToken($client_id, $client_secret) {
    $auth = base64_encode("$client_id:$client_secret");

    $url = 'https://api.sandbox.ebay.com/identity/v1/oauth2/token';

    $data = array(
        'grant_type' => 'client_credentials',
        'scope' => 'https://api.ebay.com/oauth/api_scope'  
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Basic ' . $auth,
        'Content-Type: application/x-www-form-urlencoded'
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        die("Error fetching OAuth token.");
    }

    $response_data = json_decode($response, true);
    echo($response_data["expires_in"]);
    if (isset($response_data['access_token'])) {
        return $response_data['access_token'];  
    } else {
        die('Error fetching access token: ' . json_encode($response_data));
    }
}
function getProductData($access_token) {
    $api_url = 'http://api.ebay.com/commerce/catalog/v1_beta/product_summary/search?gtin=0813917020203';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ));

    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }

    curl_close($ch);

    if (!$response) {
        die("Error fetching product data.");
    }

    return json_decode($response, true);
}
$access_token = getOAuthToken($client_id, $client_secret);  
echo "Access Token: " . $access_token . "\n";

$product_data = getProductData($access_token);

echo "<pre>";
print_r($product_data);
echo "</pre>";

?>
-------------------AUTH-----------------------
"
"
SLG_Store_di_St-SLGStore-import-djktgmst