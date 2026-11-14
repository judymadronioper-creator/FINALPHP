<?php
require_once __DIR__ . "/../includes/init.php";

if (is_logged_in()) {
    redirect("dashboard.php");
}

$errors   = [];
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($username == "" || $password == "") {
        $errors[] = "Please enter your username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        // password_verify() compares the typed password with the encrypted one
        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"]   = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            redirect("dashboard.php");
        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}

$page_title = "Login";
require __DIR__ . "/../includes/header.php";
?>

<div class="auth-wrap">
  <div class="glass p-4">
    <h4 class="mb-1 gradient-text">Welcome back</h4>
    <p class="text-secondary">Login to your account.</p>

    <?php if (count($errors) > 0): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
          <div><?= e($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label class="form-label">Username or email</label>
        <input type="text" name="username" class="form-control" value="<?= e($username) ?>" autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control">
      </div>
      <button type="submit" class="btn btn-gradient w-100">Login</button>
    </form>

    <p class="text-center mt-3 mb-0">No account yet? <a href="register.php">Register</a></p>
  </div>
</div>

<?php require __DIR__ . "/../includes/footer.php"; ?>
