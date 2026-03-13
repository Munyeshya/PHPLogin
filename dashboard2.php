<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["username"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$message = "";
$error = "";

/* =========================
   LESSON CRUD
========================= */

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
  <title>Admin Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 text-slate-800">

  <nav class="bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="index.php" class="font-bold text-sm sm:text-base">CodeCourses Admin</a>
      <div class="flex items-center gap-2">
        <a href="index.php" class="px-3 py-1.5 text-sm border border-slate-600 rounded-lg hover:bg-slate-800">Home</a>
        <a href="logout.php" class="px-3 py-1.5 text-sm bg-amber-500 text-slate-900 rounded-lg hover:bg-amber-400">Logout</a>
      </div>
    </div>
  </nav>

  <div class="max-w-7xl mx-auto px-4 py-4">
    <div class="mb-4">
      <h1 class="text-xl font-bold">Dashboard</h1>
      <p class="text-sm text-slate-500">
        Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?> (<?php echo htmlspecialchars($_SESSION["role"]); ?>)
      </p>
    </div>

    <?php if ($message !== "") : ?>
      <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <?php if ($error !== "") : ?>
      <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Available Lessons</p>
        <h2 class="text-2xl font-bold mt-1"><?php echo $lessonCount; ?></h2>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Total Users</p>
        <h2 class="text-2xl font-bold mt-1"><?php echo $userCount; ?></h2>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Admins</p>
        <h2 class="text-2xl font-bold mt-1"><?php echo $adminCount; ?></h2>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Students</p>
        <h2 class="text-2xl font-bold mt-1"><?php echo $studentCount; ?></h2>
      </div>
    </div>

    <!-- Accordion -->
    <div class="space-y-4">

      <!-- Users Accordion -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <button type="button" onclick="toggleAccordion('usersAccordion')" class="w-full px-4 py-3 flex items-center justify-between text-left">
          <span class="font-semibold text-sm sm:text-base">Users Management</span>
          <span id="usersAccordionIcon" class="text-xl">+</span>
        </button>

        <div id="usersAccordion" class="hidden border-t border-slate-200 px-4 py-4">
          <div class="flex justify-end mb-3">
            <button onclick="openModal('addUserModal')" class="px-3 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
              Add User
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
              <thead class="bg-slate-900 text-white">
                <tr>
                  <th class="px-3 py-2 text-left">ID</th>
                  <th class="px-3 py-2 text-left">Username</th>
                  <th class="px-3 py-2 text-left">Password</th>
                  <th class="px-3 py-2 text-left">Role</th>
                  <th class="px-3 py-2 text-left">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <?php if ($users) : ?>
                  <?php foreach ($users as $user) : ?>
                    <tr class="border-t border-slate-200">
                      <td class="px-3 py-2"><?php echo $user["id"]; ?></td>
                      <td class="px-3 py-2"><?php echo htmlspecialchars($user["username"]); ?></td>
                      <td class="px-3 py-2"><?php echo htmlspecialchars($user["password"]); ?></td>
                      <td class="px-3 py-2"><?php echo htmlspecialchars($user["role"]); ?></td>
                      <td class="px-3 py-2">
                        <div class="flex gap-2 whitespace-nowrap">
                          <button onclick="openModal('editUserModal<?php echo $user['id']; ?>')" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Edit
                          </button>
                          <a href="dashboard.php?delete_user=<?php echo $user["id"]; ?>" onclick="return confirm('Delete this user?')" class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Delete
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else : ?>
                  <tr>
                    <td colspan="5" class="px-3 py-3 text-center text-slate-500">No users found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Lessons Accordion -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <button type="button" onclick="toggleAccordion('lessonsAccordion')" class="w-full px-4 py-3 flex items-center justify-between text-left">
          <span class="font-semibold text-sm sm:text-base">Lessons Management</span>
          <span id="lessonsAccordionIcon" class="text-xl">+</span>
        </button>

        <div id="lessonsAccordion" class="hidden border-t border-slate-200 px-4 py-4">
          <div class="flex justify-end mb-3">
            <button onclick="openModal('addLessonModal')" class="px-3 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
              Add Lesson
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
              <thead class="bg-slate-900 text-white">
                <tr>
                  <th class="px-3 py-2 text-left">ID</th>
                  <th class="px-3 py-2 text-left">Image</th>
                  <th class="px-3 py-2 text-left">Title</th>
                  <th class="px-3 py-2 text-left">Description</th>
                  <th class="px-3 py-2 text-left">Video</th>
                  <th class="px-3 py-2 text-left">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <?php if ($lessons) : ?>
                  <?php foreach ($lessons as $lesson) : ?>
                    <tr class="border-t border-slate-200">
                      <td class="px-3 py-2"><?php echo $lesson["id"]; ?></td>
                      <td class="px-3 py-2">
                        <img src="<?php echo htmlspecialchars($lesson["image_url"]); ?>" alt="" class="w-20 h-12 object-cover rounded-lg">
                      </td>
                      <td class="px-3 py-2"><?php echo htmlspecialchars($lesson["title"]); ?></td>
                      <td class="px-3 py-2"><?php echo htmlspecialchars($lesson["description"]); ?></td>
                      <td class="px-3 py-2">
                        <iframe src="<?php echo htmlspecialchars($lesson["video_url"]); ?>" class="w-28 h-16 rounded-lg"></iframe>
                      </td>
                      <td class="px-3 py-2">
                        <div class="flex gap-2 whitespace-nowrap">
                          <button onclick="openModal('editLessonModal<?php echo $lesson['id']; ?>')" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Edit
                          </button>
                          <a href="dashboard.php?delete_lesson=<?php echo $lesson["id"]; ?>" onclick="return confirm('Delete this lesson?')" class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Delete
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else : ?>
                  <tr>
                    <td colspan="6" class="px-3 py-3 text-center text-slate-500">No lessons found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Add User Modal -->
  <div id="addUserModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4 z-50">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl">
      <form method="post">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
          <h3 class="font-semibold">Add User</h3>
          <button type="button" onclick="closeModal('addUserModal')" class="text-slate-500 hover:text-slate-800">✕</button>
        </div>
        <div class="p-4 space-y-3">
          <div>
            <label class="block text-xs text-slate-600 mb-1">Username</label>
            <input type="text" name="username" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
          </div>
          <div>
            <label class="block text-xs text-slate-600 mb-1">Password</label>
            <input type="text" name="password" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
          </div>
          <div>
            <label class="block text-xs text-slate-600 mb-1">Role</label>
            <select name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
              <option value="student">student</option>
              <option value="admin">admin</option>
            </select>
          </div>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 flex justify-end gap-2">
          <button type="button" onclick="closeModal('addUserModal')" class="px-3 py-2 text-sm border border-slate-300 rounded-lg">Close</button>
          <button type="submit" name="add_user" class="px-3 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Save User</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit User Modals -->
  <?php foreach ($users as $user) : ?>
    <div id="editUserModal<?php echo $user['id']; ?>" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4 z-50">
      <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl">
        <form method="post">
          <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
          <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-semibold">Edit User</h3>
            <button type="button" onclick="closeModal('editUserModal<?php echo $user['id']; ?>')" class="text-slate-500 hover:text-slate-800">✕</button>
          </div>
          <div class="p-4 space-y-3">
            <div>
              <label class="block text-xs text-slate-600 mb-1">Username</label>
              <input type="text" name="username" value="<?php echo htmlspecialchars($user["username"]); ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Password</label>
              <input type="text" name="password" value="<?php echo htmlspecialchars($user["password"]); ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Role</label>
              <select name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                <option value="student" <?php echo $user["role"] === "student" ? "selected" : ""; ?>>student</option>
                <option value="admin" <?php echo $user["role"] === "admin" ? "selected" : ""; ?>>admin</option>
              </select>
            </div>
          </div>
          <div class="px-4 py-3 border-t border-slate-200 flex justify-end gap-2">
            <button type="button" onclick="closeModal('editUserModal<?php echo $user['id']; ?>')" class="px-3 py-2 text-sm border border-slate-300 rounded-lg">Close</button>
            <button type="submit" name="update_user" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update User</button>
          </div>
        </form>
      </div>
    </div>
  <?php endforeach; ?>

  <!-- Add Lesson Modal -->
  <div id="addLessonModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4 z-50">
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-xl">
      <form method="post">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
          <h3 class="font-semibold">Add Lesson</h3>
          <button type="button" onclick="closeModal('addLessonModal')" class="text-slate-500 hover:text-slate-800">✕</button>
        </div>
        <div class="p-4 grid grid-cols-1 gap-3">
          <div>
            <label class="block text-xs text-slate-600 mb-1">Title</label>
            <input type="text" name="title" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
          </div>
          <div>
            <label class="block text-xs text-slate-600 mb-1">Description</label>
            <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500"></textarea>
          </div>
          <div>
            <label class="block text-xs text-slate-600 mb-1">Image URL</label>
            <input type="text" name="image_url" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
          </div>
          <div>
            <label class="block text-xs text-slate-600 mb-1">Video URL</label>
            <input type="text" name="video_url" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
          </div>
          <div>
            <label class="block text-xs text-slate-600 mb-1">Short Note</label>
            <textarea name="short_note" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500"></textarea>
          </div>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 flex justify-end gap-2">
          <button type="button" onclick="closeModal('addLessonModal')" class="px-3 py-2 text-sm border border-slate-300 rounded-lg">Close</button>
          <button type="submit" name="add_lesson" class="px-3 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Save Lesson</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Lesson Modals -->
  <?php foreach ($lessons as $lesson) : ?>
    <div id="editLessonModal<?php echo $lesson['id']; ?>" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4 z-50">
      <div class="w-full max-w-xl bg-white rounded-2xl shadow-xl">
        <form method="post">
          <input type="hidden" name="lesson_id" value="<?php echo $lesson["id"]; ?>">
          <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-semibold">Edit Lesson</h3>
            <button type="button" onclick="closeModal('editLessonModal<?php echo $lesson['id']; ?>')" class="text-slate-500 hover:text-slate-800">✕</button>
          </div>
          <div class="p-4 grid grid-cols-1 gap-3">
            <div>
              <label class="block text-xs text-slate-600 mb-1">Title</label>
              <input type="text" name="title" value="<?php echo htmlspecialchars($lesson["title"]); ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Description</label>
              <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500"><?php echo htmlspecialchars($lesson["description"]); ?></textarea>
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Image URL</label>
              <input type="text" name="image_url" value="<?php echo htmlspecialchars($lesson["image_url"]); ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Video URL</label>
              <input type="text" name="video_url" value="<?php echo htmlspecialchars($lesson["video_url"]); ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
            </div>
            <div>
              <label class="block text-xs text-slate-600 mb-1">Short Note</label>
              <textarea name="short_note" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500"><?php echo htmlspecialchars($lesson["short_note"]); ?></textarea>
            </div>
          </div>
          <div class="px-4 py-3 border-t border-slate-200 flex justify-end gap-2">
            <button type="button" onclick="closeModal('editLessonModal<?php echo $lesson['id']; ?>')" class="px-3 py-2 text-sm border border-slate-300 rounded-lg">Close</button>
            <button type="submit" name="update_lesson" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Lesson</button>
          </div>
        </form>
      </div>
    </div>
  <?php endforeach; ?>

  <script>
    function toggleAccordion(id) {
      const content = document.getElementById(id);
      const icon = document.getElementById(id + "Icon");
      if (content.classList.contains("hidden")) {
        content.classList.remove("hidden");
        icon.textContent = "−";
      } else {
        content.classList.add("hidden");
        icon.textContent = "+";
      }
    }

    function openModal(id) {
      const modal = document.getElementById(id);
      modal.classList.remove("hidden");
      modal.classList.add("flex");
    }

    function closeModal(id) {
      const modal = document.getElementById(id);
      modal.classList.add("hidden");
      modal.classList.remove("flex");
    }

    window.addEventListener("click", function (e) {
      document.querySelectorAll('[id$="Modal"]').forEach(function(modal) {
        if (e.target === modal) {
          modal.classList.add("hidden");
          modal.classList.remove("flex");
        }
      });
    });
  </script>
</body>
</html>