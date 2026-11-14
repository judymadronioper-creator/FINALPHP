<?php

require_once __DIR__ . "/../includes/init.php";
require_login();

/* ---- Search and pagination settings ---- */
$per_page = 5;

// Lesson 2: Ternary Operator
$search = isset($_GET["q"]) ? trim($_GET["q"]) : "";
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;

if ($page < 1) {
    $page = 1;
}

$like = "%" . $search . "%";

// Count matching customers
$count_sql = "SELECT COUNT(*) FROM customers
              WHERE full_name LIKE ?
              OR email LIKE ?
              OR phone LIKE ?
              OR customer_code LIKE ?";

$stmt = $conn->prepare($count_sql);
$stmt->bind_param("ssss", $like, $like, $like, $like);
$stmt->execute();
$stmt->bind_result($total_rows);
$stmt->fetch();
$stmt->close();

// Calculate total pages
$total_pages = max(1, (int)ceil($total_rows / $per_page));

if ($page > $total_pages) {
    $page = $total_pages;
}

$offset = ($page - 1) * $per_page;

// Get customer records
$list_sql = "SELECT * FROM customers
             WHERE full_name LIKE ?
             OR email LIKE ?
             OR phone LIKE ?
             OR customer_code LIKE ?
             ORDER BY id DESC
             LIMIT ? OFFSET ?";

$stmt = $conn->prepare($list_sql);
$stmt->bind_param(
    "ssssii",
    $like,
    $like,
    $like,
    $like,
    $per_page,
    $offset
);

$stmt->execute();
$result = $stmt->get_result();

// Page title
$page_title = "Customers";

// Load header
require __DIR__ . "/../includes/header.php";

?>

<!-- Customer Page Heading and Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

    <div>
        <h3 class="mb-1">Customers</h3>
        <p class="text-secondary mb-0">
            Manage your motorcycle buyers and sellers.
        </p>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">

        <!-- Search Form -->
        <form method="GET" class="d-flex flex-wrap gap-2">

            <input
                type="text"
                name="q"
                value="<?= e($search) ?>"
                class="form-control"
                placeholder="Search customers..."
                style="width: 260px; max-width: 100%;"
            >

            <button type="submit" class="btn btn-outline-light">
                Search
            </button>

            <a
                href="<?= BASE_URL ?>customers/index.php"
                class="btn btn-secondary"
            >
                Clear
            </a>

        </form>

        <!-- Add Customer Button -->
        <a
            href="<?= BASE_URL ?>customers/add.php"
            class="btn btn-gradient"
        >
            + Add Customer
        </a>

    </div>

</div>


<!-- Customer Records Card -->
<div class="glass p-3">

    <!-- Table Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">

        <div>
            <h5 class="mb-1">Customer Records</h5>
            <small class="text-secondary">
                List of registered customers
            </small>
        </div>

        <span class="badge bg-primary rounded-pill px-3 py-2">
            <?= (int)$total_rows ?> Customers
        </span>

    </div>


    <!-- Customer Table -->
    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($c = $result->fetch_assoc()): ?>

                    <tr>

                        <td class="fw-semibold">
                            <?= e($c["customer_code"]) ?>
                        </td>

                        <td>
                            <?= e($c["full_name"]) ?>
                        </td>

                        <td>
                            <?= e($c["phone"]) ?>
                        </td>

                        <td>
                            <?= ($c["email"] != "")
                                ? e($c["email"])
                                : "—" ?>
                        </td>

                        <td>
                            <?= type_badge($c["customer_type"]) ?>
                        </td>

                        <td>
                            <?= status_badge($c["status"]) ?>
                        </td>

                        <td class="text-end text-nowrap">

                            <!-- Edit Customer -->
                            <a
                                href="<?= BASE_URL ?>customers/edit.php?id=<?= (int)$c["id"] ?>"
                                class="btn btn-sm btn-outline-info"
                            >
                                Edit
                            </a>

                            <!-- Delete Customer -->
                            <form
                                method="POST"
                                action="<?= BASE_URL ?>customers/delete.php"
                                class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this customer?');"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int)$c["id"] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                >
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endwhile; ?>


                <!-- No Customer Records -->
                <?php if ($total_rows == 0): ?>

                    <tr>
                        <td colspan="7" class="text-center text-secondary py-5">

                            <h6 class="mb-1">No Customers Found</h6>

                            <small>
                                Try another search or add a new customer.
                            </small>

                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>

        <nav class="mt-4">

            <ul class="pagination justify-content-center mb-0">

                <!-- Previous Button -->
                <li class="page-item <?= ($page <= 1) ? "disabled" : "" ?>">

                    <a
                        class="page-link"
                        href="?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>"
                    >
                        Previous
                    </a>

                </li>

                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>

                    <li class="page-item <?= ($i == $page) ? "active" : "" ?>">

                        <a
                            class="page-link"
                            href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"
                        >
                            <?= $i ?>
                        </a>

                    </li>

                <?php endfor; ?>

                <!-- Next Button -->
                <li class="page-item <?= ($page >= $total_pages) ? "disabled" : "" ?>">

                    <a
                        class="page-link"
                        href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>"
                    >
                        Next
                    </a>

                </li>

            </ul>

        </nav>

    <?php endif; ?>

</div>


<!-- Page Information -->
<p class="text-secondary small mt-3">

    Showing page <?= $page ?>
    of <?= $total_pages ?>
    (<?= $total_rows ?> customers)

</p>


<?php

// Close database statement
$stmt->close();

// Load footer
require __DIR__ . "/../includes/footer.php";

?>