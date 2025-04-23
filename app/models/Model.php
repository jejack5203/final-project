<?php

namespace models;

use PDO;

abstract class Model {

    private function connect() {
        $string = "mysql:hostname=" . DBHOST . ";dbname=" . DBNAME;
        $con = new PDO($string, DBUSER);
        return $con;
    }

    public function query($query, $data = []) {
        $con = $this->connect();
        $stm = $con->prepare($query);
        $check = $stm->execute($data);
        if ($check) {
            
            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($result) && count($result)) {
                return $result;
            }
        }
        return false;
    }

}