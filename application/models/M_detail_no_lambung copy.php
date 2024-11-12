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
    // Join SparePart
    // $this->db->join('working_supply as b', 'a.id = b.asset_id', 'left');
    // $this->db->join('item_list c', 'b.item_id = c.id', 'left');

    // // Join SparePart
    // $this->db->join('bbm as d', 'a.id = d.nomor_lambung', 'left');

    // // Where Clause Sparepart
    // $this->db->where('c.coa', '1106002');
    // $this->db->where('b.jenis', 'out');

    // // Cari data Sesuai Tanggal
    // $this->db->where('MONTH(b.tanggal)', $bulan);
    // $this->db->where('YEAR(b.tanggal)', $tahun);
    // if ($id != "All") {
    //   $this->db->where('a.Id', $id);
    // }
    // // $this->db->where('MONTH(d.tanggal)', $bulan);
    // // $this->db->where('YEAR(d.tanggal)', $tahun);

    // $this->db->group_by('a.id');
    // // $this->db->group_by('a.month_year');
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
    if ($id != "All") {
      $this->db->where('b.asset_id', $id);
    }
    // $this->db->where('MONTH(d.tanggal)', $bulan);
    // $this->db->where('YEAR(d.tanggal)', $tahun);

    $this->db->group_by('b.asset_id');
    // $this->db->group_by('a.month_year');
    $this->db->order_by('b.tanggal', 'ASC');
    $spare_part = $this->db->get()->row();
    if (!empty($spare_part)) {
      return [
        'jumlah_part' => $spare_part->jumlah_part,
        'harga_part' => $spare_part->harga_part,
      ];
    }
    return [
      'hm_difference' => 0,
      'km_difference' => 0
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

    if ($start_data && $end_data) {

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
      'km_difference' => 0
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
