<?php
// netlify/functions/myFunction.php
function handler($event) {
    return [
        'statusCode' => 200,
        'body' => 'Hello from PHP function!'
    ];
}
