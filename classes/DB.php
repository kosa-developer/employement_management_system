<?php

class DB {

    private static $_instance = null;
    private $_pdo,
            $_query,
            $_error = false,
            $_result,
            $_options,
            $_dropQuery,
            $_columnName,
            $_ipNumber = null,
            $_count = 0,
            $_ugxAmount,
            $_grade, $_comment;

    private function __construct() {
        try {
            $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
        } catch (PDOException $exc) {
            die($exc->getMessage());
        }
    }

    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    public function query($sql, $params = array()) {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            if ($this->_query->execute()) {
                //echo 'hshsh';
                $this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                print_r($this->_query->errorInfo());
                $this->_error = true;
            }
        }
        return $this;
    }

    public function action($action, $table, $where = array()) {
        if (count($where) == 3) {
            $operators = array('=', '>', '<', '>=', '<=');
            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            if (in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                if (!$this->query($sql, array($value))->error()) {
                    return $this;
                }
            }
        }
        return false;
    }

    public function get($table, $where) {
        return $this->action("SELECT * ", $table, $where);
    }

    public function delete($table, $where) {
        return $this->action("DELETE ", $table, $where);
    }

    public function insert($table, $fields = array()) {
        if (count($fields)) {
            $keys = array_keys($fields);
            $values = '';
            $x = 1;
            foreach ($fields as $field) {
                $values .= "?";
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                $x++;
            }
            // die($values);
            $sql = "INSERT INTO " . $table . " (`" . implode('`,`', $keys) . "` ) VALUES ({$values})";
            //echo $sql;
            if (!$this->query($sql, $fields)->error()) {
                return TRUE;
            }
        }
        return false;
    }

    public function update($table, $id, $fields, $updateWhere) {
        $set = '';
        $x = 1;
        foreach ($fields as $name => $value) {
            $set .= "{$name}= ?";
            if ($x < count($fields)) {
                $set .= ', ';
            }
            $x++;
        }
        //die($set);
        $sql = "UPDATE {$table} SET {$set} WHERE {$updateWhere}='$id'";
//        echo $sql;
        if (!$this->query($sql, $fields)->error()) {
            return TRUE;
        }
    }

    public function results() {
        return $this->_result;
    }

    public function first() {
        return $this->results();
    }

    public function error() {
        return $this->_error;
    }

    public function count() {
        return $this->_count;
    }

    // populating drop downs
    public function dropDowns($tableName, $id, $name) {
        $this->_options = "";
        $this->_dropQuery = $this->query("SELECT * FROM $tableName ORDER BY $name ASC");
        $this->_options .= "<option value=''>Choose......</option>";
        if ($this->_dropQuery->count()) {
            foreach ($this->_dropQuery->results() as $result) {
                $this->_options .= "<option value='" . $result->{$id} . "'>" . $result->{$name} . "</option>";
            }
        }
        return $this->_options;
    }

// populating drop downs
    public function dropDownsNames($tableName, $id, $name) {
        $this->_options = "";
        $this->_dropQuery = $this->query("SELECT * FROM $tableName ORDER BY $name ASC");
        $this->_options .= "<option value=''>Choose......</option>";
        if ($this->_dropQuery->count()) {
            foreach ($this->_dropQuery->results() as $result) {
                $this->_options .= "<option value='" . $result->{$name} . "</option>";
            }
        }
        return $this->_options;
    }
    // populating drop downs
    public function dropDowns2($tableName, $id, $name, $column, $idParent) {
        $this->_options = "";
        $this->_dropQuery = $this->query("SELECT * FROM $tableName WHERE $column=$idParent ORDER BY $name ASC");
        $this->_options .= "<option value=''>Choose....</option>";
        if ($this->_dropQuery->count()) {
            foreach ($this->_dropQuery->results() as $result) {
                $this->_options .= "<option value='" . $result->{$id} . "'>" . $result->{$name} . "</option>";
            }
        }
        return $this->_options;
    }

    //get field name
    public function getName($table, $id, $return, $idColumn) {
        $this->_query = $this->query("SELECT $idColumn,$return FROM $table where $idColumn='$id'");
        if ($this->_query->count()) {
            foreach ($this->_query->results() as $result) {
                $this->_columnName = $result->{$return};
            }
        } else {
            $this->_columnName = 'NA';
        }
        return $this->_columnName;
    }

    //checking data already exists in the database
    public function checkRows($sql) {
        $this->_query = $this->query($sql);
        if ($this->count() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //function for converting for tracking orders
    public function countElements($sql) {
        $this->_columnName = 0;
        $this->_query = $this->query($sql);
        foreach ($this->_query->results() as $result) {
            $this->_columnName++;
        }
        return $this->_columnName;
    }

    public function querySample($sql) {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {

            if ($this->_query->execute()) {
                $this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                //print sql error
                print_r($this->_query->errorInfo());
                $this->_error = true;
            }
        }
//          returns array
        return $this->_result;
    }

    /*
     * function for grading students and adding a comment
     */

    public function displayTableColumnValue($sql, $tbl_col_name) {
        $this->_columnName = "";
        $this->_query = $this->query($sql);
        foreach ($this->_query->results() as $result) {
            $this->_columnName = $result->$tbl_col_name;
        }
        return $this->_columnName;
    }

    //function for converting for tracking income or expenditure
    public function calculateSum($sql,$columnResult) {
        $this->_columnName = 0;
        $this->_query = $this->query($sql);
        foreach ($this->_query->results() as $result) {
            $this->_columnName += ($result->$columnResult);
        }
        return $this->_columnName;
    }
}

?>