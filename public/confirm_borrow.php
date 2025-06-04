<?php
session_start();
include_once '../configs/api.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['ID_bill']) || !isset($input['companyName'])) {
    echo json_encode(['status' => 'Error', 'message' => 'Thiếu thông tin.']);
    exit;
}

try {
    $response = callAPI('duyetDonMuon', [
        'companyName' => $input['companyName'],
        'ID_bill' => $input['ID_bill']
    ]);
    echo $response;
} catch (Exception $e) {
    echo json_encode(['status' => 'Error', 'message' => $e->getMessage()]);
}
