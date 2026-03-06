<?php
session_start();

$correct_username = "admin";
$correct_password = "1234";
$error = "";

if (isset($_GET["username"]) && isset($_GET["password"])) {
  $username = $_GET["username"];
  $password = $_GET["password"];

  if ($password == "") {
    $error = "Password is required.";
  } elseif ($username == $correct_username && $password == $correct_password) {
    $_SESSION["username"] = $username;
    header("Location: courses.php");
    exit;
  } else {
    $error = "Wrong username or password.";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>

  <style>
    * {
      margin: 0;
      padding: 0;
    }

    body {
      font-family: Arial, sans-serif;
      background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb');
      background-size: cover;
    }

    form {
      width: 360px;
      background: white;
      border: 1px solid #ddd;
      border-radius: 12px;
      padding: 18px;
      margin-top: 10%;
      margin-left: 37%;
    }

    h2 {
      margin: 0 0 12px;
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
      box-sizing: border-box;
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
  <form method="get" action="">
    <h2>Login with GET</h2>

    <label>Username</label>
    <input type="text" name="username">

    <label>Password</label>
    <input type="password" name="password">

    <input type="submit" value="Login">

    <?php
    if ($error != "") {
      echo "<p>$error</p>";
    }
    ?>

    <a href="create-account.php" class="create-link">Create Account</a>
  </form>
</body>
</html>