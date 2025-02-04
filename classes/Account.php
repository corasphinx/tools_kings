<?php

if (!class_exists("Account")) {
    class Account extends System
    {
        var $primary_table;
        var $first_name;
        var $last_name;
        var $email;
        var $password;
        var $updated_at;
        var $created_at;
        var $permissions = [];

        public function __construct($id)
        {
            parent::__construct(); // fetch the DB ($this->db)
            $this->id = $id; // account id 
            $this->primary_table = "users";
            $this->valid = false; // not valid account by default  gets switched to true down below if results found
            $q = $this->db->prepare("
                SELECT
                    first_name,
                    last_name,
                    email,
                    password,
                    updated_at,
                    created_at
                FROM
                    users
                WHERE
                    users.id = ?
                LIMIT
                    1");
            $q->bind_param("i", $this->id);
            $q->execute();
            $q->store_result();
            if ($q->num_rows > 0) {
                $q->bind_result(
                    $this->first_name,
                    $this->last_name,
                    $this->email,
                    $this->password,
                    $this->updated_at,
                    $this->created_at
                );
                $q->fetch();
                $q->close();
                $this->valid = true;
            } else {
                $this->submitError("We could not find the specified account ID.");
            }
        }

        /*** SESSION FUNCTIONS START ***/
        public function checkSession()
        {
            if (isset($_COOKIE["account-session-id"]) && !isset($_SESSION['sessionID'])) {
                $_SESSION['sessionID'] = $_COOKIE['account-session-id'];
            }
            if (isset($_COOKIE["account-id"]) && !isset($_SESSION['accountID'])) {
                $_SESSION['accountID'] = $_COOKIE['account-id'];
            }
            if (isset($_SESSION['accountID']) && isset($_SESSION['sessionID']) && isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['HTTP_USER_AGENT']) && isset($_COOKIE['account-session-k'])) {
                //         return $_SESSION;
                $q = $this->db->prepare("SELECT iv, ip_address, device_info, start_timestamp FROM account_sessions WHERE session_id = ? AND acc_id = ?");
                $q->bind_param("si", $_SESSION['sessionID'], $_SESSION['accountID']);
                $q->execute();
                $q->store_result();
                if ($q->num_rows > 0) {
                    $q->bind_result($iv, $ip, $device, $start);
                    $q->fetch();
                    $q->close();
                    if ($ip == $_SERVER['REMOTE_ADDR'] && $device == $_SERVER['HTTP_USER_AGENT']) {
                        $starttime = strtotime($start); // convert from timestamp
                        $timesince = time() - strtotime($starttime);
                        if ((time() - (60 * 60 * 24 * 30)) >= $starttime) {
                            //               echo 'end session because it is old';
                            $this->endSession(); // end session because it's old 
                            return false;
                        } else {
                            $ivde64 = base64_decode($iv);
                            $cypher = "aes-256-cbc";
                            $original_id = openssl_decrypt($_SESSION['sessionID'], $cypher, $_COOKIE["account-session-k"], $options = 0, $ivde64);
                            if ($original_id == $_SESSION['accountID']) {
                                return true; // all session data correct
                            } else {
                                return false;
                            }
                        }
                    } else {
                        //echo 'ip device info wrong';
                        $this->endSession(); // this will log out a valid user if they're logged in, but also make it harder for old session data to be used for hijacking (forcing valid user to actually generate new session) ~ we do this because the IP & User agent don't match, but everything else does, which leads us to believe their details have been stolen  
                        return false; // ip or device info doesn't match 
                    }
                } else {
                    // 					echo 'not in db';
                    return false; // can't find session data in db 
                }
            } else {
                //echo 'missing params';
                return false; // the required params for session to be valid are not all here! 
            }
        }

        public function startSession($session_options = false)
        {
            if (session_status() == PHP_SESSION_NONE) { //If session hasn't already been started.
                session_start();
            }


            $user_info_plain_text = $this->id;
            $cypher = "aes-256-cbc";
            $key = openssl_random_pseudo_bytes(256);
            $ivlen = openssl_cipher_iv_length($cypher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $iv64 = base64_encode($iv);
            $cyphertext = openssl_encrypt($user_info_plain_text, $cypher, $key, $options = 0, $iv);
            $_SESSION['sessionID'] = $cyphertext;

            $_SESSION['accountID'] = $this->id;


            $session_insert = $this->query("INSERT INTO account_sessions (acc_id,session_id,iv,ip_address,device_info) VALUES (?,?,?,?,?)", "issss", array($this->id, $_SESSION['sessionID'], $iv64, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));

            if ($session_insert) {

                $domain_for_cookie = strtolower($_SERVER['HTTP_HOST']);
                setcookie("account-session-id", $_SESSION['sessionID'], time() + 60 * 60 * 24 * 30, "/", $domain_for_cookie, true, false);
                setcookie("account-session-k", $key, time() + 60 * 60 * 24 * 30, "/", $domain_for_cookie, true, false);
                setcookie("account-id", $_SESSION['accountID'], time() + 60 * 60 * 24 * 30, "/", $domain_for_cookie, true, false);
                $mobile_device = isset($session_options['mobile_device']) && $session_options['mobile_device'] == 1 ? 1 : 0;
                $_SESSION['mobile_device'] = $mobile_device;
                setcookie("mobile_device", $mobile_device, time() + 60 * 60 * 24 * 30, "/", $domain_for_cookie, true, false);
                return true;
            } else {
                return false;
            }
            // start a new session with a new session id 
        }

        public function endSession()
        {
            setcookie("account-session-id", "", time() - 3600);
            setcookie("account-id", "", time() - 3600);
            setcookie("account-session-k", "", time() - 3600);
            if (session_status() !== PHP_SESSION_NONE) {
                $_SESSION = NULL;
                session_destroy();
            }
            $delete = $this->query("DELETE FROM account_sessions WHERE acc_id = ?", "i", array($this->id));
            return true;
        }
        /*** SESSION FUNCTIONS END ***/
    }
} // class exists
