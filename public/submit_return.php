<?php
session_start();
include_once '../configs/api.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    $Details = $input['Details'];
    if (empty($Details)) {
        echo json_encode(["status" => "Error", "message" => "Chưa có mã phom nào được đăng ký mượn."]);
        exit;
    }

    if (empty($input['DepID'])) {
        echo json_encode(["status" => "Error", "message" => "Thiếu đơn vị hoặc đơn vị chưa đúng."]);
        exit;
    }

    if (empty($input['LastMatNo'])) {
        echo json_encode(["status" => "Error", "message" => "Thiếu mã vật tư."]);
        exit;
    }

    if (empty($input["UserID"])) {
        echo json_encode(["status" => "Error", "message" => "Thiếu mã người mượn."]);
        exit;
    }

    if (empty($input["UserName"])) {
        echo json_encode(["status" => "Error", "message" => "Thiếu tên người mượn."]);
        exit;
    }

    if (empty($input["DateBorrow"])) {
        echo json_encode(["status" => "Error", "message" => "Thiếu ngày mượn."]);
        exit;
    }

    if (empty($input["DateReceive"])) {
        echo json_encode(["status" => "Error", "message" => "Thiếu ngày muốn nhận."]);
        exit;
    }

    $data = [
        "companyName"  => $_SESSION['user']['companyName'],
        "DepID"        => $input['DepID'],
        "LastMatNo"    => $input['LastMatNo'],
        "UserID"       => $input['UserID'],
        "UserName"     => $input['UserName'],
        "DateBorrow"   => $input['DateBorrow'],
        "DateReceive"  => $input['DateReceive'],
        "Details"      => $Details
    ];

    $response = callAPI("submit_borrow", $data);
    echo $response;
}
