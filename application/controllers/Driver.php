<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Driver extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //$this->load->model('m_login');
        $this->load->model('m_driver');
        $this->load->library(array('form_validation', 'session', 'user_agent', 'Api_Whatsapp'));
        $this->load->library('pagination');
        $this->load->database();
        $this->load->helper('url', 'form', 'download');

        if ($this->session->userdata('isLogin') == FALSE) {
            redirect('login/login_form');
        }
    }
    public function list()
    {
        if ($this->session->userdata('isLogin') == FALSE) {
            redirect('home');
        } else {
            $a = $this->session->userdata('level');
            if (strpos($a, '401') !== false) {
                //pagination settings
                $config['base_url'] = site_url('driver/user');
                $config['total_rows'] = $this->m_driver->user_count($this->session->userdata('nip'));
                $config['per_page'] = "10";
                $config["uri_segment"] = 3;
                $choice = $config["total_rows"] / $config["per_page"];
                //$config["num_links"] = floor($choice);
                $config["num_links"] = 10;
                // integrate bootstrap pagination
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
                $this->pagination->initialize($config);
                $data['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
                $data['users_data'] = $this->m_driver->user_get($config["per_page"], $data['page'], $this->session->userdata('nip'));
                $data['pagination'] = $this->pagination->create_links();

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
                $data['title'] = 'User List';
                $data['pages'] = 'pages/driver/v_driver';
                $this->load->view('index', $data);
            }
        }
    }
    public function driver_cari()
    {
        if ($this->session->userdata('isLogin') == FALSE) {
            redirect('home');
        } else {
            $a = $this->session->userdata('level');
            if (strpos($a, '401') !== false) {
                // get search string
                $search = ($this->input->post("search")) ? $this->input->post("search") : "NIL";
                if ($search <> 'NIL') {
                    $this->session->set_userdata('keyword', $search);
                }
                $search = ($this->uri->segment(3)) ? $this->uri->segment(3) : $search;
                $stringLink = str_replace(' ', '_', $search);
                // pagination settings
                $config = array();
                $config['base_url'] = site_url("app/user_cari/$stringLink");
                $config['total_rows'] = $this->m_driver->user_cari_count($search, $this->session->userdata('nip'));
                $config['per_page'] = "10";
                $config["uri_segment"] = 4;
                $choice = $config["total_rows"] / $config["per_page"];
                //$config["num_links"] = floor($choice);
                $config["num_links"] = 10;

                // integrate bootstrap pagination
                $config['full_tag_open'] = '<ul class="pagination">';
                $config['full_tag_close'] = '</ul>';
                $config['first_link'] = false;
                $config['last_link'] = false;
                $config['first_tag_open'] = '<li>';
                $config['first_tag_close'] = '</li>';
                $config['prev_link'] = 'Prev';
                $config['prev_tag_open'] = '<li class="prev">';
                $config['prev_tag_close'] = '</li>';
                $config['next_link'] = 'Next';
                $config['next_tag_open'] = '<li>';
                $config['next_tag_close'] = '</li>';
                $config['last_tag_open'] = '<li>';
                $config['last_tag_close'] = '</li>';
                $config['cur_tag_open'] = '<li class="active"><a href="#">';
                $config['cur_tag_close'] = '</a></li>';
                $config['num_tag_open'] = '<li>';
                $config['num_tag_close'] = '</li>';
                $this->pagination->initialize($config);

                $data['page'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

                // get books list
                $data['users_data'] = $this->m_driver->user_cari_pagination($config["per_page"], $data['page'], $search, $this->session->userdata('nip'));
                $data['pagination'] = $this->pagination->create_links();

                //inbox notif
                $nip = $this->session->userdata('nip');
                $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
                $query = $this->db->query($sql);
                $res2 = $query->result_array();
                $result = $res2[0]['COUNT(Id)'];
                $data['count_inbox'] = $result;

                $sql4 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
                $query4 = $this->db->query($sql4);
                $res4 = $query4->result_array();
                $result4 = $res4[0]['COUNT(id)'];
                $data['count_inbox2'] = $result4;
                $data['pages'] = 'pages/driver/v_driver';
                $data['title'] = 'Data Driver Cari';
                $this->load->view('index', $data);
            }
        }
    }
    public function add_driver()
    {
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
        $data['title'] = 'Add Driver';

        if ($this->input->post('add') == 'add') {
            $today = date("Y-m-d");
            $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger">tidak boleh kosong</div>');

                $data['pages'] = 'pages/driver/v_driver_view';
                $this->load->view('index', $data);

                // echo "<script>alert('Umur Minimal 18 Tahunn !');window.history.back();</script>";
                // redirect('driver/add_driver');
            } else {
                $diff = date_diff(date_create($this->input->post('tgl_lahir')), date_create($today));
                if ($diff->format('%y') < 18) {
                    // $this->session->set_flashdata('msg','<div class="alert alert-danger">Umur Minimal 18 Tahun</div>');
                    // redirect('driver/add_driver');
                    echo "<script>alert('Umur Minimal 18 Tahun !');window.history.back();</script>";
                } else {
                    $add = [
                        "nama" => $this->input->post('nama'),
                        "status" => $this->input->post('status'),
                        "phone" => $this->input->post('phone'),
                        "nip" => $this->input->post('nip'),
                        "tgl_lahir" => $this->input->post('tgl_lahir')
                    ];
                    $this->db->insert('driver', $add);
                    $this->session->set_flashdata('msg', '<div class="alert alert-success">Registrasi Driver ' . $this->input->post('nama') . '</div>');
                    redirect('driver/add_driver');
                }
            }
        }

        $data['pages'] = 'pages/driver/v_driver_view';
        $this->load->view('index', $data);
    }

    public function driver_edit()
    {
        if ($this->session->userdata('isLogin') == FALSE) {
            redirect('home');
        } else {
            $a = $this->session->userdata('level');
            if (strpos($a, '401') !== false) {
                $data['user'] = $this->m_driver->user_get_detail($this->uri->segment(3));
                if (empty($data['user'])) {
                    echo "<script>alert('Unauthorize Privilage!');window.history.back();</script>";
                } else {
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

                    if ($this->input->post('edit') == 'edit') {
                        $id_edit = $this->input->post('id');
                        $edit_data = [
                            "nama" => $this->input->post('nama'),
                            "status" => $this->input->post('status'),
                            "tgl_lahir" => $this->input->post('tgl_lahir'),
                            "phone" => $this->input->post('phone'),
                            "nip" => $this->input->post('nip'),
                        ];
                        $this->db->where('id', $id_edit);
                        $this->db->update('driver', $edit_data);
                        $this->session->set_flashdata('msg', 'Update Driver ' . $this->input->post('nama'));
                        redirect('driver/list');
                    }
                    $data['title'] = 'Edit Driver';
                    $data['pages'] = 'pages/driver/v_driver_view';
                    $this->load->view('index', $data);
                }
            }
        }
    }
}
