<?php

function callAPI($endpointKey, $data = [])
{
    $endpoints = include __DIR__ . '/api_endpoints.php';

    if (!isset($endpoints[$endpointKey])) {
        throw new Exception("API endpoint '$endpointKey' not defined.");
    }

    $url = $endpoints[$endpointKey];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
