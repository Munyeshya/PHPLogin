<?php
session_start();

if (isset($_SESSION["username"])) {
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Programming Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .course-img{
      height: 160px;
      object-fit: cover;
    }
  </style>
</head>
<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">CodeCourses</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
          <li class="nav-item"><a class="nav-link" href="#footer">Contact</a></li>
          <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <header class="py-4 bg-white border-bottom text-center">
    <div class="container">
      <h1 class="h3 fw-bold mb-1">Our Courses</h1>
      <p class="text-muted mb-0">Welcome, <?php echo $_SESSION["username"]; ?></p>
    </div>
  </header>

  <main class="py-4" id="courses">
    <div class="container">
      <div class="row g-3">

        <div class="col-12 col-md-3">
          <div class="card h-100">
            <img class="card-img-top course-img" src="https://img.freepik.com/premium-photo/php-programming-code-abstract-technology-background_272306-152.jpg" alt="PHP">
            <div class="card-body">
              <h5 class="card-title">PHP Basics</h5>
              <p class="card-text">Learn variables, forms, GET/POST, and simple projects.</p>
              <a href="#" class="btn btn-success btn-sm">Learn more</a>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card h-100">
            <img class="card-img-top course-img" src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=900&q=60" alt="JavaScript">
            <div class="card-body">
              <h5 class="card-title">JavaScript</h5>
              <p class="card-text">Learn DOM, events, arrays, and build interactive pages.</p>
              <a href="#" class="btn btn-success btn-sm">Learn more</a>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card h-100">
            <img class="card-img-top course-img" src="https://deepsea-cdn.b-cdn.net/2024/06/uses-of-python.jpg.webp" alt="Python">
            <div class="card-body">
              <h5 class="card-title">Python</h5>
              <p class="card-text">Start with basics, loops, functions, and simple scripts.</p>
              <a href="#" class="btn btn-success btn-sm">Learn more</a>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card h-100">
            <img class="card-img-top course-img" src="https://ostraining.com/wp-content/uploads/coding/html5-css3-hd.jpg" alt="HTML & CSS">
            <div class="card-body">
              <h5 class="card-title">HTML & CSS</h5>
              <p class="card-text">Build pages with layout, colors, spacing, and responsive design.</p>
              <a href="#" class="btn btn-success btn-sm">Learn more</a>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card h-100">
            <img class="card-img-top course-img" src="https://t4.ftcdn.net/jpg/02/92/83/57/360_F_292835773_oImixQGFKLpOPnjfsbesHyqdjOk5hsxL.jpg" alt="Java">
            <div class="card-body">
              <h5 class="card-title">Java</h5>
              <p class="card-text">Learn OOP, classes, methods, and console applications.</p>
              <a href="#" class="btn btn-success btn-sm">Learn more</a>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card h-100">
            <img class="card-img-top course-img" src="https://img-c.udemycdn.com/course/480x270/14346_9972_8.jpg?w=3840&q=75" alt="C#">
            <div class="card-body">
              <h5 class="card-title">C#</h5>
              <p class="card-text">Learn syntax, OOP, and build simple desktop apps.</p>
              <a href="#" class="btn btn-success btn-sm">Learn more</a>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card h-100">
            <img class="card-img-top course-img" src="https://www.seedinfotech.com/wp-content/uploads/2023/01/C_Programming-1-1170x728.jpg" alt="C">
            <div class="card-body">
              <h5 class="card-title">C Programming</h5>
              <p class="card-text">Learn basics, memory, pointers, and small programs.</p>
              <a href="#" class="btn btn-success btn-sm">Learn more</a>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="card h-100">
            <img class="card-img-top course-img" src="https://intersog.com/wp-content/uploads/2017/02/c-plus-plus-1280x590.jpg" alt="C++">
            <div class="card-body">
              <h5 class="card-title">C++</h5>
              <p class="card-text">Learn OOP, STL basics, and beginner data structures.</p>
              <a href="#" class="btn btn-success btn-sm">Learn more</a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <footer class="bg-dark text-white py-3" id="footer">
    <div class="container text-center">
      <small>© <?php echo date("Y"); ?> CodeCourses — Beginner Bootstrap Practice</small>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
} else {
  header("Location: index.php");
  exit;
  }
?>
?>