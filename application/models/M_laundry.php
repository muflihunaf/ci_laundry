<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laundry extends CI_Model {
    function __construct(){
        $this->load->database();
    }
    // Fungsi Untuk Menghitung Data Order Laundry
    public function hitungOrder()
    {
        $this->db->select('tbl_customer.nama, tbl_customer.id_customer, tbl_paketLaundry.id_paket, tbl_paketLaundry.jenis, tbl_transaksi.*, tbl_detailTransaksi.no_invoice, tbl_detailTransaksi.status_order, tbl_detailTransaksi.status_pembayaran, tbl_detailTransaksi.total, tbl_transaksi.tanggal_order');
        $this->db->from('tbl_transaksi');
        $this->db->join('tbl_customer', 'tbl_transaksi.id_customer = tbl_customer.id_customer');
        $this->db->join('tbl_paketLaundry', 'tbl_transaksi.id_paket = tbl_paketLaundry.id_paket');
        $this->db->join('tbl_detailTransaksi', 'tbl_transaksi.no_invoice = tbl_detailTransaksi.no_invoice');
        $data = $this->db->get();
        return $data->num_rows();
    }
    // Fungsi Untuk mengambil data jenis paket dari database
    public function lihat_jenis()
    {
        $data = $this->db->get('tbl_paketLaundry')->result_array();
        return $data;
    }
    // Fungsi Untuk meng-insert data jenis paket ke database
    public function insertJenis()
    {
        $data = array(
            'jenis' => $this->input->post('jenis'),
            'harga' => $this->input->post('harga')
        );
        $this->db->insert('tbl_paketLaundry',$data);
    }
    // FUngsi Untuk Menghitung Total Pembayaran Paket
    public function total($id_paket,$berat)
    {
        $harga = $this->db->get_where('tbl_paketLaundry', ['id_paket' => $id_paket])->row_array();
        $total = $harga['harga'] * $berat;
        return $total;
    }
    // Fungsi untuk Menyimpan data transaksi Ke Database
    public function simpanTransaksi()
    {
        $transaksi = array(
            'no_invoice' => $this->input->post('invoice'),
            'id_customer' => $this->input->post('customer'),
            'id_paket' => $this->input->post('jenis'),
            'tanggal_order' => date('Y-m-d'),
            'tanggal_ambil' => $this->input->post('tanggalAmbil'),
        );
        $total = $this->total($transaksi['id_paket'],$this->input->post('berat'));
        $detail = array(
            'no_invoice' => $this->input->post('invoice'),
            'status_order' => 'Baru',
            'status_pembayaran' => $this->input->post('statusBayar'),
            'beratCucian' => $this->input->post('berat'),
            'total' => $total,
        );
        $this->db->insert('tbl_transaksi',$transaksi);
        $this->db->insert('tbl_detailTransaksi',$detail);
    }
    // Fungsi Untuk mengambil seluruh data transaksi dari database
    public function lihat_transaksi()
    {
        $this->db->select('tbl_customer.nama, tbl_customer.id_customer, tbl_paketLaundry.id_paket, tbl_paketLaundry.jenis, tbl_transaksi.*, tbl_detailTransaksi.no_invoice, tbl_detailTransaksi.status_order, tbl_detailTransaksi.status_pembayaran, tbl_detailTransaksi.total, tbl_transaksi.tanggal_order');
        $this->db->from('tbl_transaksi');
        $this->db->join('tbl_customer', 'tbl_transaksi.id_customer = tbl_customer.id_customer');
        $this->db->join('tbl_paketLaundry', 'tbl_transaksi.id_paket = tbl_paketLaundry.id_paket');
        $this->db->join('tbl_detailTransaksi', 'tbl_transaksi.no_invoice = tbl_detailTransaksi.no_invoice');
        $data = $this->db->get();
        return $data->result_array();
    }
    // Fungsi Untuk Mengambil Data transaksi Yang Dipilih
    public function detailTransaksi($invoice)
    {
        $this->db->select('tbl_customer.*, tbl_paketLaundry.id_paket, tbl_paketLaundry.*, tbl_transaksi.*, tbl_detailTransaksi.no_invoice, tbl_detailTransaksi.status_order, tbl_detailTransaksi.status_pembayaran, tbl_detailTransaksi.total, tbl_transaksi.tanggal_order, tbl_detailTransaksi.beratCucian');
        $this->db->from('tbl_transaksi');
        $this->db->join('tbl_customer', 'tbl_transaksi.id_customer = tbl_customer.id_customer');
        $this->db->join('tbl_paketLaundry', 'tbl_transaksi.id_paket = tbl_paketLaundry.id_paket');
        $this->db->join('tbl_detailTransaksi', 'tbl_transaksi.no_invoice = tbl_detailTransaksi.no_invoice');
        $this->db->where('tbl_detailTransaksi.no_invoice', $invoice);
        $data = $this->db->get();
        return $data->row_array();
    }
    // Fungsi Untuk mengubah detail data transaksi
    public function updateDetail($no_invoice)
    {
        $data = array(
            'status_order' => $this->input->post('statusOrder'),
            'status_pembayaran' => $this->input->post('statusBayar'),
        );
        $this->db->where('no_invoice',$no_invoice);
        $this->db->update('tbl_detailTransaksi',$data);
    }

}