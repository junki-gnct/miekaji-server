<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();

    $token = $_GET['token'];

    if(empty($token)) {
        header("HTTP/1.1 400 Bad Request");
        $r_mgr->returnResponse(array("users"=>array()));
        exit;
    }

    include_once __DIR__ . "/../utils/auth.php";
    $auth = new AuthUtil();

    if(!$auth->isTokenAvailable($token)){
        header("HTTP/1.1 400 Bad Request");
        $r_mgr->returnResponse(array("users"=>array()));
        exit;
    }

    include_once __DIR__ . "/../utils/friend.php";
    $friend = new FriendUtil();
    
    $r_mgr->returnResponse($friend->getFriends($token));
