<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->session->userdata('email')) {
            redirect('auth');
        }
        $data['title'] = 'Dashboard - User';
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

        $this->load->view('templates/user_header', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/user_footer');
    }


    public function ChangePassword()
    {

        if (!$this->session->userdata('email')) {
            redirect('auth');
        }

        $this->form_validation->set_rules('current_password', 'Current Password', 'required|trim');
        $this->form_validation->set_rules('new_password1', 'New Password', 'required|trim|min_length[3]|matches[new_password2]');
        $this->form_validation->set_rules('new_password2', 'Confirm New Password', 'required|trim|min_length[3]|matches[new_password1]');

        $data['title'] = 'Change Password';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/user_header', $data);
            $this->load->view('user/changepassword', $data);
            $this->load->view('templates/user_footer');
            
        } else {
            $current_password = $this->input->post('current_password');

            // Check if the entered current password matches the stored password
            if (!password_verify($current_password, $data['user']['password'])) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                    Password lama salah! </div>');
                redirect('user/changepassword');
            } else {
                $new_password = $this->input->post('new_password1');

                if ($current_password == $new_password) {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                    Password baru tidak boleh sama dengan sebelumnya! </div>');
                    redirect('user/changepassword');
                } else {
                    // Password is okay, update the password in the database
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $this->db->set('password', $password_hash);
                    $this->db->where('email', $this->session->userdata('email'));
                    $this->db->update('user');

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                    Password berhasil diganti! </div>');
                    redirect('user/changepassword');
                }
            }
        }
    }

    public function Edit()
    {
        if (!$this->session->userdata('email')) {
            redirect('auth');
        }
        $data['title'] = 'Dashboard - User';
        $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $this->form_validation->set_rules('name', 'Full Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        if ($this->form_validation->run() == false) {
            $this->load->view('user/edit', $data);
        } else {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            
            
            $upload_image = $_FILES['image']['name'];

            if($upload_image)
            {

            $config['upload_path'] = './assets/img/avatars/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']     = '2048';
            
            $this->load->library('upload', $config);

            if($this->upload->do_upload('image')){
                $new_image = $this->upload->data('file_name');
                $this->db->set('image', $new_image);

            }else{
                echo $this->upload->display_errors();
            }

        }

            $this->db->set('name', $name);
            $this->db->where('email', $email);
            $this->db->update('user');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                    Edit profile behasil! </div>');
            redirect('user');
        }
        
    }
}
