<style>
  .select2-container--default .select2-selection--multiple,
  .select2-container--default .select2-selection--single {
    min-height: 34px;
    height: 34px;
  }
</style>
<div class="right_col" role="main">
  <div class="clearfix"></div>
  <!-- Start content-->
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel card">
        <div class="x_title">
          <h2>Report Asset</h2>
        </div>
        <div class="x_content">
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12" style="padding: 0 !important; margin: 0 !important">
              <form action="<?= base_url('asset/report_asset') ?>" method="get">
                <div class="input-group">
                  <input type="text" class="form-control" id="search" name="search" placeholder="Cari nama atau kode item..." value="<?= $this->input->get('search') ?>">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Search!</button>
                    <a href="<?= base_url('asset/report_asset') ?>" class="btn btn-warning">RESET</a>
                    <a href="<?= base_url('asset/export_itemList') ?>" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export</a>
                  </span>
                </div><!-- /input-group -->
              </form>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead style="background-color: #23527c; color: white;">
                <tr>
                  <th>No.</th>
                  <th>Tanggal</th>
                  <th>Asset</th>
                  <th>Item</th>
                  <th>Serial</th>
                  <th>Harga Satuan</th>
                  <th>Jumlah</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows() > 0) {
                  $no = 1;
                  foreach ($result->result_array() as $res) {
                ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= date('d/m/y', strtotime($res['tanggal'])) ?></td>
                      <td><?= $res['nama_asset'] ?></td>
                      <td><?= $res['nama'] ?></td>
                      <td><?php
                          if ($res['serial_number']) {
                            foreach (json_decode($res['serial_number']) as $s) {
                              if ($s != 0) {
                                $serial = $this->db->get_where('item_detail', ['Id' => $s])->row_array();
                                echo $serial['serial_number'] . '<br>';
                          ?>

                        <?php } else {
                                echo '-';
                              }
                            }
                          } else {
                            echo '-';
                          } ?>
                      </td>
                      <td><?= number_format($res['harga']) ?></td>
                      <td><?= $res['jml'] ?></td>
                      <td><?= number_format($res['harga'] * $res['jml']) ?></td>
                    </tr>
                <?php };
                } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $('.select2').select2({
      width: "100%"
    })
  })
</script>