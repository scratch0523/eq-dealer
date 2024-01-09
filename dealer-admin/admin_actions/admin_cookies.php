<?php

$authAdminValue = isset($_COOKIE['auth_admin']) ? $_COOKIE['auth_admin'] : null;

if ($authAdminValue) {
    http_response_code(200);
    echo json_encode('Authentication_successful');
} else {
    http_response_code(500);
    echo json_encode('Not_authenticated');
}
?>