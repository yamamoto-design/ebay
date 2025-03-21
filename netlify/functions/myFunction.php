<?php
// PHP Serverless Function for Netlify
function handler($event) {
    // Basic response
    return [
        'statusCode' => 200,
        'body' => 'Hello from PHP Function!',
    ];
}
