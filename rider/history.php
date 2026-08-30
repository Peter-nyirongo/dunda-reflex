```php
<?php

require_once "../config/database.php";
require_once "../config/session.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Only riders
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    die("Access denied.");
}

$rider_id = $_SESSION['user_id'];

$delivery_id = $_GET['id'] ?? '';

if (empty($delivery_id)) {
    die("Delivery not specified.");
}

try {

    // Get delivery
    $delivery_sql = "SELECT *
                     FROM deliveries
                     WHERE id = ?
                     AND rider_id = ?";

    $delivery_stmt = $pdo->prepare($delivery_sql);

    $delivery_stmt->execute([
        $delivery_id,
        $rider_id
    ]);

    $delivery = $delivery_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$delivery) {
        die("Delivery not found or you are not assigned to it.");
    }


    // Get status history
    $history_sql = "SELECT *
                    FROM delivery_status_history
                    WHERE delivery_id = ?
                    ORDER BY created_at ASC";

    $history_stmt = $pdo->prepare($history_sql);

    $history_stmt->execute([
        $delivery_id
    ]);

    $history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Database error: " . $e->getMessage());

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Delivery History</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }

    </style>

</head>

<body>

<div class="container">

    <a class="back" href="dashboard.php">
        ← Back to Dashboard
    </a>


    <div class="card">

        <h1>
            Delivery #<?php
            echo htmlspecialchars($delivery['id']);
            ?>
        </h1>

        <p>
            <strong>Customer:</strong>
            <?php
            echo htmlspecialchars($delivery['customer_name']);
            ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?php
            echo htmlspecialchars($delivery['customer_phone']);
            ?>
        </p>

        <p>
            <strong>Address:</strong>
            <?php
            echo htmlspecialchars($delivery['delivery_address']);
            ?>
        </p>

        <p>
            <strong>Item:</strong>
            <?php
            echo htmlspecialchars($delivery['item_description']);
            ?>
        </p>

        <p>
            <strong>Current Status:</strong>
            <?php
            echo htmlspecialchars($delivery['status']);
            ?>
        </p>

    </div>


    <div class="card">

        <h2>
            Status History
        </h2>


        <?php if (count($history) > 0): ?>

            <table>

                <thead>

                    <tr>

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
                                    $record['status']
                                );
                                ?>
                            </td>

                            <td>
                                User ID
                                <?php
                                echo htmlspecialchars(
                                    $record['updated_by']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $record['created_at']
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p>
                No status history yet.
            </p>

        <?php endif; ?>

    </div>

</div>

</body>

</html>
```
