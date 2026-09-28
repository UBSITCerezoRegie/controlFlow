<?php
class HomeController {
    public function index() {
        header("Location: /IMDBSE2/public/login.php");
        exit;
    }
}
