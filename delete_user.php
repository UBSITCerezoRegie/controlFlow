<?php
require_once "config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php");
    exit;
}

$user_id = filter_input(
    INPUT_POST,
    "user_id",
    FILTER_VALIDATE_INT
);

if (!$user_id || $user_id < 1) {
    die("Invalid user ID.");
}

$sql = "DELETE FROM users WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Could not prepare the delete query.");
}

mysqli_stmt_bind_param($stmt, "i", $user_id);


if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) === 1) {
        mysqli_stmt_close($stmt);

        header("Location: users.php");
        exit;
    }

    mysqli_stmt_close($stmt);
    die("User not found.");
}

mysqli_stmt_close($stmt);

die(
    "Could not delete this user. " .
    "They may be referenced by other records."
);
?>
