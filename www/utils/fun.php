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

        function getMemberIDs($gid) {
            $link = $this->connect();
            $members = array();

            $query = mysqli_query($link, "SELECT * FROM GroupTable WHERE group_id=" . mysqli_real_escape_string($link, $gid) . ";");
            if(!$query) {
                mysqli_close($link);
                return -1;
            }
            while ($row = mysqli_fetch_assoc($query)) {
                array_push($members, intval($row["user_id"]));
            }

            mysqli_free_result($query);

            mysqli_close($link);
            return $members;
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

        function editGroupState($token, $state) {
            $uid = $this->getProfile($token)["ID"];
            $gid = $this->getUserGroupID($uid);
            if($gid == -1) return false;

            $link = $this->connect();
            mysqli_query($link, "UPDATE FunTable SET fun_state=" . mysqli_real_escape_string($link, $state) . " WHERE group_id=" . $gid);
            mysqli_close($link);
            return true;
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

        function addToGroup($token, $uid) {
            $uidd = $this->getProfile($token)["ID"];
            $gid = $this->getUserGroupID($uidd);
            if($gid == -1) {
                return false;
            }
            $link = $this->connect();
            mysqli_query($link, "UPDATE GroupTable SET group_id=" . $gid . " WHERE user_id=" . $uid);
            mysqli_close($link);
            return true;
        }

        function removeFromGroup($token, $uid) {
            $uidd = $this->getProfile($token)["ID"];
            $gid = $this->getUserGroupID($uidd);
            $gid2 = $this->getUserGroupID($uid);
            if($gid != $gid2) {
                return false;
            }
            $link = $this->connect();
            mysqli_query($link, "UPDATE GroupTable SET group_id=-1 WHERE user_id=" . $uid);
            mysqli_close($link);
            return true;
        }

        function leaveGroup($token) {
            $uid = $this->getProfile($token)["ID"];
            $link = $this->connect();
            mysqli_query($link, "UPDATE GroupTable SET group_id=-1 WHERE user_id=" . $uid);
            mysqli_close($link);
            return true;
        }

        function getGroupInfo($token) {
            $uid = $this->getProfile($token)["ID"];
            $gid = $this->getUserGroupID($uid);
            if($gid == -1) {
                $mids = array();
            } else {
                $mids = $this->getMemberIDs($gid);
            }

            $members = array();
            foreach($mids as $mid) {
                array_push($members, $this->getProfileByUID($mid));
            }
            
            $name = null;
            $state = -1;
            $created_at = "";
            $owner = array(
                "ID"=>-1,
                "name"=>null,
                "icon_id"=>null,
                "sum"=>0
            );

            $link = $this->connect();

            $query = mysqli_query($link, "SELECT * FROM FunTable WHERE group_id=" . mysqli_real_escape_string($link, $gid) . ";");
            if(!$query) {
                mysqli_close($link);
                return -1;
            }
            while ($row = mysqli_fetch_assoc($query)) {
                $name = $row["screen_name"];
                $state = intval($row["fun_state"]);
                $created_at = $row["created_at"];
                $owner = $this->getProfileByUID($row["owner_id"]);
            }
            mysqli_free_result($query);

            mysqli_close($link);

            return array(
                "ID"=>$gid,
                "name"=>$name,
                "state"=>$state,
                "owner"=>$owner,
                "created_at"=>$created_at,
                "members"=>$members
            );
        }
    }