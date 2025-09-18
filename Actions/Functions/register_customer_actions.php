<?php

header('Content-Type: application/json');
session_start();
$response = array();

// check if customer is already logged in
if (isset($_SESSION['customer_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone_number = $_POST['phone_number'];
$role = $_POST['role'];

$customer_id = register_customer_ctr($name, $email, $password, $phone_number, $role);
if ($customer_id) {
    $response['status'] = 'success';
    $response['message'] = 'Registered successfully';
    $response['customer_id'] = $customer_id;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to register';
}
echo json_encode($response);