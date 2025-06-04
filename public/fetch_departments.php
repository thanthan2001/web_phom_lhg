<?php
session_start();
include_once '../configs/api.php';

header('Content-Type: application/json');

try {
    $companyName = isset($_SESSION['user']['companyName']) ? $_SESSION['user']['companyName'] : '';
    $result = callAPI('getDepartment', ['companyName' => $companyName]);
    echo $result;
} catch (Exception $e) {
    echo json_encode([
        'status' => 'Error',
        'message' => $e->getMessage()
    ]);
}
