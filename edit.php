<?php
require_once __DIR__ . "/../includes/init.php";
require_login();

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

/* ---- load the customer to edit ---- */
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$customer) {
    set_flash("danger", "Customer not found.");
    redirect("customers/index.php");
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Only these fields can be changed (the customer code stays the same)
    $editable = ["full_name", "phone", "email", "address", "customer_type", "status"];

    foreach ($editable as $field) {
        $customer[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : "";
    }

    $errors = validate_customer($customer);

    if (count($errors) == 0) {
        $stmt = $conn->prepare("UPDATE customers
                                SET full_name = ?, email = ?, phone = ?, address = ?, customer_type = ?, status = ?
                                WHERE id = ?");
        $stmt->bind_param("ssssssi",
            $customer["full_name"],
            $customer["email"],
            $customer["phone"],
            $customer["address"],
            $customer["customer_type"],
            $customer["status"],
            $id
        );

        if ($stmt->execute()) {
            set_flash("success", "Customer updated.");
            redirect("customers/index.php");
        } else {
            $errors[] = "Could not update the customer. Please try again.";
        }
    }
}

$page_title   = "Edit Customer";
$button_label = "Update customer";
require __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-1">Edit customer</h3>
<p class="text-secondary">Code: <?= e($customer["customer_code"]) ?></p>
<?php require __DIR__ . "/_form.php"; ?>

<?php require __DIR__ . "/../includes/footer.php"; ?>
