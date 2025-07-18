<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testdb extends CI_Controller {

    public function index()
    {
        $this->load->database();

        if ($this->db->initialize()) {
            echo "✅ Database connection is working!";
        } else {
            echo "❌ Failed to connect to database.";
        }
    }
}
