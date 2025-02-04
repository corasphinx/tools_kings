<?php

// most/every class will extend System
if (!class_exists("System")) {
    class System
    {

        public function insert($tableName, $values = NULL)
        {
            //Two uses: listSelectID('users','id=1');
            //OR: listSelectID('users', array('id' => 1, 'name' => 'billy'));
            //Will return a list of IDs that matched the query.
            if (is_array($values) === false && is_object($values) === false) {
                $query = $this->db->prepare("INSERT INTO $tableName VALUES ($values)");
                $query->execute();
                if ($query->error != "") {
                    $this->error = $query->error;
                    return false;
                }
                $this->insert_id = $query->insert_id;
                return $this->insert_id;
            } else {
                $fieldString = "";
                $valueString = "";
                foreach ($values as $key => $value) {
                    $fieldString = $fieldString . ",$key";
                    if ($values[$key] !== NULL) {
                        $valueString = $valueString . ",?";
                    } else {
                        $valueString = $valueString . ",NULL";
                    }
                }
                $fieldString = substr($fieldString, 1);
                $valueString = substr($valueString, 1);
                $queryString = "INSERT INTO $tableName ($fieldString) VALUES ($valueString)";
                $query = $this->db->prepare($queryString);
                $bind_param_lhs = "";
                $bind_param_rhs = "";
                foreach ($values as $key => $value) {
                    if (is_string($values[$key])) {
                        $bind_param_lhs = $bind_param_lhs . "s";
                    } else if (is_int($values[$key])) {
                        $bind_param_lhs = $bind_param_lhs . "i";
                    } else if (is_float($values[$key])) {
                        $bind_param_lhs = $bind_param_lhs . "d";
                    } else if ($values[$key] !== NULL) {
                        $bind_param_lhs = $bind_param_lhs . "s";
                    }
                    if ($values[$key] !== NULL) {
                        $bind_param_rhs = $bind_param_rhs . ",\$values['$key']";
                    }
                }
                $bind_param_rhs = substr($bind_param_rhs, 1);
                $bind_param_string = "\$query->bind_param(\"$bind_param_lhs\", $bind_param_rhs);";
                eval($bind_param_string);
                $query->execute();
                if ($query->error !== "") {
                    $this->error = $query->error;
                    return false;
                }
                $this->insert_id = $query->insert_id;
                return $this->insert_id;
            }
        }


        public function fetch()
        {
            //Designed to retrieve all relevant class instance data based on the ID passed to the class.
            //See variables above. See classes/User.php for example of how this is used.
            $columnString = "";
            foreach ($this->columns as $entry) {
                $columnString = $columnString . "," . $entry;
            }
            $columnString = substr($columnString, 1);
            $queryString = "SELECT $columnString FROM $this->tableName WHERE id=?";
            $query = $this->db->prepare($queryString);
            if ($query === false) {
                error_log("Error in SQL: $queryString");
            }
            $query->bind_param("i", $this->id);
            $query->execute();
            $bindResultString = "";
            foreach ($this->columns as $entry) {
                $bindResultString = $bindResultString . ",\$this->" . $entry;
            }
            $bindResultString = substr($bindResultString, 1);
            $evalCode = "\$query->bind_result($bindResultString);";
            eval($evalCode);
            if ($query->error != "") {
                $this->error = $query->error;
                $this->valid = false;
            }
            if ($query->fetch()) {
                $this->valid = true;
            } else {
                $this->valid = false;
            }
        }

        public function update($tableName, $values, $where)
        {
            //Two uses: listSelectID('users','id=1');
            //OR: listSelectID('users', array('id' => 1, 'name' => 'billy'));
            //Will return a list of IDs that matched the query.

            if (is_array($values) === false && is_object($values) === false) {
                if (is_array($where) === false && is_object($where) === false) {
                    $query = $this->db->prepare("UPDATE $tableName SET $values WHERE $where");
                } else {
                    $whereString = "";
                    foreach ($where as $key => $value) {
                        $whereString = $whereString . " $key = ?";
                    }
                    $whereString = substr($whereString, 1);
                    $sql = "UPDATE $tableName SET $values WHERE $whereString";
                    $query = $this->db->prepare($sql);
                    if ($query === false) {
                        error_log("BAD SQL: " . $sql);
                    }
                    $bind_param_lhs = "";
                    $bind_param_rhs = "";
                    if (is_array($where) || is_object($where)) {
                        foreach ($where as $key => $value) {
                            if (is_string($where[$key])) {
                                $bind_param_lhs = $bind_param_lhs . "s";
                            } else if (is_int($where[$key])) {
                                $bind_param_lhs = $bind_param_lhs . "i";
                            } else if (is_float($where[$key])) {
                                $bind_param_lhs = $bind_param_lhs . "d";
                            }
                            $bind_param_rhs = $bind_param_rhs . ",\$where['$key']";
                        }
                    }
                    $bind_param_rhs = substr($bind_param_rhs, 1);
                    $bind_param_string = "\$query->bind_param(\"$bind_param_lhs\", $bind_param_rhs);";
                    eval($bind_param_string);
                }
                $query->execute();
                if ($query->error != "") {
                    $this->error = $query->error;
                    return false;
                }
                $this->affected_rows = $query->affected_rows;
                return true;
            } else {
                $setString = "";
                foreach ($values as $key => $value) {
                    $setString = $setString . ",`$key`=?";
                }
                $setString = substr($setString, 1);
                $queryString = "UPDATE $tableName SET $setString WHERE ";
                if (is_array($where) === false && is_object($where) === false) {
                    $queryString = "UPDATE $tableName SET $setString WHERE $where";
                } else {
                    $whereString = "";
                    foreach ($where as $key => $value) {
                        $whereString = $whereString . " $key = ?";
                    }
                    $whereString = substr($whereString, 1);
                    $queryString  = "UPDATE $tableName SET $setString WHERE $whereString";
                }
                $query = $this->db->prepare($queryString);
                if ($query === false) {
                    error_log("INVALID SQL: " . $queryString);
                }
                $bind_param_lhs = "";
                $bind_param_rhs = "";

                //DOING THE SET OF BIND PARAM
                foreach ($values as $key => $value) {
                    if (is_string($values[$key])) {
                        $bind_param_lhs = $bind_param_lhs . "s";
                    } else if (is_int($values[$key])) {
                        $bind_param_lhs = $bind_param_lhs . "i";
                    } else if (is_float($values[$key])) {
                        $bind_param_lhs = $bind_param_lhs . "d";
                    } else {
                        $bind_param_lhs = $bind_param_lhs . "s";
                    }
                    $bind_param_rhs = $bind_param_rhs . ",\$values['$key']";
                }
                //DOING THE WHERE OF BIND PARAM. IF USER PASSED DICTIONARY OF VALUES OTHERWISE THERE IS NOTHING THAT NEEDS TO BE PREPARED.
                if (is_array($where) || is_object($where)) {
                    foreach ($where as $key => $value) {
                        if (is_string($where[$key])) {
                            $bind_param_lhs = $bind_param_lhs . "s";
                        } else if (is_int($where[$key])) {
                            $bind_param_lhs = $bind_param_lhs . "i";
                        } else if (is_float($where[$key])) {
                            $bind_param_lhs = $bind_param_lhs . "d";
                        }
                        $bind_param_rhs = $bind_param_rhs . ",\$where['$key']";
                    }
                }
                $bind_param_rhs = substr($bind_param_rhs, 1);
                $bind_param_string = "\$query->bind_param(\"$bind_param_lhs\", $bind_param_rhs);";
                eval($bind_param_string);
                $query->execute();
                if ($query->error !== "") {
                    $this->error = $query->error;
                    return false;
                }
                $this->affected_rows = $query->affected_rows;
                return true;
            }
        }

        public function timestampNow()
        {
            // to keep timestamps the same (used to get the now timestamp)
            return date("Y-m-d H:i:s");
        }

        public function exitJsonResponse($success, $message, $data = array())
        {
            exit(json_encode(array("success" => $success, "message" => $message, "data" => $data)));
        }

        public function query($query, $types = NULL, $binds = array())
        {
            $query = trim($query);
            if (strpos(strtoupper($query), "SELECT") === 0) {
                $querytype = "SELECT";
                $queryverb = "finding";
            } else if (strpos(strtoupper($query), "UPDATE") === 0) {
                $querytype = "UPDATE";
                $queryverb = "updating";
            } else if (strpos(strtoupper($query), "DELETE") === 0) {
                $querytype = "DELETE";
                $queryverb = "removing";
            } else if (strpos(strtoupper($query), "INSERT") === 0) {
                $querytype = "INSERT";
                $queryverb = "creating";
            } else if (strpos(strtoupper($query), "SHOW COLUMNS") === 0) {
                $querytype = "SHOW COLUMNS";
                $queryverb = "displaying table columns";
            } else if (strpos(strtoupper($query), "SHOW TABLES") === 0) {
                $querytype = "SHOW TABLES";
                $queryverb = "displaying tables";
            }

            if (isset($types) || (is_array($binds) && count($binds) > 0)) {
                $numvars = count($binds);
                $numtypes = strlen($types);
                if ($numvars != $numtypes) {
                    $this->submitError("An error was detected (variables and types mismatched) while $queryverb this information." . json_encode($binds) . $types); // bind vars don't match # of bind types...
                    return false;
                }
            }

            if ($sql = $this->db->prepare($query)) {
                /* Now we use the $bindparams array to dynamically bind types/variables to our query... */
                if (isset($numvars)) {

                    $final = array(&$types);
                    for ($x = 0; $x < $numvars; $x++) {
                        $final[] = &$binds[$x];
                    }
                    if (!call_user_func_array(array($sql, 'bind_param'), $final)) {
                        $this->submitError("There were issues detected in the criteria (bind param error) for $queryverb this information.");
                        return false;
                    }
                }
                if ($sql->execute()) {
                    $sql->store_result();
                    if ($querytype == "SELECT" || $querytype == "SHOW COLUMNS" || $querytype == "SHOW TABLES") {
                        if ($sql->num_rows == 0) {
                            $this->submitError("No results were found for this search.");
                            return false;
                        }
                        $meta = $sql->result_metadata();
                        while ($field = $meta->fetch_field()) {
                            $params[] = &$row[$field->name];
                        }
                        if (!call_user_func_array(array($sql, 'bind_result'), $params)) {
                            $this->submitError("An error was detected (bind result) while attempting to fetch specific search results.");
                            return false;
                        }
                        while ($sql->fetch()) {
                            foreach ($row as $col => $val) {
                                $returnrow[$col] = $val;
                            }
                            $return[] = $returnrow;
                        }
                        return $return;
                    } else {
                        if ($sql->affected_rows > 0) {
                            if ($querytype == "INSERT") {
                                return $sql->insert_id;
                            }
                            return true;
                        } else if ($querytype == "UPDATE") {
                            // 								$this->submitError("There was an issue $queryverb this information. This can occur on edits if you did not change any information before pressing Save");
                            return true;
                        } else if ($querytype == "DELETE") {
                            $this->submitError("There was an issue $queryverb this information. This can occur if the information being removed has already been deleted!");
                            return false;
                        }
                    }
                } else {
                    $this->submitError("There was an issue $queryverb this information because of certain criteria. " . $this->db->error);
                    return false;
                }
            } else { // you can modify the error *temporarily* to find more info, like below:
                // $this->submitError("There is an issue with $queryverb this information due to the criteria."); // original error
                $this->submitError("There is an issue with $queryverb this information due to the criteria in the prepared statement" . $this->db->error); // for finding bug add: . $query" . htmlspecialchars($this->db->error) . json_encode($this->db));
                return false;
            }
        }

        public function NewDashboardURLPath($fromDash)
        {
            return $this->lastStrReplace("classes", "", str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__))) . $fromDash;
        }

        public function DashboardURLPath($fromDash)
        {
            return $this->lastStrReplace("classes", "", str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__))) . "dashboard/" . $fromDash;
        }

        public function loginRedirect()
        {
            // redundant login redirect, has its own function to always redirect to correct path from various locations
            $this->redirect($this->DashboardURLPath("login.php"));
        }

        public function redirect($url)
        {
            // used by scripts to redirect to a certain page
            header("Location: $url"); // redirect to URL
            echo "<script>window.location.href='$url';</script><meta http-equiv='refresh' content='0; url=$url' />"; // in case headers already sent
            die("A redirect was attempted, but it seems your JavaScript is disabled and/or your browser is preventing us from taking you to a new page. This website may not function as intended."); // in case JS is disabled
        }

        public function loadClass($class)
        {
            $path = __DIR__ . "/$class.php";
            if (file_exists($path) && is_file($path)) {
                require_once $path;
                return true;
            } else {
                $this->submitError("Cannot load essential core class file ($class) at this time.");
                return false;
            }
        }

        public function startClass($class)
        {
            // version of load class that also returns the class from its func
            $path = __DIR__ . "/$class.php";
            if (file_exists($path) && is_file($path)) {
                require_once $path;
                return new $class;
            } else {
                $this->submitError("Cannot load essential core class file ($class) at this time.");
                return false;
            }
        }

        public function submitError($msg)
        {
            // allows other functions to run through this if we want to do anything specifically
            $this->error = $msg;
        }

        private function lastStrReplace($search, $replace, $content)
        {
            // this is used for the relative linking logic in case "classes" or another string is in a file path more than once. It will fetch last occurrence, to correctly target the /classes/ folder that classes are in.
            $pos = strrpos($content, $search);
            if ($pos !== false) {
                $content = substr_replace($content, $replace, $pos, strlen($search));
            }
            return $content;
        }

        public function humanReadableTime($time)
        {
            if (!is_int($time)) {
                //$time = strtotime($time);
            }
            $time = time() - $time;
            $time = ($time < 1) ? 1 : $time;
            $tokens = array(
                31536000 => 'year',
                2592000 => 'month',
                604800 => 'week',
                86400 => 'day',
                3600 => 'hour',
                60 => 'minute',
                1 => 'second'
            );
            foreach ($tokens as $unit => $text) {
                if ($time < $unit) continue;
                $numberOfUnits = floor($time / $unit);
                return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
            }
        }

        /**
         * Get the ordinal suffix for a number
         * 
         * @param int $num The number to convert
         * @return string The number with the ordinal suffix
         */
        function getOrdinalSuffix($num)
        {
            if (!in_array(($num % 100), array(11, 12, 13))) {
                switch ($num % 10) {
                    case 1:
                        return $num . 'st';
                    case 2:
                        return $num . 'nd';
                    case 3:
                        return $num . 'rd';
                }
            }
            return $num . 'th';
        }

        /**
         * Convert a datetime string to a formatted string, with optional "hrs ago" for times within a day.
         * 
         * @param string $datetimeString The input datetime string
         * @return string The formatted date string
         */
        function humanReadableTimeStamp($datetimeString)
        {
            // Create DateTime objects
            $date = new DateTime($datetimeString);
            $now = new DateTime();

            // Calculate the difference between now and the date
            $diff = $now->diff($date);

            // Check if the difference is less than 1 day
            if ($diff->days < 1) {
                if ($diff->h > 0) {
                    // Return hours ago if there are hours
                    return $diff->h . ' hrs ago';
                } else {
                    // Return minutes ago if less than an hour
                    return $diff->i . ' mins ago';
                }
            }

            // Extract parts of the date
            $month = $date->format('M');
            $day = $this->getOrdinalSuffix($date->format('j'));
            $time = $date->format('H:i');

            // Construct the formatted date
            $formattedDate = "$month $day, $time";

            return $formattedDate;
        }

        function truncateMiddle($str, $numChars = 5)
        {
            // Check if the string length is shorter or equal to the allowed display length with "..."
            if (strlen($str) <= $numChars * 2 + 3) {
                return $str;
            }

            // Get the starting and ending parts of the string
            $start = substr($str, 0, $numChars);
            $end = substr($str, -$numChars);

            // Combine the start, "..." and end parts
            return $start . '...' . $end;
        }

        function truncateString($str, $limit = 15)
        {
            if (strlen($str) > $limit) {
                return substr($str, 0, $limit) . '...';
            } else {
                return $str;
            }
        }

        public function money($input)
        { // money format
            $input = $this->numbersDecimalsOnly($input);
            setlocale(LC_MONETARY, 'en_US.UTF-8');
            return number_format('%.2n', $input);
        }

        function number_format_short($n, $precision = 1)
        {
            if ($n < 900) {
                // 0 - 900
                $n_format = number_format($n, $precision);
                $suffix = '';
            } else if ($n < 900000) {
                // 0.9k-850k
                $n_format = number_format($n / 1000, $precision);
                $suffix = 'K';
            } else if ($n < 900000000) {
                // 0.9m-850m
                $n_format = number_format($n / 1000000, $precision);
                $suffix = 'M';
            } else if ($n < 900000000000) {
                // 0.9b-850b
                $n_format = number_format($n / 1000000000, $precision);
                $suffix = 'B';
            } else {
                // 0.9t+
                $n_format = number_format($n / 1000000000000, $precision);
                $suffix = 'T';
            }

            // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
            // Intentionally does not affect partials, eg "1.50" -> "1.50"
            if ($precision > 0) {
                $dotzero = '.' . str_repeat('0', $precision);
                $n_format = str_replace($dotzero, '', $n_format);
            }

            return $n_format . $suffix;
        }


        public function whiteSpacesToDashes($input)
        { // remove all whitespce, replace with a dash, no double dashes allowed ~ used for UIDs fore x.
            return preg_replace('/\s/', '-', trim(preg_replace('/\s+/', ' ', $input)));
        }

        public function websiteFormat($input)
        {
            if (strpos($input, "http://") !== 0 && strpos($input, "https://") !== 0) {
                $input = "https://" . $input;
            }
            $input = FILTER_VAR($input, FILTER_SANITIZE_URL);
            if (FILTER_VAR($input, FILTER_VALIDATE_URL) !== false) {
                return $input;
            } else {
                $this->submitError("This is not a valid URL...");
                return NULL;
            }
        }

        public function emailAddressFormat($input)
        {
            $input = FILTER_VAR($input, FILTER_SANITIZE_EMAIL);
            if (FILTER_VAR($input, FILTER_VALIDATE_EMAIL) !== false) {
                return $input;
            } else {
                return NULL;
            }
        }

        public function lettersNumbersOnly($input)
        {
            return preg_replace("/[^0-9a-zA-Z]/", '', $input);
        }

        public function lettersNumbersSpacesOnly($input)
        {
            return preg_replace("/[^0-9a-zA-Z ]/", '', $input);
        }

        public function lettersNumbersSpacesPunctuationOnly($input)
        {
            return preg_replace("/[^0-9a-zA-Z .,!?'\"]/", '', $input);
        }

        public function phoneNumber($input)
        {
            return preg_replace("/[^0-9()\-+]/", '', $input);
        }

        public function lettersOnly($input)
        {
            return preg_replace("/[^a-zA-Z]/", '', $input);
        }

        public function lettersSpacesOnly($input)
        {
            return preg_replace("/[^a-zA-Z ]/", '', $input);
        }

        public function lettersDashesOnly($input)
        {
            return preg_replace("/[^a-zA-Z\-]/", '', $input);
        }

        public function numbersOnly($input)
        {
            return preg_replace("/[^0-9]/", '', $input);
        }

        public function numbersDecimalsOnly($input)
        {
            return preg_replace("/[^0-9.]/", '', $input);
        }

        public function hashtagLinks($input)
        {
            return preg_replace('/(?:^|\s)#(\w+)/', " <a href='https://{$_SERVER['HTTP_HOST']}/h/$1'>#$1</a>", $input);
        }

        public function numbersSpacesOnly($input)
        {
            return preg_replace("/[^0-9 ]/", '', $input);
        }

        function randomString($length = 16)
        {
            $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $str = "";

            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[mt_rand(0, strlen($chars) - 1)];
            }

            return $str;
        }

        function randomNumber($length = 16)
        {
            $chars = "0123456789";
            $str = "";

            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[mt_rand(0, strlen($chars) - 1)];
            }

            return $str;
        }

        public function updateTableField($table, $id, $field_name, $field_value)
        {
            $q = $this->db->prepare("UPDATE $table SET $field_name=?, updated_at=NOW() WHERE id=?");
            $q->bind_param("si", $field_value, $id);
            $q->execute();
            if ($q->error != "") {
                return false;
            } else {
                return true;
            }
        }

        public function delete_row($table, $id)
        {
            $this->query("DELETE FROM $table WHERE id = ?", "i", [$id]);
        }

        public function fetch_all_rows($table)
        {
            return $this->query("SELECT * FROM $table");
        }


        var $version;
        var $error;
        var $dbhost;
        var $dbuser;
        var $dbpw;
        var $dbname;
        var $db;
        var $valid;

        var $affected_rows;
        var $columns;
        var $tableName;
        var $insert_id;
        var $id;

        public function __construct()
        {
            $this->version = "1.0.0";
            $this->error = NULL;

            $this->dbhost = "localhost";
            $this->dbuser = "tools_db";
            $this->dbpw = "dn8RX2ATNFsY";
            $this->dbname = "tools_db";

            $this->db = new mysqli($this->dbhost, $this->dbuser, $this->dbpw, $this->dbname);

            //Check Connection
            if (mysqli_connect_errno()) {
                $this->valid = false;
                printf("Connect failed: %s\n", mysqli_connect_error());
                exit();
            }
            $this->valid = true;

            date_default_timezone_set('US/Eastern');
        } // construct end

    } // class end
} // class isnt defined already
