```php
<?php

require_once "../config/database.php";
require_once "../config/session.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Only riders can update delivery status
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    die("Access denied. Rider account required.");
}

$rider_id = $_SESSION['user_id'];

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$delivery_id = $_POST['delivery_id'] ?? "";
$status = $_POST['status'] ?? "";

// Check values
if (empty($delivery_id) || empty($status)) {
    header("Location: dashboard.php?error=Please+select+a+status.");
    exit;
}

// Allowed statuses
$allowed_statuses = [
    "pending",
    "picked_up",
    "in_transit",
    "delivered"
];

if (!in_array($status, $allowed_statuses, true)) {
    header("Location: dashboard.php?error=Invalid+delivery+status.");
    exit;
}

try {

    // Make sure this delivery belongs to this rider
    $check_sql = "SELECT id
                  FROM deliveries
                  WHERE id = ?
                  AND rider_id = ?
                  LIMIT 1";

    $check_stmt = $pdo->prepare($check_sql);

    $check_stmt->execute([
        $delivery_id,
        $rider_id
    ]);

    $delivery = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$delivery) {
        header("Location: dashboard.php?error=Delivery+not+found.");
        exit;
    }


    // Start transaction
    $pdo->beginTransaction();


    // Update delivery status
    $update_sql = "UPDATE deliveries
                   SET status = ?
                   WHERE id = ?
                   AND rider_id = ?";

    $update_stmt = $pdo->prepare($update_sql);

    $update_stmt->execute([
        $status,
        $delivery_id,
        $rider_id
    ]);


    // Save status history
    $history_sql = "INSERT INTO delivery_status_history
                    (
                        delivery_id,
                        status,
                        updated_by
                    )
                    VALUES (?, ?, ?)";

    $history_stmt = $pdo->prepare($history_sql);

    $history_stmt->execute([
        $delivery_id,
        $status,
        $rider_id
    ]);


    // Finish transaction
    $pdo->commit();


    // Return to dashboard
    header(
        "Location: dashboard.php?success=Delivery+status+updated+successfully."
    );

    exit;


} catch (PDOException $e) {

    // Cancel transaction if something failed
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header(
        "Location: dashboard.php?error=Could+not+update+delivery+status."
    );

    exit;
}

?>
```
