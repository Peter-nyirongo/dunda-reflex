<?php

require_once "../config/database.php";
require_once "../config/session.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";

    } else {

        try {

            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":email" => $email
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["role"] = $user["role"];

                if ($user["role"] === "retailer") {

                    header("Location: ../retailer/dashboard.php");
                    exit;

                } elseif ($user["role"] === "dispatcher") {

                    header("Location: ../dispatcher/dashboard.php");
                    exit;

                } elseif ($user["role"] === "rider") {

                    header("Location: ../rider/dashboard.php");
                    exit;

                }

            } else {

                $message = "Invalid email or password.";

            }

        } catch (PDOException $e) {

            $message = "Login failed: " . $e->getMessage();

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>dunda-reflex Login</title>

</head>

<body>

    <h1>dunda-reflex</h1>

    <h2>Login</h2>

    <?php if (!empty($message)): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Email</label>
        <br>

        <input
            type="email"
            name="email"
            required
        >

        <br><br>

        <label>Password</label>
        <br>

        <input
            type="password"
            name="password"
            required
        >

        <br><br>

        <button type="submit">
            Login
        </button>

    </form>

</body>

</html>