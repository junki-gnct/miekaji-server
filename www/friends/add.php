<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();

    $json_obj = json_decode(file_get_contents("php://input"));
    $token = $json_obj->{"token"};
    $id = $json_obj->{"id"};

    if(empty($token) || empty($id)) {
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

    include_once __DIR__ . "/../utils/friend.php";
    $friend = new FriendUtil();
    $res = $friend->addFriend($token, $id);

    if ($res == 0) {
        $r_mgr->returnOK("Success.");
    } else if($res == -1) {
        $r_mgr->returnError("Query failed.");
    } else if($res == -2) {
        $r_mgr->returnBadRequest("User not found.");
    } else if($res == -3) {
        $r_mgr->returnBadRequest("User is same.");
    } else if($res == -4) {
        $r_mgr->returnBadRequest("User is already in friends.");
    }


