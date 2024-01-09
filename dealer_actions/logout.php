
<?php
session_start();

$_SESSION = array();

session_destroy();

$dealer_id = $_COOKIE['dealer_id'] ?? null;
if ($dealer_id) {
    setcookie('dealer_id', '', time() - 3600, '/', '', false, true);
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Logout successfully']);
?>