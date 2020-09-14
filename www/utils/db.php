<?php
    class DBManager {
        function __construct() {
            $link = $this->connect();
            if(!$link) {
                header("HTTP/1.1 503 Service Temporarily Unavailable");
                $result = array(
                    "status"=>503,
                    "message"=>"Could not connect to database server."
                );
                echo json_encode($result, JSON_PRETTY_PRINT);
                exit;
            }
            mysqli_close($link);
        }
    
        function connect() {
            include __DIR__ . '/../config.php';
            return mysqli_connect($config["db_host"] . ':' . $config['db_port'], $config['db_user'], $config['db_password'], $config['db_database']);
        }
    }
?>