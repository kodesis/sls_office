<?php

defined('BASEPATH') or exit('No direct script access allowed');

class m_ritasi extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_ritasi($limit, $start, $search)
  {
    $this->cb->limit($limit, $start);
    $this->cb->like('nomor_lambung', $search, 'both');
    // $this->cb->where('user', $nip);
    $this->cb->order_by('nomor_lambung', 'DESC');
    return $this->cb->get('t_ritasi')->result_array();
  }

  public function countritasiSpv($search)
  {
    $nip = $this->session->userdata('nip');
    if (!$search) {
      $sql = "SELECT * FROM t_ritasi WHERE ";
      return $this->cb->query($sql)->num_rows();
    } else {
      $sql = "SELECT * FROM t_ritasi WHERE t_ritasi.nomor_lambung LIKE '%$search%'";
      return $this->cb->query($sql)->num_rows();
    }
  }

  public function countListRitasi($search)
  {
    if (!$search) {
      $sql = "SELECT * FROM t_ritasi";
      return $this->cb->query($sql)->num_rows();
    } else {
      $sql = "SELECT * FROM t_ritasi WHERE t_ritasi.nomor_lambung LIKE '%$search%'";
      return $this->cb->query($sql)->num_rows();
    }
  }

  public function get_detail_ritasi($where)
  {
    return $this->cb->where($where)->get('t_detail_ritasi')->result_array();
  }
  public function get_id_edit($id)
  {
    $this->cb->select('*');
    $this->cb->from('t_detail_ritasi');
    $this->cb->where('Id', $id);
    $query = $this->cb->get();

    return $query->row();
  }
}
