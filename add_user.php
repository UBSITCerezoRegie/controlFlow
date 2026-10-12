<?php

require_once "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";

    $allowed_roles = ["resident", "staff", "admin"];

    if (
        $username === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $password === "" ||
        !in_array($role, $allowed_roles, true)
    ) {
        $message = "Please enter valid information in all fields.";
    } else {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users
                (username, email, password_hash, role)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $username,
                $email,
                $password_hash,
                $role
            );

            if (mysqli_stmt_execute($stmt)) {
                $message = "User successfully added!";
            } else {
                if (mysqli_stmt_errno($stmt) === 1062) {
                    $message = "That username or email is already registered.";
                } else {
                    $message = "Could not create user. Please try again.";
                }
            }

            mysqli_stmt_close($stmt);

        } else {
            $message = "Could not prepare the database query.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="container">

        <h1>Add User</h1>

        <?php if ($message !== ""): ?>
            <p><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <form method="POST" action="add_user.php">

            <label for="username">Username:</label>
            <input
                type="text"
                id="username"
                name="username"
                maxlength="20"
                required
            >

            <br><br>

            <label for="email">Email:</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="100"
                required
            >

            <br><br>

            <label for="password">Password:</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >

            <br><br>

            <p>Role:</p>

            <label>
                <input type="radio" name="role" value="resident" required>
                Resident
            </label>

            <br>

            <label>
                <input type="radio" name="role" value="staff">
                Staff
            </label>

            <br>

            <label>
                <input type="radio" name="role" value="admin">
                Admin
            </label>

            <br><br>

            <button type="submit">Create User</button>

        </form>

        <br>

        <a href="users.php">View Registered Users</a>

    </div>

</body>

</html>
