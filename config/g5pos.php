<?php

return [
    "api_credentials" => [
        "base_url" => env('G5_BASE_URL', 'https://posapi2.vertigopos.com/api'),
        "login_url" => 'https://posapi2.vertigopos.com/loginUser',
        "workstation_id" => env('G5_WORKSTATION_ID', 1),
        "branch_id" => env('G5_BRANCH_ID', 1),
        "password" => env('G5_PASSWORD', '8887910'),
        "employee_code" => env('G5_EMPLOYEE_CODE', '999'),
        "order_employee_code" => env('G5_ORDER_EMPLOYEE_CODE', 267)
    ]
];