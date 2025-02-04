<?php

if (!class_exists("Role")) {
    class Role extends System
    {
        public function __construct()
        {
            parent::__construct(); // sets $this->db, and $this->valid == true if good connection 
        }

        public function create(
            $data
        ) {
            $q = $this->db->prepare("INSERT INTO roles (name, created_by) VALUES (?,?)");
            $q->bind_param("si", $data['name'], $data['created_by']);
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
            $user = $this->query("SELECT * FROM roles WHERE id = ?", "i", [$id]);
            if (!$user) {
                return false;
            } else {
                return $user[0];
            }
        }

        public function wipe_permissions($role_id)
        {
            $this->query("DELETE FROM role_permission_refs WHERE role_id = ?", "i", [$role_id]);
        }

        public function assign_permission(
            $role_id,
            $permission_id
        ) {
            $q = $this->db->prepare("INSERT INTO role_permission_refs (role_id, permission_id) VALUES (?,?)");
            $q->bind_param("ii", $role_id, $permission_id);
            $q->execute();
            if ($q->affected_rows == 1) {
                return $q->insert_id;
            } else {
                return false;
            }
            $q->close();
        }

        public function get_permissions($role_id)
        {
            return $this->query("SELECT role_permission_refs.*, permissions.name FROM role_permission_refs LEFT JOIN permissions ON permissions.id = role_permission_refs.permission_id WHERE role_id = ?", "i", [$role_id]);
        }
    }
}
