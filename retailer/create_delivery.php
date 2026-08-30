<?php

require_once "../config/database.php";
require_once "../config/session.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Check if user is a retailer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'retailer') {
    die("Access denied.");
}

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}


// Get retailer ID
$retailer_id = $_SESSION['user_id'];


// Get form information
$customer_name = trim($_POST['customer_name'] ?? '');

$customer_phone = trim($_POST['customer_phone'] ?? '');

$delivery_address = trim($_POST['delivery_address'] ?? '');

$item_description = trim($_POST['item_description'] ?? '');


// Validate fields
if (
    empty($customer_name) ||
    empty($customer_phone) ||
    empty($delivery_address) ||
    empty($item_description)
) {

    die("Please fill in all fields.");

}


// Generate confirmation code
$confirmation_code = strtoupper(
    substr(md5(uniqid()), 0, 6)
);


// Default delivery status
$status = "pending";


// Insert delivery
$sql = "INSERT INTO deliveries
        (
            retailer_id,
            customer_name,
            customer_phone,
            delivery_address,
            item_description,
            status,
            confirmation_code,
            created_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            NOW()
        )";


try {

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $retailer_id,
        $customer_name,
        $customer_phone,
        $delivery_address,
        $item_description,
        $status,
        $confirmation_code
    ]);


    // Success
    header(
        "Location: dashboard.php?success=" .
        urlencode("Delivery created successfully")
    );

    exit;


} catch (PDOException $e) {

    die(
        "Could not create delivery: " .
        $e->getMessage()
    );

}

?>