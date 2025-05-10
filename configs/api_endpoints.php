<?php
// configs/api_endpoints.php

define("API_BASE_URL", "http://localhost:8989/api");

return [
    "auth_login"     => API_BASE_URL . "/auth/login",
    "phom_list"      => API_BASE_URL . "/phom/list",
    "phom_create"    => API_BASE_URL . "/phom/create",
    "phom_update"    => API_BASE_URL . "/phom/update",
    "phom_delete"    => API_BASE_URL . "/phom/delete",
];
