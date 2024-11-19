<?php if (!defined('BASEPATH')) exit('Hacking Attempt : Keluar dari sistem..!!');

class M_coa extends CI_Model
{
    // $this->cb untuk koneksi ke database corebank
    public function list_coa()
    {
        return $this->cb->order_by('no_sbb', 'ASC')->get('v_coa_all')->result();
    }

    public function cek_coa($no_coa)
    {
        return $this->cb->where('no_sbb', $no_coa)->get('v_coa_all')->row_array();
    }

    public function update_nominal_coa($no_coa, $data, $kolom, $tabel)
    {
        return $this->cb->where($kolom, $no_coa)->update($tabel, $data);
    }

    public function add_transaksi($data)
    {
        return $this->cb->insert('t_log_transaksi', $data);
    }

    public function addJurnal($data)
    {
        return $this->cb->insert('jurnal_neraca', $data);
    }

    public function getNeraca($table, $posisi, $kolom_order)
    {
        return $this->cb->where('nominal !=', '0')->where('posisi', $posisi)->order_by($kolom_order, 'ASC')->get($table)->result();
    }

    public function getSumNeraca($table, $posisi)
    {
        return $this->cb->select_sum('nominal')->where('posisi', $posisi)->get($table)->row_array();
    }

    public function getPasivaWithLaba($table, $kolom_order)
    {
        $pasiva = $this->cb->where('posisi', 'PASIVA')->where('nominal !=', '0')->or_where('no_sbb', '3103001')->order_by($kolom_order, 'ASC')->get($table)->result();

        return $pasiva;
    }

    public function getCoaReport($no_coa, $from, $to)
    {
        $this->cb->where('tanggal >=', $from)->where('tanggal <=', $to);

        if ($no_coa != "ALL") {
            $this->cb->group_start();
            $this->cb->where('akun_debit', $no_coa)->or_where('akun_kredit', $no_coa);
            $this->cb->group_end();
        }

        $this->cb->order_by('id', 'DESC');

        return $this->cb->get('jurnal_neraca')->result();
    }

    public function getCoa($no_coa)
    {
        if ($no_coa == "ALL") {
            $this->cb->select('nama_perkiraan, no_sbb');
            return $this->cb->get('v_coa_all')->result();
        } else {
            return $this->cb->where('no_sbb', $no_coa)->get('v_coa_all')->row_array();
        }
    }

    public function getCoaBB($no_coa)
    {
        if ($no_coa == "ALL") {
            $this->cb->select('nama_perkiraan, no_bb');
            return $this->cb->get('v_coabb_all')->result();
        } else {
            return $this->cb->where('no_bb', $no_coa)->get('v_coabb_all')->row_array();
        }
    }

    public function getCoaByCode($code)
    {
        return $this->cb->like('no_sbb', $code, 'after')->get('v_coa_all')->result();
    }

    public function simpanLaporan($data)
    {
        return $this->cb->insert('t_log_neraca', $data);
    }

    public function count_laporan($jenis)
    {
        return $this->cb->from('t_log_neraca')->where('jenis', $jenis)->count_all_results();
    }

    public function list_laporan($jenis, $limit, $from)
    {
        $laporan = $this->cb->where('jenis', $jenis)->order_by('tanggal_simpan', 'DESC')->limit($limit, $from)->get('t_log_neraca')->result_array();

        // Ambil semua user dari database bdl_core
        $users = $this->db->select('id, nip, nama')->get('users')->result_array();
        $user_map = array_column($users, 'nama', 'nip');  // Menggunakan nama pengguna sebagai nama kolom

        // Gabungkan hasil query
        foreach ($laporan as &$lp) {
            $lp['created_by_name'] = isset($user_map[$lp['created_by']]) ? $user_map[$lp['created_by']] : null;
        }

        return $laporan;
    }

    public function showNeraca($slug)
    {
        return $this->cb->where('slug', $slug)->get('t_log_neraca')->row_array();
    }

    public function select_max($jenis)
    {
        return $this->cb->select('max(no_urut) as max')->where('jenis', $jenis)->get('t_log_neraca')->row_array();
    }

    public function count($keyword, $tabel)
    {
        if ($keyword) {
            $this->cb->like('no_sbb', $keyword);
            $this->cb->or_like('no_bb', $keyword);
            $this->cb->or_like('nama_perkiraan', $keyword);
        }
        return $this->cb->from($tabel)->count_all_results();
    }

    public function list_coa_paginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->cb->like('no_sbb', $keyword);
            $this->cb->or_like('no_bb', $keyword);
            $this->cb->or_like('nama_perkiraan', $keyword);
        }
        $laporan = $this->cb->order_by('no_sbb', 'ASC')->limit($limit, $from)->get('v_coa_all')->result_array();

        return $laporan;
    }

    public function isAvailable($kolom, $key)
    {
        return $this->cb->from('v_coa_all')->where($kolom, $key)->count_all_results();
    }


    public function list_saldo()
    {
        return $this->cb->order_by('periode', 'DESC')->get('saldo_awal')->result();
    }

    public function showSaldo($slug)
    {
        return $this->cb->where('slug', $slug)->get('saldo_awal')->row_array();
    }

    public function showDetailSaldo($id)
    {
        return $this->cb->from('saldo_awal_detail s')->join('v_coa_all v', 's.no_sbb = v.no_sbb')->where('id_saldo_awal', $id)->get()->result();
    }

    // Fungsi untuk menyimpan saldo awal ke tabel saldo_awal_neraca
    public function insert_saldo_awal($data)
    {
        return $this->cb->insert('saldo_awal', $data);
    }

    public function update_saldo_awal($periode, $data)
    {
        return $this->cb->where('periode', $periode)->update('saldo_awal', $data);
    }

    // Fungsi untuk mendapatkan saldo awal berdasarkan bulan tertentu
    public function get_saldo_awal($bulan)
    {
        $this->cb->select('*');
        $this->cb->from('saldo_awal');
        $this->cb->where('periode', $bulan);
        $query = $this->cb->get();
        return $query->row_array();
    }

    public function calculate_saldo_awal($bulan, $tahun)
    {
        $bulan = (int) $bulan;
        $tahun = (int) $tahun;

        $query = $this->cb->query("
            SELECT 
                coa.no_sbb, coa.nama_perkiraan, coa.posisi, coa.table_source,
                SUM(
                    CASE WHEN jn.akun_debit = jn.akun_kredit THEN 0
                        WHEN coa.posisi = 'AKTIVA' AND jn.akun_debit = coa.no_sbb THEN jn.jumlah_debit
                        WHEN coa.posisi = 'AKTIVA' AND jn.akun_kredit = coa.no_sbb THEN -jn.jumlah_kredit
                        WHEN coa.posisi = 'PASIVA' AND jn.akun_kredit = coa.no_sbb THEN jn.jumlah_kredit
                        WHEN coa.posisi = 'PASIVA' AND jn.akun_debit = coa.no_sbb THEN -jn.jumlah_debit
                        ELSE 0
                    END
                ) AS saldo_awal
            FROM 
                v_coa_all coa
            LEFT JOIN 
                jurnal_neraca jn ON coa.no_sbb = jn.akun_debit OR coa.no_sbb = jn.akun_kredit
            WHERE 
                MONTH(jn.tanggal) = '$bulan' AND YEAR(jn.tanggal) = '$tahun'
            GROUP BY 
                coa.no_sbb
            ORDER BY 
                coa.no_sbb ASC
        ");

        return $query->result();
    }

    public function cek_saldo_awal($bulan)
    {
        return $this->cb->where('periode', $bulan)->get('saldo_awal')->row_array();
    }

    public function getNeracaByDate($table, $posisi, $tanggal_akhir)
    {
        $date = new DateTime($tanggal_akhir);
        $tanggal_awal = $date->format('Y-m') . '-01';

        if ($posisi == "AKTIVA") {

            $query = $this->cb->query("
            SELECT 
                coa.no_sbb, coa.nama_perkiraan, coa.posisi,
                SUM(
                    CASE 
                        WHEN jn.akun_debit = jn.akun_kredit THEN 0
                        WHEN coa.posisi = 'AKTIVA' AND jn.akun_debit = coa.no_sbb THEN jn.jumlah_debit
                        WHEN coa.posisi = 'AKTIVA' AND jn.akun_kredit = coa.no_sbb THEN -jn.jumlah_kredit
                        ELSE 0
                    END
                ) AS saldo_awal
            FROM 
                v_coa_all coa
            LEFT JOIN 
                jurnal_neraca jn ON coa.no_sbb = jn.akun_debit OR coa.no_sbb = jn.akun_kredit
            WHERE 
                (jn.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir')
                AND coa.table_source = '$table' AND coa.posisi = '$posisi'
            GROUP BY 
                coa.no_sbb
            ORDER BY 
                coa.no_sbb ASC
        ");
        } else if ($posisi == "PASIVA") {

            $query = $this->cb->query("
            SELECT 
                coa.no_sbb, coa.nama_perkiraan, coa.posisi,
                SUM(
                    CASE 
                        WHEN jn.akun_debit = jn.akun_kredit THEN 0
                        WHEN coa.posisi = 'PASIVA' AND jn.akun_kredit = coa.no_sbb THEN jn.jumlah_kredit
                        WHEN coa.posisi = 'PASIVA' AND jn.akun_debit = coa.no_sbb THEN -jn.jumlah_debit
                        ELSE 0
                    END
                ) AS saldo_awal
            FROM 
                v_coa_all coa
            LEFT JOIN 
                jurnal_neraca jn ON coa.no_sbb = jn.akun_debit OR coa.no_sbb = jn.akun_kredit
            WHERE 
                jn.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                AND coa.table_source = '$table' AND coa.posisi = '$posisi'
            GROUP BY 
                coa.no_sbb
            ORDER BY 
                coa.no_sbb ASC
        ");
        }

        return $query->result();
    }
}
