<?php

require_once '../Classes/customer_class.php';

function register_customer_ctr($name, $email, $password, $phone_number, $role)
{
    $customer = new Customer();
    $customer_id = $customer->addUser($name, $email, $password, $phone_number, $role);
    if ($customer_id) {
        return $customer_id;
    }
    return false;
}

function get_customer_by_email_ctr($email)
{
    $user = new User();
    return $user->getUserByEmail($email);
}

?>