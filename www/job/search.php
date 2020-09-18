<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();

    $token = $_GET['token'];
    $id = $_GET['id'];

    if(empty($token) || empty($id)) {
        header("HTTP/1.1 400 Bad Request");
        $r_mgr->returnResponse(array("histories"=>array()));
        exit;
    }

    include_once __DIR__ . "/../utils/auth.php";
    $auth = new AuthUtil();

    if(!$auth->isTokenAvailable($token)){
        header("HTTP/1.1 400 Bad Request");
        $r_mgr->returnResponse(array("histories"=>array()));
        exit;
    }

    include_once __DIR__ . "/../utils/job.php";
    $job = new JobUtil();

    $r_mgr->returnResponse(array("histories"=>$job->getJobHistoryByUID($id)));