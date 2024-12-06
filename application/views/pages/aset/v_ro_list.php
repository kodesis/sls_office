<div class="right_col" role="main">
  <div class="clearfix"></div>
  <!-- Start content-->
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel card">
        <div class="x_title">
          <h2>List Release Order</h2>
        </div>

        <div class="x_content">
          <div class="row">
            <a href="<?= base_url('asset/release_order') ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Create RO</a>
            <?php $a = $this->session->userdata('level');
            if (strpos($a, '503') !== false) { ?>
              <a href="<?= base_url('asset/sarlog_out') ?>" class="btn btn-success btn-sm">Approval Sarlog <span class="badge bg-red"><?= $count_sarlog ?></span></a>
            <?php }
            if ($this->session->userdata('bagian') == 10) { ?>
              <a href="<?= base_url('asset/direksi_ops_out') ?>" class="btn btn-success btn-sm">Approval Direktur Ops
                <span class="badge bg-red"><?= $count_dirops ?></span></a>
            <?php }
            ?>
          </div>
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 form-group" style="margin: 0; padding:0;">
              <form class="form-horizontal form-label-left" method="get" action="<?= base_url('asset/ro_list') ?>">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="Masukan nomor release order" name="keyword" id="keyword" value="<?= $this->input->get('keyword') ?>">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">Search</button>
                    <a href="<?= base_url('asset/ro_list') ?>" class="btn btn-warning" style="color:white;">Reset</a>
                  </span>
                </div><!-- /input-group -->
              </form>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th scope="col">No.</th>
                  <th scope="col">Release Order</th>
                  <th scope="col">Teknisi</th>
                  <th scope="col">Tanggal</th>
                  <th scope="col">Posisi</th>
                  <th scope="col">Total</th>
                  <th scope="col">#</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($ro->num_rows() < 1) { ?>
                  <tr align="center">
                    <td colspan="7">Tidak ada data</td>
                  </tr>
                  <?php } else {
                  foreach ($ro->result_array() as $value) {
                  ?>
                    <tr>
                      <td scope="row"><?= ++$page; ?></td>
                      <td scope="row"><?= $value['no_ro'] ?></td>
                      <td scope="row"><?= $value['teknisi'] ?></td>
                      <td scope="row"><?= tgl_indo(date('Y-m-d', strtotime($value['tgl_pengajuan']))) ?></td>
                      <td scope="row"><?= $value['posisi'] ?></td>
                      <td scope="row"><?= number_format($value['total']) ?></td>
                      <td scope="row">
                        <?php if ($value['status_sarlog'] == 1 and $value['status_direksi_ops'] == 1) { ?>
                          <a href="<?= base_url('asset/print_ro/' . $value['Id']) ?>" class="btn btn-primary btn-xs" target="_blank">Print</a>
                        <?php } ?>
                        <?php if ($value['status_sarlog'] == 0 or $value['status_sarlog'] == 3 or $value['status_direksi_ops'] == 3) { ?>
                          <a href="<?= base_url('asset/update_ro/' . $value['Id']) ?>" class="btn btn-success btn-xs">Update</a>
                        <?php } ?>
                        <button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal<?= $value['Id'] ?>">View</button>
                        <!-- Modal Detail -->
                        <div class="modal fade" id="myModal<?= $value['Id'] ?>" role="dialog">
                          <div class="modal-dialog modal-lg">
                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h2 class="modal-title">Release Order <?= $value['no_ro'] ?></h2>
                              </div>
                              <div class="modal-body">
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th width="20px">No.</th>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th width="25px">Qty</th>
                                        <th>UOI</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Ket</th>
                                        <!-- <th width="30px">#</th> -->
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                      $detail = $this->cb->get_where('t_ro_detail', ['no_ro' => $value['Id']])->result_array();
                                      $no = 1;
                                      foreach ($detail as $row) {
                                        $item = $this->db->get_where('item_list', ['Id' => $row['item']])->row_array();
                                        $unit = $this->db->get_where('asset_list', ['Id' => $row['asset']])->row_array();
                                        // $this->db->where_in('Id', json_decode($row['detail']));
                                        // $item_detail = $this->db->get('item_detail')->result_array();
                                        // $item_detail = $this->db->get_where('item_detail', ['kode_item' => $item['Id']])->result_array();
                                      ?>
                                        <tr>
                                          <td><?= $no++ ?></td>
                                          <td><?= $item['nama'] ?></td>
                                          <td><?= $unit['nama_asset'] ?></td>
                                          <td><?= $row['qty'] ?></td>
                                          <td><?= $row['uoi'] ?></td>
                                          <td><?= number_format($row['price'], 0) ?></td>
                                          <td><?= number_format($row['total'], 0) ?></td>
                                          <td><?= $row['keterangan'] ?></td>
                                        </tr>
                                      <?php } ?>
                                      <tr>
                                        <td colspan="5" align="right"><strong>TOTAL</strong></td>
                                        <td><?= number_format($value['total']) ?></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                                <table style="margin-top: 20px;" class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Catatan Sarlog</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr>
                                      <td><?= $value['catatan_sarlog'] ? $value['catatan_sarlog'] : "Tidak ada catatan" ?>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                                <table style="margin-top: 20px;" class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Catatan Direktur Operasional</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr>
                                      <td>
                                        <?= $value['catatan_direksi_ops'] ? $value['catatan_direksi_ops'] : "Tidak ada catatan" ?>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                                <table style="margin-top: 20px;" class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Bukti Serah Terima Barang</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr>
                                      <td>
                                        <?php if ($value['bukti_serah']) { ?>
                                          <a href="<?= base_url('upload/bukti-serah/') . $value['bukti_serah'] ?>" class="btn btn-success btn-xs" target="_blank">Lihat Bukti Serah Terima Barang</a>
                                        <?php } else { ?>
                                          -
                                        <?php } ?>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
          </div>
          <div class="row text-center">
            <?= $pagination ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>