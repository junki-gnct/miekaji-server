<?php
    include_once __DIR__ . '/db.php';
    class AuthUtil extends DBManager {
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

        function getNewExpireTime(){
            return time() + (7 * 24 * 60 * 60); // 1 week available.
        }

        function generateToken() {
            $link = $this->connect();
            do {
                $token = $this->makeRandStr(60);
                $count = mysqli_num_rows(mysqli_query($link, "SELECT token FROM AuthTable WHERE token='" . $token . "';"));
            } while($count != 0);
            mysqli_close($link);
            return $token;
        }

        function register($id, $pass) {
            $link = $this->connect();
            $count = mysqli_num_rows(mysqli_query($link, "SELECT user_id FROM AuthTable WHERE user_id='" . mysqli_real_escape_string($link, $id) . "';"));
            if($count != 0) {
                mysqli_close($link);
                return array(
                    0=>NULL,
                    1=>-1
                );
            }

            $token = $this->generateToken();
            $exp = $this->getNewExpireTime();

            mysqli_query($link, "INSERT INTO AuthTable (user_id, pass_hash, token, expires) VALUES ('" . mysqli_real_escape_string($link, $id) . "', '" . password_hash($pass, PASSWORD_BCRYPT) . "', '" . $token . "', " . $exp . ");");

            mysqli_close($link);
            return array(
                0=>$token,
                1=>$exp
            );
        }

        function login($id, $pass) {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM AuthTable WHERE user_id='" . mysqli_real_escape_string($link, $id) . "';");
            if(!$query) {
                return array(
                    "status"=>500,
                    "token"=>null,
                    "expires"=>-1
                );
            }

            $isFound = false;
            while ($row = mysqli_fetch_assoc($query)) {
                if(password_verify($pass, $row["pass_hash"])){
                    $isFound = true;
                    break;
                }
            }

            mysqli_free_result($query);

            if(!$isFound) {
                return array(
                    "status"=>400,
                    "token"=>null,
                    "expires"=>-1
                );
            }

            $token = $this->generateToken();
            $exp = $this->getNewExpireTime();

            mysqli_query($link, "UPDATE AuthTable SET token='" . $token . "', expires=" . $exp . " WHERE user_id='" . mysqli_real_escape_string($link, $id) . "';");

            return array(
                "status"=>200,
                "token"=>$token,
                "expires"=>$exp
            );
        }

    }

?>