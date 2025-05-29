<?php
// /home/ubuntu/siwes_system/app/Controllers/HomeController.php

class HomeController {
    public function index() {
        // Load the home view
        require_once VIEWS_PATH . "/home/index.php";
    }
}
?>
