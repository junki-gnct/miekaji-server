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

        function createJobCategory($name, $weight, $detail) {
            $cid = $this->generateCategoryID();
            $link = $this->connect();
            mysqli_query($link, "INSERT INTO JobCategoryTable (category_id, screen_name, job_weight, detail, isActive) VALUES (" . $cid . ", '" . mysqli_real_escape_string($link, $name) . "', " . mysqli_real_escape_string($link, $weight) . ", '" . mysqli_real_escape_string($link, $detail) . "', true);");
            mysqli_close($link);
            return $gid;
        }

    }