<?php
require_once __DIR__ . "/../includes/init.php";
require_login();

// Empty starting values for the blank form
$customer = [
    "full_name"     => "",
    "phone"         => "",
    "email"         => "",
    "address"       => "",
    "customer_type" => "Buyer",
    "status"        => "Active"
];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Lesson 3: foreach loop with key => value (copy every posted field)
    foreach ($customer as $field => $old_value) {
        $customer[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : "";
    }

    $errors = validate_customer($customer);

    if (count($errors) == 0) {
        $customer_code = generate_customer_code($conn);   // uses do...while

        $stmt = $conn->prepare("INSERT INTO customers (customer_code, full_name, email, phone, address, customer_type, status)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss",
            $customer_code,
            $customer["full_name"],
            $customer["email"],
            $customer["phone"],
            $customer["address"],
            $customer["customer_type"],
            $customer["status"]
        );

        if ($stmt->execute()) {
            set_flash("success", "Customer saved. Code: " . $customer_code);
            redirect("customers/index.php");
        } else {
            $errors[] = "Could not save the customer. Please try again.";
        }
    }
}

$page_title   = "Add Customer";
$button_label = "Save customer";
require __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-3">Add customer</h3>
<?php require __DIR__ . "/_form.php"; ?>

<?php require __DIR__ . "/../includes/footer.php"; ?>
