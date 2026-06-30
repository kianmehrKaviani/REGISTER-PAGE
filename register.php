<?php
include_once 'DataBase/connection.php'; 
// فرق require

$error = '';
$success = '';

// دریافت لیست کاربران
$users = [];
try {
    $stmt = $pdo->query("SELECT id, first_name, last_name, email FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // نادیده گرفته می‌شود
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['delete_id'])) {
        $delete_id = (int)$_POST['delete_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $delete_id]);
            if ($stmt->rowCount() > 0) {
                $success = "deleted";
                $stmt = $pdo->query("SELECT id, first_name, last_name, email FROM users ORDER BY id DESC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $error = "notfound";
            }
        } catch (PDOException $e) {
            $error = "delete_error";
        }
    } else {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']);

        if ($first_name === "" || $last_name === "" || $password === "" || $email === "") {
            $error = "missingFields";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $query = "INSERT INTO users (first_name, last_name, password, email)
                          VALUES (:first_name, :last_name, :password, :email)";

                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'password' => $hashedPassword,
                    'email' => $email
                ]);
                $success = "done";

                $stmt = $pdo->query("SELECT id, first_name, last_name, email FROM users ORDER BY id DESC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $error = "duplicate";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <?php if($error == "missingFields"): ?>
        <div style="color: red;">All fields are required</div>
    <?php elseif($error == "duplicate"): ?>
        <div style="color: red;">Email already exists</div>
    <?php elseif($error == "notfound"): ?>
        <div style="color: red;">User not found</div>
    <?php elseif($error == "delete_error"): ?>
        <div style="color: red;">Error deleting user</div>
    <?php endif; ?>
    
    <?php if($success == "done"): ?>
        <div style="color: green;">successful</div>
    <?php elseif($success == "deleted"): ?>
        <div style="color: green;">User deleted successfully</div>
    <?php endif; ?>

    <form method="POST" action="">
        <div>First_name:</div>
        <input type="text" name="first_name" placeholder="First Name" required>
        <br>
        <div>Last_name:</div>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <br>
        <div>Email:</div>
        <input type="email" name="email" placeholder="Email" required>
        <br>
        <div>Password:</div>
        <input type="password" name="password" placeholder="Password" required>

        <br>
        <button type="submit">Register</button>
    </form>

    <br>
    <h2>لیست کاربران</h2>
    <?php if (!empty($users)): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                            <button type="submit" onclick="return confirm('حذف می کنید؟');">حذف</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>هیچ کاربری یافت نشد.</p>
    <?php endif; ?>
</body>
</html>