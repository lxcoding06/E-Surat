<?php
defined('BASEPATH') or exit('No direct script access allowed');

class I_SuratKeluar extends CI_Controller
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

        $this->form_validation->set_rules('nidn', 'NIDN', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('nik', 'NIK', 'trim');
        $this->form_validation->set_rules('jabatan', 'Jabatan', 'required|trim');
        $this->form_validation->set_rules('pg_studi', 'Program Studi', 'required');
        $this->form_validation->set_rules('nkegiatan', 'Nama Kegiatan', 'required|trim');
        $this->form_validation->set_rules('a_tim', 'Anggota Tim', 'trim');

        if($this->form_validation->run() == false)
        {
            
            $data['title'] = 'Input Data Surat Keluar - User';
            $data['user'] = $this->db->get_where('user', ['email' =>
            $this->session->userdata('email')])->row_array();
            $this->load->view('templates/user_header', $data);
            $this->load->view('i_suratkeluar', $data);
            $this->load->view('templates/user_footer');
            
        }else{
            $user_data = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
            // Query to get the latest 'no_urut' value
            $latest_no_urut_query = $this->db->select('no_urut')->from('surat_keluar')->order_by('id', 'DESC')->limit(1)->get();
            $latest_no_urut = $latest_no_urut_query->row_array();

            // Increment the 'no_urut' value or set to 1 if it doesn't exist
            $no_urut = isset($latest_no_urut['no_urut']) ? $latest_no_urut['no_urut'] + 1 : 1;

            $data = [
                'id' => $this->input->post('id',true),
                'user_id' => $user_data['id'],
                'status' => '1',
                'nidn' => $this->input->post('nidn',true),
                'nama' => $this->input->post('nama'),
                'nik' => $this->input->post('nik'),
                'jabatan' => $this->input->post('jabatan'),
                'pg_studi' => $this->input->post('pg_studi'),
                'nkegiatan' => $this->input->post('nkegiatan'),
                'tgl_pl' => $this->input->post('tgl_pl'),
                'a_tim' => $this->input->post('a_tim'),
                'j_surat' => $this->input->post('j_surat'),
                'date_created' => $this->input->post('date_created'),
                'no_urut' => $no_urut,

            ];

            $upload_file = $_FILES['bukti']['name'];

            if($upload_file)
            {

            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'pdf|jpg|png';
            $config['max_size']     = '10000';
            
            $this->load->library('upload', $config);

            if($this->upload->do_upload('bukti')){
                $new_image = $this->upload->data('file_name');
                $this->db->set('bukti', $new_image);

            }else{
                echo $this->upload->display_errors();
            }
            $this->db->insert('surat_keluar', $data);
            
            $inserted_id = $this->db->insert_id();

            $data2 = [
                'id_surat' => $inserted_id,
                'nomor_surat' => $this->input->post('nomor_surat'),
                'isi' => $this->input->post('isi',true),
                'pengesahan' => '1',
                'harap' => $this->input->post('harap'),
                'catatan' => $this->input->post('catatan'),
            ];

            $this->db->insert('surat_keluar_disposisi', $data2);
            $this->session->set_flashdata('message', '<div class="alert alert-primary" role="alert">
                Berhasil Meminta surat </div>');
                redirect('suratkeluar');
            }

        }
    }
}