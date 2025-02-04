<?php

if (!class_exists("Permission")) {
    class Permission extends System
    {
        public function __construct()
        {
            parent::__construct(); // sets $this->db, and $this->valid == true if good connection 
        }

        public function create(
            $data
        ) {
            $q = $this->db->prepare("INSERT INTO permissions (name, description, created_by) VALUES (?,?,?)");
            $q->bind_param("ssi", $data['name'], $data['description'], $data['created_by']);
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
            $user = $this->query("SELECT * FROM permissions WHERE id = ?", "i", [$id]);
            if (!$user) {
                return false;
            } else {
                return $user[0];
            }
        }
    }
}
