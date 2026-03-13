<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["username"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$message = "";
$error = "";

if (isset($_POST["add_lesson"])) {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $image_url = trim($_POST["image_url"] ?? "");
    $video_url = trim($_POST["video_url"] ?? "");
    $short_note = trim($_POST["short_note"] ?? "");

    if ($title === "" || $description === "" || $image_url === "" || $video_url === "" || $short_note === "") {
        $error = "All lesson fields are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO lessons (title, description, image_url, video_url, short_note) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $image_url, $video_url, $short_note]);
        $message = "Lesson added successfully.";
    }
}

if (isset($_POST["update_lesson"])) {
    $id = $_POST["lesson_id"] ?? "";
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $image_url = trim($_POST["image_url"] ?? "");
    $video_url = trim($_POST["video_url"] ?? "");
    $short_note = trim($_POST["short_note"] ?? "");

    if ($id === "" || $title === "" || $description === "" || $image_url === "" || $video_url === "" || $short_note === "") {
        $error = "All lesson fields are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE lessons SET title = ?, description = ?, image_url = ?, video_url = ?, short_note = ? WHERE id = ?");
        $stmt->execute([$title, $description, $image_url, $video_url, $short_note, $id]);
        $message = "Lesson updated successfully.";
    }
}

if (isset($_GET["delete_lesson"])) {
    $id = $_GET["delete_lesson"];
    $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard.php");
    exit;
}

/* =========================
   USER CRUD
========================= */

if (isset($_POST["add_user"])) {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $role = trim($_POST["role"] ?? "student");

    if ($username === "" || $password === "" || $role === "") {
        $error = "All user fields are required.";
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$username]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $error = "Username already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $role]);
            $message = "User added successfully.";
        }
    }
}

if (isset($_POST["update_user"])) {
    $id = $_POST["user_id"] ?? "";
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $role = trim($_POST["role"] ?? "");

    if ($id === "" || $username === "" || $password === "" || $role === "") {
        $error = "All user fields are required.";
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$username, $id]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $error = "Another user already uses that username.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $password, $role, $id]);
            $message = "User updated successfully.";
        }
    }
}

if (isset($_GET["delete_user"])) {
    $id = $_GET["delete_user"];

    if ($id == $_SESSION["user_id"]) {
        $error = "You cannot delete your own account while logged in.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php");
        exit;
    }
}

/* =========================
   FETCH DATA
========================= */

$lessonCount = $pdo->query("SELECT COUNT(*) FROM lessons")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$adminCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$studentCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();

$lessons = $pdo->query("SELECT * FROM lessons ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/1055/1055687.png">
  <title>Admin Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-size: 14px;
    }
    .summary-card,
    .section-card {
      border: 0;
      border-radius: 12px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    }
    .summary-card .card-body,
    .section-card .card-body {
      padding: 14px;
    }
    .table td, .table th {
      vertical-align: middle;
      font-size: 13px;
    }
    .preview-img {
      width: 80px;
      height: 50px;
      object-fit: cover;
      border-radius: 6px;
    }
    .preview-video {
      width: 120px;
      height: 70px;
      border: 0;
      border-radius: 6px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-2">
  <div class="container">
    <a href="index.php" class="navbar-brand fw-bold fs-6">CodeCourses Admin</a>
    <div class="ms-auto d-flex gap-2">
      <a href="index.php" class="btn btn-outline-light btn-sm">Home</a>
      <a href="logout.php" class="btn btn-warning btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-3">

  <div class="mb-3">
    <h1 class="h4 mb-1">Dashboard</h1>
    <p class="text-muted small mb-0">
      Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?> (<?php echo htmlspecialchars($_SESSION["role"]); ?>)
    </p>
  </div>

  <?php if ($message !== "") : ?>
    <div class="alert alert-success py-2 small"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>

  <?php if ($error !== "") : ?>
    <div class="alert alert-danger py-2 small"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
      <div class="card summary-card">
        <div class="card-body">
          <div class="text-muted small">Available Lessons</div>
          <div class="fs-4 fw-bold"><?php echo $lessonCount; ?></div>
        </div>
      </div>
    </div>

    <div class="col-6 col-lg-3">
      <div class="card summary-card">
        <div class="card-body">
          <div class="text-muted small">Total Users</div>
          <div class="fs-4 fw-bold"><?php echo $userCount; ?></div>
        </div>
      </div>
    </div>

    <div class="col-6 col-lg-3">
      <div class="card summary-card">
        <div class="card-body">
          <div class="text-muted small">Admins</div>
          <div class="fs-4 fw-bold"><?php echo $adminCount; ?></div>
        </div>
      </div>
    </div>

    <div class="col-6 col-lg-3">
      <div class="card summary-card">
        <div class="card-body">
          <div class="text-muted small">Students</div>
          <div class="fs-4 fw-bold"><?php echo $studentCount; ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="accordion" id="dashboardAccordion">

    <!-- USERS ACCORDION -->
    <div class="accordion-item section-card mb-3">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#usersWrap">
          Users Management
        </button>
      </h2>
      <div id="usersWrap" class="accordion-collapse collapse show" data-bs-parent="#dashboardAccordion">
        <div class="accordion-body">

          <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
              Add User
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Password</th>
                  <th>Role</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($users) : ?>
                  <?php foreach ($users as $user) : ?>
                    <tr>
                      <td><?php echo $user["id"]; ?></td>
                      <td><?php echo htmlspecialchars($user["username"]); ?></td>
                      <td><?php echo htmlspecialchars($user["password"]); ?></td>
                      <td><?php echo htmlspecialchars($user["role"]); ?></td>
                      <td class="text-nowrap">
                        <button
                          class="btn btn-primary btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#editUserModal<?php echo $user["id"]; ?>"
                        >
                          Edit
                        </button>
                        <a
                          href="dashboard.php?delete_user=<?php echo $user["id"]; ?>"
                          class="btn btn-danger btn-sm"
                          onclick="return confirm('Delete this user?')"
                        >
                          Delete
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else : ?>
                  <tr>
                    <td colspan="5" class="text-center">No users found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <!-- LESSONS ACCORDION -->
    <div class="accordion-item section-card">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lessonsWrap">
          Lessons Management
        </button>
      </h2>
      <div id="lessonsWrap" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
        <div class="accordion-body">

          <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addLessonModal">
              Add Lesson
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Image</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Video</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($lessons) : ?>
                  <?php foreach ($lessons as $lesson) : ?>
                    <tr>
                      <td><?php echo $lesson["id"]; ?></td>
                      <td>
                        <img src="<?php echo htmlspecialchars($lesson["image_url"]); ?>" class="preview-img" alt="">
                      </td>
                      <td><?php echo htmlspecialchars($lesson["title"]); ?></td>
                      <td><?php echo htmlspecialchars($lesson["description"]); ?></td>
                      <td>
                        <iframe
                          class="preview-video"
                          src="<?php echo htmlspecialchars($lesson["video_url"]); ?>"
                          allowfullscreen
                        ></iframe>
                      </td>
                      <td class="text-nowrap">
                        <button
                          class="btn btn-primary btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#editLessonModal<?php echo $lesson["id"]; ?>"
                        >
                          Edit
                        </button>
                        <a
                          href="dashboard.php?delete_lesson=<?php echo $lesson["id"]; ?>"
                          class="btn btn-danger btn-sm"
                          onclick="return confirm('Delete this lesson?')"
                        >
                          Delete
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else : ?>
                  <tr>
                    <td colspan="6" class="text-center">No lessons found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>


<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Username</label>
            <input type="text" name="username" class="form-control form-control-sm">
          </div>
          <div class="mb-2">
            <label class="form-label small">Password</label>
            <input type="text" name="password" class="form-control form-control-sm">
          </div>
          <div class="mb-2">
            <label class="form-label small">Role</label>
            <select name="role" class="form-select form-select-sm">
              <option value="student">student</option>
              <option value="admin">admin</option>
            </select>
          </div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="add_user" class="btn btn-success btn-sm">Save User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT USER MODALS -->
<?php foreach ($users as $user) : ?>
  <div class="modal fade" id="editUserModal<?php echo $user["id"]; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <form method="post">
          <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
          <div class="modal-header">
            <h5 class="modal-title">Edit User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-2">
              <label class="form-label small">Username</label>
              <input type="text" name="username" class="form-control form-control-sm" value="<?php echo htmlspecialchars($user["username"]); ?>">
            </div>
            <div class="mb-2">
              <label class="form-label small">Password</label>
              <input type="text" name="password" class="form-control form-control-sm" value="<?php echo htmlspecialchars($user["password"]); ?>">
            </div>
            <div class="mb-2">
              <label class="form-label small">Role</label>
              <select name="role" class="form-select form-select-sm">
                <option value="student" <?php echo $user["role"] === "student" ? "selected" : ""; ?>>student</option>
                <option value="admin" <?php echo $user["role"] === "admin" ? "selected" : ""; ?>>admin</option>
              </select>
            </div>
          </div>
          <div class="modal-footer py-2">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="update_user" class="btn btn-primary btn-sm">Update User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<!-- ADD LESSON MODAL -->
<div class="modal fade" id="addLessonModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Add Lesson</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Title</label>
            <input type="text" name="title" class="form-control form-control-sm">
          </div>
          <div class="mb-2">
            <label class="form-label small">Description</label>
            <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label small">Image URL</label>
            <input type="text" name="image_url" class="form-control form-control-sm">
          </div>
          <div class="mb-2">
            <label class="form-label small">Video URL</label>
            <input type="text" name="video_url" class="form-control form-control-sm">
          </div>
          <div class="mb-2">
            <label class="form-label small">Short Note</label>
            <textarea name="short_note" class="form-control form-control-sm" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="add_lesson" class="btn btn-success btn-sm">Save Lesson</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT LESSON MODALS -->
<?php foreach ($lessons as $lesson) : ?>
  <div class="modal fade" id="editLessonModal<?php echo $lesson["id"]; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="post">
          <input type="hidden" name="lesson_id" value="<?php echo $lesson["id"]; ?>">
          <div class="modal-header">
            <h5 class="modal-title">Edit Lesson</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-2">
              <label class="form-label small">Title</label>
              <input type="text" name="title" class="form-control form-control-sm" value="<?php echo htmlspecialchars($lesson["title"]); ?>">
            </div>
            <div class="mb-2">
              <label class="form-label small">Description</label>
              <textarea name="description" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($lesson["description"]); ?></textarea>
            </div>
            <div class="mb-2">
              <label class="form-label small">Image URL</label>
              <input type="text" name="image_url" class="form-control form-control-sm" value="<?php echo htmlspecialchars($lesson["image_url"]); ?>">
            </div>
            <div class="mb-2">
              <label class="form-label small">Video URL</label>
              <input type="text" name="video_url" class="form-control form-control-sm" value="<?php echo htmlspecialchars($lesson["video_url"]); ?>">
            </div>
            <div class="mb-2">
              <label class="form-label small">Short Note</label>
              <textarea name="short_note" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($lesson["short_note"]); ?></textarea>
            </div>
          </div>
          <div class="modal-footer py-2">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="update_lesson" class="btn btn-primary btn-sm">Update Lesson</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>