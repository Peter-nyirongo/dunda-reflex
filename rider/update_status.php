```php
<?php

require_once "../config/database.php";
require_once "../config/session.php";

// Check that user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check that logged-in user is a rider
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "rider") {
    header("Location: dashboard.php?error=Rider+account+required.");
    exit;
}

$rider_id = $_SESSION["user_id"];

// Only accept POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$delivery_id = $_POST["delivery_id"] ?? "";
$new_status = $_POST["status"] ?? "";

// Check required fields
if (empty($delivery_id) || empty($new_status)) {
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

if (!in_array($new_status, $allowed_statuses, true)) {
    header("Location: dashboard.php?error=Invalid+status.");
    exit;
}

try {

    // Check that this delivery belongs to this rider
    $sql = "SELECT id, status
            FROM deliveries
            WHERE id = ?
            AND rider_id = ?
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $delivery_id,
        $rider_id
    ]);

    $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$delivery) {
        header("Location: dashboard.php?error=Delivery+not+assigned+to+you.");
        exit;
    }

    // Start database transaction
    $pdo->beginTransaction();

    // Update delivery
    $update_sql = "UPDATE deliveries
                   SET status = ?
                   WHERE id = ?
                   AND rider_id = ?";

    $update_stmt = $pdo->prepare($update_sql);

    $update_stmt->execute([
        $new_status,
        $delivery_id,
        $rider_id
    ]);

    // Add record to history
    $history_sql = "INSERT INTO delivery_status_history
                    (delivery_id, status, updated_by)
                    VALUES (?, ?, ?)";

    $history_stmt = $pdo->prepare($history_sql);

    $history_stmt->execute([
        $delivery_id,
        $new_status,
        $rider_id
    ]);

    // Save everything
    $pdo->commit();

    header(
        "Location: dashboard.php?success=Delivery+status+updated+successfully."
    );

    exit;

} catch (PDOException $e) {

    // Undo changes if something went wrong
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header(
        "Location: dashboard.php?error=Database+error+while+updating+status."
    );

    exit;
}

?>
```
