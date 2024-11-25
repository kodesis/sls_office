<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ritasi extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model(['M_ritasi', 'm_coa']);
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
        $config['base_url'] = base_url('ritasi/list');
        $config['total_rows'] = $this->M_ritasi->countListRitasi($search);
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

        $data['ritasi'] = $this->M_ritasi->get_ritasi($config['per_page'], $page, $search);

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
        $data['title'] = "Ritasi";
        $data['pages'] = "pages/ritasi/ritasi_list";

        $this->load->view('index', $data);
    }
    public function detail_list($id)
    {
        $data['detail_ritasi'] = $this->M_ritasi->get_detail_ritasi(['id_ritasi_header' => $id]);

        $response = [
            'success' => true,
            'data' => $data['detail_ritasi'],
            'msg' => 'Detail Ritasi berhasil ditampilkan!'
        ];

        echo json_encode($response);
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

        $this->db->select('Id, nama_asset');
        $this->db->from('asset_list');
        $asset = $this->db->get()->result();
        $data['asset'] = $asset;

        $data['title'] = "Create Ritasi";
        $data['pages'] = "pages/ritasi/ritasi_form";
        $this->load->view('index', $data);
        // $this->load->view('ritasi/ritasi_form', $data);

    }

    public function insert()
    {
        $tanggal = $this->input->post('tanggal');
        $nomor_lambung = $this->input->post('nomor_lambung');
        $nama_driver = $this->input->post('nama_driver');
        $shift = $this->input->post('shift');
        $jam_awal = $this->input->post('jam_awal');
        $jam_akhir = $this->input->post('jam_akhir');
        $km_awal = $this->input->post('km_awal');
        $km_akhir = $this->input->post('km_akhir');
        $total_km = $this->input->post('total_km');
        $harga_km = $this->input->post('harga_km');
        $total_harga_km = $this->input->post('total_harga_km');

        $total_tonase = $this->input->post('total_tonase');
        $harga_tonase = $this->input->post('harga_tonase');
        $total_harga_tonase = $this->input->post('total_harga_tonase');
        $now = date('Y-m-d H:i:s');

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('nomor_lambung', 'Nomor Lambung', 'required');
        $this->form_validation->set_rules('nama_driver', 'Nama Driver', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('jam_awal', 'Jam Awal', 'required');
        $this->form_validation->set_rules('jam_akhir', 'Jam Akhir', 'required');
        $this->form_validation->set_rules('km_awal', 'KM Awal', 'required');
        $this->form_validation->set_rules('km_akhir', 'KM Akhir', 'required');
        $this->form_validation->set_rules('total_km', 'Total KM', 'required');
        $this->form_validation->set_rules('harga_km', 'Harga KM', 'required');
        $this->form_validation->set_rules('total_harga_km', 'Total Harga KM', 'required');
        $this->form_validation->set_rules('total_tonase', 'Total Tonase', 'required');
        $this->form_validation->set_rules('harga_tonase', 'Harga Tonase', 'required');
        $this->form_validation->set_rules('total_harga_tonase', 'Total Harga Tonase', 'required');

        if (strtotime($tanggal) != strtotime($now)) {
            $created_at = $tanggal;
        } else {
            $created_at = date('Y-m-d H:i:s');
        }

        // $user = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
        $ritasi = [
            'nomor_lambung' => $nomor_lambung,
            // 'user_nip' => $user['nip'],
            'nama_driver' => $nama_driver,
            'tanggal' => $created_at,
            'shift' => $shift,
            'jam_awal' => $jam_awal,
            'jam_akhir' => $jam_akhir,
            'km_awal' => $km_awal,
            'km_akhir' => $km_akhir,
            'total_km' => $total_km,
            'harga_km' => $harga_km,
            'total_harga_km' => $total_harga_km,

            'total_tonase' => $total_tonase,
            'harga_tonase' => $harga_tonase,
            'total_harga_tonase' => $total_harga_tonase,
        ];

        $this->cb->insert('t_ritasi', $ritasi);

        $response = [
            'success' => true,
            'msg' => 'Ritasi berhasil ditambahkan!'
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


        $this->db->select('Id, nama_asset');
        $this->db->from('asset_list');
        $asset = $this->db->get()->result();
        $data['asset'] = $asset;


        $data['ritasi'] = $this->cb->get_where('t_ritasi', ['id' => $id])->row_array();
        $data['title'] = "Update Ritasi";
        $data['pages'] = "pages/ritasi/ritasi_form";
        $this->load->view('index', $data);
        // $this->load->view('pengajuan/pengajuan_form', $data);

    }

    public function update($id)
    {
        $tanggal = $this->input->post('tanggal');
        $nomor_lambung = $this->input->post('nomor_lambung');
        $nama_driver = $this->input->post('nama_driver');
        $shift = $this->input->post('shift');
        $jam_awal = $this->input->post('jam_awal');
        $jam_akhir = $this->input->post('jam_akhir');
        $km_awal = $this->input->post('km_awal');
        $km_akhir = $this->input->post('km_akhir');
        $total_km = $this->input->post('total_km');
        $harga_km = $this->input->post('harga_km');
        $total_harga_km = $this->input->post('total_harga_km');

        $total_tonase = $this->input->post('total_tonase');
        $harga_tonase = $this->input->post('harga_tonase');
        $total_harga_tonase = $this->input->post('total_harga_tonase');

        $now = date('Y-m-d H:i:s');

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('nomor_lambung', 'Nomor Lambung', 'required');
        $this->form_validation->set_rules('nama_driver', 'Nama Driver', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('jam_awal', 'Jam Awal', 'required');
        $this->form_validation->set_rules('jam_akhir', 'Jam Akhir', 'required');
        $this->form_validation->set_rules('km_awal', 'KM Awal', 'required');
        $this->form_validation->set_rules('km_akhir', 'KM Akhir', 'required');
        $this->form_validation->set_rules('total_km', 'Total KM', 'required');
        $this->form_validation->set_rules('harga_km', 'Harga KM', 'required');
        $this->form_validation->set_rules('total_harga_km', 'Total Harga KM', 'required');
        $this->form_validation->set_rules('total_tonase', 'Total Tonase', 'required');
        $this->form_validation->set_rules('harga_tonase', 'Harga Tonase', 'required');
        $this->form_validation->set_rules('total_harga_tonase', 'Total Harga Tonase', 'required');

        // if (strtotime($tanggal) != strtotime($now)) {
        //     $created_at = $tanggal;
        // } else {
        //     $created_at = date('Y-m-d H:i:s');
        // }

        $user = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
        $ritasi = [
            'nomor_lambung' => $nomor_lambung,
            // 'user_nip' => $user['nip'],
            'nama_driver' => $nama_driver,
            'tanggal' => $tanggal,
            'shift' => $shift,
            'jam_awal' => $jam_awal,
            'jam_akhir' => $jam_akhir,
            'km_awal' => $km_awal,
            'km_akhir' => $km_akhir,
            'total_km' => $total_km,
            'harga_km' => $harga_km,
            'total_harga_km' => $total_harga_km,

            'total_tonase' => $total_tonase,
            'harga_tonase' => $harga_tonase,
            'total_harga_tonase' => $total_harga_tonase,
        ];
        $this->cb->where(['id' => $id]);
        $this->cb->update('t_ritasi', $ritasi);


        $response = [
            'success' => true,
            'msg' => 'Ritasi berhasil Diubah!'
        ];

        echo json_encode($response);
    }

    public function hapus($id)
    {

        $this->cb->where(['id' => $id]);
        $this->cb->delete('t_ritasi');


        $response = [
            'success' => true,
            'msg' => 'Ritasi berhasil diHapus!'
        ];

        echo json_encode($response);
    }

    public function insert_detail()
    {
        $id_header = $this->input->post('id_header');
        $lokasi_loading = $this->input->post('lokasi_loading');
        $tujuan = $this->input->post('tujuan');
        $jam = $this->input->post('jam');
        $berat_awal = $this->input->post('berat_awal');
        $berat_akhir = $this->input->post('berat_akhir');
        $km = $this->input->post('km');

        $total_berat = $berat_awal - $berat_akhir;

        $now = date('Y-m-d H:i:s');

        $this->form_validation->set_rules('lokasi_loading', 'Lokasi Loading', 'required');
        $this->form_validation->set_rules('tujuan', 'Tujuan', 'required');
        $this->form_validation->set_rules('jam', 'Jam', 'required');
        $this->form_validation->set_rules('berat_awal', 'Berat Awal', 'required');
        $this->form_validation->set_rules('berat_akhir', 'Berat Akhir', 'required');
        $this->form_validation->set_rules('km', 'KM', 'required');

        $this->cb->where('id_ritasi_header', $id_header);
        $cek = $this->cb->get('t_detail_ritasi')->result();

        if (empty($cek)) {
            // Fetch details from 't_ritasi' if no records are found
            $detail = $this->cb->get_where('t_ritasi', ['id' => $id_header])->row_array();
            if ($detail && isset($detail['km_awal'])) {
                $km_awal = $detail['km_awal'];
                $total_km = $km - $km_awal; // Ensure $km is defined
            } else {
                // Handle missing data
                $total_km = 0; // Default value or handle appropriately
            }
        } else {
            // Fetch the latest record from 't_detail_ritasi'
            $this->cb->where('id_ritasi_header', $id_header);
            $this->cb->order_by('id', 'DESC'); // Ensure 'id' or relevant column exists
            $this->cb->limit(1);
            $detail = $this->cb->get('t_detail_ritasi')->row();

            if ($detail && isset($detail->km)) {
                $km_awal = $detail->km; // Use '->km' since it returns an object
                $total_km = $km - $km_awal; // Ensure $km is defined
            } else {
                // Handle missing data
                $total_km = 0; // Default value or handle appropriately
            }
        }


        // $user = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
        $ritasi = [
            'id_ritasi_header' => $id_header,
            'lokasi_loading' => $lokasi_loading,
            // 'user_nip' => $user['nip'],
            'tujuan' => $tujuan,
            'jam' => $jam,
            'berat_awal' => $berat_awal,
            'berat_akhir' => $berat_akhir,
            'total_tonase' => $total_berat,
            'km' => $km,
            'total_km' => $total_km,
        ];

        $this->cb->insert('t_detail_ritasi', $ritasi);


        $this->cb->select('SUM(total_tonase) as total_tonase, SUM(total_km) as total_km');
        $this->cb->where('id_ritasi_header', $id_header);
        $detail = $this->cb->get('t_detail_ritasi')->row();
        if ($detail) {
            // Fetch utility data
            $cari_harga = $this->db->get_where('utility', ['Id' => 1])->row();
            if ($cari_harga) {
                $harga_km = $cari_harga->km ?? 0; // Default to 0 if not set
                $harga_tonase = $cari_harga->tonase ?? 0;

                // Calculate totals
                $total_harga_km = ($detail->total_km ?? 0) * $harga_km;
                $total_harga_tonase = ($detail->total_tonase ?? 0) * $harga_tonase;

                // Prepare data for update
                $ritasi = [
                    'km_akhir' => $km,

                    'total_km' => $detail->total_km ?? 0,
                    'harga_km' => $harga_km,
                    'total_harga_km' => $total_harga_km,

                    'total_tonase' => $detail->total_tonase ?? 0,
                    'harga_tonase' => $harga_tonase,
                    'total_harga_tonase' => $total_harga_tonase,
                ];

                // Update the 't_ritasi' table
                $this->cb->where(['id' => $id_header]);
                $this->cb->update('t_ritasi', $ritasi);
            } else {
                // Handle missing 'utility' data
                $response = [
                    'success' => False,
                    'msg' => 'Harga Tidak Di Temukan'
                ];
                echo json_encode($response);
            }
        } else {
            // Handle missing 't_detail_ritasi' data
            $response = [
                'success' => False,
                'msg' => 'Shift Tidak Ditemukan'
            ];
            echo json_encode($response);
        }


        $response = [
            'success' => true,
            'msg' => 'Detail Ritasi berhasil ditambahkan!'
        ];

        echo json_encode($response);
    }
    public function detail_edit($id)
    {
        $data = $this->M_ritasi->get_id_edit($id);

        echo json_encode($data);
    }

    public function edit_detail()
    {
        $id_header = $this->input->post('id_header_edit');
        $lokasi_loading = $this->input->post('lokasi_loading');
        $tujuan = $this->input->post('tujuan');
        $jam = $this->input->post('jam');
        $hm = $this->input->post('hm');
        $km = $this->input->post('km');


        $this->form_validation->set_rules('lokasi_loading', 'Lokasi Loading', 'required');
        $this->form_validation->set_rules('tujuan', 'Tujuan', 'required');
        $this->form_validation->set_rules('jam', 'Jam', 'required');
        $this->form_validation->set_rules('hm', 'HM', 'required');
        $this->form_validation->set_rules('km', 'KM', 'required');

        // $user = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
        $ritasi = [
            // 'id_ritasi_header' => $id_header,
            'lokasi_loading' => $lokasi_loading,
            // 'user_nip' => $user['nip'],
            'tujuan' => $tujuan,
            'jam' => $jam,
            'hm' => $hm,
            'km' => $km,
        ];

        $this->cb->where('Id', $id_header)->update('t_detail_ritasi', $ritasi);

        $response = [
            'success' => true,
            'msg' => 'Detail Ritasi berhasil Edit!',
            'data' => $ritasi

        ];

        echo json_encode($response);
    }
    public function delete_detail($id)
    {

        $this->cb->where(['Id' => $id]);
        $this->cb->delete('t_detail_ritasi');


        $response = [
            'success' => true,
            'msg' => 'Ritasi berhasil diHapus!'
        ];

        echo json_encode($response);
    }
}
