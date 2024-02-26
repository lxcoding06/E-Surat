<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('email')) {
            redirect('auth');
        }
    }

    // application/controllers/Admin.php

public function index()
{
    $data['title'] = 'Dashboard - Admin';
    $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();

    // Get the sum of surat_masuk
    $query = $this->db->select_sum('id')->get('surat_masuk');
    $data['sum_of_surat_masuk'] = $query->row()->id;

    // Get the sum of surat_keluar
    $query = $this->db->select_sum('id')->get('surat_keluar');
    $data['sum_of_surat_keluar'] = $query->row()->id;

    // Get the sum of surat_masuk_disposisi
    $query = $this->db->select_sum('id')->get('surat_masuk_disposisi');
    $data['sum_of_surat_masuk_disposisi'] = $query->row()->id;

    // Get the sum of surat_keluar
    $query = $this->db->select_sum('id')->get('surat_keluar_disposisi');
    $data['sum_of_surat_keluar_disposisi'] = $query->row()->id;

    // Get the latest surat_masuk
    $query = $this->db->order_by('date_created', 'desc')->limit(1)->get('surat_masuk');
    $latest_surat_masuk = $query->row();

    // Get the latest surat_keluar
    $query = $this->db->order_by('date_created', 'desc')->limit(1)->get('surat_keluar');
    $latest_surat_keluar = $query->row();

    $data['latest_surat_masuk_date'] = $latest_surat_masuk ? $latest_surat_masuk->date_created : null;
    $data['latest_surat_keluar_date'] = $latest_surat_keluar ? $latest_surat_keluar->date_created : null;

    $this->load->view('admin/index', $data);
}


    public function suratkeluar()
{
    $data['title'] = 'Surat Keluar - Admin';
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    // Fetch data from surat_keluar table
    $query = $this->db->get('surat_keluar');
    $data['result'] = $query->result();

    // Fetch pengesahan data from surat_keluar_disposisi table
    foreach ($data['result'] as &$row) {
        $surat_disposisi_query = $this->db->select('pengesahan')
            ->from('surat_keluar_disposisi')
            ->where('id_surat', $row->id)
            ->get();

        $surat_disposisi_result = $surat_disposisi_query->row();

        if ($surat_disposisi_result) {
            $row->pengesahan = $surat_disposisi_result->pengesahan;
        } else {
            $row->pengesahan = null;
        }
    }

    $this->load->view('templates/user_header', $data);
    $this->load->view('admin/suratkeluar', $data);
    $this->load->view('templates/user_footer');
}


public function disposisi_surat($id = null)
{
    $data['title'] = 'Disposisi Surat - User';
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle form submission
        $update_data = array(
            'nomor_surat' => $this->input->post('nomor_surat'),
            'isi' => $this->input->post('isi'),
            'pengesahan' => $this->input->post('pengesahan'),
            'harap' => $this->input->post('harap'),
            'catatan' => $this->input->post('catatan'),
        );

        // Update surat_keluar_disposisi table
        $this->db->where('id_surat', $id)->update('surat_keluar_disposisi', $update_data);

        // Update status in surat_keluar table based on pengesahan value
        $status = ($this->input->post('pengesahan') == 2) ? 3 : 2;
        $this->db->where('id', $id)->update('surat_keluar', array('status' => $status));

        redirect('Admin/suratkeluar'); // Redirect to a suitable page after the form submission
    } else {
        // Display the form
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
        $this->load->view('admin/d_suratkeluar', $data);
        $this->load->view('templates/user_footer');
    }
}

        public function downloadSurat($id_surat)
    {
        // Ambil data surat keluar berdasarkan id
        $surat = $this->db->get_where('surat_keluar', ['id' => $id_surat])->row();

        // Cek apakah surat ditemukan
        if (!$surat) {
            // Tampilkan pesan error atau redirect ke halaman lain jika surat tidak ditemukan
            redirect('halaman_error');
        }

        // Ambil nomor_surat dari tabel surat_keluar_disposisi
        $this->db->select('nomor_surat');
        $this->db->from('surat_keluar_disposisi');
        $this->db->where('id_surat', $id_surat);
        $result = $this->db->get()->row();

        // Tambahkan nomor_surat ke data surat
        $surat->nomor_surat = $result ? $result->nomor_surat : null;

        // Load view untuk menampilkan data surat
        $data['surat'] = $surat;
        $this->load->view('templates/view_surat', $data);
    }

    public function suratmasuk()
    {
        $this->load->helper('date');
            $data['title'] = 'Data Surat Keluar - Admin';
            $data['user'] = $this->db->get_where('user', ['email' =>
            $this->session->userdata('email')])->row_array();
            $query = $this->db->get('surat_masuk');
            $data['result'] = $query->result();

            $this->load->view('templates/user_header', $data);
            $this->load->view('admin/suratmasuk', $data);
            $this->load->view('templates/user_footer');
    }

    public function disposisi_suratmasuk($id = null)
    {
        $data['title'] = 'Disposisi Surat - Admin';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle form submission
        $update_data = array(
            'perihal' => $this->input->post('perihal'),
            'isi' => $this->input->post('isi'),
            'jabatan_penerima' => $this->input->post('jabatan_penerima'),
        );

        // Update surat_keluar_disposisi table
        $this->db->where('id_surat', $id)->update('surat_masuk_disposisi', $update_data);

        // Update status in surat_keluar table
        $this->db->where('id', $id)->update('surat_masuk', array('status' => 2));

        redirect('Admin/suratmasuk'); // Redirect to a suitable page after the form submission
        } else {
        // Display the form
        if ($id) {
            $query = $this->db->select('*')
                ->from('surat_masuk')
                ->join('surat_masuk_disposisi', 'surat_masuk.id = surat_masuk_disposisi.id_surat', 'left')
                ->where('surat_masuk.id', $id)
                ->get();

            $data['result'] = $query->row();
        } else {
            $data['result'] = null;
        }

        $this->load->view('templates/user_header', $data);
        $this->load->view('admin/d_suratmasuk', $data);
        $this->load->view('templates/user_footer');
        }
    
    }

    public function i_suratmasuk()
    {

            $this->form_validation->set_rules('nomor_surat', 'Nomor Surat', 'required|trim');
            $this->form_validation->set_rules('pengirim', 'Pengirim', 'trim');
            $this->form_validation->set_rules('kepada', 'Ditujukan Kepada', 'trim');

            if($this->form_validation->run() == false)
            {
            
            $data['title'] = 'Input Data Surat Masuk - User';
            $data['user'] = $this->db->get_where('user', ['email' =>
            $this->session->userdata('email')])->row_array();
            $this->load->view('templates/user_header', $data);
            $this->load->view('admin/i_suratmasuk', $data);
            $this->load->view('templates/user_footer');
            
            }else{
            $user_data = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
            // Query to get the latest 'no_urut' value
            $latest_no_urut_query = $this->db->select('no_urut')->from('surat_masuk')->order_by('id', 'DESC')->limit(1)->get();
            $latest_no_urut = $latest_no_urut_query->row_array();

            // Increment the 'no_urut' value or set to 1 if it doesn't exist
            $no_urut = isset($latest_no_urut['no_urut']) ? $latest_no_urut['no_urut'] + 1 : 1;

            $data = [
                'id' => $this->input->post('id',true),
                'user_id' => $user_data['id'],
                'status' => '2',
                'nomor_surat' => $this->input->post('nomor_surat',true),
                'tgl_terima' => $this->input->post('tgl_terima'),
                'pengirim' => $this->input->post('pengirim'),
                'kepada' => $this->input->post('kepada'),
                'tgl_pl' => $this->input->post('tgl_pl'),
                'date_created' => $this->input->post('date_created'),
                'no_urut' => $no_urut,

            ];

            $upload_file = $_FILES['berkas']['name'];

            if($upload_file)
            {

            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'pdf|jpg|png';
            $config['max_size']     = '10000';
            
            $this->load->library('upload', $config);

            if($this->upload->do_upload('berkas')){
                $new_image = $this->upload->data('file_name');
                $this->db->set('berkas', $new_image);

            }else{
                echo $this->upload->display_errors();
            }

            $this->db->insert('surat_masuk', $data);
            $inserted_id = $this->db->insert_id();

            $data2 = [
                'id_surat' => $inserted_id,
                'perihal' => $this->input->post('perihal'),
                'isi' => $this->input->post('isi',true),
                'jabatan_penerima' => $this->input->post('jabatan_penerima'),
            ];
            $this->db->insert('surat_masuk_disposisi', $data2);
            $this->session->set_flashdata('message', '<div class="alert alert-primary" role="alert">
                Berhasil Mengirim Surat </div>');
                redirect('admin/suratmasuk');
            }

        }
        
    }
    public function c_suratmasuk()
    {
        $this->load->helper('date');
        $data['title'] = 'Cari Data Surat Masuk - Admin';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $search_keyword = $this->input->post('cari');

        if ($search_keyword) {
            // Memebuat Tokenisasi 
            $tokenizer = new WhitespaceTokenizer();
            $keywords = $tokenizer->tokenize(strtolower($search_keyword));

            // membuat semua huruf kecil pada setiap inputan keyword
            foreach ($keywords as $keyword) {
                $this->db->like('LOWER(surat_masuk.nomor_surat)', $keyword);
                $this->db->or_like('LOWER(surat_masuk.pengirim)', $keyword);
                $this->db->or_like('LOWER(surat_masuk.kepada)', $keyword);
                $this->db->or_like('LOWER(surat_masuk_disposisi.jabatan_penerima)', $keyword);
                $this->db->or_like('LOWER(surat_masuk_disposisi.perihal)', $keyword);
            }

            $this->db->join('surat_masuk_disposisi', 'surat_masuk.id = surat_masuk_disposisi.id_surat', 'left');
            $query = $this->db->get('surat_masuk');

            $data['result'] = $query->result();
        } else {
            $data['result'] = array();
        }

        $this->load->view('templates/user_header', $data);
        $this->load->view('admin/c_suratmasuk', $data);
        $this->load->view('templates/user_footer');
    }


    public function c_suratkeluar()
    {
        $this->load->helper('date');
        $data['title'] = 'Cari Data Surat Keluar - Admin';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $search_keyword = $this->input->post('cari');

        if ($search_keyword) {
            // Memebuat Tokenisasi 
            $tokenizer = new WhitespaceTokenizer();
            $keywords = $tokenizer->tokenize(strtolower($search_keyword));

            // membuat semua huruf kecil pada setiap inputan keyword
            foreach ($keywords as $keyword) {
                $this->db->like('LOWER(surat_keluar.nidn)', $keyword);
                $this->db->or_like('LOWER(surat_keluar.nama)', $keyword);
                $this->db->or_like('LOWER(surat_keluar.nik)', $keyword);
                $this->db->or_like('LOWER(surat_keluar.a_tim)', $keyword);
                $this->db->or_like('LOWER(surat_keluar.j_surat)', $keyword);
                $this->db->or_like('LOWER(surat_keluar_disposisi.nomor_surat)', $keyword);
            }

            $this->db->join('surat_keluar_disposisi', 'surat_keluar.id = surat_keluar_disposisi.id_surat', 'left');
            $query = $this->db->get('surat_keluar');

            $data['result'] = $query->result();
        } else {
            $data['result'] = array();
        }

        $this->load->view('templates/user_header', $data);
        $this->load->view('c_suratkeluar', $data);
        $this->load->view('templates/user_footer');
    }
}