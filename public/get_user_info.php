<?php
session_start();
include_once '../configs/api.php';

header('Content-Type: application/json');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!isset($data['userID']) || !isset($data['companyName'])) {
    echo json_encode([
        'status' => 400,
        'message' => 'Thiếu userID hoặc companyName.'
    ]);
    exit;
}

$response = callAPI('get_user_info', [
    'userID' => $data['userID'],
    'companyName' => $data['companyName']
]);

echo $response;
