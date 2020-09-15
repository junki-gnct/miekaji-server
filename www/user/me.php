<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        $token = $_GET['token'];

        if(empty($token)) {
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
        
        $r_mgr->returnResponse($prof->getProfile($token));
    } else if($_SERVER["REQUEST_METHOD"] == "POST") {
        $json_obj = json_decode(file_get_contents("php://input"));
        $token = $json_obj->{"token"};
        $name = $json_obj->{"name"};

        if(empty($token) || empty($name)) {
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

        include_once __DIR__ . "/../utils/profile.php";
        $prof = new ProfileUtil();

        $prof->updateProfile($token, $name);

        $r_mgr->returnOK("Success.");
    }



?>