<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;

class C_SuratMasuk extends CI_Controller {

    public function index()
    {
        $this->load->helper('date');
        $data['title'] = 'Cari Data Surat Masuk - User';
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
        $this->load->view('c_suratmasuk', $data);
        $this->load->view('templates/user_footer');
    }
}
?>
