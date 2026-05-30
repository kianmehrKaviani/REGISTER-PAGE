<?php
require_once 'Database/connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    if ($first_name === "" || $last_name === "" || $password === "" || $email === "") {
        $error = "empty";
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
        } catch (PDOException $e) {
            $error = "duplicate";
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
    <?php if($error == "empty"): ?>
        <div style="color: red;">All fields are required</div>
    <?php elseif($error == "duplicate"): ?>
        <div style="color: red;">Email already exists</div>
    <?php endif; ?>
    
    <?php if($success == "done"): ?>
        <div style="color: green;">successful</div>
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
</body>
</html>