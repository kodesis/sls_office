<?php if (!defined('BASEPATH')) exit('Hacking Attempt : Keluar dari sistem..!!');

class M_asset extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function __destruct()
	{
		$this->db->close();
	}

	function item_get($limit, $start, $search)
	{
		if ($search) {
			// $sql = "SELECT * FROM item_list WHERE nama LIKE '%$search%' OR nomor LIKE '%$search%' ORDER BY Id DESC limit " . $start . ", " . $limit;
			$sql = "SELECT item_list.*, item_jenis.nama_jenis FROM item_list LEFT JOIN item_jenis ON item_list.jenis_item = item_jenis.Id WHERE item_list.nama LIKE '%$search%' OR item_list.nomor LIKE '%$search%' ORDER BY item_list.nama ASC limit " . $start . ", " . $limit;
		} else {
			$sql = "SELECT item_list.*, item_jenis.nama_jenis FROM item_list LEFT JOIN item_jenis ON item_list.jenis_item = item_jenis.Id ORDER BY item_list.nama ASC limit " . $start . ", " . $limit;
		}
		$query = $this->db->query($sql);
		return $query->result();
	}

	function item_detail_get($limit, $start, $search, $id)
	{
		if ($search) {
			$sql = "SELECT * FROM item_detail WHERE kode_item = $id AND item_detail.serial_number LIKE '%$search%' ORDER BY Id DESC limit " . $start . ", " . $limit;
		} else {
			$sql = "SELECT * FROM item_detail WHERE kode_item = $id ORDER BY Id DESC limit " . $start . ", " . $limit;
		}
		$query = $this->db->query($sql);
		return $query;
	}

	function itemIn_get($limit, $start)
	{
		$sql = "SELECT item_in.jumlah, item_in.no_nota, item_in.harga_sat, item_in.date, item_list.nama, item_list.nomor FROM item_in JOIN item_list ON item_in.item_list_id = item_list.Id ORDER BY item_in.Id DESC LIMIT " . $start . ", " . $limit;
		$query = $this->db->query($sql);
		return $query->result();
	}


	function itemOut_get($limit, $start, $keyword)
	{
		if ($keyword) {
			$sql = "SELECT users.nama as nama_user, users.nip, item_list.nama, asset_list.nama_asset, asset_list.kode, working_supply.Id, working_supply.harga, working_supply.jml, working_supply.tanggal, working_supply.penerima, working_supply.status, working_supply.bukti_serah, working_supply.image_close, working_supply.keterangan FROM working_supply JOIN item_list ON working_supply.item_id = item_list.Id JOIN asset_list ON asset_list.Id = working_supply.asset_id LEFT JOIN users ON users.nip = working_supply.user WHERE (asset_list.nama_asset LIKE '%$keyword%' OR item_list.nama LIKE '%$keyword%' OR working_supply.keterangan LIKE '%$keyword%') and (working_supply.jenis LIKE '%OUT%') ORDER BY working_supply.Id DESC LIMIT " . $start . ", " . $limit;
		} else {
			$sql = "SELECT users.nama as nama_user, users.nip, item_list.nama, asset_list.nama_asset, asset_list.kode, working_supply.Id, working_supply.harga, working_supply.jml, working_supply.tanggal, working_supply.penerima, working_supply.status, working_supply.bukti_serah, working_supply.image_close, working_supply.keterangan FROM working_supply JOIN item_list ON working_supply.item_id = item_list.Id JOIN asset_list ON asset_list.Id = working_supply.asset_id LEFT JOIN users ON users.nip = working_supply.user WHERE working_supply.jenis LIKE '%OUT%' ORDER BY working_supply.Id DESC LIMIT " . $start . ", " . $limit;
		}
		$query = $this->db->query($sql);
		return $query->result();
	}

	function item_count($search)
	{
		if ($search) {
			$sql = "SELECT * FROM item_list WHERE nama LIKE '%$search%' OR nomor LIKE '%$search%'";
		} else {
			$sql = "SELECT * FROM item_list";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	function item_detail_count($search, $id)
	{
		if ($search) {
			$sql = "SELECT * FROM item_detail WHERE serial_number LIKE '%$search%' AND kode_item = $id";
		} else {
			$sql = "SELECT * FROM item_detail WHERE kode_item = $id";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	function itemOut_count($keyword)
	{
		if ($keyword) {
			$sql = "SELECT users.nama as nama_user, users.nip, item_list.nama, asset_list.nama_asset, asset_list.kode, working_supply.Id, working_supply.harga, working_supply.jml, working_supply.tanggal, working_supply.penerima, working_supply.status, working_supply.bukti_serah, working_supply.image_close, working_supply.keterangan FROM working_supply JOIN item_list ON working_supply.item_id = item_list.Id JOIN asset_list ON asset_list.Id = working_supply.asset_id LEFT JOIN users ON users.nip = working_supply.user WHERE (asset_list.nama_asset LIKE '%$keyword%' OR item_list.nama LIKE '%$keyword%' OR working_supply.keterangan LIKE '%$keyword%') AND (working_supply.jenis LIKE '%OUT%') ORDER BY working_supply.Id DESC";
		} else {
			$sql = "SELECT users.nama as nama_user, users.nip, item_list.nama, asset_list.nama_asset, asset_list.kode, working_supply.Id, working_supply.harga, working_supply.jml, working_supply.tanggal, working_supply.penerima, working_supply.status, working_supply.bukti_serah, working_supply.image_close, working_supply.keterangan FROM working_supply JOIN item_list ON working_supply.item_id = item_list.Id JOIN asset_list ON asset_list.Id = working_supply.asset_id LEFT JOIN users ON users.nip = working_supply.user WHERE working_supply.jenis LIKE '%OUT%' ORDER BY working_supply.Id DESC";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	function get_poList($limit, $start, $keyword, $where, $filter)
	{
		$this->cb->select('a.no_po, a.ppn, a.vendor, a.tgl_pengajuan, a.posisi, a.total, a.status_sarlog, a.status_direksi_ops, a.status_dirut, a.Id, a.catatan_sarlog, a.catatan_direksi_ops, a.catatan_dirut, a.keterangan, a.bukti_bayar, a.user, a.date_bayar, a.date_proses, a.jenis_pembayaran, a.status_pembayaran, b.nama as nama_vendor, c.nama as nama_user');
		$this->cb->from('t_po as a')->where($where);
		$this->cb->join($this->db->database . '.t_vendors as b', 'a.vendor = b.Id');
		$this->cb->join($this->db->database . '.users as c', 'a.user = c.nip');
		if ($keyword) {
			$this->cb->like('a.no_po', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('b.nama', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('c.nama', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('a.referensi', $keyword, 'both');
			$this->cb->where($where);
		}
		if ($filter) {
			$this->cb->where('a.vendor', $filter);
		}
		$po = $this->cb->order_by('a.tgl_pengajuan', 'DESC')->limit($limit, $start)->get();

		return $po;
	}

	function hutang_vendor($vendor)
	{
		if ($vendor) {
			$sql = "SELECT total, ppn FROM t_po WHERE jenis_pembayaran = 'hutang' AND status_pembayaran = 0 AND vendor = $vendor";
		} else {
			$sql = "SELECT total, ppn FROM t_po WHERE jenis_pembayaran = 'hutang' AND status_pembayaran = 0";
		}
		return $this->cb->query($sql)->result_array();
	}

	function get_roList($limit, $start, $keyword, $where)
	{
		$this->cb->select('a.no_ro, a.tgl_pengajuan, a.teknisi, a.posisi, a.total, a.status_sarlog, a.status_direksi_ops, a.Id, a.catatan_sarlog, a.catatan_direksi_ops,a.user, a.user_serah, a.bukti_serah, b.nama');
		$this->cb->from('t_ro as a')->where($where);
		$this->cb->join($this->db->database . '.users as b', 'a.user = b.nip', 'left');
		if ($keyword) {
			$this->cb->like('a.no_ro', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('b.nama', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('a.teknisi', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('a.referensi', $keyword, 'both');
			$this->cb->where($where);
		}

		return $this->cb->order_by('a.tgl_pengajuan', 'DESC')->limit($limit, $start)->get();
	}

	function count_po($keyword, $where, $filter)
	{
		$this->cb->select('a.no_po, a.ppn, a.vendor, a.tgl_pengajuan, a.posisi, a.total, a.status_sarlog, a.status_direksi_ops, a.status_dirut, a.Id, a.catatan_sarlog, a.catatan_direksi_ops, a.catatan_dirut, a.keterangan, a.bukti_bayar, a.user, a.date_bayar, a.date_proses, a.jenis_pembayaran, a.status_pembayaran, b.nama');
		$this->cb->from('t_po as a');
		$this->cb->join($this->db->database . '.t_vendors as b', 'a.vendor = b.Id');
		$this->cb->join($this->db->database . '.users as c', 'a.user = c.nip');
		if ($keyword) {
			$this->cb->like('a.no_po', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('b.nama', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('c.nama', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('a.referensi', $keyword, 'both');
			$this->cb->where($where);
		}
		if ($filter) {
			$this->cb->where('a.vendor', $filter);
		}
		$this->cb->where($where);
		$po = $this->cb->get()->num_rows();

		return $po;
	}

	function count_ro($keyword, $where)
	{
		$this->cb->select('a.no_ro, a.tgl_pengajuan, a.teknisi, a.posisi, a.total, a.status_sarlog, a.status_direksi_ops, a.Id, a.catatan_sarlog, a.catatan_direksi_ops,a.user, a.user_serah, a.bukti_serah, b.nama');
		$this->cb->from('t_ro as a')->where($where);
		$this->cb->join($this->db->database . '.users as b', 'a.user = b.nip', 'left');;
		if ($keyword) {
			$this->cb->like('a.no_ro', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('b.nama', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('a.teknisi', $keyword, 'both');
			$this->cb->where($where);
			$this->cb->or_like('a.referensi', $keyword, 'both');
			$this->cb->where($where);
		}
		return $this->cb->get()->num_rows();
	}

	public function total_sparepart()
	{
		$sql = "SELECT SUM(stok * harga_sat) as total FROM item_list";
		return $this->db->query($sql)->row_array();
	}

	public function total_repair()
	{
		$sql = "SELECT sum(item_list.harga_sat) as total FROM item_detail JOIN item_list ON item_list.Id = item_detail.kode_item WHERE item_detail.status = 'R'";
		return $this->db->query($sql)->row_array();
	}

	public function countPo($where)
	{
		return $this->cb->get_where('t_po', $where)->num_rows();
	}

	public function report_asset()
	{
		$sql = "SELECT a.tanggal, a.jenis, a.harga, a.jml, a.serial_number, b.nama, c.nama_asset FROM working_supply a LEFT JOIN asset_list c ON c.Id = a.asset_id LEFT JOIN item_list b ON b.Id = a.item_id WHERE a.jenis LIKE '%OUT%'";
		return $this->db->query($sql);
	}
}
