<?php
// Lesson 1: PHP can create sessions (used to remember who is logged in)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ---------- small helpers ---------- */

// Makes user text safe to print in HTML
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, "UTF-8");
}

function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

/* ---------- access control (Lesson 1: control who can open a page) ---------- */

function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

function require_login() {
    if (!is_logged_in()) {
        redirect("auth/login.php");
    }
}

/* ---------- one-time messages ("Customer saved!") ---------- */

function set_flash($type, $message) {
    $_SESSION["flash"] = ["type" => $type, "message" => $message];
}

function get_flash() {
    if (isset($_SESSION["flash"])) {
        $flash = $_SESSION["flash"];
        unset($_SESSION["flash"]);
        return $flash;
    }
    return null;
}

/* ---------- customer helpers ---------- */

function customer_types() {
    return ["Buyer", "Seller", "Both"];
}

function customer_statuses() {
    return ["Active", "Inactive"];
}

// Lesson 2: SWITCH statement with break
function type_badge($type) {
    switch ($type) {
        case "Buyer":
            $class = "bg-info text-dark";
            break;
        case "Seller":
            $class = "bg-warning text-dark";
            break;
        case "Both":
            $class = "bg-success";
            break;
        default:
            $class = "bg-secondary";
    }
    return '<span class="badge ' . $class . '">' . e($type) . '</span>';
}

// Lesson 2: TERNARY operator  (condition) ? value_if_true : value_if_false
function status_badge($status) {
    $class = ($status == "Active") ? "bg-success" : "bg-secondary";
    return '<span class="badge ' . $class . '">' . e($status) . '</span>';
}

// Lesson 2: if / elseif / else  -> returns an array of error messages
function validate_customer($c) {
    $errors = [];

    if ($c["full_name"] == "") {
        $errors[] = "Full name is required.";
    } elseif (strlen($c["full_name"]) < 3) {
        $errors[] = "Full name must be at least 3 characters.";
    }

    if ($c["phone"] == "") {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9+\-\s]{7,20}$/', $c["phone"])) {
        $errors[] = "Phone number may only contain numbers, spaces, + and -.";
    }

    if ($c["email"] != "" && !filter_var($c["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email address is not valid.";
    }

    if (!in_array($c["customer_type"], customer_types())) {
        $errors[] = "Please choose a customer type.";
    }

    if (!in_array($c["status"], customer_statuses())) {
        $errors[] = "Please choose a status.";
    }

    return $errors;
}

// Lesson 3: DO...WHILE runs the block ONCE first, then repeats while the condition is true.
// Perfect for "make a code, and if it already exists, make another one".
function generate_customer_code($conn) {
    do {
        $code = "STG-" . rand(10000, 99999);

        $stmt = $conn->prepare("SELECT id FROM customers WHERE customer_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $stmt->store_result();
        $already_exists = ($stmt->num_rows > 0);
        $stmt->close();
    } while ($already_exists);

    return $code;
}
