```php
<?php

require_once "../config/database.php";
require_once "../config/session.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check if user is a retailer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'retailer') {
    die("Access denied.");
}

$retailer_id = $_SESSION['user_id'];

$success = $_GET['success'] ?? "";
$error = $_GET['error'] ?? "";


// --------------------------------------------------
// GET RIDERS
// --------------------------------------------------

$rider_sql = "SELECT id, name, phone
              FROM users
              WHERE role = 'rider'
              ORDER BY name ASC";

$rider_stmt = $pdo->prepare($rider_sql);
$rider_stmt->execute();

$riders = $rider_stmt->fetchAll(PDO::FETCH_ASSOC);


// --------------------------------------------------
// GET DELIVERIES
// --------------------------------------------------

$delivery_sql = "SELECT *
                 FROM deliveries
                 WHERE retailer_id = ?
                 ORDER BY id DESC";

$delivery_stmt = $pdo->prepare($delivery_sql);
$delivery_stmt->execute([$retailer_id]);

$deliveries = $delivery_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>dunda-reflex Retailer Dashboard</title>

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
            margin: 0 0 8px 0;
        }

        .logout {
            float: right;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background: #dc2626;
            border-radius: 5px;
        }

        .container {
            width: 95%;
            max-width: 1400px;
            margin: 30px auto;
        }

        .welcome {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
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

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        button {
            margin-top: 10px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background: #2563eb;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .create-button {
            margin-top: 20px;
            padding: 12px 20px;
            font-size: 16px;
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
            vertical-align: middle;
        }

        th {
            background: #f3f4f6;
        }

        .status {
            padding: 5px 10px;
            border-radius: 4px;
            background: #fef3c7;
            color: #92400e;
            display: inline-block;
        }

        .assigned {
            background: #d1fae5;
            color: #065f46;
        }

        .assign-form {
            min-width: 180px;
        }

        .assign-form select {
            margin-bottom: 5px;
        }

        .history-button {
            display: inline-block;
            padding: 8px 12px;
            background: #059669;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .history-button:hover {
            background: #047857;
        }

        @media (max-width: 1000px) {

            .container {
                width: 95%;
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

    <a class="logout" href="../auth/logout.php">
        Logout
    </a>

    <h1>
        dunda-reflex Retailer Dashboard
    </h1>

    <p>
        Retailer Management System
    </p>

</div>


<div class="container">


    <!-- WELCOME -->

    <div class="welcome">

        <h2>

            Welcome,

            <?php
            echo htmlspecialchars(
                $_SESSION['name'] ?? 'Retailer'
            );
            ?>!

        </h2>

        <p>

            Your role:

            <strong>

                <?php
                echo htmlspecialchars($_SESSION['role']);
                ?>

            </strong>

        </p>

    </div>


    <!-- SUCCESS MESSAGE -->

    <?php if (!empty($success)): ?>

        <div class="success">

            <?php
            echo htmlspecialchars($success);
            ?>

        </div>

    <?php endif; ?>


    <!-- ERROR MESSAGE -->

    <?php if (!empty($error)): ?>

        <div class="error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <!-- CREATE DELIVERY -->

    <div class="card">

        <h2>
            Create New Delivery
        </h2>


        <form
            method="POST"
            action="create_delivery.php"
        >


            <label for="customer_name">
                Customer Name
            </label>

            <input
                type="text"
                id="customer_name"
                name="customer_name"
                placeholder="Enter customer name"
                required
            >


            <label for="customer_phone">
                Customer Phone
            </label>

            <input
                type="text"
                id="customer_phone"
                name="customer_phone"
                placeholder="Enter customer phone"
                required
            >


            <label for="delivery_address">
                Delivery Address
            </label>

            <input
                type="text"
                id="delivery_address"
                name="delivery_address"
                placeholder="Enter delivery address"
                required
            >


            <label for="item_description">
                Item Description
            </label>

            <textarea
                id="item_description"
                name="item_description"
                placeholder="Describe the item"
                required
            ></textarea>


            <button
                type="submit"
                class="create-button"
            >
                Create Delivery
            </button>


        </form>

    </div>


    <!-- DELIVERIES -->

    <div class="card">

        <h2>
            My Deliveries
        </h2>


        <?php if (count($deliveries) > 0): ?>


            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Customer</th>

                        <th>Phone</th>

                        <th>Address</th>

                        <th>Item</th>

                        <th>Status</th>

                        <th>Rider</th>

                        <th>Confirmation Code</th>

                        <th>History</th>

                        <th>Created</th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($deliveries as $delivery): ?>


                        <tr>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $delivery['id']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $delivery['customer_name']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $delivery['customer_phone']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $delivery['delivery_address']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $delivery['item_description']
                                );
                                ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span class="status">

                                    <?php
                                    echo htmlspecialchars(
                                        $delivery['status']
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- RIDER -->

                            <td>


                                <?php if (!empty($delivery['rider_id'])): ?>


                                    <?php

                                    $assigned_rider_sql =
                                        "SELECT name
                                         FROM users
                                         WHERE id = ?";

                                    $assigned_rider_stmt =
                                        $pdo->prepare(
                                            $assigned_rider_sql
                                        );

                                    $assigned_rider_stmt->execute([
                                        $delivery['rider_id']
                                    ]);

                                    $assigned_rider =
                                        $assigned_rider_stmt->fetch(
                                            PDO::FETCH_ASSOC
                                        );

                                    ?>


                                    <span class="status assigned">

                                        <?php

                                        echo htmlspecialchars(
                                            $assigned_rider['name']
                                            ?? 'Assigned'
                                        );

                                        ?>

                                    </span>


                                <?php else: ?>


                                    <form
                                        method="POST"
                                        action="assign_rider.php"
                                        class="assign-form"
                                    >


                                        <input
                                            type="hidden"
                                            name="delivery_id"
                                            value="<?php
                                            echo htmlspecialchars(
                                                $delivery['id']
                                            );
                                            ?>"
                                        >


                                        <select
                                            name="rider_id"
                                            required
                                        >

                                            <option value="">
                                                Select Rider
                                            </option>


                                            <?php foreach ($riders as $rider): ?>

                                                <option
                                                    value="<?php
                                                    echo $rider['id'];
                                                    ?>"
                                                >

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $rider['name']
                                                    );
                                                    ?>

                                                </option>

                                            <?php endforeach; ?>


                                        </select>


                                        <button type="submit">

                                            Assign Rider

                                        </button>


                                    </form>


                                <?php endif; ?>


                            </td>


                            <!-- CONFIRMATION CODE -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $delivery['confirmation_code']
                                    ?? ''
                                );
                                ?>

                            </td>


                            <!-- HISTORY -->

                            <td>

                                <a
                                    class="history-button"
                                    href="history.php?id=<?php echo $delivery['id']; ?>"
                                >
                                    View History
                                </a>

                            </td>


                            <!-- CREATED -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $delivery['created_at']
                                    ?? ''
                                );
                                ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>


        <?php else: ?>


            <p>
                You don't have any deliveries yet.
            </p>


        <?php endif; ?>


    </div>


</div>


</body>

</html>
```
