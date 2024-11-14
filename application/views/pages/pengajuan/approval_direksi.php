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

          <h2>List Approval Direksi</h2>

        </div>

        <div class="x_content">

          <a href="<?= base_url('pengajuan/list') ?>" class="btn btn-warning">Back</a>

          <div class="table-responsive">

            <table class="table table-bordered">

              <thead>

                <tr>

                  <th scope="col">No.</th>

                  <th scope="col">User</th>

                  <th scope="col">Tanggal</th>

                  <th scope="col">Total</th>

                  <th scope="col">Status</th>

                  <th scope="col">Posisi</th>

                  <th scope="col">#</th>

                </tr>

              </thead>

              <tbody>

                <?php if ($approval_direksi->num_rows() < 1) {  ?>

                  <tr align="center">

                    <td colspan="7">Belum ada data</td>

                  </tr>

                  <?php } else {

                  foreach ($approval_direksi->result_array() as $value) {

                    $user = $this->db->get_where('users', ['nip' => $value['user']])->row_array();

                  ?>

                    <tr>

                      <td><?= $value['no_pengajuan'] ?></td>

                      <td><?= $user['nama'] ?></td>

                      <td><?= $value['created_at'] ?></td>

                      <td><?= number_format($value['total']) ?></td>

                      <td>

                        <?php if ($value['status_direksi'] == 1) {

                          echo "Disetujui";
                        } elseif ($value['status_direksi'] == 2) {

                          echo "Ditolak";
                        } else {

                          echo "Belum diproses";
                        } ?>

                      </td>

                      <td><?= $value['posisi'] ?></td>

                      <td>

                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal<?= $value['Id'] ?>">View</button>



                        <!-- Modal Detail -->

                        <div class="modal fade" id="myModal<?= $value['Id'] ?>" role="dialog">

                          <div class="modal-dialog modal-lg">

                            <!-- Modal content-->

                            <div class="modal-content">

                              <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal">&times;</button>

                                <h2 class="modal-title">Pengajuan <?= $value['no_pengajuan'] ?></h2>

                              </div>

                              <div class="modal-body">

                                <table class="table table-bordered">

                                  <thead>

                                    <tr>

                                      <th>No.</th>

                                      <th>Item</th>

                                      <th>Qty</th>

                                      <th>Price</th>

                                      <th>Total</th>

                                    </tr>

                                  </thead>

                                  <tbody>

                                    <?php $detail = $this->cb->get_where('t_pengajuan_detail', ['no_pengajuan' => $value['no_pengajuan']])->result_array();

                                    $no = 1;

                                    foreach ($detail as $row) {

                                    ?>

                                      <tr>

                                        <td><?= $no++ ?></td>

                                        <td><?= $row['item'] ?></td>

                                        <td><?= $row['qty'] ?></td>

                                        <td><?= number_format($row['price'], 0) ?></td>

                                        <td><?= number_format($row['total'], 0) ?></td>

                                      </tr>

                                    <?php } ?>

                                    <tr>

                                      <td colspan="4" align="right"><strong>TOTAL</strong></td>

                                      <td><?= number_format($value['total']) ?></td>

                                    </tr>

                                  </tbody>

                                </table>

                                <div class="panel panel-default">

                                  <div class="panel-heading">Catatan</div>

                                  <div class="panel-body">

                                    <?= $value['catatan'] ?>

                                  </div>

                                </div>

                                <div class="panel panel-default">

                                  <div class="panel-heading">Attacment</div>

                                  <div class="panel-body">

                                    <a href="<?= base_url('upload/pengajuan/' . $value['bukti_pengajuan']) ?>" class="btn btn-success btn-xs" download="">Download</a></th>

                                  </div>

                                </div>

                                <?php if ($value['status_direksi'] == 0) { ?>

                                  <form action="<?= base_url('pengajuan/update_direksi') ?>" method="post" enctype="multipart/form-data" id="update-direksi">

                                    <input type="hidden" name="id_pengajuan" id="id_pengajuan" value="<?= $value['Id'] ?>">

                                    <div class="form-group">

                                      <div class="row" style="margin-bottom: 20px;">

                                        <div class="col-md-8">

                                          <label for="status" class="form-label">Total Pengajuan</label>

                                          <input type="text" class="form-control" name="total" id="total<?= $value['Id'] ?>" value="<?= number_format($value['total']) ?>" readonly>

                                        </div>

                                      </div>

                                      <div class="row" style="margin-bottom: 20px;">

                                        <div class="col-md-8">

                                          <label for="status" class="form-label">Status</label>

                                          <select name="status" id="status<?= $value['Id'] ?>" class="form-control">

                                            <option value=""> -- Pilih Status -- </option>

                                            <option value="1">Disetujui</option>

                                            <option value="2">Ditolak</option>

                                          </select>

                                        </div>

                                      </div>

                                      <div class="row" style="margin-bottom: 20px;">

                                        <div class="col-md-8">

                                          <label for="catatan" class="form-label">Catatan</label>

                                          <textarea name="catatan" id="catatan<?= $value['Id'] ?>" class="form-control"></textarea>

                                        </div>

                                      </div>

                                    </div>

                                    <div class="row">

                                      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                                      <button class="btn btn-primary" id="btn-direksi">Save</button>

                                    </div>

                                  </form>

                                <?php } ?>

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

        </div>

      </div>

    </div>

  </div>

</div>
<script>
  $(document).ready(function() {

    $('.select2').select2();



    $("button[id='btn-direksi']").click(function(e) {

      var url = $('form[id="update-direksi"]').attr("action");

      var formData = new FormData($("form#update-direksi")[0]);

      e.preventDefault();

      Swal.fire({

        title: "Are you sure?",

        text: "You want to submit the form?",

        icon: "warning",

        showCancelButton: true,

        confirmButtonColor: "#3085d6",

        cancelButtonColor: "#d33",

        confirmButtonText: "Yes",

      }).then((result) => {

        if (result.isConfirmed) {

          $.ajax({

            url: url,

            method: "POST",

            data: formData,

            processData: false,

            contentType: false,

            dataType: "JSON",

            beforeSend: () => {

              Swal.fire({

                title: "Loading....",

                timerProgressBar: true,

                allowOutsideClick: false,

                didOpen: () => {

                  Swal.showLoading();

                },

              });

            },

            success: function(res) {

              if (res.success) {

                Swal.fire({

                  icon: "success",

                  title: `${res.msg}`,

                  showConfirmButton: false,

                  timer: 1500,

                }).then(function() {

                  Swal.close();

                  location.reload();

                });

              } else {

                Swal.fire({

                  icon: "error",

                  title: `${res.msg}`,

                  showConfirmButton: false,

                  timer: 1500,

                }).then(function() {

                  Swal.close();

                });

              }

            },

            error: function(xhr, status, error) {

              Swal.fire({

                icon: "error",

                title: `${error}`,

                showConfirmButton: false,

                timer: 1500,

              });

            },

          });

        }

      });

    })

  })
</script>

<script>
  function formatNumber(number) {

    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  }
</script>

</body>



</html>