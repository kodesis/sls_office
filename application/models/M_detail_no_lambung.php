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
    $this->db->select('SUM(jml * harga) as total_harga, SUM(jml) as harga_part_total');
    $this->db->from('working_supply as b');
    $this->db->join('item_list c', 'b.item_id = c.id', 'left');

    // Where Clause Sparepart
    $this->db->where('b.jenis', 'out');
    $this->db->where('MONTH(b.tanggal)', $bulan);
    $this->db->where('YEAR(b.tanggal)', $tahun);
    $this->db->where('b.asset_id', $id);
    $this->db->group_by('b.asset_id');
    $this->db->order_by('b.tanggal', 'DESC');
    $spare_parts = $this->db->get()->row();

    $jumlah_part = 0;
    $harga_part_total = 0;

    if (!empty($spare_parts)) {
      // foreach ($spare_parts as $spare_part) {
      //   $harga_part_total += $spare_part->harga * $spare_part->jml;
      //   $jumlah_part += $spare_part->jml;
      // }  

      return [
        // 'jumlah_part' => $jumlah_part,
        // 'harga_part' => $harga_part_total,
        'jumlah_part' => $spare_parts->harga_part_total,
        'harga_part' => $spare_parts->total_harga,
      ];
    } else {
      return [
        'jumlah_part' => 0,
        'harga_part' => 0,
      ];
    }
  }
  public function get_detail_ritasi_tonase($id, $bulan, $tahun)
  {
    $this->cb->select('km_awal');
    $this->cb->from('t_ritasi');
    $this->cb->where('nomor_lambung', $id);
    $this->cb->where('MONTH(tanggal)', $bulan);
    $this->cb->where('YEAR(tanggal)', $tahun);
    $this->cb->order_by('tanggal', 'ASC'); // Order by earliest date
    $this->cb->limit(1); // Get the first record of the month
    $start_data = $this->cb->get()->row_array();

    // Fetch 'hm_akhir' and 'km_akhir' for the last available date in the month
    $this->cb->select('km_akhir');
    $this->cb->from('t_ritasi');
    $this->cb->where('nomor_lambung', $id);
    $this->cb->where('MONTH(tanggal)', $bulan);
    $this->cb->where('YEAR(tanggal)', $tahun);
    $this->cb->order_by('tanggal', 'DESC'); // Order by latest date
    $this->cb->limit(1); // Get the last record of the month
    $end_data = $this->cb->get()->row_array();

    // Fetch 'hm_akhir' and 'km_akhir' for the last available date in the month
    $this->cb->select('SUM(total_km) as total_km, SUM(total_harga_km) as total_harga_km, SUM(total_tonase) as total_tonase, SUM(total_harga_tonase) as total_harga_tonase');
    $this->cb->from('t_ritasi');
    $this->cb->where('nomor_lambung', $id);
    $this->cb->where('MONTH(tanggal)', $bulan);
    $this->cb->where('YEAR(tanggal)', $tahun);
    $this->cb->order_by('tanggal', 'DESC'); // Order by latest date
    $this->cb->limit(1); // Get the last record of the month
    $total = $this->cb->get()->row_array();

    if (!empty($start_data && $end_data)) {

      if ($start_data === $end_data) {
        // If there's only one record in the month
        $km_difference = $start_data['km_akhir'] - $start_data['km_awal'];
        $km_awal = $start_data['km_awal'];
        $km_akhir = $start_data['km_akhir'];
      } else {
        // Calculate differences between the start and end data

        $km_difference = $end_data['km_akhir'] - $start_data['km_awal'];
        $km_awal = $start_data['km_awal'];
        $km_akhir = $end_data['km_akhir'];
      }

      return [
        'km_awal' => $km_awal,
        'km_akhir' => $km_akhir,
        'total_km' => $total['total_km'],
        'total_harga_km' => $total['total_harga_km'],
        'total_tonase' => $total['total_tonase'],
        'total_harga_tonase' => $total['total_harga_tonase'],

      ];
    }
    return [
      'km_awal' => 0,
      'km_akhir' => 0,
      'total_km' => 0,
      'total_harga_km' => 0,
      'total_tonase' => 0,
      'total_harga_tonase' => 0,
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
