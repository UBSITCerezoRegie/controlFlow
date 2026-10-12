<?php
require_once "config/db.php";

$error = "";
$user = null;

$user_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$user_id || $user_id < 1) {
    die("Invalid user ID.");
}

$sql = "SELECT user_id, username, email, role
        FROM users
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$user) {
    die("User not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = $_POST["role"] ?? "";

    if (
        $username === "" ||
        strlen($username) > 20 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 100 ||
        !in_array($role, ["resident", "staff", "admin"], true)
    ) {
        $error = "Please enter valid user details.";
    } else {
        $sql = "UPDATE users
                SET username = ?, email = ?, role = ?
                WHERE user_id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sssi",
            $username,
            $email,
            $role,
            $user_id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: users.php");
            exit;
        } else {
            if (mysqli_stmt_errno($stmt) === 1062) {
                $error = "That username or email is already registered.";
            } else {
                $error = "Update failed. Please try again.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    $user["username"] = $username;
    $user["email"] = $email;
    $user["role"] = $role;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container">
        <h1>Edit User</h1>

        <?php if ($error !== "") { ?>
            <p style="color: red;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php } ?>

        <form method="POST"
              action="edit_user.php?id=<?= (int) $user_id ?>">

            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                maxlength="20"
                value="<?= htmlspecialchars($user["username"]) ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="100"
                value="<?= htmlspecialchars($user["email"]) ?>"
                required
            >

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="resident"
                    <?= $user["role"] === "resident" ? "selected" : "" ?>>
                    Resident
                </option>

                <option value="staff"
                    <?= $user["role"] === "staff" ? "selected" : "" ?>>
                    Staff
                </option>

                <option value="admin"
                    <?= $user["role"] === "admin" ? "selected" : "" ?>>
                    Admin
                </option>
            </select>

            <button type="submit">Save Changes</button>
        </form>

        <br>
        <a href="users.php">Back to Users</a>
    </div>

</body>
</html>
