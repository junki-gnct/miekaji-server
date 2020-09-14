<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();


    $json_obj = json_decode(file_get_contents("php://input"));
    if($json_obj == NULL) {
        $r_mgr->returnBadRequest("You need to POST json to this endpoint.");
        exit;
    }

    $id = $json_obj->{"ID"};
    $pass = $json_obj->{"pass"};

    if(empty($id) || empty($pass)) {
        $r_mgr->returnBadRequest("Not enough arguments.");
        exit;
    }

    include_once __DIR__ . '/../utils/auth.php';
    $auth_u = new AuthUtil();

    $res = $auth_u->register($id, $pass);
    $token = $res[0];
    $expires = $res[1];

    if($token == NULL) {
        header("HTTP/1.1 400 Bad Request");
        $response = array(
            "status"=>400,
            "token"=>null,
            "expires"=>-1
        );
    } else {
        $response = array(
            "status"=>200,
            "token"=>$token,
            "expires"=>$expires
        );
    }


    $r_mgr->returnResponse($response);

?>