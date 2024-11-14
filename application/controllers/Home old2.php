<?php if (!defined('BASEPATH')) exit('Hacking Attempt. Keluar dari sistem.');

class Home extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();

    $this->load->library(array('session'));
    $this->load->helper('url');
    $this->load->model('m_login');
    $this->load->database();

    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('login/login_form');
    }
  }

  public function index()
  {
    $user = $this->session->userdata('username');
    //$data['level'] = $this->session->userdata('level');        
    $data['pengguna'] = $this->m_login->dataPengguna($user);
    //$data['utility'] = $this->m_login->utility();
    //redirect('app/grab_project');
    $date = date('Y-m-d');
    $nip = $this->session->userdata('nip');
    $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
    $query = $this->db->query($sql);
    $res2 = $query->result_array();
    $result = $res2[0]['COUNT(Id)'];

    $sql2 = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` LIKE '%$nip%');";
    $sql3 = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%');";

    $query2 = $this->db->query($sql2);
    $res3 = $query2->result_array();
    $result2 = $res3[0]['COUNT(Id)'];

    $query3 = $this->db->query($sql3)->result_array();
    $result3 = $query3[0]['COUNT(Id)'];

    // $over_due = $this->db->query("SELECT COUNT(id_detail) as id FROM task_detail where activity='1' and due_date<'$date' and responsible like '%$nip'")->row_array();
    // $cek_member = $this->db->query("SELECT * FROM task where member like '%$nip%'")->num_rows();
    $cek_pic = $this->db->query("SELECT * FROM task where pic='$nip'")->num_rows();
    if ($cek_pic == true) {
      $tello_pending = $this->db->query("SELECT COUNT(id_detail) as id,a.member FROM task as a left join task_detail as b on(a.id=b.id_task) where b.activity='2' and pic ='$nip'")->row_array();
      $tello_closed = $this->db->query("SELECT COUNT(id_detail) as id,a.member FROM task as a left join task_detail as b on(a.id=b.id_task) where b.activity='3' and pic='$nip'")->row_array();
      $tello_open = $this->db->query("SELECT COUNT(id_detail) as id,a.member FROM task as a left join task_detail as b on(a.id=b.id_task) where b.activity='1' and pic = '$nip'")->row_array();
      //$tello_open = $this->db->query("SELECT COUNT(id) as id FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '$nip') and activity='1'")->row_array();
      $total_tello = $this->db->query("SELECT COUNT(id) as id FROM task where pic='$nip'")->row_array();
      $over_due = $this->db->query("SELECT COUNT(id_detail) as id,a.member FROM task as a left join task_detail as b on(a.id=b.id_task) where b.activity='1' and a.pic= '$nip' and b.due_date<'$date'")->row_array();
    } else {
      $tello_pending = $this->db->query("SELECT COUNT(id_detail) as id,a.member FROM task as a left join task_detail as b on(a.id=b.id_task) where b.activity='2' and member LIKE '%$nip%'")->row_array();
      $tello_closed = $this->db->query("SELECT COUNT(id_detail) as id,a.member FROM task as a left join task_detail as b on(a.id=b.id_task) where b.activity='3' and member LIKE '%$nip%'")->row_array();
      $tello_open = $this->db->query("SELECT COUNT(id_detail) as id,a.member FROM task as a left join task_detail as b on(a.id=b.id_task) where b.activity='1' and member like '%$nip%'")->row_array();
      //$tello_open = $this->db->query("SELECT COUNT(id) as id FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '$nip') and activity='1'")->row_array();
      $total_tello = $this->db->query("SELECT COUNT(id) as id FROM task where member like '%$nip%'")->row_array();
      $over_due = $this->db->query("SELECT COUNT(id_detail) as id,a.member FROM task as a left join task_detail as b on(a.id=b.id_task) where b.activity='1' and a.member LIKE '%$nip%' and b.due_date<'$date'")->row_array();
    }

    $data["open_tello"] = $tello_open;
    $data["pending_tello"] = $tello_pending;
    $data["closed_tello"] = $tello_closed;
    $data["total_tello"] = $total_tello;
    $data["over_due_card"] = $over_due;

    $data['total'] = $result3;
    $data['count_inbox'] = $result;
    $data['read_inbox'] = $result2;

    $sql4 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    $query4 = $this->db->query($sql4);
    $res4 = $query4->result_array();
    $result4 = $res4[0]['COUNT(id)'];
    $data['count_inbox2'] = $result4;

    // $sql4 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    // $query4 = $this->db->query($sql4);
    // $res4 = $query4->result_array();
    // $result4 = $res4[0]['COUNT(id)'];
    // $data['count_inbox2'] = $result4;
    // $this->load->view('home_view', $data);
    // $data = [
    //   "title" => "Login",
    //   "pages" => "pages/home/v_home"
    // ];
    $data['title'] = "Home";
    $data['pages'] = "pages/v_home";

    $this->load->view('index', $data);
  }
  function banner()
  {

    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('login/login_form');
    } else {
      // $banner1 = $_FILES['banner1']['name'];
      // $banner2 = $_FILES['banner2']['name'];
      // $banner3 = $_FILES['banner3']['name'];


      // $config['max_size']             = 100;
      // $config['max_width']            = 1024;
      // $config['max_height']           = 768;

      $config['upload_path']          = './upload/banner/';
      $config['allowed_types']        = 'jpg|png';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);
      if ($_FILES['banner1']['name'] == true) {
        if (!$this->upload->do_upload('banner1')) {
          $error = array('error' => $this->upload->display_errors());
          var_dump($error);
          // $this->load->view('upload_form', $error);
        } else {
          $data = array('upload_data' => $this->upload->data());
          $insert = [
            "banner1" => $this->upload->data()['file_name']
          ];
          var_dump($data);
          // $this->load->view('upload_success', $data);
        }
      }
      if ($_FILES['banner2']['name'] == true) {
        if (!$this->upload->do_upload('banner2')) {
          $error = array('error' => $this->upload->display_errors());
          var_dump($error);
          // $this->load->view('upload_form', $error);
        } else {
          $data = array('upload_data' => $this->upload->data());
          $insert = [
            "banner2" => $this->upload->data()['file_name']
          ];
          var_dump($data);
          // $this->load->view('upload_success', $data);
        }
      }
      if ($_FILES['banner3']['name'] == true) {
        if (!$this->upload->do_upload('banner3')) {
          $error = array('error' => $this->upload->display_errors());
          var_dump($error);
          // $this->load->view('upload_form', $error);
        } else {
          $data = array('upload_data' => $this->upload->data());
          $insert = [
            "banner3" => $this->upload->data()['file_name']
          ];
          var_dump($data);
          // $this->load->view('upload_success', $data);
        }
      }
      $this->db->where('Id', 1);
      $this->db->update('utility', $insert);
      redirect('home');
    }
  }
}
