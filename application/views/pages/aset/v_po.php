<style>
  .select2-container--default .select2-selection--multiple,
  .select2-container--default .select2-selection--single {
    min-height: 34px;
    height: 34px;
  }

  @media screen and (max-width:991px) {
    table.table.table-po {
      width: 1200px !important;
      max-width: none !important;
    }

  }
</style>

<!-- page content -->
<div class="right_col" role="main">
  <!--div class="pull-left">
				<font color='Grey'>Create New E-Memo </font>
			</div-->
  <div class="clearfix"></div>

  <!-- Start content-->
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel card">
        <div class="x_title">
          <h2>Purchase Order Item</h2>
        </div>
        <div class="x_content">
          <?php if (!$this->uri->segment(3)) { ?>
            <form class="form-horizontal form-label-left input_mask" method="POST" action="<?= base_url('asset/save_po') ?>" enctype="multipart/form-data" id="form-po">
              <div class="row" style="margin-bottom: 30px;">
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <label for="tanggal" class="form-label">Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-5 col-sm-6 col-xs-12">
                  <label for="tanggal" class="form-label">Vendor</label>
                  <select name="vendor" id="vendor" class="form-control">
                    <option value="">:: Pilih Vendor</option>
                    <?php foreach ($vendors->result_array() as $v) { ?>
                      <option value="<?= $v['Id'] ?>"><?= $v['nama'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-po">
                  <thead>
                    <tr>
                      <th style="width: 250px">Item</th>
                      <th>Jml</th>
                      <th style="width: 80px">Satuan</th>
                      <th>Harga</th>
                      <th>Total</th>
                      <th>Keterangan</th>
                      <th>#</th>
                    </tr>
                  </thead>
                  <tbody>
                    <div class="items">
                      <tr class="baris">
                        <td>
                          <input type="hidden" name="row[]" id="row">
                          <select name="item[]" id="item-0" class="form-control select2" width="100%">
                            <option value=""> :: Pilih Item :: </option>
                            <?php foreach ($item_list->result_array() as $il) { ?>
                              <option value="<?= $il['Id'] ?>"><?= $il['nama'] . " | " . $il['nomor'] ?></option>
                            <?php } ?>
                          </select>
                        </td>
                        <td>
                          <input type="text" class="form-control uang" name="qty[]" id="qty">
                          <div class="form-group">
                            <label for="uoi" class="form-label">UOI</label>
                            <select name="uoi[]" id="uoi" class="form-control">
                              <?php foreach ($uoi as $u) : ?>
                                <option value="<?= $u->satuan ?>"><?= $u->satuan ?></option>
                              <?php endforeach ?>
                            </select>
                          </div>
                        </td>
                        <td>
                          <input type="text" class="form-control uang" name="satuan[]" id="satuan">
                        </td>
                        <td>
                          <input type="text" class="form-control uang" name="harga[]" id="price">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="total[]" id="total" readonly>
                        </td>
                        <td>
                          <textarea name="ket[]" id="ket" class="form-control"></textarea>
                        </td>
                        <td>
                          <button type="button" class="btn btn-danger remove-form btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </td>
                      </tr>
                    </div>
                    <tr align="right">
                      <td colspan="4">TOTAL</td>
                      <td>
                        <input type="text" class="form-control" readonly name="nominal" id="nominal">
                      </td>

                    </tr>
                    <tr>
                      <td colspan="7">
                        <button type="button" class="btn btn-success btn-sm" id="add-more-form"><i class="fa fa-plus" aria-hidden="true"></i> Add new row</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="row">
                <div class="col-lg-12 text-end" style="padding: 0;">
                  <a href="<?= base_url('asset/po_list') ?>" class="btn btn-warning"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                  <button type="submit" class="btn btn-primary btn-submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                </div>
              </div>
            </form>
          <?php } else { ?>
            <form class="form-horizontal form-label-left input_mask" method="POST" action="<?= base_url('asset/simpan_update_po/' . $po['Id']) ?>" enctype="multipart/form-data" id="form-po">
              <div class="row" style="margin-bottom: 30px">
                <div class="col-md-2">
                  <label for="tanggal" class="form-label">Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?php echo date('Y-m-d', strtotime($po['tgl_pengajuan'])); ?>">
                </div>
                <div class="col-md-3">
                  <label for="tanggal" class="form-label">Vendor</label>
                  <select name="vendor" id="vendor" class="form-control">
                    <option value="">:: Pilih Vendor</option>
                    <?php foreach ($vendors->result_array() as $v) { ?>
                      <option value="<?= $v['Id'] ?>" <?= $po['vendor'] == $v['Id'] ? 'selected' : '' ?>><?= $v['nama'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 250px">Item</th>
                    <th>Jml</th>
                    <th style="width: 80px">Satuan</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>Keterangan</th>
                    <th>#</th>
                  </tr>
                </thead>
                <tbody>
                  <div class="items">
                    <?php
                    $po_detail = $this->cb->get_where('t_po_detail', ['no_po' => $po['Id']])->result_array();
                    foreach ($po_detail as $row) {
                    ?>
                      <tr class="baris">
                        <td>
                          <input type="hidden" name="row[]" id="row">
                          <select name="item[]" id="item-0" class="form-control select2" width="100%">
                            <option value=""> :: Pilih Item :: </option>
                            <?php foreach ($item_list->result_array() as $il) { ?>
                              <option value="<?= $il['Id'] ?>" <?= $row['item'] == $il['Id'] ? 'selected' : '' ?>><?= $il['nama'] . " | " . $il['nomor'] ?></option>
                            <?php } ?>
                          </select>
                        </td>
                        <td>
                          <input type="text" class="form-control uang" name="qty[]" id="qty" value="<?= $row['qty'] ?>">
                          <div class="form-group">
                            <label for="uoi" class="form-label">UOI</label>
                            <select name="uoi[]" id="uoi" class="form-control">
                              <?php foreach ($uoi as $u) : ?>
                                <option value="<?= $u->satuan ?>" <?= $row['uoi'] == $u->satuan ? 'selected' : '' ?>><?= $u->satuan ?></option>
                              <?php endforeach ?>
                            </select>
                          </div>
                        </td>
                        <td>
                          <input type="text" class="form-control uang" name="satuan[]" id="satuan" value="<?= $row['satuan'] ?>">
                        </td>
                        <td>
                          <input type="text" class="form-control uang" name="harga[]" id="price" value="<?= number_format($row['price'], 0, ',', '.') ?>">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="total[]" id="total" readonly value="<?= number_format($row['total'], 0, ',', '.') ?>">
                        </td>
                        <td>
                          <textarea name="ket[]" id="ket" class="form-control"><?= $row['keterangan'] ?></textarea>
                        </td>
                        <td>
                          <button type="button" class="btn btn-danger remove-form btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </td>
                      </tr>
                    <?php } ?>
                  </div>
                  <tr align="right">
                    <td colspan="4">TOTAL</td>
                    <td>
                      <input type="text" class="form-control" readonly name="nominal" id="nominal">
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="5">
                      <button type="button" class="btn btn-success btn-sm" id="add-more-form"><i class="fa fa-plus" aria-hidden="true"></i> Add new row</button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="row">
                <div class="col-lg-12 text-end">
                  <a href="<?= base_url('asset/po_list') ?>" class="btn btn-warning"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                  <button type="submit" class="btn btn-primary btn-submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> Update</button>
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
    $('select[name="vendor"]').select2({
      width: "100%"
    })

    $('#item-0').select2({
      width: '100%'
    });

    $('#detail-item-0').select2({
      width: '100%'
    });

    var rowCount = $(".baris").length;
    $('#add-more-form').click(function() {
      var row = '<tr class="baris"><td><input type="hidden" name="row[]" id="row"><select name="item[]" id="item-' + rowCount + '" class="form-control select2 item-out"><option value=""> :: Pilih Item :: </option><?php foreach ($item_list->result_array() as $il) { ?><option value="<?= $il['Id'] ?>"><?= $il['nama'] . " | " . $il['nomor'] ?></option><?php } ?></td><td><input type="text" class="form-control uang" name="qty[]" id="qty-' + rowCount + '"><div class="form-group"><label for="uoi" class="form-label">UOI</label><select name="uoi[]" id="uoi-' + rowCount + '" class="form-control"><?php foreach ($uoi as $u) { ?><option value="<?= $u->satuan ?>"><?= $u->satuan ?></option><?php } ?></select></div></td> <td><input type="text" class="form-control uang" name="satuan[]" id="satuan"></td><td><input type="text" class="form-control uang" name="harga[]" id="price-' + rowCount + '"></td><td><input type="text" class="form-control" name="total[]" id="total-' + rowCount + '" readonly></td><td><textarea name="ket[]" id="ket-' + rowCount + '" class="form-control"></textarea></td><td><button type="button" class="btn btn-danger btn-sm remove-form"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>';

      var previousRow = $('.baris').last();
      rowCount++;
      previousRow.after(row);

      $.each($(".select2"), function(index, value) {
        $('#item-' + index).select2({
          width: '100%'
        });

        $('#detail-item-' + index).select2({
          width: '100%'
        });
      });

      get_detail_item()
    });

    $(document).on("click", ".remove-form", function() {
      rowCount--;
      $(this).parents(".baris").remove();
      updateTotalItem();
    });
  })
</script>

<!-- Finish content-->