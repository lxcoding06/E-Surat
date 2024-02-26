<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;

class C_SuratKeluar extends CI_Controller {

    public function index()
    {
        $this->load->helper('date');
        $data['title'] = 'Cari Data Surat Keluar - User';
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
?>
