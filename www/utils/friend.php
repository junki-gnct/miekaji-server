<?php
    include_once __DIR__ . '/profile.php';
    class FriendUtil extends ProfileUtil {

        function getFriends($token) {
            $uid = $this->getProfile($token)["ID"];

            $user_friends = NULL;
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM FriendTable WHERE user_id=" . mysqli_real_escape_string($link, $uid) . ";");
            if(!$query) {
                mysqli_close($link);
                return -1;
            }
            while ($row = mysqli_fetch_assoc($query)) {
                $user_friends = explode(",", $row["friends"]);
            }
            mysqli_free_result($query);

            $friends = array();
            foreach($user_friends as $f_uid) {
                array_push($friends, $this->getProfileByUID($f_uid));
            }

            return array("users"=>$friends);
        }

        function addFriend($token, $user) {
            $uid = $this->getProfile($token)["ID"];
            $target_uid = $this->getProfileByID($user)["ID"];
            if($target_uid == $uid) return -3;

            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM FriendTable WHERE user_id=" . mysqli_real_escape_string($link, $uid) . ";");
            if(!$query) {
                mysqli_close($link);
                return -1;
            }

            $user_friends = NULL;
            $target_friends = NULL;


            while ($row = mysqli_fetch_assoc($query)) {
                $user_friends = explode(",", $row["friends"]);
            }
            mysqli_free_result($query);

            $query = mysqli_query($link, "SELECT * FROM FriendTable WHERE user_id=" . mysqli_real_escape_string($link, $target_uid) . ";");
            if(!$query) {
                mysqli_close($link);
                return -1;
            }

            while ($row = mysqli_fetch_assoc($query)) {
                $target_friends = explode(",", $row["friends"]);
            }
            mysqli_free_result($query);

            if($target_friends == NULL) {
                return -2;
            }

            $target_friends = array_diff($target_friends, array(''));
            $target_friends = array_values($target_friends);

            $user_friends = array_diff($user_friends, array(''));
            $user_friends = array_values($user_friends);

            if(in_array($uid . "", $target_friends)) {
                return -4;
            }

            array_push($target_friends, $uid);
            array_push($user_friends, $target_uid);

            $target_str = implode(",", $target_friends);
            $user_str = implode(",", $user_friends);
            
            mysqli_query($link, "UPDATE FriendTable SET friends='" . $target_str . "' WHERE user_id=" . mysqli_real_escape_string($link, $target_uid) . ";");
            mysqli_query($link, "UPDATE FriendTable SET friends='" . $user_str . "' WHERE user_id=" . mysqli_real_escape_string($link, $uid) . ";");

            mysqli_close($link);
            return 0;
        }

        function removeFriend($token, $user) {
            $uid = $this->getProfile($token)["ID"];
            $target_uid = $this->getProfileByID($user)["ID"];
            if($target_uid == $uid) return -3;

            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM FriendTable WHERE user_id=" . mysqli_real_escape_string($link, $uid) . ";");
            if(!$query) {
                mysqli_close($link);
                return -1;
            }

            $user_friends = NULL;
            $target_friends = NULL;


            while ($row = mysqli_fetch_assoc($query)) {
                $user_friends = explode(",", $row["friends"]);
            }
            mysqli_free_result($query);

            $query = mysqli_query($link, "SELECT * FROM FriendTable WHERE user_id=" . mysqli_real_escape_string($link, $target_uid) . ";");
            if(!$query) {
                mysqli_close($link);
                return -1;
            }

            while ($row = mysqli_fetch_assoc($query)) {
                $target_friends = explode(",", $row["friends"]);
            }
            mysqli_free_result($query);

            if($target_friends == NULL) {
                return -2;
            }

            $target_friends = array_diff($target_friends, array(''));
            $target_friends = array_values($target_friends);

            $user_friends = array_diff($user_friends, array(''));
            $user_friends = array_values($user_friends);

            if(!in_array($uid . "", $target_friends)) {
                return -4;
            }

            $target_friends = array_diff($target_friends, array("" . $uid));
            $target_friends = array_values($target_friends);

            $user_friends = array_diff($user_friends, array("" . $target_uid));
            $user_friends = array_values($user_friends);

            $target_str = implode(",", $target_friends);
            $user_str = implode(",", $user_friends);
            
            mysqli_query($link, "UPDATE FriendTable SET friends='" . $target_str . "' WHERE user_id=" . mysqli_real_escape_string($link, $target_uid) . ";");
            mysqli_query($link, "UPDATE FriendTable SET friends='" . $user_str . "' WHERE user_id=" . mysqli_real_escape_string($link, $uid) . ";");

            mysqli_close($link);
            return 0;
        }

    }
?>