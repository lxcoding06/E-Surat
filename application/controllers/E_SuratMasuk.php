<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_SuratMasuk extends CI_Controller
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

        $this->form_validation->set_rules('nomor_surat', 'Nomor Surat', 'required|trim');
        $this->form_validation->set_rules('pengirim', 'Pengirim', 'trim');
        $this->form_validation->set_rules('kepada', 'Ditujukan Kepada', 'trim');

        if($this->form_validation->run() == false)
        {
            
            $data['title'] = 'Input Data Surat Masuk - User';
            $data['user'] = $this->db->get_where('user', ['email' =>
            $this->session->userdata('email')])->row_array();
            $this->load->view('templates/user_header', $data);
            $this->load->view('e_suratmasuk', $data);
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
                redirect('suratmasuk');
            }

        }
    }
    public function update_data($id)
    {
        $this->form_validation->set_rules('nomor_surat', 'Nomor Surat', 'required|trim');
        $this->form_validation->set_rules('pengirim', 'Pengirim', 'trim');
        $this->form_validation->set_rules('kepada', 'Ditujukan Kepada', 'trim');

        if ($this->form_validation->run() == false) {

            $data['title'] = 'Edit Data Surat Masuk - User';
            $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
            $data['letter'] = $this->db->get_where('surat_masuk', ['id' => $id])->row_array();
            $this->load->view('templates/user_header', $data);
            $this->load->view('e_suratmasuk', $data);
            $this->load->view('templates/user_footer');
        } else {
            $user_data = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

            $data = [
                'id' => $id,
                'user_id' => $user_data['id'],
                'status' => '2',
                'nomor_surat' => $this->input->post('nomor_surat',true),
                'tgl_terima' => $this->input->post('tgl_terima'),
                'pengirim' => $this->input->post('pengirim'),
                'kepada' => $this->input->post('kepada'),
                'tgl_pl' => $this->input->post('tgl_pl'),
                'date_created' => $this->input->post('date_created'),
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

            // If no new file is uploaded, retain the existing file name
            if (!$upload_file && $letter['berkas']) {
                $data['berkas'] = $letter['berkas'];
            }

            $this->db->where('id', $id);
            $this->db->update('surat_masuk', $data);

            $data2 = [
                'id_surat' => $id,
                // ... existing data fields for surat_masuk_disposisi ...
            ];

            $this->db->where('id_surat', $id);
            $this->db->update('surat_masuk_disposisi', $data2);

            $this->session->set_flashdata('message', '<div class="alert alert-primary" role="alert">
                Berhasil Memperbarui Surat </div>');
            redirect('suratmasuk');
            }
        }
    }
}