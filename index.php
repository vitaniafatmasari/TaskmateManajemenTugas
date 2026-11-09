<?php
// TaskMate - index: arahkan sesuai status login
require_once __DIR__ . '/includes/auth.php';
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
