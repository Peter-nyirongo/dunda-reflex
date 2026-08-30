<?php

require_once "../config/database.php";
require_once "../config/session.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (
        empty($name) ||
        empty($phone) ||
        empty($email) ||
        empty($password) ||
        empty($role)
    ) {
        $message = "Please fill in all fields.";

    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {

            $sql = "INSERT INTO users
                    (name, phone, email, password, role)
                    VALUES
                    (:name, :phone, :email, :password, :role)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":name" => $name,
                ":phone" => $phone,
                ":email" => $email,
                ":password" => $hashedPassword,
                ":role" => $role
            ]);

            $message = "Registration successful!";

        } catch (PDOException $e) {

            $message = "Registration failed: " . $e->getMessage();

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>dunda-reflex Registration</title>

</head>

<body>

    <h1>dunda-reflex</h1>

    <h2>Create Account</h2>

    <?php if (!empty($message)): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Name</label>
        <br>

        <input
            type="text"
            name="name"
            required
        >

        <br><br>

        <label>Phone</label>
        <br>

        <input
            type="text"
            name="phone"
            required
        >

        <br><br>

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

        <label>Role</label>
        <br>

        <select name="role" required>

            <option value="">Select role</option>

            <option value="retailer">
                Retailer
            </option>

            <option value="dispatcher">
                Dispatcher
            </option>

            <option value="rider">
                Rider
            </option>

        </select>

        <br><br>

        <button type="submit">
            Create Account
        </button>

    </form>

</body>

</html>