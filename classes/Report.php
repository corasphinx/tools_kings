<?php

if (!class_exists("Report")) {
    class Report extends System
    {
        public function __construct()
        {
            parent::__construct(); // sets $this->db, and $this->valid == true if good connection 
        }

        public function create(
            $data
        ) {
            $q = $this->db->prepare("INSERT INTO reports (line_count, project_count, employee_count, action_count) VALUES (?,?,?,?)");
            $q->bind_param("iiii", $data['lineCount'],$data['projectCount'],$data['employeeCount'],$data['actionCount']);
            $q->execute();
            if ($q->affected_rows == 1) {
                return $q->insert_id;
            } else {
                return false;
            }
            $q->close();
        }
        public function create_lines(
            $report_id, $data
        ) {
            $values = [];
            foreach ($data as $key => $row) {
                $values []= "('" . 
                    $report_id . "', '" . 
                    addslashes($row['time']) . "', '" . 
                    addslashes($row['project']) . "', '" . 
                    addslashes($row['employee']) . "', '" . 
                    addslashes($row['action']) . "', '" . 
                    addslashes($row['description']) . "')";
            }
            $this->query("INSERT INTO report_lines (report_id, time, project, employee, action, description) VALUES " . implode(",\n    ", $values) . ";");
        }

        // New fetch_by_id method added to fetch user by their ID
        public function info($id)
        {
            $user = $this->query("SELECT * FROM reports WHERE id = ?", "i", [$id]);
            if (!$user) {
                return false;
            } else {
                return $user[0];
            }
        }

        public function fetch_all()
        {
            return $this->query("SELECT * FROM reports");
        }

        public function fetch_report_lines($report_id)
        {
            return $this->query("SELECT * FROM report_lines WHERE report_id = ?", "i", [$report_id]);
        }

        public function updateField($id, $field_name, $field_value)
        {
            $q = $this->db->prepare("UPDATE reports SET $field_name=?, updated_at=NOW() WHERE id=?");
            $q->bind_param("si", $field_value, $id);
            $q->execute();
            if ($q->error != "") {
                return false;
            } else {
                return true;
            }
        }

        public function wipe_report_by_date($date)
        {
            $this->query("DELETE FROM reports WHERE created_at LIKE '".$date."%'");
            $this->query("DELETE FROM report_lines WHERE created_at LIKE '".$date."%'");
        }
    }
}
