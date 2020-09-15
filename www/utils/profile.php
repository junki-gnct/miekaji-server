<?php
    include_once __DIR__ . '/db.php';
    class ProfileUtil extends DBManager {

        function getProfile($token) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT user_id FROM AuthTable WHERE token='" . mysqli_real_escape_string($link, $token) . "';");
            if(!$query) {
                return array(
                    "ID"=>-1,
                    "name"=>null,
                    "icon_id"=>null
                );
            }

            $user = null;
            while ($row = mysqli_fetch_assoc($query)) {
                $user = $row["user_id"];
                break;
            }
            mysqli_free_result($query);

            $query = mysqli_query($link, "SELECT * FROM ProfileTable WHERE user_id='" . mysqli_real_escape_string($link, $user) . "';");
            if(!$query) {
                return array(
                    "ID"=>-1,
                    "name"=>null,
                    "icon_id"=>null
                );
            }

            while ($row = mysqli_fetch_assoc($query)) {
                $prof = array(
                    "ID"=>intval($row["unique_id"]),
                    "name"=>$row["screen_name"],
                    "icon_id"=>$row["icon_id"]
                );
                mysqli_free_result($query);
                return $prof;
            }

            mysqli_free_result($query);
            return array(
                "ID"=>-1,
                "name"=>null,
                "icon_id"=>null
            );
        }

        function updateProfile($token, $name) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT user_id FROM AuthTable WHERE token='" . mysqli_real_escape_string($link, $token) . "';");
            if(!$query) {
                return;
            }

            $user = null;
            while ($row = mysqli_fetch_assoc($query)) {
                $user = $row["user_id"];
                break;
            }
            mysqli_free_result($query);

            mysqli_query($link, "UPDATE ProfileTable SET screen_name='" . mysqli_real_escape_string($link, $name) . "' WHERE user_id='" . mysqli_real_escape_string($link, $user) . "';");
        }

    }
?>