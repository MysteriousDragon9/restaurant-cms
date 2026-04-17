<?php
/**
 * Google reCAPTCHA v2 Verification
 * Used to verify reCAPTCHA responses on form submissions
 */

function verifyRecaptcha($response) {
    // IMPORTANT: Replace this with your actual Secret Key from Google reCAPTCHA
    $secretKey = '6LdijLssAAAAAKq7gbh51fj0EkLl6B8fF8dUM2QM';

    // Get user's IP address
    $remoteIp = $_SERVER['REMOTE_ADDR'];

    // Build the request URL
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secretKey,
        'response' => $response,
        'remoteip' => $remoteIp
    ];

    // Create context for the POST request
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    // Send the request to Google
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resultJson = json_decode($result);

    // Return true if verification succeeded
    return isset($resultJson->success) && $resultJson->success === true;
}
?>