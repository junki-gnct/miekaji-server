<?php
    include_once __DIR__ . '/profile.php';
    class FunUtil extends ProfileUtil {

        /**
         * ランダム文字列生成 (数字)
         * $length: 生成する文字数
         */
        function makeRandInt($length) {
            $str = range('0', '9');
            $r_str = null;
            for ($i = 0; $i < $length; $i++) {
                $r_str .= $str[rand(0, count($str) - 1)];
            }
            return $r_str;
        }

        function generateGroupID() {
            $link = $this->connect();
            do {
                $uid = $this->makeRandInt(9);
                $count = mysqli_num_rows(mysqli_query($link, "SELECT group_id FROM FunTable WHERE group_id=" . $uid));
            } while($count != 0);
            mysqli_close($link);
            return $uid;
        }

        function getUserGroupID($uid) {
            $link = $this->connect();

            $query = mysqli_query($link, "SELECT * FROM GroupTable WHERE user_id=" . mysqli_real_escape_string($link, $uid) . ";");
            if(!$query) {
                mysqli_close($link);
                return -1;
            }
            while ($row = mysqli_fetch_assoc($query)) {
                $gid = $row["group_id"];
                mysqli_free_result($query);
                mysqli_close($link);
                return intval($gid);
            }

            mysqli_free_result($query);
            mysqli_close($link);
            return -1;
        }

        function isGroupFound($gid) {
            $link = $this->connect();
            $count = mysqli_num_rows(mysqli_query($link, "SELECT group_id FROM FunTable WHERE group_id=" . $gid));
            mysqli_close($link);
            return ($count != 0);
        }

        function createGroup($token, $name) {
            $uid = $this->getProfile($token)["ID"];
            $gid = $this->generateGroupID();
            $link = $this->connect();
            mysqli_query($link, "INSERT INTO FunTable (group_id, owner_id, fun_state, screen_name) VALUES (" . $gid . ", " . $uid . ", 0, '" . mysqli_real_escape_string($link, $name) . "');");
            mysqli_query($link, "UPDATE GroupTable SET group_id=" . $gid . " WHERE user_id=" . $uid);
            mysqli_close($link);
            return $gid;
        }

        function joinGroup($token, $id) {
            $uid = $this->getProfile($token)["ID"];
            if(!$this->isGroupFound($id)) {
                return false;
            }
            $link = $this->connect();
            mysqli_query($link, "UPDATE GroupTable SET group_id=" . $id . " WHERE user_id=" . $uid);
            mysqli_close($link);
            return true;
        }
    }