<?php

if (!class_exists("Task")) {
    class Task extends System
    {
        public function __construct()
        {
            parent::__construct(); // sets $this->db, and $this->valid == true if good connection 
        }

        public function create(
            $data
        ) {
            $q = $this->db->prepare("INSERT INTO tasks (subject, description, due_by, created_by) VALUES (?,?,?,?)");
            $q->bind_param("sssi", $data['subject'], $data['description'], $data['due_by'], $data['created_by']);
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
            $user = $this->query("SELECT * FROM tasks WHERE id = ?", "i", [$id]);
            if (!$user) {
                return false;
            } else {
                return $user[0];
            }
        }

        public function fetch_by_user_id($user_id)
        {
            return $this->query("SELECT * FROM tasks WHERE created_by = ?", "i", [$user_id]);
        }
        public function fetch_all()
        {
            return $this->query("SELECT * FROM tasks");
        }



        public function updateField($id, $field_name, $field_value)
        {
            $q = $this->db->prepare("UPDATE tasks SET $field_name=?, updated_at=NOW() WHERE id=?");
            $q->bind_param("si", $field_value, $id);
            $q->execute();
            if ($q->error != "") {
                return false;
            } else {
                return true;
            }
        }

        public function delete_task($id)
        {
            $this->query("DELETE FROM tasks WHERE id = ?", "i", [$id]);
        }
    }
}
