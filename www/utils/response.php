<?php
    class ResponseManager {

        function returnForbidden(){
            header("HTTP/1.1 403 Forbidden");
            $result = array(
                "status"=>403,
                "message"=>"You are not allowed to access this endpoint."
            );
            echo json_encode($result, JSON_PRETTY_PRINT);
        }

        function returnOK($message) {
            $result = array(
                "status"=>200,
                "message"=>$message
            );
            echo json_encode($result, JSON_PRETTY_PRINT);
        }

        function returnUnavailable($message){
            header("HTTP/1.1 503 Service Temporarily Unavailable");
            $result = array(
                "status"=>503,
                "message"=>$message
            );
            echo json_encode($result, JSON_PRETTY_PRINT);
        }

    }
?>