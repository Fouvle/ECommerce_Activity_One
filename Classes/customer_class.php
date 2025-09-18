<?php

require_once '../settings/db_class.php';

/**
 * 
 */
class Customer extends db_connection
{
    private $customer_id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $date_created;
    private $phone_number;

    public function __construct($customer_id = null)
    {
        parent::db_connect();
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadUser();
        }
    }

    private function loadUser($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
        }
        if (!$this->customer_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->name = $result['customer_name'];
            $this->email = $result['customer_email'];
            $this->role = $result['user_role'];
            $this->date_created = isset($result['date_created']) ? $result['date_created'] : null;
            $this->phone_number = $result['customer_contact'];
        }
    }

    // create a new customer
    public function createUser($name, $email, $password, $phone_number, $role)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, user_role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $hashed_password, $phone_number, $role);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    //add customer
    public function addUser($name, $email, $password, $phone_number, $role)
    {
        return $this->createUser($name, $email, $password, $phone_number, $role);
    }

    // edit customer details
    public function editUser($name, $email, $phone_number)
    {
        if (!$this->customer_id) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE customer SET customer_name = ?, customer_email = ?, customer_contact = ? WHERE customer_id = ?");
        $stmt->bind_param("sssi", $name, $email, $phone_number, $this->customer_id);
        return $stmt->execute();
    }

    // change customer password
    public function changePassword($old_password, $new_password)
    {
        if (!$this->customer_id) {
            return false;
        }
        $user = $this->getUserByEmail($this->email);
        if ($user && password_verify($old_password, $user['customer_pass'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE customer SET customer_pass = ? WHERE customer_id = ?");
            $stmt->bind_param("si", $hashed_password, $this->customer_id);
            return $stmt->execute();
        }
        return false;
    }

    // delete customer
    public function deleteUser()
    {
        if (!$this->customer_id) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->customer_id);
        return $stmt->execute();
    }

    
}
?>
