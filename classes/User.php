<?php

if (!class_exists("User")) {
    class User extends System
    {
        public function __construct()
        {
            parent::__construct(); // sets $this->db, and $this->valid == true if good connection 
        }

        public function create(
            $first_name,
            $last_name,
            $email,
            $password
        ) {
            // create a new account with bare minimum basic info from registration 
            $first_name = $this->lettersSpacesOnly($first_name);
            $last_name = $this->lettersSpacesOnly($last_name);
            if (($email = $this->emailAddressFormat($email)) == null) {
                $this->submitError("Your email is not compliant with the valid email address format.");
                return false;
            }

            $password = password_hash($password, PASSWORD_DEFAULT);

            $q = $this->db->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?,?,?,?)");
            $q->bind_param("ssss", $first_name, $last_name, $email, $password);
            $q->execute();
            if ($q->affected_rows == 1) {
                return $q->insert_id;
            } else {
                return false;
            }
            $q->close();
        }

        // New fetch_by_id method added to fetch user by their ID
        public function info($id)
        {
            $user = $this->query("SELECT * FROM users WHERE id = ?", "i", [$id]);
            if (!$user) {
                return false;
            } else {
                return $user[0];
            }
        }

        public function fetch_all()
        {
            return $this->query("SELECT * FROM users");
        }

        public function updateField($id, $field_name, $field_value)
        {
            $q = $this->db->prepare("UPDATE users SET $field_name=?, updated_at=NOW() WHERE id=?");
            $q->bind_param("si", $field_value, $id);
            $q->execute();
            if ($q->error != "") {
                return false;
            } else {
                return true;
            }
        }

        public function delete_user($id)
        {
            return $this->query("DELETE FROM users WHERE id = ?", "i", [$id]);
        }
    }
}
