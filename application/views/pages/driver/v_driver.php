<!-- DataTables -->
<link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
<link href="<?= base_url() ?>assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
<div class="right_col" role="main">

  <div class="x_panel card">
    <?php if ($this->session->flashdata('success_reset')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('success_reset'); ?>
      </div>
    <?php } ?>
    <?php if ($this->session->flashdata('msg')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
      </div>
    <?php } ?>

    <!-- search -->
    <form data-parsley-validate action="<?php echo base_url(); ?>driver/driver_cari" method="post" name="form_input" id="form_input">
      <label class="control-label col-md-1 col-sm-1 col-xs-4" for="cari_nama">Filter
        <span class="required">*</span>
      </label>
      <div class="col-md-6 col-sm-6 col-xs-8">
        <input type="text" id="search" name="search" class="form-control col-md-7 col-xs-12" placeholder="isi nama atau nip">
      </div>
      <?php echo form_submit('cari_user', 'Cari', 'class="btn btn-primary"'); ?>
      <input type="button" class="btn btn-primary" value="Tampilkan Semua" onclick="window.location.href='<?php echo base_url(); ?>driver/list'" />
      <a href="<?= base_url('driver/add_driver') ?>" class="btn btn-success">Add Driver</a>
    </form>


    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th bgcolor="#34495e">
              <font color="white">No.</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Nama</font>
            </th>
            <!-- <th bgcolor="#34495e">
          <font color="white">Level</font>
        </th> -->
            <th bgcolor="#34495e">
              <font color="white">Nip</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Status</font>
            </th>

            <!--th bgcolor="#008080"><font color="white">Status</font></th-->
            <th bgcolor="#34495e">
              <font color="white">Detail</font>
            </th>
          </tr>
        </thead>
        <?php
        if ($this->uri->segment(3) == '') {
          $no = 1;
        } else {
          $no = $this->uri->segment(3) + 1;
        }
        if (empty($users_data)) {
        ?>

          <?php
        } else {
          foreach ($users_data as $data) :
          ?>
            <!--content here-->
            <tbody>
              <tr>
                <?php
                // $nip = $this->session->userdata('nip');
                // $kalimat = $data->read;
                //if (preg_match("/$nip/i", $kalimat)) { 
                ?>
                <p style="font-weight: normal;">
                  <td><?php echo $no; ?></td>
                  <td><?php echo $data->nama; ?></td>
                  <!-- <td><?php echo $data->level; ?></td> -->
                  <td><?php echo $data->nip; ?></td>
                  <td>
                    <?php
                    if ($data->status == 1) {
                      echo 'Active';
                    }
                    if ($data->status == 0) {
                      echo 'Not Active';
                    }
                    ?>
                  </td>

                  <td>
                    <!-- <form action="<?php echo base_url() . "app/user_view/" . $data->id; ?>" target="">
								<button type="submit" class="btn btn-dark btn-xs">Open</button>
							</form> -->
                    <a class="btn btn-warning btn-xs" href="<?= base_url('driver/driver_edit/' . $data->id . '/e') ?>">Edit</a>
                    <a class="btn btn-danger btn-xs" href="<?= base_url('driver/delete/' . $data->id) ?>" id="btn-reset-pass">Delete</a>
                  </td>

                  <!--td>
						<form action="<?php echo base_url() . "app/surat_keluar_edit/" . $data->id; ?>">
							<button type="submit" class="btn btn-warning btn-xs">Edit</button>
						</form>
					</td-->
              </tr>
            </tbody>

        <?php
            $no++;
          endforeach;
        }
        ?>
      </table>
    </div>

    <!--pagination-->
    <div class="row col-12 text-center">
      <?php echo $pagination; ?>
    </div>

    <!-- DataTables -->
    <script src="<?= base_url() ?>assets/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="<?= base_url() ?>assets/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script>
      $(document).ready(function() {
        $("a[id='button-reset-cuti']").click(function(e) {
          if (!confirm('Apakah anda yakin ingin mereset cuti?')) {
            e.preventDefault();
          }

        });

        <?php if ($this->session->flashdata('error')) { ?>
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= $this->session->flashdata('error') ?>',
          })
        <?php } ?>

        $("button[id='btn-hapus-tgl-libur']").click(function(e) {
          if (!confirm('Apakah anda yakin ingin menghapus tanggal libur tersebut?')) {
            e.preventDefault();
          }
        });


        $('#myTable').dataTable();
      })
    </script>