<?php
    include __DIR__ . '/config.php';
    include_once __DIR__ . '/utils/response.php';
    $r_mgr = new ResponseManager();

    $link = mysqli_connect($config["db_host"] . ':' . $config['db_port'], $config['db_user'], $config['db_password'], $config['db_database']);
    if(!$link) $r_mgr->returnUnavailable(mysqli_connect_error());
    else $r_mgr->returnOK("Service is active.");

?>