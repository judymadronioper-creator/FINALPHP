<?php
require_once __DIR__ . "/includes/init.php";
require_login();

/* ---- Count customers by type ---- */
$counts = ["Buyer" => 0, "Seller" => 0, "Both" => 0];
$result = $conn->query("SELECT customer_type, COUNT(*) AS total FROM customers GROUP BY customer_type");

while ($row = $result->fetch_assoc()) {
    if (isset($counts[$row["customer_type"]])) {
        $counts[$row["customer_type"]] = (int)$row["total"];
    }
}

$total_customers = array_sum($counts);

/* ---- Get the 5 newest customers ---- */
$recent = $conn->query("SELECT * FROM customers ORDER BY id DESC LIMIT 5");

$page_title = "Dashboard";
require __DIR__ . "/includes/header.php";
?>

<section class="dashboard-heading">
  <div>
    <div class="dashboard-eyebrow">Serrano Trading Garage</div>
    <h2>Dashboard Overview</h2>
    <p class="text-secondary mb-0">Here's a quick look at your customer records.</p>
  </div>
  <a href="<?= BASE_URL ?>customers/add.php" class="btn btn-gradient">
    + Add Customer
  </a>
</section>

<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="glass dashboard-stat h-100" style="--stat-glow: rgba(118,87,255,.22);">
      <div class="dashboard-stat-label">Total Customers</div>
      <div class="dashboard-stat-value gradient-text"><?= number_format($total_customers) ?></div>
      <div class="dashboard-stat-note">All registered records</div>
    </div>
  </div>

  <?php foreach ($counts as $type => $total): ?>
    <div class="col-6 col-xl-3">
      <div class="glass dashboard-stat h-100">
        <div class="dashboard-stat-label"><?= e($type) ?>s</div>
        <div class="dashboard-stat-value"><?= number_format($total) ?></div>
        <div class="dashboard-stat-note">Registered <?= strtolower(e($type)) ?> records</div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<section class="glass dashboard-table-card">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
      <div class="dashboard-eyebrow mb-1">Latest activity</div>
      <h5 class="dashboard-section-title">Recently Added Customers</h5>
      <p class="text-secondary small mb-0">The five most recently registered customers.</p>
    </div>
    <a href="<?= BASE_URL ?>customers/index.php" class="btn btn-outline-light btn-sm">
      View All Customers
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>Customer Code</th>
          <th>Customer Name</th>
          <th>Phone Number</th>
          <th>Customer Type</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($c = $recent->fetch_assoc()): ?>
          <tr>
            <td><?= e($c["customer_code"]) ?></td>
            <td class="fw-semibold"><?= e($c["full_name"]) ?></td>
            <td><?= e($c["phone"]) ?></td>
            <td><?= type_badge($c["customer_type"]) ?></td>
            <td><?= status_badge($c["status"]) ?></td>
          </tr>
        <?php endwhile; ?>

        <?php if ($total_customers === 0): ?>
          <tr>
            <td colspan="5" class="text-center py-5">
              <div class="fw-semibold mb-1">No customers yet</div>
              <div class="text-secondary small">Add your first customer to see records here.</div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
