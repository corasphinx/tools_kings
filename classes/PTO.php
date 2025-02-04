<?php

if (!class_exists("PTO")) {
    class PTO extends System
    {
        public function __construct()
        {
            parent::__construct(); // sets $this->db, and $this->valid == true if good connection 
        }

        public function create(
            $data
        ) {
            $q = $this->db->prepare("INSERT INTO ptos (user_id, amount, time_off, created_by) VALUES (?,?,?,?)");
            $q->bind_param("idsi", $data['user_id'], $data['amount'], $data['time_off'], $data['created_by']);
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
            $user = $this->query("SELECT * FROM ptos WHERE id = ?", "i", [$id]);
            if (!$user) {
                return false;
            } else {
                return $user[0];
            }
        }

        public function fetch_by_user_id($user_id)
        {
            return $this->query("SELECT * FROM ptos WHERE user_id = ?", "i", [$user_id]);
        }

        public function fetch_all()
        {
            return $this->query("SELECT * FROM ptos");
        }

        public function wipe_user_pto($user_id)
        {
            return $this->query("DELETE FROM ptos WHERE user_id = ?", "i", [$user_id]);
        }
    }
}
