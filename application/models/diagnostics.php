<?php

//error_reporting(E_ERROR);
//ini_set('display_errors', '0');

class diagnostics extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();

        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    function get_appliance_diagnostics_count($appliance_type) {
        //log_message('info', "Entering: " . __METHOD__);
	//log_message('info', "Appliance Type: " . $appliance_type);

        $sql = "select count(*) from diagnostics where `Appliance` = '$appliance_type'";
        $query = $this->db->query($sql);

        $result = $query->result_array();
        //log_message('info', print_r($result, true));

        return $result[0]['count(*)'];
    }

    function get_appliance_diagnostics_distinct_years($appliance_type) {
        //log_message('info', "Entering: " . __METHOD__);
	//log_message('info', "Appliance Type: " . $appliance_type);

        $sql = "SELECT DISTINCT (`Age Range`) FROM `diagnostics` WHERE `Appliance` = '$appliance_type' "
            . "ORDER BY LENGTH(`Age Range`), `Age Range`";
        $query = $this->db->query($sql);

        return $query->result_array();
    }

    function get_appliance_diagnostics_data_symptoms($appliance_type, $year) {
        //log_message('info', "Entering: " . __METHOD__);
	//log_message('info', "Type: " . $appliance_type . ", `Age Range`: " . $year);

        if ($year == "All") {
            $sql = "SELECT p.`symptom`, CEILING(SUM(100) / `total`) as `percentage` FROM
            (SELECT * FROM diagnostics
            WHERE `appliance` = '$appliance_type' AND `App Display Check` = '1') p
            CROSS JOIN (SELECT COUNT(*) AS `total` FROM diagnostics
            WHERE `appliance` = '$appliance_type' AND `App Display Check` = '1') t GROUP BY 1";
        } else {
            $sql = "SELECT p.symptom, CEILING(SUM(100) / total) as percentage FROM
            (SELECT * FROM diagnostics WHERE `appliance` = '$appliance_type'
            AND `App Display Check` = '1' AND `Age Range` = '$year') p
            CROSS JOIN (SELECT COUNT(*) AS total FROM diagnostics
            WHERE `appliance` = '$appliance_type'  AND `App Display Check` = '1' AND `Age Range` = '$year') t
            GROUP BY 1";
        }

        //log_message('info', "SQL: " . $sql);

        $query = $this->db->query($sql);

        //log_message('info', "Error msg: " . $this->db->_error_message()); // (mysql_error equivalent)
        //log_message('info', "Error no: " . $this->db->_error_number()); // (mysql_errno equivalent)

        //log_message('info', "SQL: " . $this->db->last_query());

        return $query->result_array();
    }

    function get_appliance_diagnostics_data_symptom_tips($appliance_type, $year, $symptom) {
        //log_message('info', "Entering: " . __METHOD__);
	//log_message('info', "Type: " . $appliance_type . ", Year: " . $year . ", Symptom: " . $symptom);

        if ($year == "All") {
            $sql = "SELECT DISTINCT(Level2_Problem) FROM diagnostics
                    WHERE `appliance` = '$appliance_type' AND `App Display Check` = '1' AND `symptom` = '$symptom'";
        } else {
            $sql = "SELECT DISTINCT(Level2_Problem) FROM diagnostics
                    WHERE `appliance` = '$appliance_type'
                    AND `App Display Check` = '1' AND `Age Range` = '$year' AND `symptom` = '$symptom'";
        }

        $query = $this->db->query($sql);

        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    function get_appliance_diagnostics_data_l2_issues($appliance_type, $year, $symptom) {
        //log_message('info', "Entering: " . __METHOD__);
	//log_message('info', "Type: " . $appliance_type . ", Year: " . $year . ", Symptom: " . $symptom);

        if ($year == "All") {
            $sql = "SELECT DISTINCT(Level2_Problem) FROM diagnostics
                    WHERE `appliance` = '$appliance_type' AND `App Display Check` = '1' AND `symptom` = '$symptom'";
        } else {
            $sql = "SELECT DISTINCT(Level2_Problem) FROM diagnostics
                    WHERE `appliance` = '$appliance_type'
                    AND `App Display Check` = '1' AND `Age Range` = '$year' AND `symptom` = '$symptom'";
        }

        $query = $this->db->query($sql);

        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    function get_appliance_diagnostics_data_solutions($appliance_type, $year, $symptom, $l2_issue) {
//        log_message('info', "Entering: " . __METHOD__ .
//            " => Type: " . $appliance_type . ", Year: " . $year . ", Symptom: " . $symptom);

	if ($year == "All") {
            $sql = "SELECT p.solution, CEILING(SUM(100) / total) as percentage FROM
                    (SELECT * FROM diagnostics
                    WHERE `appliance` = '$appliance_type' AND `App Display Check` = '1'
                    AND `symptom` = '$symptom' AND `Level2_Problem` = '$l2_issue') p
                    CROSS JOIN (SELECT COUNT(*) AS total FROM diagnostics
                    WHERE `appliance` = '$appliance_type' AND `App Display Check` = '1'
                    AND `symptom` = '$symptom' AND `Level2_Problem` = '$l2_issue') t GROUP BY 1";
        } else {
            $sql = "SELECT p.solution, CEILING(SUM(100) / total) as percentage FROM
                    (SELECT * FROM diagnostics WHERE `appliance` = '$appliance_type'
                    AND `App Display Check` = '1' AND `Age Range` = '$year' AND `symptom` = '$symptom'
                    AND `Level2_Problem` = '$l2_issue') p
                    CROSS JOIN (SELECT COUNT(*) AS total FROM diagnostics
                    WHERE `appliance` = '$appliance_type'  AND `App Display Check` = '1' AND `Age Range` = '$year'
                    AND `symptom` = '$symptom' AND `Level2_Problem` = '$l2_issue') t GROUP BY 1";
        }

        $query = $this->db->query($sql);

        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    function get_avg_solution_cost_symptom($appliance_type, $year, $symptom) {
        //log_message('info', "Entering: " . __METHOD__);

	if ($year == "All") {
            $sql = "SELECT ROUND(AVG(price)) as avg_cost FROM `diagnostics`
                WHERE `appliance` = '$appliance_type' AND `symptom` = '$symptom' AND
                `App Display Check` = '1'";
        } else {
            $sql = "SELECT ROUND(AVG(price)) as avg_cost FROM `diagnostics`
                WHERE `appliance` = '$appliance_type' AND `symptom` = '$symptom' AND
                `Age Range` = '$year' AND `App Display Check` = '1'";
        }

        $query = $this->db->query($sql);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query());

        $results = $query->result_array();
        $avg_cost = $results[0]['avg_cost'];
        //log_message('info', __METHOD__ . " => Avg Cost: " . $avg_cost);

        return $avg_cost;
    }

    function get_avg_solution_cost_symptom_solution($appliance_type, $year, $symptom, $solution) {
        //log_message('info', "Entering: " . __METHOD__);
	//log_message('info', "Type: " . $appliance_type . ", Year: " . $year .
        //    ", Symptom: " . $symptom . ", Solution: " . $solution);

        if ($year == "All") {
            $sql = "SELECT ROUND(AVG(price)) as avg_cost FROM `diagnostics`
                WHERE `appliance` = '$appliance_type' AND `symptom` = '$symptom' AND
                `App Display Check` = '1' AND `solution` = '$solution'";
        } else {
            $sql = "SELECT ROUND(AVG(price)) as avg_cost FROM `diagnostics`
                WHERE `appliance` = '$appliance_type' AND `symptom` = '$symptom' AND
                `Age Range` = '$year' AND `App Display Check` = '1' AND `solution` = '$solution'";
        }

        $query = $this->db->query($sql);

        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query());

        $results = $query->result_array();
        $avg_cost = $results[0]['avg_cost'];
        //log_message('info', __METHOD__ . " => Avg Cost: " . $avg_cost);

        return $avg_cost;
    }

    function get_year_range_for_most_occurences_of_any_symptom($appliance_type) {
        //log_message('info', "Entering: " . __METHOD__);

	$sql = "Select `Age Range`, MAX(CAST(`Occurence` AS SIGNED)) as occurence_count from
            (SELECT `Age Range`, COUNT(*) AS Occurence from
            (SELECT * from diagnostics where `Appliance` = '$appliance_type' AND
            `App Display Check` = '1') t group by `Age Range`) t";

        $query = $this->db->query($sql);

        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query());

        $results = $query->result_array();
        $year_range = $results[0]['Age Range'];

        return $year_range;
    }

    function get_year_range_for_most_occurences_of_symptom($appliance_type, $symptom) {
        //log_message('info', "Entering: " . __METHOD__);

	$sql = "Select `Age Range`, MAX(CAST(`Occurence` AS SIGNED)) as occurence_count from
            (SELECT `Age Range`, COUNT(*) AS Occurence from
            (SELECT * from diagnostics where `Appliance` = '$appliance_type' AND
            `App Display Check` = '1' AND `symptom` = '$symptom') t group by `Age Range`) t";

        $query = $this->db->query($sql);

        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query());

        $results = $query->result_array();
        $year_range = $results[0]['Age Range'];

        return $year_range;
    }

}
