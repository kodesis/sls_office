<style>
  .select2-container--default .select2-selection--multiple,
  .select2-container--default .select2-selection--single {
    min-height: 34px;
    height: 34px;
  }

  .padding-0 {
    padding: 0;
  }

  @media screen and (max-width:991px) {
    table#item {
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
          <h2>Form Release Order</h2>
        </div>
        <div class="x_content">
          <?php if (!$this->uri->segment(3)) { ?>
            <form class="form-horizontal form-label-left input_mask" method="POST" action="<?= base_url('asset/save_release_order') ?>" enctype="multipart/form-data" id="form-po">
              <div class="row" style="margin-bottom: 30px">
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <label for="tanggal" class="form-label">Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                  <label for="teknisi" class="form-label">Nama Teknisi</label>
                  <input type="text" class="form-control" name="teknisi" id="teknisi">
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered" id="item-out">
                  <div class="items">
                    <tr class="baris-out">
                      <input type="hidden" name="row[]" id="row">
                      <td width="250px">
                        <div class="form-group">
                          <div>
                            <label for="asset" class="form-label">Asset</label>
                          </div>
                          <select name="asset[]" id="asset-0" class="form-control asset select2">
                            <option value=""> :: Pilih Asset :: </option>
                            <?php
                            $asset = $this->db->get('asset_list')->result_array();
                            foreach ($asset as $row) {
                            ?>
                              <option value="<?= $row['Id'] ?>"><?= $row['nama_asset'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group">
                          <div>
                            <label for="item" class="form-label">Item</label>
                          </div>
                          <select name="item[]" id="item-0" class="form-control item-out select2" width="100%">
                            <option value=""> :: Pilih Item :: </option>
                            <?php foreach ($item_list->result_array() as $il) { ?>
                              <option value="<?= $il['Id'] ?>"><?= $il['nama'] . " | " . $il['nomor'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div>
                            <label for="uoi" class="form-label">QTY</label>
                          </div>
                          <input type="text" class="form-control uang" name="qty_out[]" id="qty_out-0">
                        </div>
                        <div class="form-group">
                          <div>
                            <label for="uoi" class="form-label">UOI</label>
                          </div>
                          <select name="uoi[]" id="uoi" class="form-control uoi select2">
                            <?php foreach ($uoi as $u) : ?>
                              <option value="<?= $u->satuan ?>"><?= $u->satuan ?></option>
                            <?php endforeach ?>
                          </select>
                        </div>
                      </td>
                      <td>
                        <label for="harga_out" class="form-label">Harga</label>
                        <input type="text" class="form-control uang" name="harga_out[]" id="price_out-0" readonly>
                      </td>
                      <td>
                        <label for="total" class="form-label">TOTAL</label>
                        <input type="text" class="form-control uang" name="total_out[]" id="total_out-0" readonly>
                      </td>
                      <td>
                        <label for="ket" class="form-label">Keterangan</label>
                        <textarea name="ket[]" id="ket-0" class="form-control"></textarea>
                      </td>
                      <td>
                        <button type="button" class="btn btn-danger btn-xs remove-form-out" style="margin-top: 20px;"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-success btn-xs add-more-form-out"><i class="fa fa-plus" aria-hidden="true"></i></button>
                      </td>
                    </tr>
                  </div>
                  <tr align="right">
                    <td colspan="3">TOTAL</td>
                    <td>
                      <input type="text" class="form-control" readonly name="nominal-out" id="nominal-out">
                    </td>
                    <td></td>
                  </tr>
                </table>
              </div>
              <div class="row">
                <div class="col-lg-12 text-end padding-0">
                  <a href="<?= base_url('asset/ro_list') ?>" class="btn btn-warning">Back</a>
                  <button type="submit" class="btn btn-primary btn-submit">Save</button>
                </div>
              </div>
            </form>
          <?php } else { ?>
            <form class="form-horizontal form-label-left input_mask" method="POST" action="<?= base_url('asset/simpan_update_ro/' . $ro['Id']) ?>" enctype="multipart/form-data" id="form-po">
              <div class="row" style="margin-bottom: 30px">
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <label for="tanggal" class="form-label">Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?php echo date('Y-m-d', strtotime($ro['tgl_pengajuan'])); ?>">
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                  <label for="teknisi" class="form-label">Nama Teknisi</label>
                  <input type="text" class="form-control" name="teknisi" id="teknisi" value="<?= $ro['teknisi'] ?>">
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered" id="item-out">
                  <?php
                  $i = 1;
                  $ro_detail = $this->cb->get_where('t_ro_detail', ['no_ro' => $ro['Id']])->result_array();
                  foreach ($ro_detail as $rd) {
                  ?>
                    <tr class="baris-out">
                      <input type="hidden" name="row[]" id="row-<?= $i ?>">
                      <td width="250px">
                        <div class="form-group">
                          <div>
                            <label for="asset" class="form-label">Asset</label>
                          </div>
                          <select name="asset[]" id="asset-<?= $i ?>" class="form-control asset select2">
                            <option value=""> :: Pilih Asset :: </option>
                            <?php
                            $asset = $this->db->get('asset_list')->result_array();
                            foreach ($asset as $row) {
                            ?>
                              <option value="<?= $row['Id'] ?>" <?= $rd['asset'] == $row['Id'] ? 'selected' : '' ?>><?= $row['nama_asset'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group">
                          <div>
                            <label for="item" class="form-label">Item</label>
                          </div>
                          <select name="item[]" id="item-<?= $i ?>" class="form-control item-out select2" width="100%">
                            <option value=""> :: Pilih Item :: </option>
                            <?php foreach ($item_list->result_array() as $il) { ?>
                              <option value="<?= $il['Id'] ?>" <?= $rd['item'] == $il['Id'] ? 'selected' : '' ?>><?= $il['nama'] . " | " . $il['nomor'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div>
                            <label for="uoi" class="form-label">QTY</label>
                          </div>
                          <input type="text" class="form-control uang" name="qty_out[]" id="qty_out-<?= $i ?>" value="<?= $rd['qty'] ?>">
                        </div>
                        <div class="form-group">
                          <div>
                            <label for="uoi" class="form-label">UOI</label>
                          </div>
                          <select name="uoi[]" id="uoi-<?= $rd['Id'] ?>" class="form-control uoi select2">
                            <?php foreach ($uoi as $u) : ?>
                              <option value="<?= $u->satuan ?>" <?= $rd['uoi'] == $u->satuan ? 'selected' : '' ?>><?= $u->satuan ?></option>
                            <?php endforeach ?>
                          </select>
                        </div>
                      </td>
                      <td>
                        <label for="harga_out" class="form-label">Harga</label>
                        <input type="text" class="form-control uang" name="harga_out[]" id="price_out-<?= $i ?>" readonly value="<?= number_format($rd['price'], 0, ',', '.') ?>">
                      </td>
                      <td>
                        <label for="total" class="form-label">TOTAL</label>
                        <input type="text" class="form-control uang" name="total_out[]" id="total_out-<?= $i ?>" readonly value="<?= number_format($rd['total'], 0, ',', '.') ?>">
                      </td>
                      <td>
                        <label for="ket" class="form-label">Keterangan</label>
                        <textarea name="ket[]" id="ket-<?= $i ?>" class="form-control"><?= $rd['keterangan'] ?></textarea>
                      </td>
                      <td>
                        <button type="button" class="btn btn-danger btn-xs remove-form-out" style="margin-top: 20px;"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-success btn-xs add-more-form-out"><i class="fa fa-plus" aria-hidden="true"></i></button>
                      </td>
                    </tr>
                  <?php
                    $i++;
                  } ?>
                  <tr align="right">
                    <td colspan="3">TOTAL</td>
                    <td>
                      <input type="text" class="form-control" readonly name="nominal-out" id="nominal-out">
                    </td>
                    <td></td>
                  </tr>
                </table>
              </div>
              <div class="row">
                <div class="col-lg-12 text-end padding-0">
                  <a href="<?= base_url('asset/ro_list') ?>" class="btn btn-warning">Back</a>
                  <button type="submit" class="btn btn-primary btn-submit">Save</button>
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
    // get_detail_item();
    setSelect2();
    updateTotalItemOut();
    get_detail_item()

    // var rowCountOut = $(".baris-out").length;

    $('table#item-out').on('click', '.add-more-form-out', function() {
      var row = $(this).parents().closest('tr');
      var newId = Date.now();

      row.find("select.select2").each(function(index, value) {
        $(this).select2('destroy');
      });

      // row.find("select.asset").each(function(index, value) {
      //   $(this).select2('destroy');
      // });

      // row.find("select.item-out").each(function(index, value) {
      //   $(this).select2('destroy');
      // });
      // Membuat baris baru
      var newRow = row.clone();

      newRow.find('select.uoi').each(function(index, value) {
        $(this).val('');
        $(this).attr('id', 'uoi-' + newId)
        $(this).select2({
          width: "100%"
        })
      })

      newRow.find('select.asset').each(function(index, value) {
        $(this).val('');
        $(this).attr('id', 'asset-' + newId)
        $(this).select2({
          width: "100%"
        })
      })

      newRow.find('select.item-out').each(function(index, value) {
        $(this).val('');
        $(this).attr('id', newId)
        $(this).select2({
          width: "100%"
        });
      })

      newRow.find('input[name="qty_out[]"]').each(function(index, value) {
        $(this).attr('id', 'qty-' + newId)
        $(this).val('0');
      })
      newRow.find('input[name="harga_out[]"]').each(function(index, value) {
        $(this).attr('id', newId)
        $(this).val('0');
      })
      newRow.find('input[name="total_out[]"]').each(function(index, value) {
        $(this).attr('id', 'total-' + newId)
        $(this).val('0');
      })

      newRow.insertAfter(row);
      // $("select.asset").select2();
      // $("select.item-out").select2();
      // $("select.uoi").select2();
      setSelect2();
      updateTotalItemOut();
      get_detail_item()
    })

    $('table#item-out').on('click', '.remove-form-out', function() {
      if (countBaris() > 1) {
        $(this).closest('tr').remove();
        updateTotalItemOut();
      } else {
        Swal.fire({
          icon: "error",
          title: "The first form can't be deleted",
          showConfirmButton: false,
          timer: 1500,
        }).then(function() {
          Swal.close();
        });
      }
    });

    // $(document).on("click", ".remove-form-out", function() {
    //   rowCountOut--;
    //   $(this).parents(".baris-out").remove();
    //   updateTotalItemOut();
    // });
  })

  $(document).on(
    "input",
    'input[name="qty_out[]"], input[name="harga_out[]"]',
    function() {
      // var value = $(this).val();
      var row_out = $(this).closest(".baris-out");
      hitungTotalOut(row_out);
      updateTotalItemOut();
    }
  );
</script>

<script>
  function setSelect2() {
    let select2 = document.querySelectorAll('select.select2');
    for (let index = 0; index < select2.length; index++) {
      $("." + select2[index].classList[1] + "").select2({
        width: "100%"
      })
    }
  }

  function countBaris() {
    var jmlBaris = $('.baris-out').length;
    return jmlBaris;
  }

  function hitungTotalOut(row) {
    var qty = row.find('input[name="qty_out[]"]').val().replace(/\./g, ""); // Hapus tanda titik
    var harga = row.find('input[name="harga_out[]"]').val().replace(/\./g, ""); // Hapus tanda titik
    qty = parseInt(qty); // Ubah string ke angka float
    harga = parseInt(harga); // Ubah string ke angka float

    qty = isNaN(qty) ? 0 : qty;
    harga = isNaN(harga) ? 0 : harga;

    var total = qty * harga;
    row.find('input[name="total_out[]"]').val(formatNumber(total));
    updateTotalItemOut();
  }

  function updateTotalItemOut() {
    var total_pos_fix = 0;
    $(".baris-out").each(function() {
      var total = $(this)
        .find('input[name="total_out[]"]')
        .val()
        .replace(/\./g, ""); // Ambil nilai total dari setiap baris
      total = parseFloat(total); // Ubah string ke angka float
      if (!isNaN(total)) {
        // Pastikan total adalah angka
        total_pos_fix += total; // Tambahkan nilai total ke total_pos_fix
      }
    });
    $("input[name='nominal-out']").val(formatNumber(total_pos_fix)); // Atur nilai input #nominal dengan total_pos_fix
  }

  function get_detail_item() {
    var amount = document.querySelectorAll('input[name="harga_out[]"]');
    $.each($("select.item-out"), function(index, value) {
      $('#' + value.id).change(function() {
        var id = $(this).val();
        $.ajax({
          url: "<?= base_url('asset/getItemById/') ?>",
          type: "POST",
          chace: false,
          data: {
            id: id,
          },
          dataType: "JSON",
          success: function(res) {
            var harga = res.harga;
            $('input[id="' + amount[index].id + '"]').val(harga.replace(/\,/g, "."));
            updateTotalItemOut();
          }
        })
      });
    })
  }
</script>
<!-- Finish content-->