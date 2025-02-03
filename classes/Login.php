<?php

if (!class_exists("Login")) {
    class Login extends System
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function logged_in_account_data()
        {
            if (session_status() != PHP_SESSION_ACTIVE) {
                session_start();
            }
            if (isset($_COOKIE["account-session-id"]) && !isset($_SESSION['sessionID'])) {
                $_SESSION['sessionID'] = $_COOKIE['account-session-id'];
            }
            if (isset($_COOKIE["account-id"]) && !isset($_SESSION['accountID'])) {
                $_SESSION['accountID'] = $_COOKIE['account-id'];
            }
            if (isset($_COOKIE["mobile_device"]) && (!isset($_SESSION['mobile_device']) || $_SESSION['mobile_device'] != $_COOKIE['mobile_device'])) {
                $_SESSION['mobile_device'] = $_COOKIE['mobile_device'];
            } else if (isset($_SESSION["mobile_device"]) && (!isset($_COOKIE['mobile_device']) || $_SESSION['mobile_device'] != $_COOKIE['mobile_device'])) {
                setcookie("mobile_device", $_SESSION["mobile_device"], time() + 60 * 60 * 24 * 30, "/", $domain_for_cookie, true, false);
            }
            if (isset($_SESSION['accountID']) && isset($_SESSION['sessionID'])) {
                $this->loadClass("Account");
                $Account = new Account($_SESSION['accountID']);
                if ($Account->valid == false) {
                    session_destroy();
                    setcookie("account-session-id", time() - 3600);
                    setcookie("account-session-k", time() - 3600);
                    setcookie("account-id", time() - 3600);
                    setcookie("mobile_device", time() - 3600);
                    $_COOKIE = array();
                    return false;
                }
                $check = $Account->checkSession();
                //         return $check;
                if (!$check) {
                    return false;
                }
                return $Account;
            }
            return false;
        }

        public function infoByEmail($email)
        {
            // searches for the password hash by email address
            $q = $this->db->prepare("SELECT id, password FROM users WHERE email = ?");
            $q->bind_param("s", $email);
            $q->execute();
            $q->store_result();
            if ($q->num_rows > 0) {
                $q->bind_result($id, $password_hash);
                $q->fetch();
                $q->close();
                return array("id" => $id, "hash" => $password_hash);
            } else {
                return false; // not valid email
            }
        }

        public function infoByName($name)
        {
            // searches for the password hash by email address
            $q = $this->db->prepare("SELECT id, password FROM users WHERE first_name = ? AND last_name = ?");
            $q->bind_param("ss", explode(" ", $name)[0], explode(" ", $name)[1]);
            $q->execute();
            $q->store_result();
            if ($q->num_rows > 0) {
                $q->bind_result($id, $password_hash);
                $q->fetch();
                $q->close();
                return array("id" => $id, "hash" => $password_hash);
            } else {
                return false; // not valid email
            }
        }

        public function tryLogin($email, $password)
        {
            $info = $this->infoByEmail($email);
            if ($info !== false) {
                $check = password_verify($password, $info['hash']);
                if ($check !== false) {
                    return $info['id']; // valid login, return accountID!
                } else {
                    // wrong password
                    return false;
                }
            }
            return false;
        }
    } // class
} // class doesnt exist already
