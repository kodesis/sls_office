<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_detail_ritasi extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_ritasi($limit, $start, $search)
  {
    $this->cb->limit($limit, $start);
    $this->cb->like('no_lambung', $search, 'both');
    // $this->cb->where('user', $nip);
    $this->cb->order_by('no_lambung', 'DESC');
    return $this->cb->get('t_detail_ritasi')->result_array();
  }

  public function countritasiSpv($search)
  {
    $nip = $this->session->userdata('nip');
    if (!$search) {
      $sql = "SELECT * FROM t_detail_ritasi WHERE ";
      return $this->cb->query($sql)->num_rows();
    } else {
      $sql = "SELECT * FROM t_detail_ritasi WHERE t_detail_ritasi.no_lambung LIKE '%$search%'";
      return $this->cb->query($sql)->num_rows();
    }
  }

  public function countListRitasi($search)
  {
    if (!$search) {
      $sql = "SELECT * FROM t_detail_ritasi";
      return $this->cb->query($sql)->num_rows();
    } else {
      $sql = "SELECT * FROM t_detail_ritasi WHERE t_detail_ritasi.no_lambung LIKE '%$search%'";
      return $this->cb->query($sql)->num_rows();
    }
  }
}
