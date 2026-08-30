```php
<?php

require_once "../config/database.php";
require_once "../config/session.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Only retailers can view delivery history
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'retailer') {
    die("Access denied. Retailer account required.");
}

$retailer_id = $_SESSION['user_id'];

// Get delivery ID from URL
$delivery_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($delivery_id <= 0) {
    die("Invalid delivery ID.");
}


// --------------------------------------------------
// GET DELIVERY
// --------------------------------------------------

$delivery_sql = "SELECT *
                 FROM deliveries
                 WHERE id = ?
                 AND retailer_id = ?
                 LIMIT 1";

$delivery_stmt = $pdo->prepare($delivery_sql);

$delivery_stmt->execute([
    $delivery_id,
    $retailer_id
]);

$delivery = $delivery_stmt->fetch(PDO::FETCH_ASSOC);

if (!$delivery) {
    die("Delivery not found.");
}


// --------------------------------------------------
// GET DELIVERY HISTORY
// --------------------------------------------------

$history_sql = "SELECT
                    delivery_status_history.*,
                    users.name AS updated_by_name
                FROM delivery_status_history
                LEFT JOIN users
                    ON delivery_status_history.updated_by = users.id
                WHERE delivery_status_history.delivery_id = ?
                ORDER BY delivery_status_history.id ASC";

$history_stmt = $pdo->prepare($history_sql);

$history_stmt->execute([
    $delivery_id
]);

$history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Delivery History - dunda-reflex</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #333;
        }

        .header {
            background: #1f2937;
            color: white;
            padding: 20px 30px;
        }

        .header h1 {
            margin: 0;
        }

        .container {
            width: 95%;
            max-width: 1100px;
            margin: 30px auto;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: white;
            background: #2563eb;
            padding: 10px 16px;
            border-radius: 5px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .card h2 {
            margin-top: 0;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail {
            padding: 12px;
            background: #f9fafb;
            border-radius: 5px;
        }

        .detail strong {
            display: block;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            background: #fef3c7;
            color: #92400e;
            font-weight: bold;
        }

        .empty {
            padding: 15px;
            background: #f3f4f6;
            border-radius: 5px;
        }

        @media (max-width: 700px) {

            .details {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

        }

    </style>

</head>

<body>


<div class="header">

    <h1>dunda-reflex</h1>

    <p>Delivery History</p>

</div>


<div class="container">


    <a class="back" href="dashboard.php">
        ← Back to Dashboard
    </a>


    <!-- DELIVERY INFORMATION -->

    <div class="card">

        <h2>Delivery #<?php echo htmlspecialchars($delivery['id']); ?></h2>


        <div class="details">


            <div class="detail">

                <strong>Customer</strong>

                <?php
                echo htmlspecialchars(
                    $delivery['customer_name']
                );
                ?>

            </div>


            <div class="detail">

                <strong>Phone</strong>

                <?php
                echo htmlspecialchars(
                    $delivery['customer_phone']
                );
                ?>

            </div>


            <div class="detail">

                <strong>Address</strong>

                <?php
                echo htmlspecialchars(
                    $delivery['delivery_address']
                );
                ?>

            </div>


            <div class="detail">

                <strong>Item</strong>

                <?php
                echo htmlspecialchars(
                    $delivery['item_description']
                );
                ?>

            </div>


            <div class="detail">

                <strong>Current Status</strong>

                <span class="status">

                    <?php
                    echo htmlspecialchars(
                        $delivery['status']
                    );
                    ?>

                </span>

            </div>


            <div class="detail">

                <strong>Confirmation Code</strong>

                <?php
                echo htmlspecialchars(
                    $delivery['confirmation_code'] ?? ''
                );
                ?>

            </div>


        </div>

    </div>


    <!-- HISTORY -->

    <div class="card">

        <h2>Status History</h2>


        <?php if (count($history) > 0): ?>


            <table>

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Status</th>

                        <th>Updated By</th>

                        <th>Date & Time</th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($history as $record): ?>


                        <tr>

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $record['id']
                                );
                                ?>

                            </td>


                            <td>

                                <span class="status">

                                    <?php
                                    echo htmlspecialchars(
                                        $record['status']
                                    );
                                    ?>

                                </span>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $record['updated_by_name']
                                    ?? 'Unknown'
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $record['created_at']
                                    ?? ''
                                );
                                ?>

                            </td>

                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>


        <?php else: ?>


            <div class="empty">

                No status history has been recorded yet.

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>
```
