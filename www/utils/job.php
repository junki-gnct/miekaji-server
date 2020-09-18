<?php
    include_once __DIR__ . '/profile.php';
    class JobUtil extends ProfileUtil {

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

        function generateCategoryID() {
            $link = $this->connect();
            do {
                $uid = $this->makeRandInt(12);
                $count = mysqli_num_rows(mysqli_query($link, "SELECT category_id FROM JobCategoryTable WHERE category_id=" . $uid));
            } while($count != 0);
            mysqli_close($link);
            return $uid;
        }

        function generateJobID() {
            $link = $this->connect();
            do {
                $uid = $this->makeRandInt(12);
                $count = mysqli_num_rows(mysqli_query($link, "SELECT job_id  FROM JobTable WHERE job_id=" . $uid));
            } while($count != 0);
            mysqli_close($link);
            return $uid;
        }

        function createJobCategory($name, $weight, $detail) {
            $cid = $this->generateCategoryID();
            $link = $this->connect();
            mysqli_query($link, "INSERT INTO JobCategoryTable (category_id, screen_name, job_weight, detail, isActive) VALUES (" . $cid . ", '" . mysqli_real_escape_string($link, $name) . "', " . mysqli_real_escape_string($link, $weight) . ", '" . mysqli_real_escape_string($link, $detail) . "', true);");
            mysqli_close($link);
        }

        function isCategoryFound($id) {
            $link = $this->connect();
            $count = mysqli_num_rows(mysqli_query($link, "SELECT category_id FROM JobCategoryTable WHERE isActive=true AND category_id=" . mysqli_real_escape_string($link, $id)));
            mysqli_close($link);
            return ($count != 0);
        }

        function isJobFound($id) {
            $link = $this->connect();
            $count = mysqli_num_rows(mysqli_query($link, "SELECT job_id FROM JobTable WHERE job_id=" . mysqli_real_escape_string($link, $id)));
            mysqli_close($link);
            return ($count != 0);
        }

        function removeJobCategory($id) {
            if(!$this->isCategoryFound($id)) {
                return false;
            }

            $link = $this->connect();
            mysqli_query($link, "UPDATE JobCategoryTable SET isActive=false WHERE category_id=" . mysqli_real_escape_string($link, $id) . ";");
            mysqli_close($link);
            return true;
        }

        function editJobCategory($category, $name, $weight, $detail) {
            if(!$this->isCategoryFound($category)) {
                return -1;
            }

            $link = $this->connect();
            $update_values = array(
                $name == NULL ? "" : "screen_name='" . mysqli_real_escape_string($link, $name) . "'",
                $weight == NULL ? "" : "job_weight=" . mysqli_real_escape_string($link, $weight),
                $detail == NULL ? "" : "detail='" . mysqli_real_escape_string($link, $detail) . "'"
            );

            $update_values = array_diff($update_values, array(""));
            $update_values = array_values($update_values);

            mysqli_query($link, "UPDATE JobCategoryTable SET " . implode(", ", $update_values) . " WHERE category_id=" . mysqli_real_escape_string($link, $category) . ";");
            mysqli_close($link);
            return 0;
        }

        function getJobHistory($token) {
            return $this->getJobHistoryByUID($this->getProfile($token)["ID"]);
        }

        function getJobHistoryByUID($uid) {
            $histories = array();

            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM JobTable WHERE user_id=" . mysqli_real_escape_string($link, $uid));
            if(!$query) {
                mysqli_close($link);
                return array();
            }

            while ($row = mysqli_fetch_assoc($query)) {
                $category = $this->getCategoryInfo($row["category_id"]);
                array_push($histories, array(
                    "ID"=>intval($row["job_id"]),
                    "category"=>$category,
                    "date"=>$row["created_at"],
                    "motion"=>floatval($row["motion"]),
                    "time"=>floatval($row["m_time"]),
                    "value"=>floatval($row["motion"]) * floatval($row["m_time"]) * $category["weight"]
                ));
            }

            return $histories;
        }

        function addJobDetail($token, $category, $motion, $time) {
            if(!$this->isCategoryFound($category)) {
                return false;
            }
            $jid = $this->generateJobID();
            $uid = $this->getProfile($token)["ID"];

            $link = $this->connect();
            mysqli_query($link, "INSERT INTO JobTable (job_id, category_id, user_id, motion, m_time) VALUES (" . $jid . ", " . mysqli_real_escape_string($link, $category) . ", " . $uid . ", " . mysqli_real_escape_string($link, $motion) . ", " . mysqli_real_escape_string($link, $time) . ");");
            mysqli_close($link);
            return true;
        }

        function removeJob($id) {
            if(!$this->isJobFound($id)) {
                return false;
            }

            $link = $this->connect();
            mysqli_query($link, "DELETE FROM JobTable WHERE job_id=" . mysqli_real_escape_string($link, $id) . ";");
            mysqli_close($link);
            return true;
        }

        function getCategoryInfo($id) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM JobCategoryTable WHERE category_id=" . mysqli_real_escape_string($link, $id));
            if(!$query) {
                mysqli_close($link);
                return array();
            }

            $categories = array();
            while ($row = mysqli_fetch_assoc($query)) {
                $category = array(
                    "ID"=>intval($row["category_id"]),
                    "name"=>$row["screen_name"],
                    "weight"=>floatval($row["job_weight"]),
                    "detail"=>$row["detail"]
                );
                mysqli_free_result($query);
                mysqli_close($link);    
                return $category;
            }
            mysqli_free_result($query);
            mysqli_close($link);

            return array(
                "ID"=>-1,
                "name"=>null,
                "weight"=>0,
                "detail"=>null
            );
        }

        function listJobCategory() {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM JobCategoryTable WHERE isActive=true;");
            if(!$query) {
                mysqli_close($link);
                return array();
            }

            $categories = array();
            while ($row = mysqli_fetch_assoc($query)) {
                array_push($categories, array(
                    "ID"=>intval($row["category_id"]),
                    "name"=>$row["screen_name"],
                    "weight"=>floatval($row["job_weight"]),
                    "detail"=>$row["detail"]
                ));
            }
            mysqli_free_result($query);
            mysqli_close($link);

            return $categories;
        }

    }