<?php
    include_once __DIR__ . '/../utils/response.php';
    $r_mgr = new ResponseManager();
    $r_mgr->returnBadRequest("You need to specify the endpoint.");