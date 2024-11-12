<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_detail_no_lambung extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_detail($bulan, $tahun, $id)
  {
    $this->db->select('a.Id, a.nama_asset');
    $this->db->from('asset_list as a');
    if ($id != "ALL") {
      $this->db->where('a.Id', $id);
    }
    $this->db->order_by('a.Id', 'ASC');
    return $this->db->get()->result();
  }
  public function get_detail_ritasi_spare_part($id, $bulan, $tahun)
  {
    $this->db->select('SUM(b.jml) as jumlah_part, SUM(b.harga) as harga_part');
    // Join SparePart
    $this->db->from('working_supply as b');
    $this->db->join('item_list c', 'b.item_id = c.id', 'left');

    // Where Clause Sparepart
    $this->db->where('c.coa', '1106002');
    $this->db->where('b.jenis', 'out');

    // Cari data Sesuai Tanggal
    $this->db->where('MONTH(b.tanggal)', $bulan);
    $this->db->where('YEAR(b.tanggal)', $tahun);
    $this->db->where('b.asset_id', $id);

    $this->db->group_by('b.asset_id');
    $this->db->order_by('b.tanggal', 'DESC');
    $spare_part = $this->db->get()->row();
    if (!empty($spare_part)) {
      return [
        'jumlah_part' => $spare_part->jumlah_part,
        'harga_part' => $spare_part->harga_part,
      ];
    }
    return [
      'jumlah_part' => 0,
      'harga_part' => 0
    ];
  }
  public function get_detail_ritasi_tonase($id, $bulan, $tahun)
  {
    $this->cb->select('hm_awal, km_awal');
    $this->cb->from('t_ritasi');
    $this->cb->where('nomor_lambung', $id);
    $this->cb->where('MONTH(tanggal)', $bulan);
    $this->cb->where('YEAR(tanggal)', $tahun);
    $this->cb->order_by('tanggal', 'ASC'); // Order by earliest date
    $this->cb->limit(1); // Get the first record of the month
    $start_data = $this->cb->get()->row_array();

    // Fetch 'hm_akhir' and 'km_akhir' for the last available date in the month
    $this->cb->select('hm_akhir, km_akhir');
    $this->cb->from('t_ritasi');
    $this->cb->where('nomor_lambung', $id);
    $this->cb->where('MONTH(tanggal)', $bulan);
    $this->cb->where('YEAR(tanggal)', $tahun);
    $this->cb->order_by('tanggal', 'DESC'); // Order by latest date
    $this->cb->limit(1); // Get the last record of the month
    $end_data = $this->cb->get()->row_array();

    if (!empty($start_data && $end_data)) {

      if ($start_data === $end_data) {
        // If there's only one record in the month
        $hm_difference = $start_data['hm_akhir'] - $start_data['hm_awal'];
        $hm_awal = $start_data['hm_awal'];
        $hm_akhir = $start_data['hm_akhir'];

        $km_difference = $start_data['km_akhir'] - $start_data['km_awal'];
        $km_awal = $start_data['km_awal'];
        $km_akhir = $start_data['km_akhir'];
      } else {
        // Calculate differences between the start and end data
        $hm_difference = $end_data['hm_akhir'] - $start_data['hm_awal'];
        $hm_awal = $start_data['hm_awal'];
        $hm_akhir = $end_data['hm_akhir'];
        $km_difference = $end_data['km_akhir'] - $start_data['km_awal'];
        $km_awal = $start_data['km_awal'];
        $km_akhir = $end_data['km_akhir'];
      }

      return [
        'hm_difference' => $hm_difference,
        'km_difference' => $km_difference,
        'hm_awal' => $hm_awal,
        'hm_akhir' => $hm_akhir,
        'km_awal' => $km_awal,
        'km_akhir' => $km_akhir
      ];
    }
    return [
      'hm_difference' => 0,
      'km_difference' => 0,
      'hm_awal' => 0,
      'hm_akhir' => 0,
      'km_awal' => 0,
      'km_akhir' => 0
    ];
  }
  public function get_detail_ritasi_bbm($id, $bulan, $tahun)
  {
    $this->db->select('SUM(b.total_harga) as total_harga, SUM(b.total_liter) as total_liter');
    // Join SparePart
    $this->db->from('bbm as b');

    // Cari data Sesuai Tanggal
    $this->db->where('MONTH(b.tanggal)', $bulan);
    $this->db->where('YEAR(b.tanggal)', $tahun);

    $this->db->where('b.nomor_lambung', $id);

    $this->db->group_by('b.nomor_lambung');
    $this->db->order_by('b.tanggal', 'DESC');
    $bbm = $this->db->get()->row();
    if (!empty($bbm)) {
      return [
        'total_harga' => $bbm->total_harga,
        'total_liter' => $bbm->total_liter,
      ];
    }
    return [
      'total_harga' => 0,
      'total_liter' => 0
    ];
  }
  public function countritasiSpv($search)
  {
    if (!$search) {
      $sql = "SELECT * FROM working_supply WHERE asset_id IS NOT NULL OR asset_id != 0";
      return $this->db->query($sql)->num_rows();
    } else {
      $sql = "SELECT * FROM working_supply WHERE asset_id LIKE '%$search%'";
      return $this->db->query($sql)->num_rows();
    }
  }

  public function countListDetail($search)
  {
    if (!$search) {
      $sql = "SELECT * FROM working_supply WHERE asset_id IS NOT NULL OR asset_id != 0";
      return $this->db->query($sql)->num_rows();
    } else {
      $sql = "SELECT * FROM working_supply WHERE asset_id LIKE '%$search%'";
      return $this->db->query($sql)->num_rows();
    }
  }
}
