<style>
  @media screen and (max-width:991px) {
    table.table.table-ro {
      width: 1200px !important;
      max-width: none !important;
    }
  }

  .select2-container--default {
    overflow: inherit;
  }
</style>

<div class="right_col" role="main">
  <div class="clearfix"></div>
  <!-- Start content-->
  <div class="">
    <div class="">
      <div class="x_panel card">
        <div class="x_title">
          <h2>Detail Release Order <?= $ro['no_ro'] ?></h2>
        </div>
        <div class="x_content">
          <a href="<?= base_url('asset/sarlog_out') ?>" class="btn btn-warning">Back</a>

          <form action="<?= base_url('asset/update_serahItem') ?>" method="post" enctype="multipart/form-data" id="update-bayar" style="width: 100%;">
            <input type="hidden" name="id_po" id="id_po" value="<?= $ro['Id'] ?>">
            <div class="table-responsive">
              <table class="table table-bordered table-ro">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Asset</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>COA Persediaan</th>
                    <th>COA Beban</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $item = $this->cb->get_where('t_ro_detail', ['no_ro' => $ro['Id']])->result_array();
                  foreach ($item as $key => $i) {
                    $detail = $this->db->get_where('item_list', ['Id' => $i['item']])->row_array();
                    $asset = $this->db->get_where('asset_list', ['Id' => $i['asset']])->row_array();
                    $this->db->where(['kode_item' => $detail['Id'], 'status' => 'A']);
                    $this->db->order_by('tanggal_masuk', 'ASC');
                    $item_detail = $this->db->get('item_detail')->result_array();
                  ?>
                    <input type="hidden" class="form-control" name="row_item[]" id="row<?= $i['Id'] ?>">
                    <input type="hidden" class="form-control" name="id_item[]" id="id_item<?= $i['Id'] ?>" value="<?= $i['Id'] ?>">
                    <tr>
                      <td>
                        <?= $detail['nama']  ?>
                        <div style="margin-top: 10px;">
                          <div>
                            <label for="label" class="form-label" class="form-label">Select Detail</label>
                          </div>
                          <select name="detail_item[<?= $key ?>][]" id="detail-item-<?= $i['Id'] ?>" class="form-control" multiple>
                            <?php if (!$item_detail) { ?>
                              <option value="0" selected>Tidak Ada Serial Number</option>
                            <?php } ?>
                            <?php foreach ($item_detail as $id) { ?>
                              <option value="<?= $id['Id'] ?>"><?= $id['serial_number'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </td>
                      <td><?= $asset['nama_asset'] ?></td>
                      <td><?= $i['qty'] ?></td>
                      <td><?= number_format($i['price']) ?></td>
                      <td><?= number_format($i['total']) ?></td>
                      <td>
                        <div class="select-coa">
                          <?php if ($ro['posisi'] == 'Barang sudah diserahkan!') { ?>
                            <select name="coa_persediaan[]" id="coa_persediaan<?= $i['Id'] ?>" class="form-control select2" disabled>
                              <?php foreach ($coa->result_array() as $row) { ?>
                                <option value="<?= $row['no_sbb'] ?>" <?= $row['no_sbb'] == $i['persediaan'] ? 'selected' : '' ?>><?= $row['no_sbb'] . ' - ' . $row['nama_perkiraan'] ?></option>
                              <?php } ?>
                            </select>
                          <?php } else { ?>
                            <select name="coa_persediaan[]" id="coa_persediaan<?= $i['Id'] ?>" class="form-control select2">
                              <option value=""> -- Pilih COA Persediaan -- </option>
                              <?php foreach ($coa->result_array() as $cp) { ?>
                                <option value="<?= $cp['no_sbb'] ?>" <?= $cp['no_sbb'] == $detail['coa'] ? 'selected' : '' ?>><?= $cp['no_sbb'] . ' - ' . $cp['nama_perkiraan'] ?></option>
                              <?php } ?>
                            </select>
                          <?php } ?>
                        </div>
                      </td>
                      <td>
                        <div class="select-coa">
                          <?php if ($ro['posisi'] == 'Barang sudah diserahkan!') { ?>
                            <select name="coa_beban[]" id="coa_beban<?= $i['Id'] ?>" class="form-control select2" disabled>
                              <?php foreach ($coa->result_array() as $row) { ?>
                                <option value="<?= $row['no_sbb'] ?>" <?= $row['no_sbb'] == $i['beban'] ? 'selected' : '' ?>><?= $row['no_sbb'] . ' - ' . $row['nama_perkiraan'] ?></option>
                              <?php } ?>
                            </select>
                          <?php } else { ?>
                            <select name="coa_beban[]" id="coa_beban<?= $i['Id'] ?>" class="form-control select2">
                              <option value=""> -- Pilih COA Beban -- </option>
                              <?php foreach ($coa->result_array() as $cb) { ?>
                                <option value="<?= $cb['no_sbb'] ?>"><?= $cb['no_sbb'] . ' - ' . $cb['nama_perkiraan'] ?></option>
                              <?php } ?>
                            </select>
                          <?php } ?>
                          <script>
                            $(document).ready(function() {
                              $('#detail-item-<?= $i['Id'] ?>, #coa_beban<?= $i['Id'] ?>, #coa_persediaan<?= $i['Id'] ?>').select2({
                                width: "100%",
                              })

                            })
                          </script>
                        </div>
                      </td>
                    </tr>
                  <?php } ?>
                  <tr>
                    <td colspan="3"><b>Total</b></td>
                    <td colspan="3"><?= number_format($ro['total']) ?></td>
                  </tr>
                  <tr>
                    <td colspan="3"><b>Terbilang</b></td>
                    <td colspan="3"><?= terbilang($ro['total']) ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <?php if ($ro['posisi'] != 'Barang sudah diserahkan!') { ?>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <?php if ($ro['posisi'] == 'Sudah Dibayar') { ?>
                      <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d', strtotime($po['date_bayar'])) ?>" disabled>
                    <?php } else { ?>
                      <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="row">
                <?php if ($ro['posisi'] != 'Barang sudah diserahkan') { ?>
                  <button class="btn btn-danger" type="reset">Reset</button>
                  <button class="btn btn-primary btn-submit" type="submit">Serahkan</button>
                <?php } ?>
              </div>
            <?php } ?>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>