<?php if (!defined('BASEPATH')) exit('Hacking Attempt : Keluar dari sistem..!!');

class M_driver extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function __destruct()
	{
		$this->db->close();
	}

	function user_get($limit, $start, $nip)
	{
		$nip = '';
		$sql = "SELECT * FROM driver ORDER BY id DESC limit " . $start . ", " . $limit;
		$query = $this->db->query($sql);
		return $query->result();
	}
	function user_get_detail($id)
	{
		$sql = "SELECT * from driver where id='$id' ";
		$query = $this->db->query($sql);
		return $query->row();
	}



	function user_count($nip)
	{
		$sql = "SELECT id FROM driver";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	function user_cari_count($st = NULL, $nip = NULL)
	{
		if ($st == "NIL") $st = "";
		$sql = "SELECT id FROM driver WHERE (nama LIKE '%$st%' AND nip LIKE '%$nip%')";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	function user_cari_pagination($limit, $start, $st = NULL, $nip = null)
	{
		if ($st == "NIL") $st = "";
		$sql = "select *
	FROM driver
	WHERE (nama LIKE '%$st%' OR nip LIKE '%$st%') ORDER BY id DESC limit " . $start . ", " . $limit;
		$query = $this->db->query($sql);
		return $query->result();
	}

	function send_cari_pagination($limit, $start, $st = NULL, $nip = NULL)
	{
		if ($st == "NIL") $st = "";
		$sql = "select a.id,a.nomor_memo,a.nip_kpd,a.judul,a.tanggal,a.read,a.nip_dari,b.nama
	FROM memo a LEFT JOIN driver b ON a.nip_dari = b.nip
	WHERE (a.judul LIKE '%$st%' AND a.nip_dari LIKE '%$nip%') ORDER BY a.tanggal DESC limit " . $start . ", " . $limit;
		$query = $this->db->query($sql);
		return $query->result();
	}
}
