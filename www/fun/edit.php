<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();

    $json_obj = json_decode(file_get_contents("php://input"));
    $token = $json_obj->{"token"};
    $state = $json_obj->{"state"};

    if(empty($token) || empty($state)) {
        header("HTTP/1.1 400 Bad Request");
        $r_mgr->returnBadRequest("Not enough arguments.");
        exit;
    }

    include_once __DIR__ . "/../utils/auth.php";
    $auth = new AuthUtil();

    if(!$auth->isTokenAvailable($token)){
        header("HTTP/1.1 400 Bad Request");
        $r_mgr->returnBadRequest("Your token is not available.");
        exit;
    }

    include_once __DIR__ . "/../utils/fun.php";
    $fun = new FunUtil();
    if($fun->editGroupState($token, $state)) {
        $r_mgr->returnOK("Success.");
    } else {
        $r_mgr->returnBadRequest("You are not in the group.");
    }
