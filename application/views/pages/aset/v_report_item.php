<style>
  .select2-container--default .select2-selection--multiple,
  .select2-container--default .select2-selection--single {
    min-height: 34px;
    height: 34px;
  }

  #item {
    border-collapse: collapse;
    width: 100%;
    font-size: 8.5pt;
  }

  #item td,
  #item th {
    border: 1px solid #ddd;
    padding: 5px;
  }

  #item tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  #item tr:hover {
    background-color: #ddd;
  }

  #item th {
    padding-top: 5px;
    padding-bottom: 5px;
    text-align: left;
    background-color: #615e5e;
    color: white;
  }

  .text-right {
    text-align: right;
  }

  .text-center {
    text-align: center;
  }

  .judul {
    margin-bottom: 30px;
    text-align: center;
    font-weight: 800;
    text-transform: uppercase;
  }

  @media screen and (max-width:991px) {
    table.table#item {
      width: 1200px !important;
      max-width: none !important;
    }
  }
</style>


<div class="right_col" role="main">
  <div class="clearfix"></div>
  <!-- Start content-->
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel card">
        <div class="x_title">
          <h2>Report Item</h2>
        </div>
        <div class="x_content">
          <?php if ($this->input->post('item')) { ?>
            <form action="<?= base_url('asset/report_item') ?>" method="post">
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="form">
                    <label for="item">Nama Item</label>
                    <select name="item" id="item" class="form-control select2" required>
                      <option value=""> :: Pilih Item :: </option>
                      <option value="all" <?= $this->input->post('item') == 'all' ? 'selected' : '' ?>> All Item </option>
                      <?php foreach ($data_item->result_array() as $a) { ?>
                        <option value="<?= $a['Id'] ?>" <?= $this->input->post('item') == $a['Id'] ? 'selected' : '' ?>><?= $a['nama'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12">
                  <div class="form">
                    <label for="asset">Dari</label>
                    <input type="date" class="form-control" name="dari" id="dari" required value="<?= $this->input->post('dari') ?>">
                  </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12">
                  <div class="form">
                    <label for="asset">sampai</label>
                    <input type="date" class="form-control" name="sampai" id="sampai" value="<?= $this->input->post('sampai') ?>">
                  </div>
                </div>
                <div class="col-md-2" style="margin-top: 24px;">
                  <button type="submit" class="btn btn-primary">Search</button>
                  <a href="<?= base_url('asset/working_supply') ?>" class="btn btn-warning">Reset</a>
                </div>
              </div>
            </form>
            <div class="row">
              <?php if ($jenis == 'all') { ?>
                <div class="judul">Working Supply All Item</div>
                <div class="row">
                  <form action="<?= base_url('asset/export_item') ?>" target="_blank" method="post">
                    <input type="hidden" class="form-control" name="item" id="item" value="<?= $this->input->post('item') ?>">
                    <input type="hidden" class="form-control" name="dari" id="dari" required value="<?= $this->input->post('dari') ?>">
                    <input type="hidden" class="form-control" name="sampai" id="sampai" value="<?= $this->input->post('sampai') ?>">
                    <button type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Print PDF</button>
                  </form>
                </div>
                <div class="table-responsive">
                  <table id="item">
                    <thead>
                      <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Item</th>
                        <th>Serial Number</th>
                        <th>Unit</th>
                        <th>Stok Awal</th>
                        <th>IN</th>
                        <th>OUT</th>
                        <th>Stok Akhir</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $no = 1;
                      $total = 0;
                      foreach ($report as $r) {
                        $asset = $this->db->get_where('asset_list', ['Id' => $r['asset_id']])->row_array();
                        $total += $r['harga'] * $r['jml'];

                      ?>
                        <tr>
                          <td><?= $no++; ?></td>
                          <td><?= tgl_indo(date('Y-m-d', strtotime($r['tanggal']))); ?></td>
                          <td><?= $r['jenis']; ?></td>
                          <td><?= $r['keterangan'] ?></td>
                          <td><?= $r['nama']; ?></td>
                          <td>
                            <?php
                            if ($r['serial_number']) {
                              foreach (json_decode($r['serial_number']) as $s) {
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
                          <td><?= $r['asset_id'] ? $asset['nama_asset'] : "-"; ?></td>
                          <td><?= $r['stok_awal'] ?></td>
                          <td>
                            <?php
                            if ($r['jenis'] == 'IN' or $r['jenis'] == 'REPAIR IN') {
                              echo $r['jml'];
                            } else {
                              echo "-";
                            }
                            ?>
                          </td>
                          <td>
                            <?php
                            if ($r['jenis'] == 'OUT' or $r['jenis'] == 'REPAIR OUT') {
                              echo $r['jml'];
                            } else {
                              echo "-";
                            }
                            ?>
                          </td>
                          <td><?= $r['stok_akhir'] ?></td>
                        </tr>
                        <?php if (count($report) < 1) { ?>
                          <tr>
                            <td colspan="9" class="text-center">Tidak ada data</td>
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              <?php } else { ?>
                <div class="judul">Working Supply Item <?= $item_list['nama'] ?></div>
                <div class="row">
                  <form action="<?= base_url('asset/export_item') ?>" target="_blank" method="post">
                    <input type="hidden" class="form-control" name="item" id="item" value="<?= $this->input->post('item') ?>">
                    <input type="hidden" class="form-control" name="dari" id="dari" required value="<?= $this->input->post('dari') ?>">
                    <input type="hidden" class="form-control" name="sampai" id="sampai" value="<?= $this->input->post('sampai') ?>">
                    <button type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Print PDF</button>
                  </form>
                </div>
                <table id="item">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Tanggal</th>
                      <th>Serial Number</th>
                      <th>Keterangan</th>
                      <th>Jenis</th>
                      <th>Unit</th>
                      <th>Stok Awal</th>
                      <th>IN</th>
                      <th>OUT</th>
                      <th>Stok Akhir</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $total = 0;
                    foreach ($report as $r) {
                      $asset = $this->db->get_where('asset_list', ['Id' => $r['asset_id']])->row_array();
                      $total += $r['harga'] * $r['jml'];
                    ?>
                      <tr>
                        <td><?= $no++; ?></td>
                        <td><?= tgl_indo(date('Y-m-d', strtotime($r['tanggal']))); ?></td>
                        <td>
                          <?php
                          if ($r['serial_number']) {
                            foreach (json_decode($r['serial_number']) as $s) {
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
                        <td><?= $r['keterangan'] ?></td>
                        <td><?= $r['jenis']; ?></td>
                        <td><?= $r['asset_id'] ? $asset['nama_asset'] : "-"; ?></td>
                        <td><?= $r['stok_awal'] ?></td>
                        <td>
                          <?php
                          if ($r['jenis'] == 'IN' or $r['jenis'] == 'REPAIR IN') {
                            echo $r['jml'];
                          } else {
                            echo "-";
                          }
                          ?>
                        </td>
                        <td>
                          <?php
                          if ($r['jenis'] == 'OUT' or $r['jenis'] == 'REPAIR OUT') {
                            echo $r['jml'];
                          } else {
                            echo "-";
                          }
                          ?>
                        </td>
                        <td><?= $r['stok_akhir'] ?></td>
                      </tr>
                    <?php } ?>
                    <?php if (count($report) < 1) { ?>
                      <tr>
                        <td colspan="9" class="text-center">Tidak ada data</td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              <?php } ?>
            </div>
          <?php } else { ?>
            <form action="<?= base_url('asset/report_item') ?>" method="post">
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="form">
                    <label for="item">Nama Item</label>
                    <select name="item" id="item" class="form-control select2" required>
                      <option value=""> :: Pilih Item :: </option>
                      <option value="all"> All Item </option>
                      <?php foreach ($data_item->result_array() as $a) { ?>
                        <option value="<?= $a['Id'] ?>"><?= $a['nama'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12">
                  <div class="form">
                    <label for="asset">Dari</label>
                    <input type="date" class="form-control" name="dari" id="dari" required>
                  </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12">
                  <div class="form">
                    <label for="asset">sampai</label>
                    <input type="date" class="form-control" name="sampai" id="sampai" value="<?= date('Y-m-d') ?>">
                  </div>
                </div>
                <div class="col-md-2" style="margin-top: 24px;">
                  <button type="submit" class="btn btn-primary">Search</button>
                </div>
              </div>
            </form>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    // window.location.reload();
    $('.select2').select2({
      width: "100%"
    })
  })
</script>