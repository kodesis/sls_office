<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Asset extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_asset');
		$this->load->library(array('form_validation', 'session', 'user_agent', 'Api_Whatsapp', 'pdfgenerator'));
		$this->load->library('pagination');
		$this->cb = $this->load->database('corebank', TRUE);
		$this->load->helper('url', 'form', 'download');
		date_default_timezone_set('Asia/Jakarta');
		if ($this->session->userdata('isLogin') == FALSE) {
			redirect('login/login_form');
		}
	}

	public function item_list()
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '502') !== false) {
			$search = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');

			//pagination settings
			$config['base_url'] = site_url('asset/item_list');
			$config['total_rows'] = $this->m_asset->item_count($search);
			$config['per_page'] = "20";
			$config["uri_segment"] = 3;
			$choice = $config["total_rows"] / $config["per_page"];
			//$config["num_links"] = floor($choice);
			$config["num_links"] = 10;
			// integrate bootstrap pagination
			$config['full_tag_open'] = '<ul class="pagination">';
			$config['full_tag_close'] = '</ul>';
			$config['first_link'] = false;
			$config['last_link'] = false;
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['prev_link'] = '«';
			$config['prev_tag_open'] = '<li class="prev">';
			$config['prev_tag_close'] = '</li>';
			$config['next_link'] = '»';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			$this->pagination->initialize($config);
			$data['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
			$data['users_data'] = $this->m_asset->item_get($config["per_page"], $data['page'], $search);
			$data['pagination'] = $this->pagination->create_links();

			//inbox notif
			$nip = $this->session->userdata('nip');
			$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
			$query = $this->db->query($sql);
			$res2 = $query->result_array();
			$result = $res2[0]['COUNT(Id)'];
			$data['count_inbox'] = $result;

			// Tello
			$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
			$query4 = $this->db->query($sql4);
			$res4 = $query4->result_array();
			$result4 = $res4[0]['COUNT(Id)'];
			$data['count_inbox2'] = $result4;

			$data['jenis_item'] = $this->db->get('item_jenis');
			$data['title'] = "Asset item list";
			$data['pages'] = "pages/aset/v_item_list";
			$data['coa'] = $this->cb->get('v_coa_all')->result_array();
			$data['total'] = $this->m_asset->total_sparepart();
			$data['total_repair'] = $this->m_asset->total_repair();
			$this->load->view('index', $data);
		} else {
			redirect('home');
		}
	}

	function ubah_item($id)
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '502') !== false) {
			//inbox notif
			$nip = $this->session->userdata('nip');
			$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
			$query = $this->db->query($sql);
			$res2 = $query->result_array();
			$result = $res2[0]['COUNT(Id)'];
			$data['count_inbox'] = $result;

			// Tello
			$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
			$query4 = $this->db->query($sql4);
			$res4 = $query4->result_array();
			$result4 = $res4[0]['COUNT(Id)'];
			$data['count_inbox2'] = $result4;

			$data['coa'] = $this->cb->get('v_coa_all')->result_array();
			$data['jenis_item'] = $this->db->get('item_jenis');
			$data['item'] = $this->db->get_where('item_list', ['Id' => $id])->row_array();
			$data['title'] = "Ubah data item";
			$data['pages'] = "pages/aset/v_ubah_item";
			$this->load->view('index', $data);
		} else {
			redirect('home');
		}
	}

	function update_item($id)
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '502') !== false) {
			$nama_item = $this->input->post('nama');
			$jenis = $this->input->post('jenis');
			$coa = $this->input->post('coa');

			$this->form_validation->set_rules('nama', 'nama item', 'required|trim', array('required' => '%s wajib diisi!'));
			$this->form_validation->set_rules('jenis', 'jenis item', 'required|trim', array('required' => '%s wajib diisi!'));

			if ($this->form_validation->run() == FALSE) {
				$response = [
					'success' => false,
					'status' => '401',
					'msg' => array_values($this->form_validation->error_array())[0],
				];
			} else {
				$update = [
					'nama' => $nama_item,
					'jenis_item' => $jenis,
					'coa' => $coa
				];

				$this->db->where('Id', $id);
				$this->db->update('item_list', $update);

				$response = [
					'success' => true,
					'status' => '200',
					'msg' => 'Data item berhasil diubah!'
				];
			}

			echo json_encode($response, http_response_code($response['status']));
		} else {
			redirect('home');
		}
	}

	public function detail($id)
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '502') !== false) {
			$search = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
			//pagination settings
			$config['base_url'] = site_url('asset/detail/' . $id);
			$config['total_rows'] = $this->m_asset->item_detail_count($search, $id);
			$config['per_page'] = "10";
			$config["uri_segment"] = 3;
			$choice = $config["total_rows"] / $config["per_page"];
			$config['enable_query_strings'] = TRUE;
			$config['page_query_string'] = TRUE;
			$config['use_page_numbers'] = TRUE;
			$config['reuse_query_string'] = TRUE;
			$config['query_string_segment'] = 'page';
			// $config["num_links"] = floor($choice);
			$config["num_links"] = 10;
			// integrate bootstrap pagination
			$config['full_tag_open'] = '<ul class="pagination">';
			$config['full_tag_close'] = '</ul>';
			$config['first_link'] = false;
			$config['last_link'] = false;
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['prev_link'] = '«';
			$config['prev_tag_open'] = '<li class="prev">';
			$config['prev_tag_close'] = '</li>';
			$config['next_link'] = '»';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			$this->pagination->initialize($config);
			// $data['page'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['page'] = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
			// $data['users_data'] = $this->m_asset->item_get($config["per_page"], $data['page'], $search);
			$data['pagination'] = $this->pagination->create_links();
			$nip = $this->session->userdata('nip');
			$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
			$query = $this->db->query($sql);
			$res2 = $query->result_array();
			$result = $res2[0]['COUNT(Id)'];
			$data['count_inbox'] = $result;

			$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
			$query4 = $this->db->query($sql4);
			$res4 = $query4->result_array();
			$result4 = $res4[0]['COUNT(Id)'];
			$data['count_inbox2'] = $result4;

			$data['title'] = "Detail item";
			$data['pages'] = "pages/aset/v_item_detail";
			$data['item'] = $this->db->get_where('item_list', ['Id' => $id])->row_array();
			// $data['detail'] = $this->db->get_where('item_detail', ['kode_item' => $id]);
			$data['detail'] = $this->m_asset->item_detail_get($config["per_page"], $data['page'], $search, $id);
			$this->load->view('index', $data);
		} else {
			redirect('home');
		}
	}

	function add_detail_item()
	{
		$id_item = $this->input->post('id_item');
		$serial = $this->input->post('serial');
		$tgl = $this->input->post('tanggal');

		$this->form_validation->set_rules('serial', 'serial number', 'required|trim|is_unique[item_detail.serial_number]', array('required' => '%s wajib diisi!', 'is_unique' => '%s sudah tersedia!'));
		$this->form_validation->set_rules('tanggal', 'tanggal masuk', 'required|trim', array('required' => '%s wajib diisi!'));

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'status' => '401',
				'msg' => array_values($this->form_validation->error_array())[0],
			];
		} else {
			$item = $this->db->get_where('item_list', ['Id' => $id_item])->row_array();
			$query = "SELECT * FROM item_detail WHERE kode_item = '$id_item' AND (status = 'A' OR status = 'R')";
			$detail = $this->db->query($query)->num_rows();
			// $detail = $this->db->get_where('item_detail', ['kode_item' => $id_item, 'status' => 'A'])->num_rows();

			if ($detail >= $item['stok']) {
				$response = [
					'success' => false,
					'status' => '401',
					'msg' => 'Detail serial number melebihi stok tersedia',
				];

				echo json_encode($response);
				return false;
			}

			$insert = [
				'kode_item' => $id_item,
				'serial_number' => $serial,
				'tanggal_masuk' => $tgl,
				'user' => $this->session->userdata('nip')
			];

			$this->db->insert('item_detail', $insert);

			$response = [
				'success' => true,
				'status' => '200',
				'msg' => 'Detail item berhasil ditambahkan!'
			];
		}

		echo json_encode($response);
	}

	function filter_jenis_item()
	{
		$jenis = $this->input->post('jenis_item');
		$this->session->set_userdata('filterJenis', $jenis);
		redirect('asset/item_list');
	}
	// function export_item()
	// {
	// 	$this->load->view('pages/aset/export_asset');
	// }

	function reset_jenis_item()
	{
		$this->session->unset_userdata('filterJenis');
		redirect('asset/item_list');
	}

	public function item_detail()
	{
		if ($this->session->userdata('isLogin') == FALSE) {
			redirect('home');
		} else {
			$a = $this->session->userdata('level');
			if (strpos($a, '501') !== false) {
				$nip = $this->session->userdata('nip');
				$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
				$query = $this->db->query($sql);
				$res2 = $query->result_array();
				$result = $res2[0]['COUNT(Id)'];
				$data['count_inbox'] = $result;

				$sql2 = "SELECT * FROM asset_ruang";
				$sql3 = "SELECT * FROM asset_lokasi";
				$query2 = $this->db->query($sql2);
				$query3 = $this->db->query($sql3);
				$asset_ruang = $query2->result();
				$asset_lokasi = $query3->result();

				//ambil data asset_list
				$data['asset_list'] = $this->m_app->ambil_asset_list($this->uri->segment(3));
				$data['asset_history'] = $this->m_app->ambil_asset_history($this->uri->segment(3));
				$data['asset_ruang'] = $asset_ruang;
				$data['asset_lokasi'] = $asset_lokasi;

				$this->load->view('asset_detail', $data);
			}
		}
	}

	public function add_item_in()
	{
		$id = $this->input->post('id_po');
		$po = $this->cb->get_where('t_po', ['Id' => $id])->row_array();
		$detail = $this->cb->get_where('t_po_detail', ['no_po' => $po['Id']])->result_array();

		$user = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
		$i = 0;
		foreach ($detail as $d) {
			$item[] = $this->db->get_where('item_list', ['Id' => $d['item']])->row_array();
			$item_list_jumlah[] = $item[$i]['stok'];
			$item_list_hargasat[] = $item[$i]['harga_sat'];

			$jml[] = ($d['qty'] * $d['satuan']);
			$price[] = ($d['qty'] * $d['price']);

			$total[] = $jml[$i] + $item_list_jumlah[$i];

			$harga[] = ($price[$i]) + ($item_list_jumlah[$i] * $item_list_hargasat[$i]);
			$harga_baru[] = $harga[$i] / $total[$i];

			$ws = [
				'no_po' => $po['Id'],
				'item_id' => $d['item'],
				'harga' => $harga_baru[$i],
				'jml' => $d['qty'],
				'stok_awal' => $item_list_jumlah[$i],
				'stok_akhir' => $total[$i],
				'user' => $user['nip'],
				'jenis' => 'IN',
				'keterangan' => $po['referensi']
			];

			$this->db->insert('working_supply', $ws);

			$update = [
				'stok' => $total[$i],
				'harga_sat' => $harga_baru[$i]
			];

			$this->db->where('Id', $item[$i]['Id']);
			$this->db->update('item_list', $update);

			$this->cb->where('Id', $item[$i]['Id']);
			$this->cb->update('t_po_detail', ['status_add' => 1]);

			$i++;
		}

		$this->cb->where('Id', $id);
		$this->cb->update('t_po', ['posisi' => 'Item PO sudah ditambahkan']);

		$response = [
			'success' => true,
			'status' => '200',
			'msg' => 'Data item berhasil ditambahkan!'
		];

		echo json_encode($response, http_response_code($response['status']));
	}

	public function add_item()
	{
		$nomor = htmlspecialchars($this->input->post('kode') ?? '', ENT_QUOTES, 'UTF-8');
		$nama = htmlspecialchars($this->input->post('name'), ENT_QUOTES, 'UTF-8');
		$jenis = $this->input->post('jenis_item');
		$coa = $this->input->post('coa');
		$catatan = $this->input->post('catatan');

		$this->form_validation->set_rules('kode', 'kode item', 'required|alpha_dash');
		$this->form_validation->set_rules('name', 'nama item', 'required');
		$this->form_validation->set_rules('jenis_item', 'jenis item', 'required');
		$this->form_validation->set_rules('coa', 'coa item', 'required');

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'status' => '401',
				'msg' => array_values($this->form_validation->error_array())[0],
			];
			echo json_encode($response);
		} else {
			$insert = [
				'nomor' => $nomor,
				'nama' => $nama,
				'stok' => 0,
				'harga_sat' => 0,
				'jenis_item' => $jenis,
				'catatan' => $catatan,
				'coa' => $coa
			];

			$this->db->insert('item_list', $insert);

			$response = [
				'success' => true,
				'status' => '200',
				'msg' => 'Data item berhasil ditambahkan!'
			];

			echo json_encode($response, http_response_code($response['status']));
		}
	}

	public function purchaseorder()
	{
		if ($this->session->userdata('isLogin') == FALSE) {
			redirect('home');
		} else {
			$a = $this->session->userdata('level');
			if (strpos($a, '502') !== false) {
				//inbox notif
				$nip = $this->session->userdata('nip');
				$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
				$query = $this->db->query($sql);
				$res1 = $query->result_array();
				$result = $res1[0]['COUNT(Id)'];
				$data['count_inbox'] = $result;

				$sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
				$query2 = $this->db->query($sql2);
				$res2 = $query2->result_array();
				$result2 = $res2[0]['COUNT(id)'];
				$data['count_inbox2'] = $result2;

				$data['item_list'] = $this->db->get('item_list');
				$data['vendors'] = $this->db->get('t_vendors');
				$data['title'] = "Create PO";
				$data['pages'] = "pages/aset/v_po";
				$this->load->view('index', $data);
			} else {
				redirect('home');
			}
		}
	}

	public function update_po($id)
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '502') !== false) {
			//inbox notif
			$nip = $this->session->userdata('nip');
			$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
			$query = $this->db->query($sql);
			$res1 = $query->result_array();
			$result = $res1[0]['COUNT(Id)'];
			$data['count_inbox'] = $result;

			$sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
			$query2 = $this->db->query($sql2);
			$res2 = $query2->result_array();
			$result2 = $res2[0]['COUNT(id)'];
			$data['count_inbox2'] = $result2;

			$data['item_list'] = $this->db->get('item_list');
			$data['vendors'] = $this->db->get('t_vendors');
			$data['po'] = $this->cb->get_where('t_po', ['Id' => $id])->row_array();
			$data['title'] = "Create PO";
			$data['pages'] = "pages/aset/v_po";
			$this->load->view('index', $data);
		} else {
			redirect('home');
		}
	}

	public function save_po()
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '502') !== false) {
			$nip = $this->session->userdata('nip');
			$tgl = $this->input->post('tanggal');
			$vendor = $this->input->post('vendor');
			// $keterangan = $this->input->post('keterangan');
			$rows = $this->input->post('row[]');
			$item = $this->input->post('item[]');
			$qty = $this->input->post('qty[]');
			$uoi = $this->input->post('uoi[]');
			$satuan = $this->input->post('satuan[]');
			$keterangan = $this->input->post('ket[]');
			$price = $this->input->post('harga[]');
			$subtotal = $this->input->post('total[]');
			$total = $this->input->post('nominal');
			$now = date('Y-m-d');
			if (strtotime($tgl) != strtotime($now)) {
				$tgl = $tgl;
			} else {
				$tgl = date('Y-m-d H:i:s');
			}

			$this->form_validation->set_rules('tanggal', 'tanggal po', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('vendor', 'vendor', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('item[]', 'nama item', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('qty[]', 'qty item', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('harga[]', 'harga item', 'required', ['required' => '%s wajib diisi']);

			if ($this->form_validation->run() == FALSE) {
				$response = [
					'success' => false,
					'msg' => array_values($this->form_validation->error_array())[0]
				];
			} else {
				// $sql = "SELECT MAX(nomor) as maximal FROM letter LEFT JOIN jenis_surat ON letter.jenis_surat = jenis_surat.id WHERE jenis_surat.company = '$jenis_surat->company' AND YEAR(date_created) = YEAR(curdate()) AND letter.back_date = 0;";
				$sql = "SELECT count(a.Id) as jml FROM t_po as a WHERE a.vendor ='$vendor' AND YEAR(tgl_pengajuan) = YEAR(curdate())";
				$result = $this->cb->query($sql);

				if ($result->num_rows() > 0) {
					$res = $result->row_array();
					$nomor = $res['jml'] + 1;
				} else {
					$nomor = 1;
				}
				$data_vendor = $this->db->get_where('t_vendors', ['Id' => $vendor])->row_array();
				$array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
				$bln = $array_bln[date('n')];
				$no_po = sprintf("%06d", $nomor) . '-' . $data_vendor['kode'];
				$ref = "PO-" . sprintf("%06d", $nomor) . '/' . $bln . '/' . $data_vendor['kode'] . '/' . date('y');

				// $count = $this->cb->get('t_po')->num_rows();
				// $count = $count + 1;
				// $no_po = sprintf("%06d", $count);

				$insert = [
					'no_po' => $no_po,
					'referensi' => $ref,
					'user' => $nip,
					// 'keterangan' => $keterangan,
					'tgl_pengajuan' => $tgl,
					'total' => preg_replace('/[^a-zA-Z0-9\']/', '', $total),
					'vendor' => $vendor,
					'posisi' => 'diajukan kepada sarlog'
				];

				$this->cb->insert('t_po', $insert);
				$last_id = $this->cb->insert_id();

				for ($i = 0; $i < count($rows); $i++) {
					$detail = [
						'no_po' => $last_id,
						'item' => $item[$i],
						'qty' => preg_replace('/[^a-zA-Z0-9\']/', '', $qty[$i]),
						'uoi' => $uoi[$i],
						'price' => preg_replace('/[^a-zA-Z0-9\']/', '', $price[$i]),
						'total' => preg_replace('/[^a-zA-Z0-9\']/', '', $subtotal[$i]),
						'satuan' => preg_replace('/[^a-zA-Z0-9\']/', '', $satuan[$i]),
						'keterangan' => $keterangan[$i]
					];

					$this->cb->insert('t_po_detail', $detail);
				}

				$this->db->select('phone');
				$this->db->where(['level_jabatan' => 2, 'bagian' => 2]);
				$sarlog = $this->db->get('users')->result_array();
				$phone = '';
				foreach ($sarlog as $val) {
					$phone .= $val['phone'] . ',';
				}

				$nama_session = $this->session->userdata('nama');
				$msg = "There's a new Purchase Order\nNo : *$no_po*\nFrom : *$nama_session*\n\nMohon untuk segera diproses.";
				$this->api_whatsapp->wa_notif($msg, $phone);

				$response = [
					'success' => true,
					'msg' => 'PO berhasil dibuat!'
				];
			}

			echo json_encode($response);
		} else {
			redirect('home');
		}
	}

	public function simpan_update_po($id)
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '502') !== false) {
			$po = $this->cb->get_where('t_po', ['Id' => $id])->row_array();
			$nip = $this->session->userdata('nip');
			$tgl = $this->input->post('tanggal');
			$vendor = $this->input->post('vendor');
			// $keterangan = $this->input->post('keterangan');
			$rows = $this->input->post('row[]');
			$item = $this->input->post('item[]');
			$qty = $this->input->post('qty[]');
			$uoi = $this->input->post('uoi[]');
			$satuan = $this->input->post('satuan[]');
			$keterangan = $this->input->post('ket[]');
			$price = $this->input->post('harga[]');
			$subtotal = $this->input->post('total[]');
			$total = $this->input->post('nominal');
			$now = date('Y-m-d');
			if (strtotime($tgl) != strtotime($now)) {
				$tgl = $tgl;
			} else {
				$tgl = date('Y-m-d H:i:s');
			}

			$this->form_validation->set_rules('tanggal', 'tanggal po', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('vendor', 'vendor', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('item[]', 'nama item', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('qty[]', 'qty item', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('harga[]', 'harga item', 'required', ['required' => '%s wajib diisi']);

			if ($this->form_validation->run() == FALSE) {
				$response = [
					'success' => false,
					'msg' => array_values($this->form_validation->error_array())[0]
				];
			} else {

				$update = [
					'user' => $nip,
					'tgl_pengajuan' => $tgl,
					'total' => preg_replace('/[^a-zA-Z0-9\']/', '', $total),
					'vendor' => $vendor,
					'posisi' => 'diajukan kepada sarlog',
					'sarlog' => null,
					'status_sarlog' => 0,
					'catatan_sarlog' => null,
					'date_sarlog' => null,
					'direksi_ops' => null,
					'date_direksi_ops' => null,
					'status_direksi_ops' => 0,
					'catatan_direksi_ops' => null,
					'dirut' => null,
					'date_dirut' => null,
					'status_dirut' => 0,
					'catatan_dirut' => null
				];
				$this->cb->where('Id', $id);
				$this->cb->update('t_po', $update);

				$this->cb->where('no_po',  $po['Id']);
				$this->cb->delete('t_po_detail');

				for ($i = 0; $i < count($rows); $i++) {
					$detail = [
						'no_po' => $po['Id'],
						'item' => $item[$i],
						'qty' => preg_replace('/[^a-zA-Z0-9\']/', '', $qty[$i]),
						'uoi' => $uoi[$i],
						'price' => preg_replace('/[^a-zA-Z0-9\']/', '', $price[$i]),
						'total' => preg_replace('/[^a-zA-Z0-9\']/', '', $subtotal[$i]),
						'satuan' => preg_replace('/[^a-zA-Z0-9\']/', '', $satuan[$i]),
						'keterangan' => $keterangan[$i]
					];

					$this->cb->insert('t_po_detail', $detail);
				}

				$this->db->select('phone');
				$this->db->where(['level_jabatan' => 2, 'bagian' => 2]);
				$sarlog = $this->db->get('users')->result_array();
				$phone = '';
				foreach ($sarlog as $val) {
					$phone .= $val['phone'] . ',';
				}

				$nama_session = $this->session->userdata('nama');
				$msg = "There's a update Purchase Order\nNo : *$po[no_po]*\nFrom : *$nama_session*\n\nMohon untuk segera diproses.";
				$this->api_whatsapp->wa_notif($msg, $phone);

				$response = [
					'success' => true,
					'msg' => 'PO berhasil diubah!'
				];
			}

			echo json_encode($response);
		} else {
			redirect('home');
		}
	}

	public function po_list()
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '502') !== false) {
			$nip = $this->session->userdata('nip');
			// Pagination
			$keyword = htmlspecialchars($this->input->get('keyword') ?? '', ENT_QUOTES, 'UTF-8');
			$config['base_url'] = base_url('asset/po_list');
			$config['total_rows'] = $this->m_asset->count_po($keyword, ['a.user' => $nip], null);
			$config['per_page'] = 10;
			$config['uri_segment'] = 3;
			$config['num_links'] = 3;
			$config['enable_query_strings'] = TRUE;
			$config['page_query_string'] = TRUE;
			$config['use_page_numbers'] = TRUE;
			$config['reuse_query_string'] = TRUE;
			$config['query_string_segment'] = 'page';

			// Bootstrap style pagination
			$config['full_tag_open'] = '<ul class="pagination">';
			$config['full_tag_close'] = '</ul>';
			$config['first_link'] = false;
			$config['last_link'] = false;
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['prev_link'] = '«';
			$config['prev_tag_open'] = '<li class="prev">';
			$config['prev_tag_close'] = '</li>';
			$config['next_link'] = '»';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';

			// Initialize paginaton
			$this->pagination->initialize($config);
			$page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
			$data['pagination'] = $this->pagination->create_links();
			$data['page'] = $page;

			//inbox notif
			$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
			$sql2 = "SELECT * FROM asset_ruang";
			$sql3 = "SELECT * FROM asset_lokasi";
			$query = $this->db->query($sql);
			$query2 = $this->db->query($sql2);
			$query3 = $this->db->query($sql3);
			$res2 = $query->result_array();
			$asset_ruang = $query2->result();
			$asset_lokasi = $query3->result();
			$result = $res2[0]['COUNT(Id)'];
			$data['count_inbox'] = $result;
			$data['asset_ruang'] = $asset_ruang;
			$data['asset_lokasi'] = $asset_lokasi;

			// Tello
			$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
			$query4 = $this->db->query($sql4);
			$res4 = $query4->result_array();
			$result4 = $res4[0]['COUNT(Id)'];
			$data['count_inbox2'] = $result4;

			$data['po'] = $this->m_asset->get_poList($config['per_page'], $page, $keyword, ['a.user' => $nip], null);
			$data['count_sarlog'] = $this->m_asset->countPo(['status_sarlog' => 0]);
			$data['count_sarlog'] = $this->m_asset->countPo(['status_sarlog' => 0]);
			$data['count_dirops'] = $this->m_asset->countPo(['status_sarlog' => 1, 'direksi_ops' => $this->session->userdata('nip'), 'status_direksi_ops' => 0]);
			$data['count_dirut'] = $this->m_asset->countPo(['status_sarlog' => 1, 'dirut' => $this->session->userdata('nip'), 'status_dirut' => 0]);
			$data['title'] = "Purchase Order List";
			$data['pages'] = "pages/aset/v_po_list";
			$this->load->view('index', $data);
		} else {
			redirect('home');
		}
	}

	public function sarlog()
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '503') !== false) {
			$filter = $this->input->get('vendor');
			$keyword = htmlspecialchars($this->input->get('keyword') ?? '', ENT_QUOTES, 'UTF-8');
			$config['base_url'] = base_url('asset/sarlog');
			$config['total_rows'] = $this->m_asset->count_po($keyword, [], $filter);
			$config['per_page'] = 20;
			$config['uri_segment'] = 3;
			$config['num_links'] = 3;
			$config['enable_query_strings'] = TRUE;
			$config['page_query_string'] = TRUE;
			$config['use_page_numbers'] = TRUE;
			$config['reuse_query_string'] = TRUE;
			$config['query_string_segment'] = 'page';

			// Bootstrap style pagination
			$config['full_tag_open'] = '<ul class="pagination">';
			$config['full_tag_close'] = '</ul>';
			$config['first_link'] = false;
			$config['last_link'] = false;
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['prev_link'] = '«';
			$config['prev_tag_open'] = '<li class="prev">';
			$config['prev_tag_close'] = '</li>';
			$config['next_link'] = '»';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';

			// Initialize paginaton
			$this->pagination->initialize($config);
			$page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
			$data['pagination'] = $this->pagination->create_links();
			//inbox notif
			$nip = $this->session->userdata('nip');
			$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
			$sql2 = "SELECT * FROM asset_ruang";
			$sql3 = "SELECT * FROM asset_lokasi";
			$query = $this->db->query($sql);
			$query2 = $this->db->query($sql2);
			$query3 = $this->db->query($sql3);
			$res2 = $query->result_array();
			$asset_ruang = $query2->result();
			$asset_lokasi = $query3->result();
			$result = $res2[0]['COUNT(Id)'];
			$data['count_inbox'] = $result;
			$data['asset_ruang'] = $asset_ruang;
			$data['asset_lokasi'] = $asset_lokasi;

			// Tello
			$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
			$query4 = $this->db->query($sql4);
			$res4 = $query4->result_array();
			$result4 = $res4[0]['COUNT(Id)'];
			$data['count_inbox2'] = $result4;

			$data['po'] = $this->m_asset->get_poList($config['per_page'], $page, $keyword, [], $filter);
			$data['coa'] = $this->cb->get('v_coa_all');
			$data['direksi'] = $this->db->get_where('users', ['level_jabatan >' => 4])->result_array();
			$data['hutang'] = $this->m_asset->hutang_vendor($filter);
			$data['title'] = "PO List Sarlog";
			$data['pages'] = "pages/aset/v_sarlog";
			$this->load->view('index', $data);
		} else {
			redirect('home');
		}
	}

	public function update_sarlog()
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '503') !== false) {
			$id = $this->input->post('id_po');
			$tgl = $this->input->post('tanggal');
			$status = $this->input->post('status');
			$catatan = $this->input->post('catatan');

			$now = date('Y-m-d');
			if (strtotime($tgl) != strtotime($now)) {
				$tgl = $tgl;
			} else {
				$tgl = date('Y-m-d H:i:s');
			}

			$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
			$this->form_validation->set_rules('status', 'status', 'required|in_list[1,2,3]', ['required' => '%s wajib diisi!']);

			if ($this->form_validation->run() == FALSE) {
				$response = [
					'success' => false,
					'msg' => array_values($this->form_validation->error_array())[0]
				];
			} else {
				$po = $this->cb->get_where('t_po', ['Id' => $id])->row_array();
				if ($status == 1) {
					$posisi = 'diajukan kepada direktur operasional';
				} else {
					$posisi = 'ditolak sarlog';
				}

				$update = [
					'status_sarlog' => $status,
					'sarlog' => $this->session->userdata('nip'),
					'posisi' => $posisi,
					'date_sarlog' => $tgl,
					'catatan_sarlog' => $catatan,
					'direksi_ops' => 'SLS0004'
				];

				$this->cb->where('Id', $id);
				$this->cb->update('t_po', $update);

				$user = $this->db->get_where('users', ['nip' => $po['user']])->row_array();

				$this->db->like('nama_jabatan', 'Direktur Utama', 'both');
				$this->db->or_like('nama_jabatan', 'Direktur Operasional', 'both');
				$direksi = $this->db->get('users')->result_array();

				$nama_session = $this->session->userdata('nama');

				if ($status == 1) {
					$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* sudah disetujui oleh *$nama_session* sebagai penanggung jawab logistik.\nSelanjutnya pengajuan anda akan diajukan kepada Direktur Operasional.\n\n*Catatan* : $catatan";

					$msgdireksi = "There's a new Purchase Order\n\nNo : *$po[no_po]*\nFrom : *$user[nama]*\n\nMohon untuk segera diproses.";
					$this->api_whatsapp->wa_notif($msg, $user['phone']);

					$phone = '';
					foreach ($direksi as $val) {
						$phone .= $val['phone'] . ',';
					}
					$this->api_whatsapp->wa_notif($msgdireksi, $phone);
				} else if ($status == 2) {
					$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* diminta untuk direvisi oleh *$nama_session* sebagai penanggung jawab logistik.\n\n*Catatan* : $catatan";
					$this->api_whatsapp->wa_notif($msg, $user['phone']);
				} else {
					$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* ditolak oleh *$nama_session* sebagai penanggung jawab logistik.\n\n*Catatan* : $catatan";
					$this->api_whatsapp->wa_notif($msg, $user['phone']);
				}

				$response = [
					'success' => true,
					'msg' => 'Status PO berhasil diubah!'
				];
			}

			echo json_encode($response);
		} else {
			$response = [
				'success' => false,
				'msg' => 'Access Denied'
			];
			echo json_encode($response);
		}
	}

	public function direksi_ops()
	{
		$keyword = htmlspecialchars($this->input->get('keyword') ?? '', ENT_QUOTES, 'UTF-8');
		$config['base_url'] = base_url('asset/direksi_ops');
		$config['total_rows'] = $this->m_asset->count_po($keyword, ['status_sarlog' => 1, 'direksi_ops' => $this->session->userdata('nip')], null);
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;
		$config['num_links'] = 3;
		$config['enable_query_strings'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['use_page_numbers'] = TRUE;
		$config['reuse_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';

		// Bootstrap style pagination
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['prev_link'] = '«';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '»';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		// Initialize paginaton
		$this->pagination->initialize($config);
		$page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
		$data['pagination'] = $this->pagination->create_links();
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['po'] = $this->m_asset->get_poList($config['per_page'], $page, $keyword, ['status_sarlog' => 1, 'direksi_ops' => $this->session->userdata('nip')], null);
		// $data['direksi'] = $this->db->get_where('users', ['level_jabatan >' => 4])->result_array();
		$data['title'] = "PO List Direksi";
		$data['pages'] = "pages/aset/v_direksi";
		$this->load->view('index', $data);
	}

	public function dirut()
	{
		$keyword = htmlspecialchars($this->input->get('keyword') ?? '', ENT_QUOTES, 'UTF-8');
		$config['base_url'] = base_url('asset/dirut');
		$config['total_rows'] = $this->m_asset->count_po($keyword, ['status_sarlog' => 1, 'status_direksi_ops' => 1, 'dirut' => $this->session->userdata('nip')], null);
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;
		$config['num_links'] = 3;
		$config['enable_query_strings'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['use_page_numbers'] = TRUE;
		$config['reuse_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';

		// Bootstrap style pagination
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['prev_link'] = '«';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '»';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		// Initialize paginaton
		$this->pagination->initialize($config);
		$page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
		$data['pagination'] = $this->pagination->create_links();
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['po'] = $this->m_asset->get_poList($config['per_page'], $page, $keyword, ['status_sarlog' => 1, 'status_direksi_ops' => 1, 'dirut' => $this->session->userdata('nip')], null);
		// $data['direksi'] = $this->db->get_where('users', ['level_jabatan >' => 4])->result_array();
		$data['title'] = "PO List Direksi";
		$data['pages'] = "pages/aset/v_dirut";
		$this->load->view('index', $data);
	}

	public function update_dirut()
	{
		$id = $this->input->post('id_po');
		$tgl = $this->input->post('tanggal');
		$status = $this->input->post('status');
		$catatan = $this->input->post('catatan');

		$now = date('Y-m-d');
		if (strtotime($tgl) != strtotime($now)) {
			$tgl = $tgl;
		} else {
			$tgl = date('Y-m-d H:i:s');
		}

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('status', 'status', 'required|in_list[1,2,3]', ['required' => '%s wajib diisi!']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			$po = $this->cb->get_where('t_po', ['Id' => $id])->row_array();
			if ($status == 1) {
				$posisi = 'disetujui untuk diproses';
			} else {
				$posisi = 'ditolak oleh direktur utama';
			}
			$update = [
				'status_dirut' => $status,
				'posisi' => $posisi,
				'date_dirut' => $tgl,
				'catatan_dirut' => $catatan,
			];

			$this->cb->where('Id', $id);
			$this->cb->update('t_po', $update);

			$user = $this->db->get_where('users', ['nip' => $po['user']])->row_array();
			$sarlog = $this->db->get_where('users', ['nip' => $po['sarlog']])->row_array();

			$nama_session = $this->session->userdata('nama');

			if ($status == 1) {
				$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* sudah disetujui oleh *$nama_session* sebagai Direktur Utama.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);

				$msgsarlog = "Pemberitahuan Purchase Order\n\nHallo *$sarlog[nama]*, Purchase Order dengan No. *$po[no_po]* sudah disetujui oleh *$nama_session* sebagai Direktur Utama.\nMohon untuk segera diproses lebih lanjut.\n*Catatan* : $catatan";

				$this->api_whatsapp->wa_notif($msgsarlog, $sarlog['phone']);
			} else if ($status == 2) {
				$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* diminta untuk direvisi oleh *$nama_session* sebagai Direktur Utama.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);
			} else {
				$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* ditolak oleh *$nama_session* sebagai Direktur Utama.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);
			}

			$response = [
				'success' => true,
				'msg' => 'Status PO berhasil diubah!'
			];
		}

		echo json_encode($response);
	}

	public function update_direksi_ops()
	{
		$id = $this->input->post('id_po');
		$tgl = $this->input->post('tanggal');
		$status = $this->input->post('status');
		$catatan = $this->input->post('catatan');

		$now = date('Y-m-d');
		if (strtotime($tgl) != strtotime($now)) {
			$tgl = $tgl;
		} else {
			$tgl = date('Y-m-d H:i:s');
		}

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('status', 'status', 'required|in_list[1,2,3]', ['required' => '%s wajib diisi!']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			$po = $this->cb->get_where('t_po', ['Id' => $id])->row_array();
			if ($status == 1) {
				$posisi = 'disetujui untuk diproses';
			} else {
				$posisi = 'ditolak oleh direktur operasional';
			}

			$update = [
				'status_direksi_ops' => $status,
				'posisi' => $posisi,
				'date_direksi_ops' => $tgl,
				'catatan_direksi_ops' => $catatan,
			];

			$this->cb->where('Id', $id);
			$this->cb->update('t_po', $update);

			$user = $this->db->get_where('users', ['nip' => $po['user']])->row_array();

			$this->db->like('nama_jabatan', 'Direktur Utama', 'both');
			$direksi = $this->db->get('users')->row_array();

			$nama_session = $this->session->userdata('nama');

			$sarlog = $this->db->get_where('users', ['nip' => $po['sarlog']])->row_array();

			if ($status == 1) {
				$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* sudah disetujui oleh *$nama_session* sebagai Direktur Operasional.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);

				$msgsarlog = "Pemberitahuan Purchase Order\n\nHallo *$sarlog[nama]*, Purchase Order dengan No. *$po[no_po]* sudah disetujui oleh *$nama_session* sebagai Direktur Operasional.\nMohon untuk segera diproses lebih lanjut.\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msgsarlog, $sarlog['phone']);
			} else if ($status == 2) {
				$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* diminta untuk direvisi oleh *$nama_session* sebagai Direktur Operasional.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);
			} else {
				$msg = "Pemberitahuan Purchase Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$po[no_po]* ditolak oleh *$nama_session* sebagai Direktur Operasional.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);
			}

			$response = [
				'success' => true,
				'msg' => 'Status PO berhasil diubah!'
			];
		}

		echo json_encode($response);
	}

	public function process($id)
	{
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['coa'] = $this->cb->get('v_coa_all');
		$data['po'] = $this->m_asset->get_poList(null, null, null, ['a.Id' => $id], null)->row_array();
		$data['title'] = "Proses PO";
		$data['pages'] = "pages/aset/v_process";
		$this->load->view('index', $data);
	}

	public function update_process()
	{
		$id = $this->input->post('id_po');
		$coa_debit = $this->input->post('coa_debit[]');
		$coa_kredit = $this->input->post('coa-kredit');
		$id_item = $this->input->post('id_item[]');
		$rows = $this->input->post('row_item[]');
		$tgl = $this->input->post('tanggal');
		$now = date('Y-m-d');
		$jenis_pembayaran = $this->input->post('jenis-pembayaran');
		$ppn = $this->input->post('opsi_ppn');

		if (strtotime($tgl) != strtotime($now)) {
			$date_bayar = $tgl;
		} else {
			$date_bayar = date('Y-m-d H:i:s');
		}

		$this->form_validation->set_rules('coa_debit[]', 'coa persediaan', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('jenis-pembayaran', 'jenis pembayaran', 'required|in_list[kas,hutang]', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('coa-kredit', 'coa kredit', 'required', ['required' => '%s wajib diisi!']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			$config['upload_path']          = './upload/po';
			$config['allowed_types']        = 'jpg|jpeg|png|pdf';
			$config['encrypt_name']         = TRUE;
			$this->load->library('upload', $config);
			$po = $this->cb->get_where('t_po', ['Id' => $id])->row_array();
			if ($jenis_pembayaran == 'kas') {
				if (!$this->upload->do_upload('bukti-bayar')) {
					$response = [
						'success' => false,
						'msg' => $this->upload->display_errors()
					];
				} else {
					$upload = $this->upload->data();
					for ($i = 0; $i < count($rows); $i++) {
						$po_detail[] = $this->cb->get_where('t_po_detail', ['Id' => $id_item[$i]])->row_array();
						$item[] = $this->db->get_where('item_list', ['Id' => $po_detail[$i]['item']])->row_array();
						// Update coa debit
						$detail_coa_debit[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_debit[$i]])->row_array();
						$posisi_debit[] = $detail_coa_debit[$i]['posisi'];
						$nominal_debit[] = $detail_coa_debit[$i]['nominal'];
						$substr_coa_debit[] = substr($coa_debit[$i], 0, 1);
						$nominal_debit_baru[] = 0;

						if ($posisi_debit[$i] == "AKTIVA") {
							$nominal_debit_baru[$i] = $nominal_debit[$i] + $po_detail[$i]['total'];
						}

						if ($posisi_debit[$i] == "PASIVA") {
							$nominal_debit_baru[$i] = $nominal_debit[$i] - $po_detail[$i]['total'];
						}

						if ($substr_coa_debit[$i] == "1" || $substr_coa_debit[$i] == "2" || $substr_coa_debit[$i] == "3") {
							$table_debit[] = "t_coa_sbb";
							$kolom_debit[] = "no_sbb";
						}
						if ($substr_coa_debit[$i] == "4" || $substr_coa_debit[$i] == "5" || $substr_coa_debit[$i] == "6" || $substr_coa_debit[$i] == "7" || $substr_coa_debit[$i] == "8" || $substr_coa_debit[$i] == "9") {
							$table_debit[] = "t_coalr_sbb";
							$kolom_debit[] = "no_lr_sbb";
						}

						$this->cb->where([$kolom_debit[$i] => $coa_debit[$i]]);
						$this->cb->update($table_debit[$i], ['nominal' => $nominal_debit_baru[$i]]);


						// update coa credit
						$detail_coa_credit[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_kredit])->row_array();
						$posisi_credit[] = $detail_coa_credit[$i]['posisi'];
						$nominal_credit[] = $detail_coa_credit[$i]['nominal'];
						$substr_coa_credit[] = substr($coa_kredit, 0, 1);
						$saldo_credit_baru[] = 0;
						$nominal_credit_baru[] = 0;

						if ($posisi_credit[$i] == "AKTIVA") {
							$nominal_credit_baru[$i] = $nominal_credit[$i] - $po_detail[$i]['total'];
						}
						if ($posisi_credit[$i] == "PASIVA") {
							$nominal_credit_baru[$i] = $nominal_credit[$i] + $po_detail[$i]['total'];
						}

						if ($substr_coa_credit[$i] == "1" || $substr_coa_credit[$i] == "2" || $substr_coa_credit[$i] == "3") {
							$table_credit[] = "t_coa_sbb";
							$kolom_credit[] = "no_sbb";
						}

						if ($substr_coa_credit[$i] == "4" || $substr_coa_credit[$i] == "5" || $substr_coa_credit[$i] == "6" || $substr_coa_credit[$i] == "7" || $substr_coa_credit[$i] == "8" || $substr_coa_credit[$i] == "9") {
							$table_credit[] = "t_coalr_sbb";
							$kolom_credit[] = "no_lr_sbb";
						}

						$this->cb->where([$kolom_credit[$i] => $coa_kredit]);
						$this->cb->update($table_credit[$i], ['nominal' => $nominal_credit_baru[$i]]);

						// update table pengajuan detail
						$this->cb->where('Id', $id_item[$i]);
						$this->cb->update('t_po_detail', [
							'kredit' => $coa_kredit,
							'debit' => $coa_debit[$i]
						]);

						// create jurnal
						$jurnal = [
							'tanggal' => $date_bayar,
							'akun_debit' => $coa_debit[$i],
							'jumlah_debit' => $po_detail[$i]['total'],
							'akun_kredit' => $coa_kredit,
							'jumlah_kredit' => $po_detail[$i]['total'],
							'saldo_debit' => $nominal_debit_baru[$i],
							'saldo_kredit' => $nominal_credit_baru[$i],
							'keterangan' => $item[$i]['nama'],
							'created_by' => $this->session->userdata('nip'),
						];
						$this->cb->insert('jurnal_neraca', $jurnal);
					}

					if ($ppn == 1) {
						$nominal_ppn = $po['total'] * 0.11;
						$ppn_masukan = $this->cb->get_where('v_coa_all', ['no_sbb' => '1108007'])->row_array();
						$nominal_ppn_masukan = $ppn_masukan['nominal'];
						$nominal_ppn_masukan_baru = $nominal_ppn_masukan + $nominal_ppn;
						$this->cb->where('no_sbb', $ppn_masukan['no_sbb']);
						$this->cb->update('t_coa_sbb', ['nominal' => $nominal_ppn_masukan_baru]);

						$cr = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_kredit])->row_array();
						$posisi_cr = $cr['posisi'];
						$nominal_cr = $cr['nominal'];
						$substr_coa_cr = substr($coa_kredit, 0, 1);
						$nominal_cr_baru = 0;

						if ($posisi_cr == "AKTIVA") {
							$nominal_cr_baru = $nominal_cr - $nominal_ppn;
						}
						if ($posisi_cr == "PASIVA") {
							$nominal_cr_baru = $nominal_cr + $nominal_ppn;
						}

						if ($substr_coa_cr == "1" || $substr_coa_cr == "2" || $substr_coa_cr == "3") {
							$table_cr = "t_coa_sbb";
							$kolom_cr = "no_sbb";
						}

						if ($substr_coa_cr == "4" || $substr_coa_cr == "5" || $substr_coa_cr == "6" || $substr_coa_cr == "7" || $substr_coa_cr == "8" || $substr_coa_cr == "9") {
							$table_cr = "t_coalr_sbb";
							$kolom_cr = "no_lr_sbb";
						}

						$this->cb->where([$kolom_cr => $coa_kredit]);
						$this->cb->update($table_cr, ['nominal' => $nominal_cr_baru]);

						// create jurnal
						$jurnal = [
							'tanggal' => $date_bayar,
							'akun_debit' => $ppn_masukan['no_sbb'],
							'jumlah_debit' => $nominal_ppn,
							'akun_kredit' => $coa_kredit,
							'jumlah_kredit' => $nominal_ppn,
							'saldo_debit' => $nominal_ppn_masukan_baru,
							'saldo_kredit' => $nominal_cr_baru,
							'keterangan' => 'PPN MASUKAN ' . $po['no_po'],
							'created_by' => $this->session->userdata('nip'),
						];
						$this->cb->insert('jurnal_neraca', $jurnal);
					}

					// Update table pengajuan
					$update = [
						'user_bayar' => $this->session->userdata('nip'),
						'posisi' => 'Sudah Dibayar',
						'date_proses' => $date_bayar,
						'date_bayar' => $date_bayar,
						'user_proses' => $this->session->userdata('nip'),
						'bukti_bayar' => $upload['file_name'],
						'jenis_pembayaran' => $jenis_pembayaran,
						'status_pembayaran' => 1,
						'ppn' => $ppn
					];

					$this->cb->where(['Id' => $id]);
					$this->cb->update('t_po', $update);

					$response = [
						'success' => true,
						'msg' => 'PO berhasil diproses!'
					];
				}
			} else {
				for ($i = 0; $i < count($rows); $i++) {
					$po_detail[] = $this->cb->get_where('t_po_detail', ['Id' => $id_item[$i]])->row_array();
					$item[] = $this->db->get_where('item_list', ['Id' => $po_detail[$i]['item']])->row_array();
					// Update coa debit
					// $item_detail = $this
					// Update coa debit
					$detail_coa_debit[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_debit[$i]])->row_array();
					$posisi_debit[] = $detail_coa_debit[$i]['posisi'];
					$nominal_debit[] = $detail_coa_debit[$i]['nominal'];
					$substr_coa_debit[] = substr($coa_debit[$i], 0, 1);
					$nominal_debit_baru[] = 0;

					if ($posisi_debit[$i] == "AKTIVA") {
						$nominal_debit_baru[$i] = $nominal_debit[$i] + $po_detail[$i]['total'];
					}

					if ($posisi_debit[$i] == "PASIVA") {
						$nominal_debit_baru[$i] = $nominal_debit[$i] - $po_detail[$i]['total'];
					}

					if ($substr_coa_debit[$i] == "1" || $substr_coa_debit[$i] == "2" || $substr_coa_debit[$i] == "3") {
						$table_debit[] = "t_coa_sbb";
						$kolom_debit[] = "no_sbb";
					}
					if ($substr_coa_debit[$i] == "4" || $substr_coa_debit[$i] == "5" || $substr_coa_debit[$i] == "6" || $substr_coa_debit[$i] == "7" || $substr_coa_debit[$i] == "8" || $substr_coa_debit[$i] == "9") {
						$table_debit[] = "t_coalr_sbb";
						$kolom_debit[] = "no_lr_sbb";
					}

					$this->cb->where([$kolom_debit[$i] => $coa_debit[$i]]);
					$this->cb->update($table_debit[$i], ['nominal' => $nominal_debit_baru[$i]]);


					// update coa credit
					$detail_coa_credit[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_kredit])->row_array();
					$posisi_credit[] = $detail_coa_credit[$i]['posisi'];
					$nominal_credit[] = $detail_coa_credit[$i]['nominal'];
					$substr_coa_credit[] = substr($coa_kredit, 0, 1);
					$saldo_credit_baru[] = 0;
					$nominal_credit_baru[] = 0;

					if ($posisi_credit[$i] == "AKTIVA") {
						$nominal_credit_baru[$i] = $nominal_credit[$i] - $po_detail[$i]['total'];
					}
					if ($posisi_credit[$i] == "PASIVA") {
						$nominal_credit_baru[$i] = $nominal_credit[$i] + $po_detail[$i]['total'];
					}

					if ($substr_coa_credit[$i] == "1" || $substr_coa_credit[$i] == "2" || $substr_coa_credit[$i] == "3") {
						$table_credit[] = "t_coa_sbb";
						$kolom_credit[] = "no_sbb";
					}
					if ($substr_coa_credit[$i] == "4" || $substr_coa_credit[$i] == "5" || $substr_coa_credit[$i] == "6" || $substr_coa_credit[$i] == "7" || $substr_coa_credit[$i] == "8" || $substr_coa_credit[$i] == "9") {
						$table_credit[] = "t_coalr_sbb";
						$kolom_credit[] = "no_lr_sbb";
					}

					$this->cb->where([$kolom_credit[$i] => $coa_kredit]);
					$this->cb->update($table_credit[$i], ['nominal' => $nominal_credit_baru[$i]]);


					// update table pengajuan detail
					$this->cb->where('Id', $id_item[$i]);
					$this->cb->update('t_po_detail', [
						'kredit' => $coa_kredit,
						'debit' => $coa_debit[$i]
					]);

					// create jurnal
					$jurnal = [
						'tanggal' => $date_bayar,
						'akun_debit' => $coa_debit[$i],
						'jumlah_debit' => $po_detail[$i]['total'],
						'akun_kredit' => $coa_kredit,
						'jumlah_kredit' => $po_detail[$i]['total'],
						'saldo_debit' => $nominal_debit_baru[$i],
						'saldo_kredit' => $nominal_credit_baru[$i],
						'keterangan' => $item[$i]['nama'],
						'created_by' => $this->session->userdata('nip'),
					];
					$this->cb->insert('jurnal_neraca', $jurnal);
				}

				if ($ppn == 1) {
					$nominal_ppn = $po['total'] * 0.11;
					$ppn_masukan = $this->cb->get_where('v_coa_all', ['no_sbb' => '1108007'])->row_array();
					$nominal_ppn_masukan = $ppn_masukan['nominal'];
					$nominal_ppn_masukan_baru = $nominal_ppn_masukan + $nominal_ppn;
					$this->cb->where('no_sbb', $ppn_masukan['no_sbb']);
					$this->cb->update('t_coa_sbb', ['nominal' => $nominal_ppn_masukan_baru]);

					$cr = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_kredit])->row_array();
					$posisi_cr = $cr['posisi'];
					$nominal_cr = $cr['nominal'];
					$substr_coa_cr = substr($coa_kredit, 0, 1);
					$nominal_cr_baru = 0;

					if ($posisi_cr == "AKTIVA") {
						$nominal_cr_baru = $nominal_cr - $nominal_ppn;
					}
					if ($posisi_cr == "PASIVA") {
						$nominal_cr_baru = $nominal_cr + $nominal_ppn;
					}

					if ($substr_coa_cr == "1" || $substr_coa_cr == "2" || $substr_coa_cr == "3") {
						$table_cr = "t_coa_sbb";
						$kolom_cr = "no_sbb";
					}

					if ($substr_coa_cr == "4" || $substr_coa_cr == "5" || $substr_coa_cr == "6" || $substr_coa_cr == "7" || $substr_coa_cr == "8" || $substr_coa_cr == "9") {
						$table_cr = "t_coalr_sbb";
						$kolom_cr = "no_lr_sbb";
					}

					$this->cb->where([$kolom_cr => $coa_kredit]);
					$this->cb->update($table_cr, ['nominal' => $nominal_cr_baru]);

					// create jurnal
					$jurnal = [
						'tanggal' => $date_bayar,
						'akun_debit' => $ppn_masukan['no_sbb'],
						'jumlah_debit' => $nominal_ppn,
						'akun_kredit' => $coa_kredit,
						'jumlah_kredit' => $nominal_ppn,
						'saldo_debit' => $nominal_ppn_masukan_baru,
						'saldo_kredit' => $nominal_cr_baru,
						'keterangan' => 'PPN MASUKAN ' . $po['no_po'],
						'created_by' => $this->session->userdata('nip'),
					];
					$this->cb->insert('jurnal_neraca', $jurnal);
				}

				// Update table pengajuan
				$update = [
					'user_proses' => $this->session->userdata('nip'),
					'posisi' => 'Hutang',
					'jenis_pembayaran' => $jenis_pembayaran,
					'date_proses' => $date_bayar,
					'ppn' => $ppn
				];

				$this->cb->where(['Id' => $id]);
				$this->cb->update('t_po', $update);

				$response = [
					'success' => true,
					'msg' => 'PO berhasil diproses!'
				];
			}
		}

		echo json_encode($response);
	}

	public function getDataCoa($id)
	{
		$data_coa = $this->cb->get_where('v_coa_all', ['no_sbb' => $id])->row_array();
		echo json_encode($data_coa);
	}

	public function release_order()
	{
		if ($this->session->userdata('isLogin') == FALSE) {
			redirect('home');
		} else {
			$a = $this->session->userdata('level');
			if (strpos($a, '501') !== false) {
				//inbox notif
				$nip = $this->session->userdata('nip');
				$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
				$query = $this->db->query($sql);
				$res1 = $query->result_array();
				$result = $res1[0]['COUNT(Id)'];
				$data['count_inbox'] = $result;

				$sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
				$query2 = $this->db->query($sql2);
				$res2 = $query2->result_array();
				$result2 = $res2[0]['COUNT(id)'];
				$data['count_inbox2'] = $result2;

				$this->db->where('stok >', 0);
				$data['item_list'] = $this->db->get('item_list');
				// $data['vendors'] = $this->db->get('t_vendors');
				$data['title'] = "Create Release Order";
				$data['pages'] = "pages/aset/v_ro";
				$this->load->view('index', $data);
			}
		}
	}

	public function getItemById()
	{
		$id = $this->input->post('id');
		$item = $this->db->get_where('item_list', ['Id' => $id])->row_array();
		$this->db->order_by('tanggal_masuk', 'DESC');
		$data = $this->db->get_where('item_detail', ['kode_item' => $id, 'status' => 'A'])->result();
		$option = "";
		if ($data) {
			foreach ($data as $row) {
				$option .= "<option value='$row->Id'>$row->serial_number</option>";
			}
		} else {
			$option .= "<option value='' selected>Tidak ada data detail</option>";
		}

		$response = [
			'option' => $option,
			'harga' => $item['harga_sat']
		];
		echo json_encode($response);
	}

	public function save_release_order()
	{
		$tanggal = $this->input->post('tanggal');
		$rows = $this->input->post('row[]');
		$item = $this->input->post('item[]');
		$qty = $this->input->post('qty_out[]');
		$asset = $this->input->post('asset[]');
		$price = $this->input->post('harga_out[]');
		$keterangan = $this->input->post('ket[]');
		$uoi = $this->input->post('uoi_out[]');
		// $detail_item = $this->input->post('detail_item[]');
		$sub_total = $this->input->post('total_out[]');
		$total = $this->input->post('nominal');
		$now = date('Y-m-d');
		$teknisi = $this->input->post('teknisi');
		if (strtotime($tanggal) != strtotime($now)) {
			$tgl = $tanggal;
		} else {
			$tgl = date('Y-m-d H:i:s');
		}

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('teknisi', 'nama teknisi', 'required|trim', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('asset[]', 'asset', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('item[]', 'item', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('qty_out[]', 'qty', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('harga_out[]', 'harga', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('uoi_out[]', 'uoi', 'required', ['required' => '%s wajib diisi!']);
		// for ($i = 0; $i < count($rows); $i++) {
		// 	$detail[] = $this->db->get_where('item_detail', ['kode_item' => $item[$i], 'status' => 'A']);
		// 	if ($detail[$i]->num_rows() > 0) {
		// 		$this->form_validation->set_rules('detail_item[]', 'detail item', 'required', ['required' => '%s wajib diisi!']);
		// 	}
		// }

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			$sql = "SELECT count(a.Id) as jml FROM t_ro as a WHERE YEAR(tgl_pengajuan) = YEAR(curdate())";
			$result = $this->cb->query($sql);

			if ($result->num_rows() > 0) {
				$res = $result->row_array();
				$nomor = $res['jml'] + 1;
			} else {
				$nomor = 1;
			}

			$array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
			$bln = $array_bln[date('n')];
			$no_ro = sprintf("%06d", $nomor) . '-OUT';
			$ref = "RO-" . sprintf("%06d", $nomor) . '/' . $bln . '/OUT' . '/' . date('y');

			$insert = [
				'tgl_pengajuan' => $tgl,
				'user' => $this->session->userdata('nip'),
				'referensi' => $ref,
				'no_ro' => $no_ro,
				'total' => str_replace('.', '', $total),
				'posisi' => 'Diajukan kepada sarlog',
				'teknisi' => $teknisi
			];

			$this->cb->insert('t_ro', $insert);
			$last_id = $this->cb->insert_id();

			for ($i = 0; $i < count($rows); $i++) {
				// $this->db->select('Id, kode_item, serial_number');
				// $item_detail[] = $this->db->get_where('item_detail', ['kode_item' => $item[$i]])->result_array();
				$item_list[] = $this->db->get_where('item_list', ['Id' => $item[$i]])->row_array();

				if ($qty[$i] < 1) {
					$response = [
						'success' => false,
						'msg' => 'Stok item ' . $item_list[$i]['nama'] . ' tidak boleh kosong'
					];

					echo json_encode($response);
					return false;
				}

				if ($qty[$i] > $item_list[$i]['stok']) {
					$response = [
						'success' => false,
						'msg' => 'Stok item ' . $item_list[$i]['nama'] . ' kurang'
					];

					echo json_encode($response);
					return false;
				}

				$insert_detail = [
					'no_ro' => $last_id,
					'item' => $item[$i],
					'asset' => $asset[$i],
					'qty' => str_replace('.', '', $qty[$i]),
					'uoi' => $uoi[$i],
					'price' => str_replace('.', '', $price[$i]),
					'total' => str_replace('.', '', $sub_total[$i]),
					'keterangan' => $keterangan[$i]
				];

				$this->cb->insert('t_ro_detail', $insert_detail);
			}

			$this->db->select('phone');
			$this->db->where(['level_jabatan' => 2, 'bagian' => 2]);
			$sarlog = $this->db->get('users')->result_array();
			$phone = '';
			foreach ($sarlog as $val) {
				$phone .= $val['phone'] . ',';
			}

			$nama_session = $this->session->userdata('nama');
			$msg = "There's a new Release Order\nNo : *$no_ro*\nFrom : *$nama_session*\n\nMohon untuk segera diproses.";
			$this->api_whatsapp->wa_notif($msg, $phone);

			$response = [
				'success' => true,
				'msg' => 'Release Order berhasil diajukan!'
			];
		}
		echo json_encode($response);
	}

	public function ro_list()
	{
		$nip = $this->session->userdata('nip');
		// Pagination
		$keyword = htmlspecialchars($this->input->get('keyword') ?? '', ENT_QUOTES, 'UTF-8');
		$config['base_url'] = base_url('asset/ro_list');
		$config['total_rows'] = $this->m_asset->count_ro($keyword, ['a.user' => $nip]);
		$config['per_page'] = 10;
		$config['uri_segment'] = 3;
		$config['num_links'] = 3;
		$config['enable_query_strings'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['use_page_numbers'] = TRUE;
		$config['reuse_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';

		// Bootstrap style pagination
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['prev_link'] = '«';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '»';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		// Initialize paginaton
		$this->pagination->initialize($config);
		$page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
		$data['page'] = $page;
		$data['pagination'] = $this->pagination->create_links();
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['count_sarlog'] = $this->m_asset->count_ro(null, ['a.status_sarlog' => 0]);
		$data['count_dirops'] = $this->m_asset->count_ro(null, ['a.status_sarlog' => 1, 'a.direksi_ops' => $this->session->userdata('nip'), 'a.status_direksi_ops' => 0]);
		$data['ro'] = $this->m_asset->get_roList($config['per_page'], $page, $keyword, ['a.user' => $nip]);
		$data['title'] = "List Release Order";
		$data['pages'] = "pages/aset/v_ro_list";
		$this->load->view('index', $data);
	}

	public function sarlog_out()
	{
		// Pagination
		$keyword = htmlspecialchars($this->input->get('keyword') ?? '', ENT_QUOTES, 'UTF-8');
		$config['base_url'] = base_url('asset/sarlog_out');
		$config['total_rows'] = $this->m_asset->count_ro($keyword, ['user !=' => $this->session->userdata('nip')]);
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;
		$config['num_links'] = 3;
		$config['enable_query_strings'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['use_page_numbers'] = TRUE;
		$config['reuse_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';

		// Bootstrap style pagination
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['prev_link'] = '«';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '»';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		// Initialize paginaton
		$this->pagination->initialize($config);
		$page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
		$data['page'] = $page;
		$data['pagination'] = $this->pagination->create_links();
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['ro'] = $this->m_asset->get_roList($config['per_page'], $page, $keyword, ['user !=' => $this->session->userdata('nip')]);
		$data['direksi'] = $this->db->get_where('users', ['level_jabatan >' => 4])->result_array();
		$data['title'] = "Release Order";
		$data['pages'] = "pages/aset/v_sarlog_out";
		$this->load->view('index', $data);
	}

	public function update_sarlog_out()
	{
		$id = $this->input->post('id_po');
		$tgl = $this->input->post('tanggal');
		$status = $this->input->post('status');
		$catatan = $this->input->post('catatan');

		$now = date('Y-m-d');
		if (strtotime($tgl) != strtotime($now)) {
			$tgl = $tgl;
		} else {
			$tgl = date('Y-m-d H:i:s');
		}

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('status', 'status', 'required|in_list[1,2]', ['required' => '%s wajib diisi!']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			if ($status == 1) {
				$posisi = 'diajukan kepada direktur operasional';
				$direksi_ops = 'SLS0004';
			} else {
				$posisi = 'ditolak sarlog';
				$direksi_ops = null;
			}

			$update = [
				'status_sarlog' => $status,
				'sarlog' => $this->session->userdata('nip'),
				'posisi' => $posisi,
				'date_sarlog' => $tgl,
				'catatan_sarlog' => $catatan,
				'direksi_ops' => $direksi_ops
			];

			$this->cb->where('Id', $id);
			$this->cb->update('t_ro', $update);

			$ro = $this->cb->get_where('t_ro', ['Id' => $id])->row_array();
			$user = $this->db->get_where('users', ['nip' => $ro['user']])->row_array();

			$this->db->like('nama_jabatan', 'Direktur Utama', 'both');
			$this->db->or_like('nama_jabatan', 'Direktur Operasional', 'both');
			$direksi = $this->db->get('users')->result_array();

			$nama_session = $this->session->userdata('nama');

			if ($status == 1) {
				$msg = "Pemberitahuan Release Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$ro[no_ro]* sudah disetujui oleh *$nama_session* sebagai penanggung jawab logistik.\nSelanjutnya pengajuan anda akan diajukan kepada Direktur Operasional.\n\n*Catatan* : $catatan";

				$msgdireksi = "There's a new Release Order\n\nNo : *$ro[no_ro]*\nFrom : *$user[nama]*\n\nMohon untuk segera diproses.";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);

				$phone = '';
				foreach ($direksi as $val) {
					$phone .= $val['phone'] . ',';
				}
				$this->api_whatsapp->wa_notif($msgdireksi, $phone);
			}

			if ($status == 2) {
				$msg = "Pemberitahuan Release Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$ro[no_ro]* ditolak oleh *$nama_session* sebagai penanggung jawab logistik.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);
			}

			$response = [
				'success' => true,
				'msg' => 'Status Release Order Telah Diubah!'
			];
		}

		echo json_encode($response);
	}

	public function direksi_ops_out()
	{
		$nip = $this->session->userdata('nip');
		// Pagination
		$keyword = htmlspecialchars($this->input->get('keyword') ?? '', ENT_QUOTES, 'UTF-8');
		$config['base_url'] = base_url('asset/direksi_ops_out');
		$config['total_rows'] = $this->m_asset->count_ro($keyword, ['status_sarlog' => 1, 'direksi_ops' => $nip]);
		$config['per_page'] = 10;
		$config['uri_segment'] = 3;
		$config['num_links'] = 3;
		$config['enable_query_strings'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['use_page_numbers'] = TRUE;
		$config['reuse_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';

		// Bootstrap style pagination
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['prev_link'] = '«';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '»';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		// Initialize paginaton
		$this->pagination->initialize($config);
		$page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
		$data['page'] = $page;
		$data['pagination'] = $this->pagination->create_links();
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['ro'] = $this->m_asset->get_roList($config['per_page'], $page, $keyword, ['status_sarlog' => 1, 'direksi_ops' => $nip]);
		$data['direksi'] = $this->db->get_where('users', ['level_jabatan >' => 4])->result_array();
		$data['title'] = "List PO Item Out Direksi";
		$data['pages'] = "pages/aset/v_direksi_ops_out";
		$this->load->view('index', $data);
	}

	public function update_direksi_ops_out()
	{
		$id = $this->input->post('id_po');
		$tgl = $this->input->post('tanggal');
		$status = $this->input->post('status');
		$catatan = $this->input->post('catatan');

		$now = date('Y-m-d');
		if (strtotime($tgl) != strtotime($now)) {
			$tgl = $tgl;
		} else {
			$tgl = date('Y-m-d H:i:s');
		}

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('status', 'status', 'required|in_list[1,2]', ['required' => '%s wajib diisi!']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			if ($status == 1) {
				$posisi = 'Disetujui Direktur Operasional';
			} else {
				$posisi = 'Ditolak Direktur Operasional';
			}
			$update = [
				'status_direksi_ops' => $status,
				'posisi' => $posisi,
				'date_direksi_ops' => $tgl,
				'catatan_direksi_ops' => $catatan,
			];

			$this->cb->where('Id', $id);
			$this->cb->update('t_ro', $update);

			$ro = $this->cb->get_where('t_ro', ['Id' => $id])->row_array();
			$user = $this->db->get_where('users', ['nip' => $ro['user']])->row_array();
			$sarlog = $this->db->get_where('users', ['nip' => $ro['sarlog']])->row_array();
			$nama_session = $this->session->userdata('nama');

			if ($status == 1) {
				$msg = "Pemberitahuan Release Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$ro[no_ro]* sudah disetujui oleh *$nama_session* sebagai Direktur Operasional.\nBarang akan segera diserahkan oleh penanggung jawab logistik.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);

				$msgsarlog = "Pemberitahuan Release Order\n\nHallo *$sarlog[nama]*, Release Order dengan No. *$ro[no_ro]* sudah disetujui oleh *$nama_session* sebagai Direktur Operasional.\nMohon untuk segera diproses lebih lanjut.\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msgsarlog, $sarlog['phone']);
			}

			if ($status == 2) {
				$msg = "Pemberitahuan Release Order\n\nHallo *$user[nama]*, Pengajuan anda dengan No. *$ro[no_ro]* ditolak oleh *$nama_session* sebagai Direktur Operasional.\n\n*Catatan* : $catatan";
				$this->api_whatsapp->wa_notif($msg, $user['phone']);
			}

			$response = [
				'success' => true,
				'msg' => 'Status Release Order Berhasil Diubah!'
			];
		}

		echo json_encode($response);
	}

	public function dirut_out()
	{
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['po'] = $this->m_asset->get_poOutList(['status_sarlog' => 1, 'status_direksi_ops' => 1, 'dirut' => $this->session->userdata('nip')]);
		$data['title'] = "List PO Item Out Direktur Utama";
		$data['pages'] = "pages/aset/v_dirut_out";
		$this->load->view('index', $data);
	}

	// public function update_dirut_out()
	// {
	// 	$id = $this->input->post('id_po');
	// 	$tgl = $this->input->post('tanggal');
	// 	$status = $this->input->post('status');
	// 	$catatan = $this->input->post('catatan');

	// 	$now = date('Y-m-d');
	// 	if (strtotime($tgl) != strtotime($now)) {
	// 		$tgl = $tgl;
	// 	} else {
	// 		$tgl = date('Y-m-d H:i:s');
	// 	}

	// 	$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
	// 	$this->form_validation->set_rules('status', 'status', 'required|in_list[1,2]', ['required' => '%s wajib diisi!']);

	// 	if ($this->form_validation->run() == FALSE) {
	// 		$response = [
	// 			'success' => false,
	// 			'msg' => array_values($this->form_validation->error_array())[0]
	// 		];
	// 	} else {
	// 		if ($status == 1) {
	// 			$posisi = 'Disetujui Direktur Utama';
	// 		} else {
	// 			$posisi = 'Ditolak Direktur Utama';
	// 		}
	// 		$update = [
	// 			'status_dirut' => $status,
	// 			'posisi' => $posisi,
	// 			'date_dirut' => $tgl,
	// 			'catatan_dirut' => $catatan,
	// 		];

	// 		$this->cb->where('Id', $id);
	// 		$this->cb->update('t_po_out', $update);

	// 		$response = [
	// 			'success' => true,
	// 			'msg' => 'Status PO berhasil diubah!'
	// 		];
	// 	}

	// 	echo json_encode($response);
	// }

	public function serah_item($id)
	{
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['coa'] = $this->cb->get('v_coa_all');
		$data['ro'] = $this->m_asset->get_roList(null, null, null, ['a.Id' => $id])->row_array();
		$data['title'] = "Serahkan Item";
		$data['pages'] = "pages/aset/v_serah_item";
		$this->load->view('index', $data);
	}

	public function update_serahItem()
	{
		$id = $this->input->post('id_po');
		$coa_beban = $this->input->post('coa_beban[]');
		$coa_persediaan = $this->input->post('coa_persediaan[]');
		$detail_item = $this->input->post('detail_item[]');
		$id_item = $this->input->post('id_item[]');
		$rows = $this->input->post('row_item[]');
		$tgl = $this->input->post('tanggal');
		$now = date('Y-m-d');

		if (strtotime($tgl) != strtotime($now)) {
			$date_serah = $tgl;
		} else {
			$date_serah = date('Y-m-d H:i:s');
		}

		$ro = $this->cb->get_where('t_ro', ['Id' => $id])->row_array();

		for ($i = 0; $i < count($rows); $i++) {
			$this->form_validation->set_rules('detail_item[' . $i . ']', 'detail item', 'required', ['required' => '%s wajib diisi!']);
		}
		// $this->form_validation->set_rules('detail_item[]', 'detail item', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('coa_persediaan[]', 'coa persediaan', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('coa_beban[]', 'coa beban', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			for ($i = 0; $i < count($rows); $i++) {
				$item[] = $this->cb->get_where('t_ro_detail', ['Id' => $id_item[$i]])->row_array();
				$item_list[] = $this->db->get_where('item_list', ['Id' => $item[$i]['item']])->row_array();
				$stok_awal[] = $item_list[$i]['stok'];
				$stok_akhir[] = $stok_awal[$i] - $item[$i]['qty'];

				if ($stok_akhir[$i] < 0) {
					$response = [
						'success' => false,
						'msg' => 'Stok ' . $item_list[$i]['nama'] . ' Tidak Tersedia Lagi'
					];

					echo json_encode($response);
					return false;
				}

				// update stok item list
				$this->db->where('Id', $item[$i]['item']);
				$this->db->update('item_list', ['stok' => $stok_akhir[$i]]);

				// update statu item detail 
				$this->db->where_in('Id', $detail_item[$i]);
				$this->db->update('item_detail', ['status' => 'O']);

				// debit
				$detail_coa_beban[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_beban[$i]])->row_array();
				$posisi_beban[] = $detail_coa_beban[$i]['posisi'];
				$nominal_beban[] = $detail_coa_beban[$i]['nominal'];
				$substr_coa_beban[] = substr($coa_beban[$i], 0, 1);
				$nominal_beban_baru[] = 0;

				if ($posisi_beban[$i] == "AKTIVA") {
					$nominal_beban_baru[$i] = $nominal_beban[$i] + $item[$i]['total'];
				}

				if ($posisi_beban[$i] == "PASIVA") {
					$nominal_beban_baru[$i] = $nominal_beban[$i] - $item[$i]['total'];
				}

				if ($substr_coa_beban[$i] == "1" || $substr_coa_beban[$i] == "2" || $substr_coa_beban[$i] == "3") {
					$table_beban[] = "t_coa_sbb";
					$kolom_beban[] = "no_sbb";
				}
				if ($substr_coa_beban[$i] == "4" || $substr_coa_beban[$i] == "5" || $substr_coa_beban[$i] == "6" || $substr_coa_beban[$i] == "7" || $substr_coa_beban[$i] == "8" || $substr_coa_beban[$i] == "9") {
					$table_beban[] = "t_coalr_sbb";
					$kolom_beban[] = "no_lr_sbb";
				}

				$this->cb->where([$kolom_beban[$i] => $coa_beban[$i]]);
				$this->cb->update($table_beban[$i], ['nominal' => $nominal_beban_baru[$i]]);


				// update coa credit
				$detail_coa_persediaan[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_persediaan[$i]])->row_array();
				$posisi_persediaan[] = $detail_coa_persediaan[$i]['posisi'];
				$nominal_persediaan[] = $detail_coa_persediaan[$i]['nominal'];
				$substr_coa_persediaan[] = substr($coa_persediaan[$i], 0, 1);
				$saldo_persediaan_baru[] = 0;
				$nominal_persediaan_baru[] = 0;

				if ($posisi_persediaan[$i] == "AKTIVA") {
					$nominal_persediaan_baru[$i] = $nominal_persediaan[$i] - $item[$i]['total'];
				}
				if ($posisi_persediaan[$i] == "PASIVA") {
					$nominal_persediaan_baru[$i] = $nominal_persediaan[$i] + $item[$i]['total'];
				}

				if ($substr_coa_persediaan[$i] == "1" || $substr_coa_persediaan[$i] == "2" || $substr_coa_persediaan[$i] == "3") {
					$table_persediaan[] = "t_coa_sbb";
					$kolom_persediaan[] = "no_sbb";
				}
				if ($substr_coa_persediaan[$i] == "4" || $substr_coa_persediaan[$i] == "5" || $substr_coa_persediaan[$i] == "6" || $substr_coa_persediaan[$i] == "7" || $substr_coa_persediaan[$i] == "8" || $substr_coa_persediaan[$i] == "9") {
					$table_persediaan[] = "t_coalr_sbb";
					$kolom_persediaan[] = "no_lr_sbb";
				}

				$this->cb->where([$kolom_persediaan[$i] => $coa_persediaan[$i]]);
				$this->cb->update($table_persediaan[$i], ['nominal' => $nominal_persediaan_baru[$i]]);


				// update table pengajuan detail
				$this->cb->where('Id', $id_item[$i]);
				$this->cb->update('t_ro_detail', [
					'persediaan' => $coa_persediaan[$i],
					'beban' => $coa_beban[$i],
					'detail' => json_encode($detail_item[$i])
				]);

				// create jurnal
				$jurnal = [
					'tanggal' => $date_serah,
					'akun_debit' => $coa_beban[$i],
					'jumlah_debit' => $item[$i]['total'],
					'akun_kredit' => $coa_persediaan[$i],
					'jumlah_kredit' => $item[$i]['total'],
					'saldo_debit' => $nominal_beban_baru[$i],
					'saldo_kredit' => $nominal_persediaan_baru[$i],
					'keterangan' => $item_list[$i]['nama'],
					'created_by' => $this->session->userdata('nip'),
				];

				$this->cb->insert('jurnal_neraca', $jurnal);

				// create item out
				$item_out = [
					'no_po' => $ro['Id'],
					'item_id' => $item[$i]['item'],
					'asset_id' => $item[$i]['asset'],
					'harga' => $item[$i]['price'],
					'jml' => $item[$i]['qty'],
					'status' => 1,
					'tanggal' => $date_serah,
					'user' => $ro['user'],
					'user_serah' => $this->session->userdata('nip'),
					'penerima' => $ro['teknisi'],
					'date_serah' => $date_serah,
					'stok_awal' => $stok_awal[$i],
					'stok_akhir' => $stok_akhir[$i],
					'jenis' => 'OUT',
					'keterangan' => $ro['teknisi'],
					'serial_number' => json_encode($detail_item[$i])
				];
				$this->db->insert('working_supply', $item_out);
			}

			// Update table pengajuan
			$update = [
				'posisi' => 'Barang sudah diserahkan!',
				'date_serah' => $date_serah,
				'user_serah' => $this->session->userdata('nip'),
			];

			$this->cb->where(['Id' => $id]);
			$this->cb->update('t_ro', $update);

			$response = [
				'success' => true,
				'msg' => 'Barang berhasil diserahkan!'
			];
			// }
		}
		echo json_encode($response);
	}

	public function bayar($id)
	{
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['coa'] = $this->cb->get('v_coa_all');
		$data['po'] = $this->m_asset->get_poList(null, null, null, ['a.Id' => $id], null)->row_array();
		$data['title'] = "Form Bayar";
		$data['pages'] = "pages/aset/v_bayar";
		$this->load->view('index', $data);
	}

	public function update_bayar()
	{
		$id = $this->input->post('id_po');
		$coa_kas = $this->input->post('coa-kas');
		$id_item = $this->input->post('id_item[]');
		$rows = $this->input->post('row_item[]');
		$tgl = $this->input->post('tanggal');
		$now = date('Y-m-d');
		$jenis_pembayaran = $this->input->post('jenis-pembayaran');

		$po = $this->cb->get_where("t_po", ['Id' => $id])->row_array();

		if (strtotime($tgl) != strtotime($now)) {
			$date_bayar = $tgl;
		} else {
			$date_bayar = date('Y-m-d H:i:s');
		}

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('coa-kas', 'coa kas', 'required', ['required' => '%s wajib diisi!']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			$config['upload_path']          = './upload/po';
			$config['allowed_types']        = 'jpg|jpeg|png|pdf';
			$config['encrypt_name']         = TRUE;
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('bukti-bayar')) {
				$response = [
					'success' => false,
					'msg' => $this->upload->display_errors()
				];
			} else {
				$upload = $this->upload->data();
				$file = $_FILES['bukti-bayar']['name'];
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				if ($ext != 'pdf') {
					$config2 = [
						'image_library' => 'gd2',
						'source_image' => './upload/po/' . $upload['file_name'],
						'create_thumb' => false,
						'maintain_ratio' => false,
						'quality' => '75%',
						'width' => '100%',
						'heigth' => '100%'
					];

					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();
				}

				for ($i = 0; $i < count($rows); $i++) {
					$po_detail[] = $this->cb->get_where('t_po_detail', ['Id' => $id_item[$i]])->row_array();
					$item[] = $this->db->get_where('item_list', ['Id' => $po_detail[$i]['item']])->row_array();
					// Update coa debit
					$detail_coa_hutang[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $po_detail[$i]['kredit']])->row_array();
					$posisi_hutang[] = $detail_coa_hutang[$i]['posisi'];
					$nominal_hutang[] = $detail_coa_hutang[$i]['nominal'];
					$substr_coa_hutang[] = substr($po_detail[$i]['kredit'], 0, 1);
					$nominal_hutang_baru[] = 0;

					if ($posisi_hutang[$i] == "AKTIVA") {
						$nominal_hutang_baru[$i] = $nominal_hutang[$i] + $po_detail[$i]['total'];
					}

					if ($posisi_hutang[$i] == "PASIVA") {
						$nominal_hutang_baru[$i] = $nominal_hutang[$i] - $po_detail[$i]['total'];
					}

					if ($substr_coa_hutang[$i] == "1" || $substr_coa_hutang[$i] == "2" || $substr_coa_hutang[$i] == "3") {
						$table_hutang[] = "t_coa_sbb";
						$kolom_hutang[] = "no_sbb";
					}
					if ($substr_coa_hutang[$i] == "4" || $substr_coa_hutang[$i] == "5" || $substr_coa_hutang[$i] == "6" || $substr_coa_hutang[$i] == "7" || $substr_coa_hutang[$i] == "8" || $substr_coa_hutang[$i] == "9") {
						$table_hutang[] = "t_coalr_sbb";
						$kolom_hutang[] = "no_lr_sbb";
					}

					$this->cb->where([$kolom_hutang[$i] => $po_detail[$i]['kredit']]);
					$this->cb->update($table_hutang[$i], ['nominal' => $nominal_hutang_baru[$i]]);


					// update coa credit
					$detail_coa_kas[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_kas])->row_array();
					$posisi_kas[] = $detail_coa_kas[$i]['posisi'];
					$nominal_kas[] = $detail_coa_kas[$i]['nominal'];
					$substr_coa_kas[] = substr($coa_kas, 0, 1);
					$saldo_kas_baru[] = 0;
					$nominal_kas_baru[] = 0;

					if ($posisi_kas[$i] == "AKTIVA") {
						$nominal_kas_baru[$i] = $nominal_kas[$i] - $po_detail[$i]['total'];
					}
					if ($posisi_kas[$i] == "PASIVA") {
						$nominal_kas_baru[$i] = $nominal_kas[$i] + $po_detail[$i]['total'];
					}

					if ($substr_coa_kas[$i] == "1" || $substr_coa_kas[$i] == "2" || $substr_coa_kas[$i] == "3") {
						$table_kas[] = "t_coa_sbb";
						$kolom_kas[] = "no_sbb";
					}
					if ($substr_coa_kas[$i] == "4" || $substr_coa_kas[$i] == "5" || $substr_coa_kas[$i] == "6" || $substr_coa_kas[$i] == "7" || $substr_coa_kas[$i] == "8" || $substr_coa_kas[$i] == "9") {
						$table_kas[] = "t_coalr_sbb";
						$kolom_kas[] = "no_lr_sbb";
					}

					$this->cb->where([$kolom_kas[$i] => $coa_kas]);
					$this->cb->update($table_kas[$i], ['nominal' => $nominal_kas_baru[$i]]);


					// update table pengajuan detail
					$this->cb->where('Id', $id_item[$i]);
					$this->cb->update('t_po_detail', [
						'kas' => $coa_kas,
					]);

					// create jurnal
					$jurnal = [
						'tanggal' => $date_bayar,
						'akun_debit' => $po_detail[$i]['kredit'],
						'jumlah_debit' => $po_detail[$i]['total'],
						'akun_kredit' => $coa_kas,
						'jumlah_kredit' => $po_detail[$i]['total'],
						'saldo_debit' => $nominal_hutang_baru[$i],
						'saldo_kredit' => $nominal_kas_baru[$i],
						'keterangan' => $item[$i]['nama'],
						'created_by' => $this->session->userdata('nip'),
					];
					$this->cb->insert('jurnal_neraca', $jurnal);
				}

				if ($po['ppn'] == 1) {
					$nominal_ppn = $po['total'] * 0.11;
					$po_detail = $this->cb->get_where('t_po_detail', ['no_po' => $po['Id']])->row_array();

					$coa_utang = $this->cb->get_where('v_coa_all', ['no_sbb' => $po_detail['kredit']])->row_array();
					$posisi_coa_utang = $coa_utang['posisi'];
					$nominal_coa_utang = $coa_utang['nominal'];
					$substr_coa_utang = substr($po_detail['kredit'], 0, 1);
					$nominal_coa_utang_baru = 0;

					if ($posisi_coa_utang == "AKTIVA") {
						$nominal_coa_utang_baru = $nominal_coa_utang + $nominal_ppn;
					}
					if ($posisi_coa_utang == "PASIVA") {
						$nominal_coa_utang_baru = $nominal_coa_utang - $nominal_ppn;
					}

					if ($substr_coa_utang == "1" || $substr_coa_utang == "2" || $substr_coa_utang == "3") {
						$table_utang = "t_coa_sbb";
						$kolom_utang = "no_sbb";
					}

					if ($substr_coa_utang == "4" || $substr_coa_utang == "5" || $substr_coa_utang == "6" || $substr_coa_utang == "7" || $substr_coa_utang == "8" || $substr_coa_utang == "9") {
						$table_utang = "t_coalr_sbb";
						$kolom_utang = "no_lr_sbb";
					}

					$this->cb->where([$kolom_utang => $po_detail['kredit']]);
					$this->cb->update($table_utang, ['nominal' => $nominal_coa_utang_baru]);

					$cr = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_kas])->row_array();
					$posisi_cr = $cr['posisi'];
					$nominal_cr = $cr['nominal'];
					$substr_coa_cr = substr($coa_kas, 0, 1);
					$nominal_cr_baru = 0;

					if ($posisi_cr == "AKTIVA") {
						$nominal_cr_baru = $nominal_cr - $nominal_ppn;
					}
					if ($posisi_cr == "PASIVA") {
						$nominal_cr_baru = $nominal_cr + $nominal_ppn;
					}

					if ($substr_coa_cr == "1" || $substr_coa_cr == "2" || $substr_coa_cr == "3") {
						$table_cr = "t_coa_sbb";
						$kolom_cr = "no_sbb";
					}

					if ($substr_coa_cr == "4" || $substr_coa_cr == "5" || $substr_coa_cr == "6" || $substr_coa_cr == "7" || $substr_coa_cr == "8" || $substr_coa_cr == "9") {
						$table_cr = "t_coalr_sbb";
						$kolom_cr = "no_lr_sbb";
					}

					$this->cb->where([$kolom_cr => $coa_kas]);
					$this->cb->update($table_cr, ['nominal' => $nominal_cr_baru]);

					// create jurnal
					$jurnal = [
						'tanggal' => $date_bayar,
						'akun_debit' => $po_detail['kredit'],
						'jumlah_debit' => $nominal_ppn,
						'akun_kredit' => $coa_kas,
						'jumlah_kredit' => $nominal_ppn,
						'saldo_debit' => $nominal_coa_utang_baru,
						'saldo_kredit' => $nominal_cr_baru,
						'keterangan' => 'PPN MASUKAN ' . $po['no_po'],
						'created_by' => $this->session->userdata('nip'),
					];
					$this->cb->insert('jurnal_neraca', $jurnal);
				}

				// Update table pengajuan
				$update = [
					'user_bayar' => $this->session->userdata('nip'),
					'posisi' => 'Sudah Dibayar',
					'date_bayar' => $date_bayar,
					'bukti_bayar' => $upload['file_name'],
					'status_pembayaran' => 1
				];

				$this->cb->where(['Id' => $id]);
				$this->cb->update('t_po', $update);

				$response = [
					'success' => true,
					'msg' => 'PO berhasil dibayar!'
				];
			}
		}
		echo json_encode($response);
	}

	public function batchBayar()
	{
		$tanggal = $this->input->post('tanggal_batch');
		$po_hutang = $this->input->post('po_hutang[]');
		$coa_kas = $this->input->post('coa-kas-batch');
		$now = date('Y-m-d');

		if (strtotime($tanggal) != strtotime($now)) {
			$date_bayar = $tanggal;
		} else {
			$date_bayar = date('Y-m-d H:i:s');
		}

		$this->form_validation->set_rules('tanggal_batch', 'Tanggal', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('po_hutang[]', 'No. PO', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('coa-kas-batch', 'COA', 'required', ['required' => '%s wajib diisi!']);
		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			for ($i = 0; $i < count($po_hutang); $i++) {
				$po[$i][] = $this->cb->get_where('t_po', ['Id' => $po_hutang[$i]])->row_array();
				$po_detail[$i][] = $this->cb->get_where('t_po_detail', ['no_po' => $po_hutang[$i]])->row_array();
				for ($j = 0; $j < count($po_detail[$i]); $j++) {
					$item[] = $this->db->get_where('item_list', ['Id' => $po_detail[$i][$j]['item']])->row_array();
					// Update coa debit
					$detail_coa_hutang[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $po_detail[$i][$j]['kredit']])->row_array();
					$posisi_hutang[] = $detail_coa_hutang[$j]['posisi'];
					$nominal_hutang[] = $detail_coa_hutang[$j]['nominal'];
					$substr_coa_hutang[] = substr($po_detail[$i][$j]['kredit'], 0, 1);
					$nominal_hutang_baru[] = 0;

					if ($posisi_hutang[$j] == "AKTIVA") {
						$nominal_hutang_baru[$j] = $nominal_hutang[$j] + $po_detail[$i][$j]['total'];
					}

					if ($posisi_hutang[$j] == "PASIVA") {
						$nominal_hutang_baru[$j] = $nominal_hutang[$j] - $po_detail[$i][$j]['total'];
					}

					if ($substr_coa_hutang[$j] == "1" || $substr_coa_hutang[$j] == "2" || $substr_coa_hutang[$j] == "3") {
						$table_hutang[] = "t_coa_sbb";
						$kolom_hutang[] = "no_sbb";
					}
					if ($substr_coa_hutang[$j] == "4" || $substr_coa_hutang[$j] == "5" || $substr_coa_hutang[$j] == "6" || $substr_coa_hutang[$j] == "7" || $substr_coa_hutang[$j] == "8" || $substr_coa_hutang[$j] == "9") {
						$table_hutang[] = "t_coalr_sbb";
						$kolom_hutang[] = "no_lr_sbb";
					}

					// $this->cb->where([$kolom_hutang[$j] => $po_detail[$i][$j]['kredit']]);
					// $this->cb->update($table_hutang[$j], ['nominal' => $nominal_hutang_baru[$j]]);

					// update coa credit
					$detail_coa_kas[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_kas])->row_array();
					$posisi_kas[] = $detail_coa_kas[$j]['posisi'];
					$nominal_kas[] = $detail_coa_kas[$j]['nominal'];
					$substr_coa_kas[] = substr($coa_kas, 0, 1);
					$saldo_kas_baru[] = 0;
					$nominal_kas_baru[] = 0;

					if ($posisi_kas[$j] == "AKTIVA") {
						$nominal_kas_baru[$j] = $nominal_kas[$j] - $po_detail[$i][$j]['total'];
					}
					if ($posisi_kas[$j] == "PASIVA") {
						$nominal_kas_baru[$j] = $nominal_kas[$j] + $po_detail[$i][$j]['total'];
					}

					if ($substr_coa_kas[$j] == "1" || $substr_coa_kas[$j] == "2" || $substr_coa_kas[$j] == "3") {
						$table_kas[] = "t_coa_sbb";
						$kolom_kas[] = "no_sbb";
					}
					if ($substr_coa_kas[$j] == "4" || $substr_coa_kas[$j] == "5" || $substr_coa_kas[$j] == "6" || $substr_coa_kas[$j] == "7" || $substr_coa_kas[$j] == "8" || $substr_coa_kas[$j] == "9") {
						$table_kas[] = "t_coalr_sbb";
						$kolom_kas[] = "no_lr_sbb";
					}

					// $this->cb->where([$kolom_kas[$j] => $coa_kas]);
					// $this->cb->update($table_kas[$j], ['nominal' => $nominal_kas_baru[$j]]);


					// update table pengajuan detail
					// $this->cb->where('Id', $po_detail[$i][$j]['Id']);
					// $this->cb->update('t_po_detail', [
					// 	'kas' => $coa_kas,
					// ]);

					// create jurnal
					$jurnal = [
						'tanggal' => $date_bayar,
						'akun_debit' => $po_detail[$i][$j]['kredit'],
						'jumlah_debit' => $po_detail[$i][$j]['total'],
						'akun_kredit' => $coa_kas,
						'jumlah_kredit' => $po_detail[$i][$j]['total'],
						'saldo_debit' => $nominal_hutang_baru[$j],
						'saldo_kredit' => $nominal_kas_baru[$j],
						'keterangan' => $item[$j]['nama'],
						'created_by' => $this->session->userdata('nip'),
					];
					// $this->cb->insert('jurnal_neraca', $jurnal);
				}

				if ($po[$i][0]['ppn'] == 1) {
					$nominal_ppn[$i][] = $po[$i][0]['total'] * 0.11;
					$po_det[$i][] = $this->cb->get_where('t_po_detail', ['no_po' => $po[$i][0]['Id']])->row_array();

					foreach ($po_det[$i] as $k => $row) {
						$coa_utang[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $row['kredit']])->row_array();
						$posisi_coa_utang[] = $coa_utang[$k]['posisi'];
						$nominal_coa_utang[] = $coa_utang[$k]['nominal'];
						$substr_coa_utang[] = substr($row['kredit'], 0, 1);
						$nominal_coa_utang_baru[] = 0;


						if ($posisi_coa_utang[$k] == "AKTIVA") {
							$nominal_coa_utang_baru[$k] = $nominal_coa_utang[$k] + $nominal_ppn[$k][0];
						}

						if ($posisi_coa_utang[$k] == "PASIVA") {
							$nominal_coa_utang_baru[$k] = $nominal_coa_utang[$k] - $nominal_ppn[$k][0];
						}

						if ($substr_coa_utang[$k] == "1" || $substr_coa_utang[$k] == "2" || $substr_coa_utang[$k] == "3") {
							$table_utang[] = "t_coa_sbb";
							$kolom_utang[] = "no_sbb";
						}

						if ($substr_coa_utang[$k] == "4" || $substr_coa_utang[$k] == "5" || $substr_coa_utang[$k] == "6" || $substr_coa_utang[$k] == "7" || $substr_coa_utang[$k] == "8" || $substr_coa_utang[$k] == "9") {
							$table_utang[] = "t_coalr_sbb";
							$kolom_utang[] = "no_lr_sbb";
						}

						// $this->cb->where([$kolom_utang[$k] => $row['kredit']]);
						// $this->cb->update($table_utang[$k], ['nominal' => $nominal_coa_utang_baru[$k]]);

						$cr[] = $this->cb->get_where('v_coa_all', ['no_sbb' => $coa_kas])->row_array();
						$posisi_cr[] = $cr[$k]['posisi'];
						$nominal_cr[] = $cr[$k]['nominal'];
						$substr_coa_cr[] = substr($coa_kas, 0, 1);
						$nominal_cr_baru[] = 0;

						if ($posisi_cr[$k] == "AKTIVA") {
							$nominal_cr_baru[$k] = $nominal_cr[$k] - $nominal_ppn[$k][0];
						}
						if ($posisi_cr[$k] == "PASIVA") {
							$nominal_cr_baru[$k] = $nominal_cr[$k] + $nominal_ppn[$k][0];
						}

						if ($substr_coa_cr[$k] == "1" || $substr_coa_cr[$k] == "2" || $substr_coa_cr[$k] == "3") {
							$table_cr[] = "t_coa_sbb";
							$kolom_cr[] = "no_sbb";
						}

						if ($substr_coa_cr[$k] == "4" || $substr_coa_cr[$k] == "5" || $substr_coa_cr[$k] == "6" || $substr_coa_cr[$k] == "7" || $substr_coa_cr[$k] == "8" || $substr_coa_cr[$k] == "9") {
							$table_cr[] = "t_coalr_sbb";
							$kolom_cr[] = "no_lr_sbb";
						}

						// $this->cb->where([$kolom_cr[$k] => $coa_kas]);
						// $this->cb->update($table_cr[$k], ['nominal' => $nominal_cr_baru[$k]]);

						// create jurnal
						// $jurnal = [
						// 	'tanggal' => $date_bayar,
						// 	'akun_debit' => $row['kredit'],
						// 	'jumlah_debit' => $nominal_ppn[$i][$k],
						// 	'akun_kredit' => $coa_kas,
						// 	'jumlah_kredit' => $nominal_ppn[$i][$k],
						// 	'saldo_debit' => $nominal_coa_utang_baru[$k],
						// 	'saldo_kredit' => $nominal_cr_baru[$k],
						// 	'keterangan' => 'PPN MASUKAN ' . $po[$i][$k]['no_po'],
						// 	'created_by' => $this->session->userdata('nip'),
						// ];
						// $this->cb->insert('jurnal_neraca', $jurnal);
					}
				}

				$update = [
					'user_bayar' => $this->session->userdata('nip'),
					'posisi' => 'Sudah Dibayar',
					'date_bayar' => $date_bayar,
					'status_pembayaran' => 1
				];

				// $this->cb->where(['Id' => $po[$i][0]['Id']]);
				// $this->cb->update('t_po', $update);

				$response = [
					'success' => true,
					'msg' => 'PO berhasil dibayar!'
				];
			}
			// echo json_encode($response);
			print_r($nominal_coa_utang_baru);
		}
	}

	public function item_out()
	{
		if ($this->session->userdata('isLogin') == FALSE) {
			redirect('home');
		} else {
			$a = $this->session->userdata('level');
			if (strpos($a, '501') !== false) {
				// Pagination
				$keyword = htmlspecialchars($this->input->get('keyword') ?? '', ENT_QUOTES, 'UTF-8');
				$config['base_url'] = base_url('asset/item_out');
				$config['total_rows'] = $this->m_asset->itemOut_count($keyword);
				$config['per_page'] = 10;
				$config['uri_segment'] = 3;
				$config['num_links'] = 3;
				$config['enable_query_strings'] = TRUE;
				$config['page_query_string'] = TRUE;
				$config['use_page_numbers'] = TRUE;
				$config['reuse_query_string'] = TRUE;
				$config['query_string_segment'] = 'page';

				// Bootstrap style pagination
				$config['full_tag_open'] = '<ul class="pagination">';
				$config['full_tag_close'] = '</ul>';
				$config['first_link'] = false;
				$config['last_link'] = false;
				$config['first_tag_open'] = '<li>';
				$config['first_tag_close'] = '</li>';
				$config['prev_link'] = '«';
				$config['prev_tag_open'] = '<li class="prev">';
				$config['prev_tag_close'] = '</li>';
				$config['next_link'] = '»';
				$config['next_tag_open'] = '<li>';
				$config['next_tag_close'] = '</li>';
				$config['last_tag_open'] = '<li>';
				$config['last_tag_close'] = '</li>';
				$config['cur_tag_open'] = '<li class="active"><a href="#">';
				$config['cur_tag_close'] = '</a></li>';
				$config['num_tag_open'] = '<li>';
				$config['num_tag_close'] = '</li>';

				// Initialize paginaton
				$this->pagination->initialize($config);
				$page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
				$data['page'] = $page;
				$data['pagination'] = $this->pagination->create_links();

				$data['users_data'] = $this->m_asset->itemOut_get($config["per_page"], $page, $keyword);

				//inbox notif
				$nip = $this->session->userdata('nip');
				$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
				$sql2 = "SELECT * FROM asset_ruang";
				$sql3 = "SELECT * FROM asset_lokasi";
				$query = $this->db->query($sql);
				$query2 = $this->db->query($sql2);
				$query3 = $this->db->query($sql3);
				$res2 = $query->result_array();
				$asset_ruang = $query2->result();
				$asset_lokasi = $query3->result();
				$result = $res2[0]['COUNT(Id)'];
				$data['count_inbox'] = $result;
				$data['asset_ruang'] = $asset_ruang;
				$data['asset_lokasi'] = $asset_lokasi;

				// Tello
				$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
				$query4 = $this->db->query($sql4);
				$res4 = $query4->result_array();
				$result4 = $res4[0]['COUNT(Id)'];
				$data['count_inbox2'] = $result4;

				$data['title'] = "Item Out";
				$data['pages'] = 'pages/aset/v_item_out';
				$this->load->view('index', $data);
			}
		}
	}

	public function close_item_out()
	{
		$id = $this->input->post('id_item_out');
		$config = [
			'upload_path' => './upload/bukti-close',
			'allowed_types' => 'jpg|jpeg|png|pdf',
			'encrypt_name' => TRUE
		];

		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('image_close')) {
			$response = [
				'success' => FALSE,
				'msg' => $this->upload->display_errors()
			];
		} else {
			$image = $this->upload->data();
			$file = $_FILES['image_close']['name'];
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			if ($ext != 'pdf') {
				$config2 = [
					'image_library' => 'gd2',
					'source_image' => './upload/bukti-close/' . $image['file_name'],
					'create_thumb' => false,
					'maintain_ratio' => false,
					'quality' => '75%',
					'width' => '100%',
					'heigth' => '100%'
				];

				$this->load->library('image_lib', $config2);
				$this->image_lib->resize();
			}

			$update = [
				'image_close' => $image['file_name'],
				'status' => 2,
				'user_close' => $this->session->userdata('nip'),
				'date_close' => date('Y-m-d H:i:s')
			];

			$this->db->where('Id', $id);
			$this->db->update('working_supply', $update);

			$response = [
				'success' => true,
				'msg' => 'Data berhasil diubah!'
			];
		}
		echo json_encode($response);
	}

	public function report_asset()
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '504') !== false) {
			//inbox notif
			$nip = $this->session->userdata('nip');
			$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
			$sql2 = "SELECT * FROM asset_ruang";
			$sql3 = "SELECT * FROM asset_lokasi";
			$query = $this->db->query($sql);
			$query2 = $this->db->query($sql2);
			$query3 = $this->db->query($sql3);
			$res2 = $query->result_array();
			$asset_ruang = $query2->result();
			$asset_lokasi = $query3->result();
			$result = $res2[0]['COUNT(Id)'];
			$data['count_inbox'] = $result;
			$data['asset_ruang'] = $asset_ruang;
			$data['asset_lokasi'] = $asset_lokasi;

			// Tello
			$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
			$query4 = $this->db->query($sql4);
			$res4 = $query4->result_array();
			$result4 = $res4[0]['COUNT(Id)'];
			$data['count_inbox2'] = $result4;

			$data['title'] = "Report asset";
			$data['asset'] = $this->db->get('asset_list');
			$data['pages'] = 'pages/aset/v_report_asset';
			$this->load->view('index', $data);
		} else {
			redirect('home');
		}
	}

	public function working_supply()
	{
		$a = $this->session->userdata('level');
		if (strpos($a, '505') !== false) {
			//inbox notif
			$nip = $this->session->userdata('nip');
			$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
			$sql2 = "SELECT * FROM asset_ruang";
			$sql3 = "SELECT * FROM asset_lokasi";
			$query = $this->db->query($sql);
			$query2 = $this->db->query($sql2);
			$query3 = $this->db->query($sql3);
			$res2 = $query->result_array();
			$asset_ruang = $query2->result();
			$asset_lokasi = $query3->result();
			$result = $res2[0]['COUNT(Id)'];
			$data['count_inbox'] = $result;

			// Tello
			$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
			$query4 = $this->db->query($sql4);
			$res4 = $query4->result_array();
			$result4 = $res4[0]['COUNT(Id)'];
			$data['count_inbox2'] = $result4;

			$data['title'] = "Working Supply";
			$data['data_item'] = $this->db->get('item_list');
			$data['pages'] = 'pages/aset/v_report_item';
			$this->load->view('index', $data);
		} else {
			redirect('home');
		}
	}

	public function report_item()
	{
		$nip = $this->session->userdata('nip');
		// Fetch counts
		$result = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
		$result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};
		$data['data_item'] = $this->db->get('item_list');
		$data['count_inbox'] = $result;
		$data['count_inbox2'] = $result2;
		$item = $this->input->post('item');
		$dari = $this->input->post('dari');
		$sampai = $this->input->post('sampai');

		if ($item == 'all') {
			$sql = "SELECT * FROM working_supply JOIN item_list ON item_list.Id = working_supply.item_id WHERE (tanggal >= '$dari' OR tanggal <= '$sampai')";
			$data['report'] = $this->db->query($sql)->result_array();
			$data['jenis'] = 'all';
		} else {
			$sql = "SELECT * FROM working_supply JOIN item_list ON item_list.Id = working_supply.item_id WHERE item_id = '$item' and (tanggal >= '$dari' OR tanggal <= '$sampai')";
			$data['report'] = $this->db->query($sql)->result_array();
			$data['jenis'] = 'not all';
			$data['item_list'] = $this->db->get_where('item_list', ['Id' => $item])->row_array();
		}


		if ($item) {
			$data['title'] = "Working Supply";
			$data['pages'] = "pages/aset/v_report_item";

			$this->load->view('index', $data);
		} else {
			$data['title'] = "Working Supply";
			$data['pages'] = "pages/aset/v_report_item";

			$this->load->view('index', $data);
		}
	}

	public function export_item()
	{
		$item = $this->input->post('item');
		$dari = $this->input->post('dari');
		$sampai = $this->input->post('sampai');

		if ($item == 'all') {
			$sql = "SELECT * FROM working_supply LEFT JOIN item_list ON item_list.Id = working_supply.item_id WHERE (tanggal >= '$dari' OR tanggal <= '$sampai')";
			$data['report'] = $this->db->query($sql)->result_array();
			$data['jenis'] = 'all';
		} else {
			$sql = "SELECT * FROM working_supply LEFT JOIN item_list ON item_list.Id = working_supply.item_id WHERE item_id = '$item' and (tanggal >= '$dari' OR tanggal <= '$sampai')";
			$data['report'] = $this->db->query($sql)->result_array();
			$data['jenis'] = 'not all';
			$data['item_list'] = $this->db->get_where('item_list', ['Id' => $item])->row_array();
		}
		$file_pdf = 'Working Supply';
		// setting paper
		$paper = 'A4';

		//orientasi paper potrait / landscape
		$orientation = "landscape";

		// $this->load->view('pages/aset/v_report_item_pdf', $data);
		$html = $this->load->view('pages/aset/v_report_item_pdf', $data, true);

		$this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
	}

	public function export_report()
	{
		$item = $this->input->post('asset');
		$dari = $this->input->post('dari');
		$sampai = $this->input->post('sampai');

		$sql = "SELECT * FROM working_supply WHERE asset_id = '$item' and (tanggal >= '$dari' AND tanggal <= '$sampai')";
		$data['report'] = $this->db->query($sql)->result_array();
		$data['asset'] = $this->db->get_where('asset_list', ['Id' => $item])->row_array();
		$file_pdf = 'Penggunaan Asset . ' . $data['asset']['nama_asset'];
		// setting paper
		$paper = 'A4';

		//orientasi paper potrait / landscape
		$orientation = "landscape";

		$html = $this->load->view('pages/aset/v_report_asset_pdf', $data, true);

		$this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
		// $this->load->view('pages/aset/v_report_asset_pdf', $data);
	}


	public function print($id)
	{
		$data['po'] = $this->cb->get_where('t_po', ['Id' => $id])->row_array();
		if ($data['po']['status_sarlog'] == 1 and $data['po']['status_direksi_ops'] == 1) {
			// filename dari pdf ketika didownload
			// $file_pdf = 'Purchase Order Item In. ' . $data['po']['no_po'];

			// // setting paper
			// $paper = 'A4';

			// //orientasi paper potrait / landscape
			// $orientation = "landscape";

			// $html = $this->load->view('pages/aset/v_print_po', $data, true);

			// // run dompdf
			// $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
			$this->load->view('pages/aset/v_print_po', $data);
		} else {
			redirect('home');
		}
	}

	public function print_ro($id)
	{
		$data['ro'] = $this->cb->get_where('t_ro', ['Id' => $id])->row_array();
		if ($data['ro']['status_sarlog'] == 1 and $data['ro']['status_direksi_ops'] == 1) {
			$this->load->view('pages/aset/v_print_ro', $data);
			// filename dari pdf ketika didownload
			// $file_pdf = 'Purchase Order Item In. ' . $data['po']['no_po'];

			// // setting paper
			// $paper = 'A4';

			// //orientasi paper potrait / landscape
			// $orientation = "landscape";

			// $html = $this->load->view('pages/aset/v_print_po_out', $data, true);

			// // run dompdf
			// $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
		} else {
			redirect('asset/sarlog_out');
		}
	}

	public function vendors()
	{
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['title'] = "List Vendors";
		$data['vendors'] = $this->db->get('t_vendors');
		$data['pages'] = 'pages/aset/v_vendors';
		$this->load->view('index', $data);
	}

	public function ubah_vendor($id)
	{
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$query = $this->db->query($sql);
		$res2 = $query->result_array();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['vendor'] = $this->db->get_where('t_vendors', ['Id' => $id])->row_array();
		$data['title'] = "Ubah data vendor";
		$data['pages'] = "pages/aset/v_form_vendor";
		$this->load->view('index', $data);
	}

	public function update_vendor($id)
	{
		$nama = $this->input->post('nama');
		$alamat = $this->input->post('alamat');
		$email = $this->input->post('email');
		$tlp = $this->input->post('tlp');
		$kode = $this->input->post('kode');

		$this->form_validation->set_rules('nama', 'nama vendor', 'required|trim', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('alamat', 'alamat', 'required|trim', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('email', 'alamat', 'required|valid_email|trim', ['required' => '%s wajib diisi!', 'valid_email' => 'email tidak valid']);
		$this->form_validation->set_rules('tlp', 'No. tlp', 'required|trim|numeric', ['required' => '%s wajib diisi!', 'numeric' => '%s harus berisi angka']);
		$this->form_validation->set_rules('kode', 'Kode vendor', 'required|trim|max_length[5]', ['required' => '%s wajib diisi', 'max_length' => '%s maksimal %d karakter']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'status' => '401',
				'msg' => array_values($this->form_validation->error_array())[0],
			];
		} else {
			$update = [
				'nama' => $nama,
				'alamat' => $alamat,
				'email' => $email,
				'no_telpon' => $tlp,
				'kode' => $kode
			];

			$this->db->where('Id', $id);
			$this->db->update('t_vendors', $update);

			$response = [
				'success' => true,
				'msg' => 'Data vendor berhasil diubah!'
			];
		}

		echo json_encode($response);
	}

	public function add_vendor()
	{
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$query = $this->db->query($sql);
		$res2 = $query->result_array();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['title'] = "Tambah data vendor";
		$data['pages'] = "pages/aset/v_form_vendor";
		$this->load->view('index', $data);
	}

	public function insert_vendor()
	{
		$nama = $this->input->post('nama');
		$alamat = $this->input->post('alamat');
		$email = $this->input->post('email');
		$tlp = $this->input->post('tlp');
		$kode = $this->input->post('kode');

		$this->form_validation->set_rules('nama', 'nama vendor', 'required|trim', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('alamat', 'alamat', 'required|trim', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('email', 'alamat', 'required|valid_email|trim', ['required' => '%s wajib diisi!', 'valid_email' => 'email tidak valid']);
		$this->form_validation->set_rules('tlp', 'No. tlp', 'required|trim|numeric', ['required' => '%s wajib diisi!', 'numeric' => '%s harus berisi angka']);
		$this->form_validation->set_rules('kode', 'Kode vendor', 'required|trim|max_length[5]', ['required' => '%s wajib diisi', 'max_length' => '%s maksimal %d karakter']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'status' => '401',
				'msg' => array_values($this->form_validation->error_array())[0],
			];
		} else {
			$insert = [
				'nama' => $nama,
				'alamat' => $alamat,
				'email' => $email,
				'no_telpon' => $tlp,
				'kode' => $kode
			];

			$this->db->insert('t_vendors', $insert);

			$response = [
				'success' => true,
				'msg' => 'Data vendor berhasil ditambahkan!'
			];
		}

		echo json_encode($response);
	}

	public function revisi($id)
	{
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['coa'] = $this->cb->get('v_coa_all');
		$data['po'] = $this->m_asset->get_poList(null, null, null, ['a.Id' => $id], null)->row_array();
		$data['title'] = "Revisi PO";
		$data['pages'] = "pages/aset/v_revisi";
		$this->load->view('index', $data);
	}

	public function update_revisi()
	{
		$id = $this->input->post('id_po');
		$id_item = $this->input->post('id_item[]');
		$rows = $this->input->post('row_item[]');
		$uoi = $this->input->post('uoi[]');
		$satuan = $this->input->post('satuan[]');
		$qty = $this->input->post('qty[]');
		$harga = $this->input->post('harga[]');
		$keterangan = $this->input->post('ket[]');
		$subtotal = $this->input->post('total[]');
		$total = $this->input->post('nominal');


		$this->form_validation->set_rules('qty[]', 'qty', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('harga[]', 'harga', 'required', ['required' => '%s wajib diisi!']);
		$this->form_validation->set_rules('uoi[]', 'uoi', 'required', ['required' => '%s wajib diisi!']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			for ($i = 0; $i < count($rows); $i++) {
				$item[] = $this->cb->get_where('t_po_detail', ['Id' => $id_item[$i]])->row_array();
				$item_list[] = $this->db->get_where('item_list', ['Id' => $item[$i]['item']])->row_array();

				if ($qty[$i] < 1) {
					$response = [
						'success' => false,
						'msg' => 'Stok item ' . $item_list[$i]['nama'] . ' tidak boleh kosong'
					];

					echo json_encode($response);
					return false;
				}

				if ($satuan[$i] < 1) {
					$response = [
						'success' => false,
						'msg' => 'Satuan ' . $item_list[$i]['nama'] . ' tidak boleh kosong'
					];

					echo json_encode($response);
					return false;
				}

				// update table pengajuan detail
				$this->cb->where('Id', $id_item[$i]);
				$this->cb->update('t_po_detail', [
					'qty' => preg_replace('/[^a-zA-Z0-9\']/', '', $qty[$i]),
					'price' => preg_replace('/[^a-zA-Z0-9\']/', '', $harga[$i]),
					'total' => preg_replace('/[^a-zA-Z0-9\']/', '', $subtotal[$i]),
					'uoi' => $uoi[$i],
					'satuan' => preg_replace('/[^a-zA-Z0-9\']/', '', $satuan[$i]),
					'keterangan' => $keterangan[$i]
				]);
			}

			// Update table pengajuan
			$update = [
				'posisi' => 'diajukan kepada direktur operasional',
				'status_direksi_ops' => 0,
				'date_direksi_ops' => null,
				'catatan_direksi_ops' => null,
				'status_dirut' => 0,
				'date_dirut' => null,
				'catatan_dirut' => null,
				'total' => preg_replace('/[^a-zA-Z0-9\']/', '', $total)
			];

			$this->cb->where(['Id' => $id]);
			$this->cb->update('t_po', $update);

			$response = [
				'success' => true,
				'msg' => 'PO berhasil direvisi!'
			];
		}
		echo json_encode($response);
	}

	public function repair()
	{
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['list_repair'] = $this->db->get('item_repair');
		$data['item_list'] = $this->db->get('item_list')->result_array();
		$data['asset_list'] = $this->db->get('asset_list')->result_array();
		$data['title'] = "Repair Item";
		$data['pages'] = "pages/aset/v_repair";
		$this->load->view('index', $data);
	}

	public function getSerialNumber()
	{
		$id = $this->input->post('id');
		// $this->db->where(['kode_item' => $id]);
		// $this->db->or_where('status', 'O');
		// $this->db->or_where('status', 'RO');
		// $serial = $this->db->get_where('item_detail', ['kode_item' => $id, 'status' => 'O', 'status' => 'RO'])->result();
		// $serial = $this->db->get('item_detail')->result();
		$sql = "SELECT * FROM item_detail WHERE kode_item = '$id' and (item_detail.status = 'O' OR item_detail.status = 'RO')";
		$serial = $this->db->query($sql)->result();
		$option = "";
		if ($serial) {
			foreach ($serial as $row) {
				$option .= "<option value='$row->Id'>$row->serial_number</option>";
			}
		} else {
			$option .= "<option value='' selected>Tidak ada detail</option>";
		}

		echo json_encode($option);
	}

	public function add_repair()
	{
		$tgl = $this->input->post('tanggal');
		$asset = $this->input->post('asset');
		$item = $this->input->post('item');
		$serial = $this->input->post('serial-number');
		$ket = $this->input->post('keterangan');

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi']);
		$this->form_validation->set_rules('asset', 'asset', 'required', ['required' => '%s wajib diisi']);
		$this->form_validation->set_rules('item', 'item', 'required', ['required' => '%s wajib diisi']);
		$this->form_validation->set_rules('serial-number', 'serial number', 'required', ['required' => '%s wajib diisi']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {

			$config['upload_path']          = './upload/bukti-repair';
			$config['allowed_types']        = 'jpg|jpeg|png|pdf';
			$config['encrypt_name']         = TRUE;
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('bukti-repair')) {
				$response = [
					'success' => false,
					'msg' => $this->upload->display_errors()
				];
			} else {
				$upload = $this->upload->data();
				$file = $_FILES['bukti-repair']['name'];
				$ext = pathinfo($file, PATHINFO_EXTENSION);

				if ($ext != 'pdf') {
					$config2 = [
						'image_library' => 'gd2',
						'source_image' => './upload/bukti-repair/' . $upload['file_name'],
						'create_thumb' => false,
						'maintain_ratio' => false,
						'quality' => '75%',
						'width' => '100%',
						'heigth' => '100%'
					];

					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();
				}

				$asset_list = $this->db->get_where('asset_list', ['Id' => $asset])->row_array();
				$insert = [
					'item' => $item,
					'serial_number' => $serial,
					'user' => $this->session->userdata('nip'),
					'tgl_pengajuan' => $tgl,
					'qty' => 1,
					'keterangan' => $ket,
					'asset' => $asset,
					'bukti_repair' => $upload['file_name']
				];

				$this->db->insert('item_repair', $insert);

				$item_list = $this->db->get_where('item_list', ['Id' => $item])->row_array();
				$stok_lama = $item_list['stok'];
				$stok_baru = $stok_lama + 1;

				$this->db->where('Id', $item);
				$this->db->update('item_list', ['stok' => $stok_baru]);

				// update status serial number
				$this->db->where('Id', $serial);
				$this->db->update('item_detail', ['status' => 'R']);

				$array_serial[] = $serial;
				// working supply
				$ws = [
					'item_id' => $item,
					'jml' => 1,
					'stok_awal' => $stok_lama,
					'stok_akhir' => $stok_baru,
					'user' => $this->session->userdata('nip'),
					'jenis' => 'REPAIR IN',
					'asset_id' => $asset,
					'harga' => 0,
					'serial_number' => json_encode($array_serial),
					'keterangan' => $asset_list['nama_asset']
				];

				$this->db->insert('working_supply', $ws);

				$response = [
					'success' => true,
					'msg' => 'Item repair berhasil ditambahkan!',
				];
			}
		}
		echo json_encode($response);
	}

	public function repair_out()
	{
		//inbox notif
		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$sql2 = "SELECT * FROM asset_ruang";
		$sql3 = "SELECT * FROM asset_lokasi";
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$query3 = $this->db->query($sql3);
		$res2 = $query->result_array();
		$asset_ruang = $query2->result();
		$asset_lokasi = $query3->result();
		$result = $res2[0]['COUNT(Id)'];
		$data['count_inbox'] = $result;
		$data['asset_ruang'] = $asset_ruang;
		$data['asset_lokasi'] = $asset_lokasi;

		// Tello
		$sql4 = "SELECT COUNT(Id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query4 = $this->db->query($sql4);
		$res4 = $query4->result_array();
		$result4 = $res4[0]['COUNT(Id)'];
		$data['count_inbox2'] = $result4;

		$data['list_repair'] = $this->db->get('item_repair_out');
		$data['asset'] = $this->db->get('asset_list')->result_array();

		$sql_item = "SELECT item_list.nama, item_repair.Id, item_detail.serial_number FROM item_repair JOIN item_list ON item_list.Id = item_repair.item JOIN item_detail ON item_repair.serial_number = item_detail.Id WHERE item_repair.out = 0";
		$data['item_list'] = $this->db->query($sql_item)->result_array();
		$data['title'] = "Repair Item Out";
		$data['pages'] = "pages/aset/v_repair_out";
		$this->load->view('index', $data);
	}

	public function add_repair_out()
	{
		$tgl = $this->input->post('tanggal');
		$asset = $this->input->post('asset');
		$item = $this->input->post('item');
		$ket = $this->input->post('keterangan');
		$teknisi = $this->input->post('teknisi');

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi']);
		$this->form_validation->set_rules('asset', 'asset', 'required', ['required' => '%s wajib diisi']);
		$this->form_validation->set_rules('item', 'item', 'required', ['required' => '%s wajib diisi']);
		$this->form_validation->set_rules('teknisi', 'teknisi', 'required', ['required' => '%s wajib diisi']);

		$item_repair = $this->db->get_where('item_repair', ['Id' => $item])->row_array();


		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			$config['upload_path']          = './upload/bukti-serah';
			$config['allowed_types']        = 'jpg|jpeg|png|pdf';
			$config['encrypt_name']         = TRUE;
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('bukti-serah')) {
				$response = [
					'success' => false,
					'msg' => $this->upload->display_errors()
				];
			} else {
				$upload = $this->upload->data();
				$file = $_FILES['bukti-serah']['name'];
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				if ($ext != 'pdf') {
					$config2 = [
						'image_library' => 'gd2',
						'source_image' => './upload/bukti-serah/' . $upload['file_name'],
						'create_thumb' => false,
						'maintain_ratio' => false,
						'quality' => '75%',
						'width' => '100%',
						'heigth' => '100%'
					];


					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();
				}

				$insert = [
					'user' => $this->session->userdata('nip'),
					'tanggal' => $tgl,
					'asset' => $asset,
					'item' => $item_repair['item'],
					'serial_number' => $item_repair['serial_number'],
					'keterangan' => $ket,
					'teknisi' => $teknisi,
					'user_serah' => $this->session->userdata('nip'),
					'bukti_serah' => $upload['file_name']
				];

				$this->db->insert('item_repair_out', $insert);

				$item_list = $this->db->get_where('item_list', ['Id' => $item_repair['item']])->row_array();
				$stok_lama = $item_list['stok'];
				$stok_baru = $stok_lama - 1;

				$this->db->where('Id', $item_repair['item']);
				$this->db->update('item_list', ['stok' => $stok_baru]);

				// update status serial number
				$this->db->where('Id', $item_repair['serial_number']);
				$this->db->update('item_detail', ['status' => 'RO']);

				$array_serial[] = $item_repair['serial_number'];

				$item_out = [
					'item_id' => $item_repair['item'],
					'asset_id' => $asset,
					'harga' => 0,
					'jml' => 1,
					'stok_awal' => $stok_lama,
					'stok_akhir' => $stok_baru,
					'user' => $this->session->userdata('nip'),
					'status' => 1,
					'jenis' => "REPAIR OUT",
					'serial_number' => json_encode($array_serial),
					'keterangan' => $teknisi,
					'user_serah' => $this->session->userdata('nip'),
					'bukti_serah' => $upload['file_name'],
					'date_serah' => $tgl
				];

				$this->db->insert('working_supply', $item_out);

				$this->db->where('Id', $item);
				$this->db->update('item_repair', ['out' => 1]);

				$response = [
					'success' => true,
					'msg' => 'Item repair out berhasil ditambahkan!'
				];
			}
		}

		echo json_encode($response);
	}

	public function hapus_detail_item()
	{
		$tgl = $this->input->post('tanggal');
		$serial_number = $this->input->post('serial');
		$asset = $this->input->post('asset');
		$ket = $this->input->post('keterangan');

		$this->form_validation->set_rules('tanggal', 'tanggal', 'required', ['required' => '%s wajib diisi']);
		$this->form_validation->set_rules('asset', 'asset', 'required', ['required' => '%s wajib diisi']);
		$this->form_validation->set_rules('serial', 'serial number', 'required', ['required' => '%s wajib diisi']);

		if ($this->form_validation->run() == FALSE) {
			$response = [
				'success' => false,
				'msg' => array_values($this->form_validation->error_array())[0]
			];
		} else {
			$config = [
				'upload_path' => './upload/bukti-musnah',
				'allowed_types' => 'jpg|jpeg|png|pdf',
				'encrypt_name' => TRUE
			];

			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('bukti')) {
				$response = [
					'success' => FALSE,
					'msg' => $this->upload->display_errors()
				];
			} else {
				$upload = $this->upload->data();
				$file = $_FILES['bukti']['name'];
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				if ($ext != 'pdf') {
					$config2 = [
						'image_library' => 'gd2',
						'source_image' => './upload/bukti-musnah/' . $upload['file_name'],
						'create_thumb' => false,
						'maintain_ratio' => false,
						'quality' => '75%',
						'width' => '100%',
						'heigth' => '100%'
					];

					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();
				}

				$item_detail = $this->db->get_where('item_detail', ['Id' => $serial_number])->row_array();
				$insert = [
					'user' => $this->session->userdata('nip'),
					'tanggal' => $tgl,
					'asset' => $asset,
					'item' => $item_detail['kode_item'],
					'serial_number' => $item_detail['serial_number'],
					'keterangan' => $ket
				];

				$this->db->insert('item_musnah', $insert);


				// update status serial number
				$this->db->where('Id', $serial_number);
				$this->db->update('item_detail', ['status' => 'M']);

				$array_serial[] = $serial_number;
				$asset_list = $this->db->get_where('asset_list', ['Id' => $asset])->row_array();

				$item_out = [
					'item_id' => $item_detail['kode_item'],
					'harga' => 0,
					'user' => $this->session->userdata('nip'),
					'status' => 0,
					'jenis' => "MUSNAH",
					'serial_number' => json_encode($array_serial),
					'keterangan' => $asset_list['nama_asset']
				];

				$this->db->insert('working_supply', $item_out);

				$response = [
					'success' => true,
					'msg' => 'Barang dimusnahkan!'
				];
			}
		}

		echo json_encode($response);
	}

	public function update_bukti_serah()
	{
		$id = $this->input->post('id_po');
		$config['upload_path']          = './upload/bukti-serah';
		$config['allowed_types']        = 'jpg|jpeg|png|pdf';
		$config['encrypt_name']         = TRUE;
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('bukti-serah')) {
			$response = [
				'success' => false,
				'msg' => $this->upload->display_errors()
			];
		} else {
			$upload = $this->upload->data();
			$update = [
				'bukti_serah' => $upload['file_name'],
			];

			// update table release order
			$this->cb->where('Id', $id);
			$this->cb->update('t_ro', $update);

			// update table working supply
			$this->db->where(['no_po' => $id, 'jenis' => 'OUT']);
			$this->db->update('working_supply', $update);

			$response = [
				'success' => true,
				'msg' => 'Bukti Serah Terima Barang Berhasil Diupload!'
			];
		}

		echo json_encode($response);
	}
}
