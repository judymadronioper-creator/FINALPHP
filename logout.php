<?php
require_once __DIR__ . "/../includes/init.php";

session_unset();
session_destroy();

session_start();   // start a fresh session just to carry the message
set_flash("success", "You have been logged out.");
redirect("auth/login.php");
