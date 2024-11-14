<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Detail_No_Lambung extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model(['M_detail_no_lambung', 'm_coa']);
        $this->load->library(array('form_validation', 'session', 'user_agent', 'Api_Whatsapp'));
        $this->load->library('pagination');
        $this->cb = $this->load->database('corebank', TRUE);
        $this->load->helper('url', 'form', 'download');
        date_default_timezone_set('Asia/Jakarta');

        if ($this->session->userdata('isLogin') == FALSE) {
            redirect('home');
        }
    }

    public function list()
    {
        $this->db->select('Id, nama_asset');
        $this->db->from('asset_list');
        $aset = $this->db->get()->result();
        $data['asset'] = $aset;
        //inbox notif
        $nip = $this->session->userdata('nip');
        $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
        $query = $this->db->query($sql);
        $res2 = $query->result_array();
        $result = $res2[0]['COUNT(Id)'];
        $data['count_inbox'] = $result;

        $sql3 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
        $query3 = $this->db->query($sql3);
        $res3 = $query3->result_array();
        $result3 = $res3[0]['COUNT(id)'];
        $data['count_inbox2'] = $result3;
        $data['title'] = "Detail Nomor Lambung";
        $data['pages'] = "pages/detail_no_lambung/detail_list";

        $this->load->view('index', $data);
    }

    public function cari($bulan, $tahun, $id)
    {
        $data = $this->M_detail_no_lambung->get_detail($bulan, $tahun, $id);

        if (!empty($data)) {
            echo json_encode(array("status" => "Success", "data" => $data));
        } else {
            echo json_encode(array("status" => "No Data"));
        }
    }
    public function cari_ritasi_sparepart($id, $bulan, $tahun)
    {
        $data = $this->M_detail_no_lambung->get_detail_ritasi_spare_part($id, $bulan, $tahun);

        echo json_encode(array("status" => "Success", "data" => $data));
    }
    public function cari_ritasi_tonase($id, $bulan, $tahun)
    {
        $data = $this->M_detail_no_lambung->get_detail_ritasi_tonase($id, $bulan, $tahun);

        echo json_encode(array("status" => "Success", "data" => $data));
    }
    public function cari_ritasi_bbm($id, $bulan, $tahun)
    {
        $data = $this->M_detail_no_lambung->get_detail_ritasi_bbm($id, $bulan, $tahun);

        echo json_encode(array("status" => "Success", "data" => $data));
    }
}
