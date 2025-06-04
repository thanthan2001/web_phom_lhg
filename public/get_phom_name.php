<?php
session_start();
include_once '../configs/api.php';

header('Content-Type: application/json');

$matNo = $_POST['itemCodeInput'] ?? '';

if (!$matNo) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu mã vật tư']);
    exit;
}

$payload = [
    'companyName' => $_SESSION['user']['companyName'] ?? '',
    'LastMatNo' => $matNo
];

if (empty($payload['companyName'])) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin công ty']);
    exit;
}

try {
    $response = callAPI('phom_getNamePhom', $payload);
    $result = json_decode($response, true);

    if ($result['status'] === 'Success' && isset($result['data']['jsonArray'][0]['LastName'])) {
        echo json_encode([
            'status' => 'Success',
            'phomName' => $result['data']['jsonArray'][0]['LastName']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy tên phom']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
}
