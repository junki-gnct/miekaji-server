<?php
    include_once __DIR__ . '/db.php';
    class ProfileUtil extends DBManager {

        /**
         * ランダム文字列生成 (英数字)
         * $length: 生成する文字数
         */
        function makeRandStr($length) {
            $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
            $r_str = null;
            for ($i = 0; $i < $length; $i++) {
                $r_str .= $str[rand(0, count($str) - 1)];
            }
            return $r_str;
        }

        function generateIconID() {
            $link = $this->connect();
            do {
                $token = $this->makeRandStr(30);
                $count = mysqli_num_rows(mysqli_query($link, "SELECT icon_id FROM IconTable WHERE icon_id='" . $token . "';"));
            } while($count != 0);
            mysqli_close($link);
            return $token;
        }

        function getProfileByUID($uid) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM ProfileTable WHERE unique_id=" . mysqli_real_escape_string($link, $uid) . ";");
            if(!$query) {
                mysqli_close($link);
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
                mysqli_close($link);
                return $prof;
            }

            mysqli_free_result($query);
            mysqli_close($link);
            return array(
                "ID"=>-1,
                "name"=>null,
                "icon_id"=>null
            );
        }

        function getIconID($token) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT user_id FROM AuthTable WHERE token='" . mysqli_real_escape_string($link, $token) . "';");
            if(!$query) {
                mysqli_close($link);
                return null;
            }

            $user = null;
            while ($row = mysqli_fetch_assoc($query)) {
                $user = $row["user_id"];
                break;
            }
            mysqli_free_result($query);

            $query = mysqli_query($link, "SELECT icon_id FROM ProfileTable WHERE user_id='" . mysqli_real_escape_string($link, $user) . "';");
            if(!$query) {
                mysqli_close($link);
                return null;
            }

            while ($row = mysqli_fetch_assoc($query)) {
                $prof = $row["icon_id"];
                mysqli_free_result($query);
                mysqli_close($link);
                return $prof;
            }

            mysqli_free_result($query);
            mysqli_close($link);
            return null;
        }

        function getProfile($token) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT user_id FROM AuthTable WHERE token='" . mysqli_real_escape_string($link, $token) . "';");
            if(!$query) {
                mysqli_close($link);
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
                mysqli_close($link);
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
                mysqli_close($link);
                return $prof;
            }

            mysqli_free_result($query);
            mysqli_close($link);
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
                mysqli_close($link);
                return;
            }

            $user = null;
            while ($row = mysqli_fetch_assoc($query)) {
                $user = $row["user_id"];
                break;
            }
            mysqli_free_result($query);

            mysqli_query($link, "UPDATE ProfileTable SET screen_name='" . mysqli_real_escape_string($link, $name) . "' WHERE user_id='" . mysqli_real_escape_string($link, $user) . "';");
            mysqli_close($link);
        }

    }
?>