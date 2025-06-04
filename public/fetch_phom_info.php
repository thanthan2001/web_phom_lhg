<?php
session_start();
include_once '../configs/api.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $lastMatNo = isset($input['LastMatNo']) ? trim($input['LastMatNo']) : '';

    if (empty($lastMatNo)) {
        echo json_encode(["status" => "Error", "message" => "Mã vật tư không hợp lệ hoặc không tồn tại."]);
        exit;
    }

    $companyName = isset($_SESSION['user']['companyName']) ? $_SESSION['user']['companyName'] : '';

    $response = callAPI("getInfoPhomByID", [
        "companyName" => $companyName,
        "LastMatNo"   => $lastMatNo
    ]);

    echo $response;
} else {
    echo json_encode(["status" => "Error", "message" => "Chỉ hỗ trợ POST."]);
}
