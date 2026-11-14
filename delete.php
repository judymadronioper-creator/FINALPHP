<?php
require_once __DIR__ . "/../includes/init.php";
require_login();

// Deleting is only allowed from the form's POST button (not by typing a URL)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = (int)$_POST["id"];

    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Lesson 2: ternary operator picks the message
    $message = ($stmt->affected_rows > 0) ? "Customer deleted." : "Customer not found.";
    set_flash(($stmt->affected_rows > 0) ? "success" : "danger", $message);
}

redirect("customers/index.php");
