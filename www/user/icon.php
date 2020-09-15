<?php
    ini_set('error_reporting', E_ALL);
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();

    if($_SERVER["REQUEST_METHOD"] == "GET") {
        $token = $_GET['token'];
        $icon_id = $_GET['icon_id'];

        if(empty($token) || empty($icon_id)) {
            $r_mgr->returnBadRequest("Not enough arguments.");
            exit;
        }

        include_once __DIR__ . "/../utils/auth.php";
        $auth = new AuthUtil();

        if(!$auth->isTokenAvailable($token)){
            $r_mgr->returnBadRequest("Your token is not available.");
            exit;
        }

        $b64_icon = "";
        $link = $auth->connect();
        $query = mysqli_query($link, "SELECT icon_b64 FROM IconTable WHERE icon_id='" . mysqli_real_escape_string($link, $icon_id) . "'");
        while ($row = mysqli_fetch_assoc($query)) {
            $b64_icon = $row["icon_b64"];
            break;
        }
        mysqli_close($link);

        if($b64_icon == "") {
            header('Content-Type: image/png');
            $image = imagecreatefrompng(__DIR__ . "/../utils/default.png");
            imagepng($image);
            imagedestroy($image);
        } else {
            $mime = str_replace("data:", "", explode(";", $b64_icon)[0]);
            $data = str_replace("base64,", "", explode(";", $b64_icon)[1]);
    
            $img_d = base64_decode($data);
            $image = imagecreatefromstring($img_d);
    
            if($mime == "image/jpeg" || $mime == "image/jpg") {
                header('Content-Type: image/jpeg');
                imagejpeg($image);
            } else {
                header('Content-Type: image/png');
                imagepng($image);
            }
    
            imagedestroy($image);
        }
    } else if($_SERVER["REQUEST_METHOD"] == "POST") {
        $json_obj = json_decode(file_get_contents("php://input"));
        $token = $json_obj->{"token"};
        $b64_icon = $json_obj->{"b64_icon"};

        if(empty($token) || empty($b64_icon)) {
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

        $mime = str_replace("data:", "", explode(";", $b64_icon)[0]);
        $data = str_replace("base64,", "", explode(";", $b64_icon)[1]);
        if($mime != "image/jpeg" && $mime != "image/jpg" && $mime != "image/png") {
            $r_mgr->returnBadRequest("Unsupported image format.");
            exit;
        }

        $img_d = base64_decode($data);
        list($width, $height) = getimagesizefromstring($img_d);
        $image = imagecreatefromstring($img_d);
        $thumb = imagecreatetruecolor(400, 400);
        
        imagecopyresized($thumb, $image, 0, 0, 0, 0, 400, 400, $width, $height);
    
        ob_start();
        if($mime == "image/jpeg" || $mime == "image/jpg") {
            imagejpeg($thumb);
        } else {
            imagepng($thumb);
        }
        $b64 = explode(";", $b64_icon)[0] . ";base64," . base64_encode(ob_get_contents());
        ob_end_clean();
        imagedestroy($image);
        imagedestroy($thumb);

        include_once __DIR__ . "/../utils/profile.php";
        $prof = new ProfileUtil();
        $id = $prof->getIconID($token);
        if($id == "") {
            $id = $prof->generateIconID();
            $link = $prof->connect();
            mysqli_query($link, "INSERT INTO IconTable (icon_id, icon_b64) VALUES ('" . mysqli_real_escape_string($link, $id) . "', '" . mysqli_real_escape_string($link, $b64) . "');");
            mysqli_query($link, "UPDATE ProfileTable SET icon_id='" . mysqli_real_escape_string($link, $id) . "' WHERE unique_id=" . $prof->getProfile($token)["ID"] . ";");
            mysqli_close($link);
        } else {
            $link = $prof->connect();
            mysqli_query($link, "UPDATE IconTable SET icon_b64='" . mysqli_real_escape_string($link, $b64) . "' where icon_id='" . mysqli_real_escape_string($link, $id) . "'");
            mysqli_close($link);
        }

        $r_mgr->returnOK($id);
    }
?>