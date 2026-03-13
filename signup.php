<?php
session_start();
require_once "db.php";

if (isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $role = "student";

    if ($username === "" || $password === "") {
        $error = "Username and password are required.";
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$username]);
        $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $error = "Username already exists.";
        } else {
            $insertStmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $insertStmt->execute([$username, $password, $role]);
            $success = "Account created successfully. You can now login.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/1055/1055687.png">
  <title>Sign Up</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb');
      background-size: cover;
      background-position: center;
      min-height: 100vh;
    }

    form {
      width: 360px;
      background: white;
      border: 1px solid #ddd;
      border-radius: 12px;
      padding: 18px;
      margin: 100px auto;
    }

    h2 {
      margin-bottom: 12px;
      text-align: center;
    }

    label {
      display: block;
      margin-top: 10px;
      font-size: 14px;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid grey;
      margin-top: 6px;
    }

    input[type="submit"] {
      width: 100%;
      margin-top: 14px;
      padding: 10px;
      border: 0;
      background: green;
      color: white;
      font-weight: bold;
      margin-bottom: 20px;
      cursor: pointer;
    }

    p {
      font-size: 14px;
      padding: 8px;
      text-align: center;
      margin-top: 10px;
    }

    .error {
      color: red;
      border: 2px dotted red;
    }

    .success {
      color: green;
      border: 2px dotted green;
    }

    .login-link {
      display: block;
      text-align: center;
      text-decoration: none;
      color: green;
      font-size: 14px;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <form method="post" action="">
    <h2>Create Account</h2>

    <label>Username</label>
    <input type="text" name="username">

    <label>Password</label>
    <input type="password" name="password">

    <input type="submit" value="Sign Up">

    <?php if ($error != "") : ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success != "") : ?>
      <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <a href="login.php" class="login-link">Go to Login</a>
  </form>
</body>
</html>