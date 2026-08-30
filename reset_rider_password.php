<?php

require_once "config/database.php";

$email = "rider@gmail.com";
$new_password = "12345678";

try {

    // Create secure password hash
    $hashed_password = password_hash(
        $new_password,
        PASSWORD_DEFAULT
    );

    // Update John's password
    $sql = "UPDATE users
            SET password = :password
            WHERE email = :email
            AND role = 'rider'";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":password" => $hashed_password,
        ":email" => $email
    ]);

    if ($stmt->rowCount() > 0) {

        echo "John Banda's password has been reset successfully.";

    } else {

        echo "No rider account was found with that email.";

    }

} catch (PDOException $e) {

    echo "Database error: " . $e->getMessage();

}

?>