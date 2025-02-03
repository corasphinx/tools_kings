<?php

if (!class_exists("UserDocument")) {
    class UserDocument extends System
    {
        public function __construct()
        {
            parent::__construct(); // sets $this->db, and $this->valid == true if good connection 
        }

        public function create($user_id, $origin_name, $file_name, $path, $created_by)
        {

            $q = $this->db->prepare("INSERT INTO user_documents (user_id, origin_name, file_name, path, created_by) VALUES (?,?,?,?,?)");
            $q->bind_param("isssi", $user_id, $origin_name, $file_name, $path, $created_by);
            $q->execute();
            if ($q->affected_rows == 1) {
                return $q->insert_id;
            } else {
                return false;
            }
            $q->close();
        } // create 

        public function fetch_by_user_id($user_id)
        {
            return $this->query("
                SELECT
                    user_documents.*,
                    users.first_name,
                    users.last_name,
                    users.email
                FROM
                    user_documents
                LEFT JOIN users ON users.id = user_documents.created_by
                WHERE
                    user_id = ?", "i", [$user_id]);
        }
    } // class 
} // class doesnt exist 
