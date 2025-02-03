<?php

if (!class_exists("Event")) {
    class Event extends System
    {
        public function __construct()
        {
            parent::__construct(); // sets $this->db, and $this->valid == true if good connection 
        }

        public function create(
            $data
        ) {
            $q = $this->db->prepare("INSERT INTO events (subject, date, start_at, end_at, status, description, created_by) VALUES (?,?,?,?,?,?,?)");
            $q->bind_param("ssssssi", $data['subject'], $data['date'], $data['start_at'], $data['end_at'], $data['status'], $data['description'], $data['created_by']);
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
            $user = $this->query("SELECT * FROM events WHERE id = ?", "i", [$id]);
            if (!$user) {
                return false;
            } else {
                return $user[0];
            }
        }

        public function fetch_by_user_id($user_id)
        {
            return $this->query("SELECT * FROM events WHERE created_by = ?", "i", [$user_id]);
        }
        public function fetch_all()
        {
            return $this->query("SELECT * FROM events");
        }



        public function updateField($id, $field_name, $field_value)
        {
            $q = $this->db->prepare("UPDATE events SET $field_name=?, updated_at=NOW() WHERE id=?");
            $q->bind_param("si", $field_value, $id);
            $q->execute();
            if ($q->error != "") {
                return false;
            } else {
                return true;
            }
        }

        public function delete_event($id)
        {
            $this->query("DELETE FROM events WHERE id = ?", "i", [$id]);
        }
    }
}
