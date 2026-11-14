<?php
// Set $page_title BEFORE including this file.
$flash = get_flash();
$path  = $_SERVER["PHP_SELF"];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(isset($page_title) ? $page_title : "Welcome") ?> | <?= SITE_NAME ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-md glass-nav sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold gradient-text" href="<?= BASE_URL ?>index.php">🏍 <?= SITE_NAME ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <?php if (is_logged_in()): ?>
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link <?= (strpos($path, "dashboard.php") !== false) ? "active" : "" ?>" href="<?= BASE_URL ?>dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= (strpos($path, "/customers/") !== false) ? "active" : "" ?>" href="<?= BASE_URL ?>customers/index.php">Customers</a>
          </li>
        </ul>
        <span class="navbar-text me-3">Hi, <?= e($_SESSION["full_name"]) ?></span>
        <a class="btn btn-sm btn-outline-light" href="<?= BASE_URL ?>auth/logout.php">Logout</a>
      <?php else: ?>
        <div class="ms-auto d-flex gap-2">
          <a class="btn btn-sm btn-outline-light" href="<?= BASE_URL ?>auth/login.php">Login</a>
          <a class="btn btn-sm btn-gradient" href="<?= BASE_URL ?>auth/register.php">Register</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="container py-4">
<?php if ($flash): ?>
  <div class="alert alert-<?= e($flash["type"]) ?> alert-dismissible fade show">
    <?= e($flash["message"]) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
