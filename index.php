<?php
session_start();
require_once "db.php";

$stmt = $pdo->query("SELECT * FROM lessons ORDER BY id DESC");
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isLoggedIn = isset($_SESSION["username"]);
$isAdmin = $isLoggedIn && isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/1055/1055687.png">
  <title>Programming Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    .course-img {
      height: 180px;
      object-fit: cover;
    }

    .video-frame {
      width: 100%;
      height: 350px;
      border: 0;
    }

    .hero-section {
      background: linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
        url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1400&q=80');
      background-size: cover;
      background-position: center;
      color: white;
      padding: 90px 0;
    }

    .teaser-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">CodeCourses</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#footer">Contact</a></li>

          <?php if (!$isLoggedIn) : ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
          <?php else : ?>
            <?php if ($isAdmin) : ?>
              <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <section class="hero-section text-center">
    <div class="container">
      <h1 class="display-5 fw-bold">Learn Programming Step by Step</h1>
      <p class="lead mb-4">Browse lessons freely and sign in to watch full videos.</p>

      <?php if ($isLoggedIn) : ?>
        <p class="mb-0">Welcome, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong></p>
      <?php else : ?>
        <a href="login.php" class="btn btn-success btn-lg me-2">Login</a>
        <a href="signup.php" class="btn btn-outline-light btn-lg">Create Account</a>
      <?php endif; ?>
    </div>
  </section>

  <header class="py-4 bg-white border-bottom text-center">
    <div class="container">
      <h2 class="h3 fw-bold mb-1">Our Lessons</h2>
      <p class="text-muted mb-0">Choose any lesson below</p>
    </div>
  </header>

  <main class="py-4" id="courses">
    <div class="container">
      <div class="row g-4">

        <?php foreach ($lessons as $lesson) : ?>
          <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
              <img
                class="card-img-top course-img"
                src="<?php echo htmlspecialchars($lesson["image_url"]); ?>"
                alt="<?php echo htmlspecialchars($lesson["title"]); ?>"
              >
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($lesson["title"]); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($lesson["description"]); ?></p>

                <?php if ($isLoggedIn) : ?>
                  <button
                    class="btn btn-success btn-sm d-inline-flex align-items-center gap-1 mt-auto"
                    data-bs-toggle="modal"
                    data-bs-target="#lessonModal<?php echo $lesson["id"]; ?>"
                  >
                    <i class="bi bi-play-circle-fill"></i>
                    <span>Watch Video</span>
                  </button>
                <?php else : ?>
                  <button
                    class="btn btn-secondary btn-sm d-inline-flex align-items-center gap-1 mt-auto"
                    data-bs-toggle="modal"
                    data-bs-target="#guestLessonModal<?php echo $lesson["id"]; ?>"
                  >
                    <i class="bi bi-lock-fill"></i>
                    <span>Watch Video</span>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </main>

  <footer class="bg-dark text-white py-3" id="footer">
    <div class="container text-center">
      <small>© <?php echo date("Y"); ?> <a href="index2.php">CodeCourses</a></small>
    </div>
  </footer>

  <?php if ($isLoggedIn) : ?>
    <?php foreach ($lessons as $lesson) : ?>
      <div class="modal fade" id="lessonModal<?php echo $lesson["id"]; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><?php echo htmlspecialchars($lesson["title"]); ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <iframe
                class="video-frame"
                src="<?php echo htmlspecialchars($lesson["video_url"]); ?>"
                allowfullscreen
              ></iframe>
              <p class="mt-3 mb-0"><?php echo htmlspecialchars($lesson["short_note"]); ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <?php foreach ($lessons as $lesson) : ?>
      <div class="modal fade" id="guestLessonModal<?php echo $lesson["id"]; ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><?php echo htmlspecialchars($lesson["title"]); ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <img
                src="<?php echo htmlspecialchars($lesson["image_url"]); ?>"
                alt="<?php echo htmlspecialchars($lesson["title"]); ?>"
                class="teaser-img"
              >

              <p class="mb-2"><strong>Lesson teaser:</strong></p>
              <p class="mb-2"><?php echo htmlspecialchars($lesson["description"]); ?></p>
              <p class="text-muted mb-3"><?php echo htmlspecialchars($lesson["short_note"]); ?></p>

              <div class="border-top pt-3">
                <p class="mb-3">Login or create an account to watch the full lesson video.</p>
                <a href="login.php" class="btn btn-success me-2">Login</a>
                <a href="signup.php" class="btn btn-outline-success">Create Account</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>