<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();


    $json_obj = json_decode(file_get_contents("php://input"));
    if($json_obj == NULL) {
        $r_mgr->returnBadRequest("You need to POST json to this endpoint.");
        exit;
    }

    $token = $json_obj->{"token"};

    if(empty($token)) {
        $r_mgr->returnBadRequest("Not enough arguments.");
        exit;
    }

    include_once __DIR__ . '/../utils/auth.php';
    $auth_u = new AuthUtil();

    $isAvailable = $auth_u->isTokenAvailable($token);

    if($isAvailable) {
        $auth_u->logout($token);
        $r_mgr->returnOK("Successfully logged out.");
    } else {
        $r_mgr->returnBadRequest("Your token is not available.");
    }


?>