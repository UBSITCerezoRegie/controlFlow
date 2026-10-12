<?php
require_once "config/db.php";

$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container users-container">
        <h1>Registered Users</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($user["user_id"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["username"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["email"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["role"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["created_at"] ?? "") ?>
                        </td>

                        <td>
                            <a href="edit_user.php?id=<?= (int) $user['user_id'] ?>">
                                Edit
                            </a>

                            <form method="POST"
                                  action="delete_user.php"
                                  onsubmit="return confirm('Are you sure you want to delete this user?');"
                                  style="display: inline;">

                                <input
                                    type="hidden"
                                    name="user_id"
                                    value="<?= (int) $user['user_id'] ?>"
                                >

                                <button type="submit" name="delete_user">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <br>

        <a href="add_user.php">Add Another User</a>
    </div>

</body>
</html>
