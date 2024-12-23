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
          <form action="<?= base_url('asset/export_report') ?>" method="post" target="_blank">
            <div class="row">
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form">
                  <label for="asset">Nama Asset</label>
                  <select name="asset" id="asset" class="form-control select2" required>
                    <option value=""> :: Pilih Item :: </option>
                    <option value="all"> ALL </option>
                    <?php foreach ($asset->result_array() as $a) { ?>
                      <option value="<?= $a['Id'] ?>"><?= $a['nama_asset'] . ' | ' ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2 col-sm-2 col-xs-12">
                <div class="form">
                  <label for="asset">Dari</label>
                  <input type="date" class="form-control" name="dari" id="dari" required>
                </div>
              </div>
              <div class="col-md-2 col-sm-2 col-xs-12">
                <div class="form">
                  <label for="asset">sampai</label>
                  <input type="date" class="form-control" name="sampai" id="sampai" value="<?= date('Y-m-d') ?>">
                </div>
              </div>
              <div class="col-md-2 col-sm-2 col-xs-12">
                <div class="form">
                  <label for="asset">Jenis File</label>
                  <select name="jenis-file" id="jenis-file" class="form-control select2">
                    <option value="excel">Excel</option>
                    <option value="pdf">PDF</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2" style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Export</button>
              </div>
            </div>
          </form>
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