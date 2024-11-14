<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_bbm extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_bbm($limit, $start, $search)
  {
    $this->db->limit($limit, $start);
    $this->db->like('nomor_lambung', $search, 'both');
    // $this->db->where('bbm IS NOT NULL OR bbm !=', 0);
    $this->db->where('nomor_lambung IS NOT NULL');
    $this->db->where('nomor_lambung !=', 0);
    $this->db->order_by('id', 'DESC');
    return $this->db->get('bbm')->result_array();
  }

  public function countritasiSpv($search)
  {
    if (!$search) {
      $sql = "SELECT * FROM bbm WHERE nomor_lambung IS NOT NULL OR nomor_lambung != 0";
      return $this->db->query($sql)->num_rows();
    } else {
      $sql = "SELECT * FROM bbm WHERE nomor_lambung LIKE '%$search%'";
      return $this->db->query($sql)->num_rows();
    }
  }

  public function countListBbm($search)
  {
    if (!$search) {
      $sql = "SELECT * FROM bbm WHERE nomor_lambung IS NOT NULL OR nomor_lambung != 0";
      return $this->db->query($sql)->num_rows();
    } else {
      $sql = "SELECT * FROM bbm WHERE nomor_lambung LIKE '%$search%'";
      return $this->db->query($sql)->num_rows();
    }
  }
}
