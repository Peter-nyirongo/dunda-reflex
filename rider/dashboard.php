```php
<?php

require_once "../config/database.php";
require_once "../config/session.php";

// --------------------------------------------------
// CHECK LOGIN
// --------------------------------------------------

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


// --------------------------------------------------
// CHECK RIDER ROLE
// --------------------------------------------------

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    die("Access denied. Rider account required.");
}


$rider_id = $_SESSION['user_id'];

$success = $_GET['success'] ?? "";
$error = $_GET['error'] ?? "";


// --------------------------------------------------
// GET ASSIGNED DELIVERIES
// --------------------------------------------------

$sql = "SELECT *
        FROM deliveries
        WHERE rider_id = ?
        ORDER BY id DESC";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $rider_id
]);

$deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>dunda-reflex Rider Dashboard</title>


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


        /* HEADER */

        .header {
            background: #1f2937;
            color: white;
            padding: 20px 30px;
        }


        .header h1 {
            margin: 0 0 8px 0;
        }


        .header p {
            margin: 0;
        }


        .logout {
            float: right;
            color: white;
            text-decoration: none;
            padding: 9px 16px;
            background: #dc2626;
            border-radius: 5px;
        }


        .logout:hover {
            background: #b91c1c;
        }


        /* CONTAINER */

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 30px auto;
        }


        /* WELCOME */

        .welcome {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }


        .welcome h2 {
            margin-top: 0;
        }


        /* MESSAGES */

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


        /* CARD */

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


        /* TABLE */

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


        /* STATUS */

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            background: #fef3c7;
            color: #92400e;
            font-weight: bold;
        }


        /* UPDATE FORM */

        .update-form {
            min-width: 180px;
        }


        .update-form select {
            width: 100%;
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 7px;
        }


        .update-button {
            width: 100%;
            padding: 9px;
            border: none;
            border-radius: 5px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }


        .update-button:hover {
            background: #1d4ed8;
        }


        /* HISTORY LINK */

        .history-link {
            display: inline-block;
            padding: 9px 13px;
            background: #059669;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }


        .history-link:hover {
            background: #047857;
        }


        /* EMPTY */

        .empty {
            padding: 15px;
            background: #f3f4f6;
            border-radius: 6px;
        }


        /* MOBILE */

        @media (max-width: 900px) {

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


<!-- --------------------------------------------------
     HEADER
--------------------------------------------------- -->

<div class="header">


    <a
        class="logout"
        href="/dunda-reflex/auth/logout.php"
    >
        Logout
    </a>


    <h1>
        dunda-reflex Rider Dashboard
    </h1>


    <p>
        Delivery Management System
    </p>


</div>



<div class="container">


    <!-- --------------------------------------------------
         WELCOME
    --------------------------------------------------- -->

    <div class="welcome">


        <h2>

            Welcome,

            <?php

            echo htmlspecialchars(
                $_SESSION['name'] ?? 'Rider'
            );

            ?>!


        </h2>


        <p>

            Your role:

            <strong>

                <?php

                echo htmlspecialchars(
                    $_SESSION['role']
                );

                ?>

            </strong>

        </p>


    </div>



    <!-- --------------------------------------------------
         SUCCESS MESSAGE
    --------------------------------------------------- -->

    <?php if (!empty($success)): ?>


        <div class="success">

            <?php

            echo htmlspecialchars(
                $success
            );

            ?>

        </div>


    <?php endif; ?>



    <!-- --------------------------------------------------
         ERROR MESSAGE
    --------------------------------------------------- -->

    <?php if (!empty($error)): ?>


        <div class="error">

            <?php

            echo htmlspecialchars(
                $error
            );

            ?>

        </div>


    <?php endif; ?>



    <!-- --------------------------------------------------
         DELIVERIES
    --------------------------------------------------- -->

    <div class="card">


        <h2>
            My Assigned Deliveries
        </h2>



        <?php if (count($deliveries) > 0): ?>


            <table>


                <thead>


                    <tr>

                        <th>ID</th>

                        <th>Customer</th>

                        <th>Phone</th>

                        <th>Delivery Address</th>

                        <th>Item</th>

                        <th>Status</th>

                        <th>Confirmation Code</th>

                        <th>Update Status</th>

                        <th>History</th>

                        <th>Created</th>

                    </tr>


                </thead>



                <tbody>


                    <?php foreach ($deliveries as $delivery): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $delivery['id']
                                );

                                ?>

                            </td>



                            <!-- CUSTOMER -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $delivery['customer_name']
                                );

                                ?>

                            </td>



                            <!-- PHONE -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $delivery['customer_phone']
                                );

                                ?>

                            </td>



                            <!-- ADDRESS -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $delivery['delivery_address']
                                );

                                ?>

                            </td>



                            <!-- ITEM -->

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



                            <!-- CONFIRMATION CODE -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $delivery['confirmation_code'] ?? ''
                                );

                                ?>

                            </td>



                            <!-- UPDATE STATUS -->

                            <td>


                                <form
                                    method="POST"
                                    action="update_status.php"
                                    class="update-form"
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
                                        name="status"
                                        required
                                    >

                                        <option value="">
                                            Select
                                        </option>


                                        <option value="pending">
                                            Pending
                                        </option>


                                        <option value="picked_up">
                                            Picked Up
                                        </option>


                                        <option value="in_transit">
                                            In Transit
                                        </option>


                                        <option value="delivered">
                                            Delivered
                                        </option>


                                    </select>


                                    <button
                                        type="submit"
                                        class="update-button"
                                    >
                                        Update
                                    </button>


                                </form>


                            </td>



                            <!-- HISTORY -->

                            <td>


                                <a
                                    class="history-link"
                                    href="history.php?id=<?php

                                    echo htmlspecialchars(
                                        $delivery['id']
                                    );

                                    ?>"
                                >

                                    View History

                                </a>


                            </td>



                            <!-- CREATED -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $delivery['created_at'] ?? ''
                                );

                                ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="empty">

                You don't have any assigned deliveries yet.

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>
```
