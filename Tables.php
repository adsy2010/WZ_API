<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 21/02/15
 * Time: 20:32
 */

class Tables {

    private $db;

    public function __construct()
    {
        $this->db = new mysqli("localhost", "wz_test", "C8#8jm6l", "wz_api");
    }

    public function PullData($table = "cars")
    {
        try{
            $query = "SELECT * FROM ?";

            if(!$stmt = $this->db->prepare($query))
                throw new Exception("Failed to bind data");

            $stmt->bind_param("s", $table);

            //borrowed and modified from stackoverflow to return statment
            //http://stackoverflow.com/questions/4466697/attach-data-in-array-in-a-php-mysqli-prepared-statement

            if(!$stmt->execute())
                throw new Exception("Query failed to execute");

            // get all field info in returned set
            $result_metadata = $stmt->result_metadata();
            $result_fields = $result_metadata->fetch_fields();
            $result_metadata->free_result();
            unset($result_metadata);

            $bind_array = array();
            $current_row = array();
            $all_rows = array();
            foreach($result_fields as $val) {
                // use returned field names to populate associative array
                $bind_array[$val->name] = &$current_row[$val->name];
            }
            // bind results to associative array
            call_user_func_array(array($stmt, "bind_result"), $bind_array);
            // continually fetch all records into associative array and add it to final $all_rows array
            while($stmt->fetch()) $all_rows[] = $current_row;
            return json_encode($all_rows);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }

    }
} 