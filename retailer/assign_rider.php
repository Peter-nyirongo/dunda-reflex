<?php

require_once "../config/database.php";
require_once "../config/session.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Only retailers can assign riders
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'retailer') {
    die("Access denied.");
}

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// Get data from form
$delivery_id = $_POST['delivery_id'] ?? '';
$rider_id = $_POST['rider_id'] ?? '';


// Check that both values exist
if (empty($delivery_id) || empty($rider_id)) {
    header(
        "Location: dashboard.php?error=" .
        urlencode("Please select a rider.")
    );
    exit;
}


try {

    // Check that the selected user is actually a rider
    $rider_sql = "SELECT id
                  FROM users
                  WHERE id = ?
                  AND role = 'rider'";

    $rider_stmt = $pdo->prepare($rider_sql);

    $rider_stmt->execute([$rider_id]);

    $rider = $rider_stmt->fetch(PDO::FETCH_ASSOC);


    if (!$rider) {

        header(
            "Location: dashboard.php?error=" .
            urlencode("Selected user is not a valid rider.")
        );

        exit;
    }


    // Assign rider to delivery
    $update_sql = "UPDATE deliveries
                   SET rider_id = ?
                   WHERE id = ?";

    $update_stmt = $pdo->prepare($update_sql);

    $update_stmt->execute([
        $rider_id,
        $delivery_id
    ]);


    // Check whether delivery was updated
    if ($update_stmt->rowCount() > 0) {

        header(
            "Location: dashboard.php?success=" .
            urlencode("Rider assigned successfully.")
        );

        exit;

    } else {

        header(
            "Location: dashboard.php?error=" .
            urlencode("Delivery could not be updated.")
        );

        exit;
    }


} catch (PDOException $e) {

    die(
        "Database error: " .
        $e->getMessage()
    );

}

?>