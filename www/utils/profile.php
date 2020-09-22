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

        function getJobValueSum($uid) {
            $this_month = date("Y-m", time()) . "-01";

            $next_month_y = intval(date("Y", time()));
            $next_month_i = intval(date("m", time())) + 1;
            if($next_month_i >= 13) {
                $next_month_i = 1;
                $next_month_y += 1;
            }
            if($next_month_i < 10) $next_month_i = "0" . $next_month_i;

            $next_month = $next_month_y . "-" . $next_month_i . "-01";

            $link = $this->connect();
            $query = mysqli_query($link, "SELECT category_id, motion, m_time FROM JobTable WHERE user_id=" . mysqli_real_escape_string($link, $uid) . " AND (created_at BETWEEN '" . $this_month . "' AND '" . $next_month . "');");
            if(!$query) {
                mysqli_close($link);
                return 0.0;
            }

            $sum = 0.0;
            $category = array(); // for caching.

            while ($row = mysqli_fetch_assoc($query)) {
                if(!array_key_exists($row["category_id"], $category)) {
                    include_once __DIR__ . '/job.php';
                    $ju = new JobUtil();
                    $category[$row["category_id"]] = $ju->getCategoryInfo($row["category_id"]);
                }

                $sum += floatval($row["motion"]) * floatval($row["m_time"]) * $category[$row["category_id"]]["weight"];
            }

            mysqli_close($link);
            return $sum;
        }

        
        function getTodayJobValueSum($uid) {
            $today = date("Y-m-d", time());
            $tomorrow = date("Y-m-d", time() + 24* 60* 60);

            $link = $this->connect();
            $query = mysqli_query($link, "SELECT category_id, motion, m_time FROM JobTable WHERE user_id=" . mysqli_real_escape_string($link, $uid) . " AND (created_at BETWEEN '" . $today . "' AND '" . $tomorrow . "')");
            if(!$query) {
                mysqli_close($link);
                return 0.0;
            }

            $sum = 0.0;
            $category = array(); // for caching.

            while ($row = mysqli_fetch_assoc($query)) {
                if(!array_key_exists($row["category_id"], $category)) {
                    include_once __DIR__ . '/job.php';
                    $ju = new JobUtil();
                    $category[$row["category_id"]] = $ju->getCategoryInfo($row["category_id"]);
                }

                $sum += floatval($row["motion"]) * floatval($row["m_time"]) * $category[$row["category_id"]]["weight"];
            }

            mysqli_close($link);
            return $sum;
        }

        function getProfileByUID($uid) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM ProfileTable WHERE unique_id=" . mysqli_real_escape_string($link, $uid) . ";");
            if(!$query) {
                mysqli_close($link);
                return array(
                    "ID"=>-1,
                    "name"=>null,
                    "icon_id"=>null,
                    "sum"=>0,
                    "today"=>0
                );
            }

            while ($row = mysqli_fetch_assoc($query)) {
                $prof = array(
                    "ID"=>intval($row["unique_id"]),
                    "name"=>$row["screen_name"],
                    "icon_id"=>$row["icon_id"],
                    "sum"=>$this->getJobValueSum($row["unique_id"]),
                    "today"=>$this->getTodayJobValueSum($row["unique_id"])
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
                "icon_id"=>null,
                "sum"=>0,
                "today"=>0
            );
        }

        function getProfileByID($id) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM ProfileTable WHERE user_id='" . mysqli_real_escape_string($link, $id) . "';");
            if(!$query) {
                mysqli_close($link);
                return array(
                    "ID"=>-1,
                    "name"=>null,
                    "icon_id"=>null,
                    "sum"=>0,
                    "today"=>0
                );
            }

            while ($row = mysqli_fetch_assoc($query)) {
                $prof = array(
                    "ID"=>intval($row["unique_id"]),
                    "name"=>$row["screen_name"],
                    "icon_id"=>$row["icon_id"],
                    "sum"=>$this->getJobValueSum($row["unique_id"]),
                    "today"=>$this->getTodayJobValueSum($row["unique_id"])
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
                "icon_id"=>null,
                "sum"=>0,
                "today"=>0
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
                    "icon_id"=>null,
                    "sum"=>0,
                    "today"=>0
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
                    "icon_id"=>null,
                    "sum"=>0,
                    "today"=>0
                );
            }

            while ($row = mysqli_fetch_assoc($query)) {
                $prof = array(
                    "ID"=>intval($row["unique_id"]),
                    "name"=>$row["screen_name"],
                    "icon_id"=>$row["icon_id"],
                    "sum"=>$this->getJobValueSum($row["unique_id"]),
                    "today"=>$this->getTodayJobValueSum($row["unique_id"])
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
                "icon_id"=>null,
                "sum"=>0,
                "today"=>0
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