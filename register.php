<?php
require_once __DIR__ . "/../includes/init.php";

if (is_logged_in()) {
    redirect("dashboard.php");
}

$errors    = [];
$full_name = "";
$email     = "";
$username  = "";

// Runs only when the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name        = trim($_POST["full_name"]);
    $email            = trim($_POST["email"]);
    $username         = trim($_POST["username"]);
    $password         = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Lesson 2: if / elseif / else (validation)
    if ($full_name == "" || $email == "" || $username == "" || $password == "") {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        // Is the username or email already taken?
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "That username or email is already registered.";
        } else {
            // Lesson 1: PHP can encrypt data -> never store the plain password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO users (full_name, email, username, password) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $full_name, $email, $username, $hashed_password);

            if ($insert->execute()) {
                set_flash("success", "Account created! You can now log in.");
                redirect("auth/login.php");
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
        $stmt->close();
    }
}

$page_title = "Register";
require __DIR__ . "/../includes/header.php";
?>

<div class="auth-wrap">
  <div class="glass p-4">
    <h4 class="mb-1 gradient-text">Create your account</h4>
    <p class="text-secondary">Register to manage customers.</p>

    <?php if (count($errors) > 0): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label class="form-label">Full name</label>
        <input type="text" name="full_name" class="form-control" value="<?= e($full_name) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= e($email) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= e($username) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm password</label>
        <input type="password" name="confirm_password" class="form-control">
      </div>
      <button type="submit" class="btn btn-gradient w-100">Register</button>
    </form>

    <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php">Login</a></p>
  </div>
</div>

<?php require __DIR__ . "/../includes/footer.php"; ?>
