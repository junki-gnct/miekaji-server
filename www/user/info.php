<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();

    $token = $_GET['token'];
    $uid = $_GET['ID'];

    if(empty($token) || empty($uid)) {
        header("HTTP/1.1 400 Bad Request");
        $r_mgr->returnResponse(array(
            "ID"=>-1,
            "name"=>null,
            "icon_id"=>null
        ));
        exit;
    }

    include_once __DIR__ . "/../utils/auth.php";
    $auth = new AuthUtil();

    if(!$auth->isTokenAvailable($token)){
        header("HTTP/1.1 400 Bad Request");
        $r_mgr->returnResponse(array(
            "ID"=>-1,
            "name"=>null,
            "icon_id"=>null
        ));
        exit;
    }

    include_once __DIR__ . "/../utils/profile.php";
    $prof = new ProfileUtil();
    
    $r_mgr->returnResponse($prof->getProfileByUID($uid));



?>