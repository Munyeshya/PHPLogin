<?php
session_start();

if (!isset($_SESSION["username"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h3 mb-3">Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>.</p>
        <p>Your role is: <strong><?php echo htmlspecialchars($_SESSION["role"]); ?></strong></p>
        <a href="index.php" class="btn btn-success">Back Home</a>
      </div>
    </div>
  </div>
</body>
</html>