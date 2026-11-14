<?php
require_once __DIR__ . "/includes/init.php";
require_login();

/* ---- count customers per type ---- */
// Lesson 3: associative array  (key => value)
$counts = ["Buyer" => 0, "Seller" => 0, "Both" => 0];

$result = $conn->query("SELECT customer_type, COUNT(*) AS total FROM customers GROUP BY customer_type");

// Lesson 3: WHILE loop - repeats for every row returned by the database
while ($row = $result->fetch_assoc()) {
    $counts[$row["customer_type"]] = $row["total"];
}

$total_customers = array_sum($counts);

/* ---- 5 newest customers ---- */
$recent = $conn->query("SELECT * FROM customers ORDER BY id DESC LIMIT 5");

$page_title = "Dashboard";
require __DIR__ . "/includes/header.php";
?>

<h3 class="mb-4">Dashboard</h3>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="glass p-3 h-100">
      <div class="text-secondary">Total customers</div>
      <div class="stat-number gradient-text"><?= $total_customers ?></div>
    </div>
  </div>

  <?php foreach ($counts as $type => $total): ?>
    <div class="col-6 col-md-3">
      <div class="glass p-3 h-100">
        <div class="text-secondary"><?= e($type) ?>s</div>
        <div class="stat-number"><?= $total ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="glass p-3">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Newest customers</h5>
    <a href="<?= BASE_URL ?>customers/add.php" class="btn btn-sm btn-gradient">+ Add customer</a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr><th>Code</th><th>Name</th><th>Phone</th><th>Type</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php while ($c = $recent->fetch_assoc()): ?>
          <tr>
            <td><?= e($c["customer_code"]) ?></td>
            <td><?= e($c["full_name"]) ?></td>
            <td><?= e($c["phone"]) ?></td>
            <td><?= type_badge($c["customer_type"]) ?></td>
            <td><?= status_badge($c["status"]) ?></td>
          </tr>
        <?php endwhile; ?>

        <?php if ($total_customers == 0): ?>
          <tr><td colspan="5" class="text-center text-secondary">No customers yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . "/includes/footer.php"; ?>
