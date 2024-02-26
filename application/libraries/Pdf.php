<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pdf
{
    public function createPDF($html, $fileName)
    {
        // Load library TCPDF
        require_once APPPATH . 'libraries/tcpdf/tcpdf.php';

        // Buat objek TCPDF
        $pdf = new TCPDF();

        // Set format, margin, dan halaman
        $pdf->SetCreator('Your Name');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Surat PDF');
        $pdf->SetSubject('Surat PDF');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->AddPage();

        // Tambahkan konten HTML ke dalam PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Simpan PDF ke file atau tampilkan di browser
        $pdf->Output($fileName, 'I');
    }
}