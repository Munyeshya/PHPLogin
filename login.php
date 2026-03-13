<?php
session_start();
require_once "db.php";

if (isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $error = "Username and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            header("Location: index.php");
            exit;
        } else {
            $error = "Wrong username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/1055/1055687.png">
  <title>Login</title>

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
      color: red;
      border: 2px dotted red;
      padding: 8px;
      text-align: center;
      margin-top: 10px;
    }

    .create-link {
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
    <h2>Login</h2>

    <label>Username</label>
    <input type="text" name="username">

    <label>Password</label>
    <input type="password" name="password">

    <input type="submit" value="Login">

    <?php if ($error != "") : ?>
      <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <a href="signup.php" class="create-link">Create Account</a>
  </form>
</body>
</html>