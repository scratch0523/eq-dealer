<?php

class Auth{
    public function auth(){
        $authAdminValue = isset($_COOKIE['dealer_id']) ? $_COOKIE['dealer_id'] : null;

        if ($authAdminValue) {
            http_response_code(200);
            echo json_encode(array('status' => 'Authentication_successful', 'dealer_id' => $authAdminValue));
        } else {
            http_response_code(500);
            echo json_encode('Not_authenticated');
        }
    }
}

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['check_cookie'])){
        $auth->auth();
    }
}

?>