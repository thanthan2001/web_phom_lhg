<?php
// configs/api_endpoints.php

if (!defined("API_BASE_URL")) {
    define("API_BASE_URL", "http://192.168.30.175:8989/api");
}

return [
    "auth_login"     => API_BASE_URL . "/auth/login",
    "get_user_info" => API_BASE_URL . "/auth/getUserById",
    "phom_getSizeByLastMatNo" => API_BASE_URL . "/phom/getSizeByLastMatNo",
    "phom_getNamePhom" => API_BASE_URL . "/phom/getPhomByLastMatNo",
    "phom_search"=> API_BASE_URL . "/phom/searchPhomBinding",
    "getDepartment" => API_BASE_URL . "/phom/getDepartment",
    "getInfoPhomByID"=> API_BASE_URL . "/phom/getInfoPhom",
    "submit_borrow"=> API_BASE_URL . "/phom/submit_borrow",
    "submit_return"=> API_BASE_URL . "/phom/confirmReturnPhom", 
    "getOldBill"=> API_BASE_URL . "/phom/getOldBill",
    "layphieumuon"=> API_BASE_URL . "/phom/layphieumuon",
    "layTatCaDonMuon"=> API_BASE_URL . "/phom/getBorrowBill",
    "duyetDonMuon"=> API_BASE_URL . '/phom/confirmBorrowBill',
    'layDonMuonTheoUser'=> API_BASE_URL . '/phom/getBorrowBillByUser',
    "layTatCaDonTra" => API_BASE_URL . '/phom/getAllReturnBill',
    'layTatCaPhom'=> API_BASE_URL . '/phom/getAllPhomManagement',
];
