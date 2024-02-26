<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cara_Penggunaan extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

	public function index()
	{
		$data['title'] = 'Cara Penggunaan - User';
        $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();
		$this->load->view('templates/user_header', $data);
        $this->load->view('Cara_Penggunaan', $data);
        $this->load->view('templates/user_footer');
	}
}
