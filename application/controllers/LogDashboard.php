<?php

class LogDashboard extends CI_Controller{
    
    private $logViewer;
    
    function __construct() {
        parent::__construct();
        $this->logViewer = new \CILogViewer\CILogViewer();
    }
    
    public function index() {
        echo $this->logViewer->showLogs();
        return;
    }
}
?>