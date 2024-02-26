<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuratMasuk extends CI_Controller
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
        $data['title'] = 'Data Surat Keluar - User';
        $data['user'] = $this->db->get_where('user', ['email' =>
            $this->session->userdata('email')])->row_array();
        $query = $this->db->get('surat_masuk');
        $data['result'] = $query->result();

        $this->load->view('templates/user_header', $data);
        $this->load->view('suratmasuk', $data);
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

    public function EditSurat($id = null)
{
    $this->load->helper('date');
    $data['title'] = 'Edit Surat Masuk - User';
    $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();

    // Fetch the letter data based on $id
    $data['letter'] = $this->db->get_where('surat_masuk', ['id' => $id, 'user_id' => $data['user']['id']])->row_array();

    if (!$data['letter']) {
        // Redirect or show an error message if the letter doesn't exist or the user doesn't have permission to edit it
        redirect('suratmasuk');
    }

    // Check if the form is submitted for updating
    if ($this->input->post('update_surat')) {

        if ($this->form_validation->run() == FALSE) {
            // Form validation passed, update the letter in the database
            $updated_data = array(
                'nomor_surat' => $this->input->post('nomor_surat'),
                'tgl_terima' => $this->input->post('tgl_terima'),
                'pengirim' => $this->input->post('pengirim'),
                // Add other fields as needed
            );

            $this->db->where('id', $id);
            $this->db->update('surat_masuk', $updated_data);

            // Redirect to the index page or any other appropriate page after updating
            redirect('suratmasuk');
        }
    }

    $this->load->view('templates/user_header', $data);
    $this->load->view('e_suratmasuk', $data);
    $this->load->view('templates/user_footer');
}


}