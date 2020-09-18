<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();

    $json_obj = json_decode(file_get_contents("php://input"));
    $token = $json_obj->{"token"};
    $category = $json_obj->{"category"};
    $name = $json_obj->{"name"};
    $weight = $json_obj->{"weight"};
    $detail = $json_obj->{"detail"};

    if(empty($token) || empty($category) || (empty($name) && empty($weight) && empty($detail))) {
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

    include_once __DIR__ . "/../utils/job.php";
    $job = new JobUtil();

    $re = $job->editJobCategory($category, $name, $weight, $detail);

    if($re == 0) {
        $r_mgr->returnOK("Success.");
    } else if($re == -1) {
        $r_mgr->returnBadRequest("Category is not found.");
    }