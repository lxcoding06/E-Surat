<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuratKeluar extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('email')) {
            redirect('auth');
        }
    }

    public function index()
    {
            $this->load->helper('date');
            $data['title'] = 'Input Data Surat Keluar - User';
            $data['user'] = $this->db->get_where('user', ['email' =>
            $this->session->userdata('email')])->row_array();
            $query = $this->db->get('surat_keluar');
            $data['result'] = $query->result();

            $this->load->view('templates/user_header', $data);
            $this->load->view('suratkeluar', $data);
            $this->load->view('templates/user_footer');  

    }
    
    public function delete($id)
    {
        $user = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $letter = $this->db->get_where('surat_masuk', ['id' => $id, 'user_id' => $user['id']])->row_array();

        if ($letter) {
            // If the letter exists and belongs to the logged-in user, proceed with deletion
            $this->db->delete('surat_masuk', ['id' => $id]);

            // Redirect to the index page or any other appropriate page after deletion
            redirect('suratmasuk');
        } else {
            // Redirect or show an error message, as the letter doesn't exist or the user doesn't have permission to delete it
            redirect('suratmasuk');
        }
    }
}
