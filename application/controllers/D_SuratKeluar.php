<?php
defined('BASEPATH') or exit('No direct script access allowed');

class D_SuratKeluar extends CI_Controller
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
        $data['title'] = 'Disposisi Surat - User';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $query = $this->db->get('surat_keluar');
        $data['result'] = $query->result();
        $this->load->view('templates/user_header', $data);
        $this->load->view('d_suratkeluar', $data);
        $this->load->view('templates/user_footer');
    }

    public function surat($id = null)
    {
        $data['title'] = 'Disposisi Surat - User';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

       
        if ($id) {
            
            $query = $this->db->select('*')
                ->from('surat_keluar')
                ->join('surat_keluar_disposisi', 'surat_keluar.id = surat_keluar_disposisi.id_surat', 'left')
                ->where('surat_keluar.id', $id)
                ->get();

            $data['result'] = $query->row();
        } else {
           
            $data['result'] = null;
        }

        $this->load->view('templates/user_header', $data);
        $this->load->view('d_suratkeluar', $data);
        $this->load->view('templates/user_footer');
    }
}
