<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bbm extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model(['M_bbm', 'm_coa']);
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
        // Pagination
        $search = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
        // Pagination
        $config['base_url'] = base_url('bbm/list');
        $config['total_rows'] = $this->M_bbm->countListBbm($search);
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['num_links'] = 3;
        $config['enable_query_strings'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        // Bootstrap style pagination
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '«';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '»';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        // Initialize paginaton
        $this->pagination->initialize($config);
        $page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
        $data['pagination'] = $this->pagination->create_links();

        $data['bbm'] = $this->M_bbm->get_bbm($config['per_page'], $page, $search);

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
        $data['title'] = "BBM";
        $data['pages'] = "pages/bbm/bbm_list";

        $this->load->view('index', $data);
    }

    public function create()
    {
        //inbox notif
        $nip = $this->session->userdata('nip');
        $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
        $sql2 = "SELECT * FROM asset_ruang";
        $sql3 = "SELECT * FROM asset_lokasi";
        $query = $this->db->query($sql);
        $query2 = $this->db->query($sql2);
        $query3 = $this->db->query($sql3);
        $res2 = $query->result_array();
        $asset_ruang = $query2->result();
        $asset_lokasi = $query3->result();
        $result = $res2[0]['COUNT(Id)'];
        $data['count_inbox'] = $result;
        $data['asset_ruang'] = $asset_ruang;
        $data['asset_lokasi'] = $asset_lokasi;

        // Tello
        $sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
        $query4 = $this->db->query($sql4);
        $res4 = $query4->result_array();
        $result4 = $res4[0]['COUNT(Id)'];
        $data['count_inbox2'] = $result4;

        $data['title'] = "Create Bbm";
        $data['pages'] = "pages/bbm/bbm_form";
        $this->load->view('index', $data);
        // $this->load->view('bbm/bbm_form', $data);

    }

    public function insert()
    {
        $tanggal = $this->input->post('tanggal');
        $nomor_lambung = $this->input->post('nomor_lambung');
        $total_harga = $this->input->post('total_harga');
        $now = date('Y-m-d');

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('nomor_lambung', 'Nomor Lambung', 'required');
        $this->form_validation->set_rules('total_harga', 'Total Harga', 'required');

        if (strtotime($tanggal) != strtotime($now)) {
            $created_at = $tanggal;
        } else {
            $created_at = date('Y-m-d H:i:s');
        }

        $user = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
        $cari_bbm = $this->db->get_where('utility', ['Id' => 1])->row();
        $harga_per_liter = $cari_bbm->bbm;
        $total_liter = $total_harga / $harga_per_liter;
        $bbm = [
            'nomor_lambung' => $nomor_lambung,
            'user_id' => $user['nip'],
            'total_harga' => $total_harga,
            'total_liter' => $total_liter,
            'harga_per_liter' => $harga_per_liter,
            'tanggal' => $created_at,
        ];

        $this->db->insert('bbm', $bbm);

        $response = [
            'success' => true,
            'msg' => 'Bbm berhasil ditambahkan!'
        ];

        echo json_encode($response);
    }

    public function ubah($id)
    {

        //inbox notif
        $nip = $this->session->userdata('nip');
        $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
        $sql2 = "SELECT * FROM asset_ruang";
        $sql3 = "SELECT * FROM asset_lokasi";
        $query = $this->db->query($sql);
        $query2 = $this->db->query($sql2);
        $query3 = $this->db->query($sql3);
        $res2 = $query->result_array();
        $asset_ruang = $query2->result();
        $asset_lokasi = $query3->result();
        $result = $res2[0]['COUNT(Id)'];
        $data['count_inbox'] = $result;
        $data['asset_ruang'] = $asset_ruang;
        $data['asset_lokasi'] = $asset_lokasi;

        // Tello
        $sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
        $query4 = $this->db->query($sql4);
        $res4 = $query4->result_array();
        $result4 = $res4[0]['COUNT(Id)'];
        $data['count_inbox2'] = $result4;

        $data['bbm'] = $this->db->get_where('bbm', ['Id' => $id])->row_array();
        $data['title'] = "Update Bbm";
        $data['pages'] = "pages/bbm/bbm_form";
        $this->load->view('index', $data);
        // $this->load->view('pengajuan/pengajuan_form', $data);

    }

    public function update($id)
    {

        $tanggal = $this->input->post('tanggal');
        $nomor_lambung = $this->input->post('nomor_lambung');
        $total_harga = $this->input->post('total_harga');
        $total_liter = $this->input->post('total_liter');
        $harga_per_liter = $this->input->post('harga_per_liter');
        $now = date('Y-m-d');

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('nomor_lambung', 'Nomor Lambung', 'required');
        $this->form_validation->set_rules('total_harga', 'Total Harga', 'required');
        $this->form_validation->set_rules('total_liter', 'Total Liter', 'required');
        $this->form_validation->set_rules('harga_per_liter', 'Harga Per Liter', 'required');

        if (strtotime($tanggal) != strtotime($now)) {
            $created_at = $tanggal;
        } else {
            $created_at = date('Y-m-d H:i:s');
        }


        $user = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
        $bbm = [

            'user_id' => $user['nip'],
            'nomor_lambung' => $nomor_lambung,
            'total_harga' => $total_harga,
            'total_liter' => $total_liter,
            'harga_per_liter' => $harga_per_liter,
            'tanggal' => $created_at,
        ];
        $this->db->where(['Id' => $id]);
        $this->db->update('bbm', $bbm);


        $response = [
            'success' => true,
            'msg' => 'Bbm berhasil Diubah!'
        ];

        echo json_encode($response);
    }

    public function hapus($id)
    {

        $this->db->where(['Id' => $id]);
        $this->db->delete('bbm');


        $response = [
            'success' => true,
            'msg' => 'Bbm berhasil diHapus!'
        ];

        echo json_encode($response);
    }

    public function ubah_harga_bbm()
    {

        //inbox notif
        $nip = $this->session->userdata('nip');
        $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
        $sql2 = "SELECT * FROM asset_ruang";
        $sql3 = "SELECT * FROM asset_lokasi";
        $query = $this->db->query($sql);
        $query2 = $this->db->query($sql2);
        $query3 = $this->db->query($sql3);
        $res2 = $query->result_array();
        $asset_ruang = $query2->result();
        $asset_lokasi = $query3->result();
        $result = $res2[0]['COUNT(Id)'];
        $data['count_inbox'] = $result;
        $data['asset_ruang'] = $asset_ruang;
        $data['asset_lokasi'] = $asset_lokasi;

        // Tello
        $sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
        $query4 = $this->db->query($sql4);
        $res4 = $query4->result_array();
        $result4 = $res4[0]['COUNT(Id)'];
        $data['count_inbox2'] = $result4;

        $data['bbm'] = $this->db->get_where('utility', ['Id' => 1])->row_array();
        $data['title'] = "Update Bbm";
        $data['pages'] = "pages/bbm/bbm_form";
        $this->load->view('index', $data);
        // $this->load->view('pengajuan/pengajuan_form', $data);

    }
    public function update_harga()
    {

        $bbm = $this->input->post('bbm');

        $this->form_validation->set_rules('bbm', 'Harga BBM', 'required');
        $bbm = [


            'bbm' => $bbm,
        ];
        $this->db->where(['Id' => 1]);
        $this->db->update('utility', $bbm);


        $response = [
            'success' => true,
            'msg' => 'Bbm berhasil Diubah!'
        ];

        echo json_encode($response);
    }
}
