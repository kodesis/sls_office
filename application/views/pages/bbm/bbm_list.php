<!-- page content -->
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <!-- Start content-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_title">
                    <h2>List Bbm</h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <a href="<?= base_url('bbm/create') ?>" class="btn btn-primary">Create Bbm</a>
                        <a href="<?= base_url('bbm/ubah_harga_bbm') ?>" class="btn btn-primary">Ubah Harga Bbm</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">No. Lambung</th>
                                    <th scope="col">User</th>
                                    <!-- <th scope="col">RIT</th> -->
                                    <th scope="col">Total Harga</th>
                                    <!-- <th scope="col">KM</th> -->
                                    <th scope="col">Total Liter</th>
                                    <th scope="col">Harga/Liter</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$bbm) { ?>
                                    <tr align="center">
                                        <td colspan="7">Belum ada data</td>
                                    </tr>
                                    <?php } else {
                                    $no = 1;
                                    foreach ($bbm as $value) {  ?>
                                        <tr>
                                            <td scope="row"><?= $no ?></td>
                                            <td scope="row"><?= $value['nomor_lambung'] ?></td>
                                            <?php
                                            $this->db->select('nama');
                                            $this->db->from('users');
                                            $this->db->where('nip', $value['user_id']);
                                            $users = $this->db->get()->row();
                                            ?>
                                            <td scope="row"><?= $users->nama ?></td>
                                            <td scope="row"><?= $value['total_harga'] ?></td>
                                            <td scope="row"><?= $value['total_liter'] ?></td>
                                            <td scope="row"><?= $value['harga_per_liter'] ?></td>
                                            <td scope="row"><?= $value['tanggal'] ?></td>
                                            <td scope="row">
                                                <a href="<?= base_url('bbm/ubah/' . $value['Id']) ?>" class="btn btn-success btn-xs">Update</a>
                                                <a href="<?= base_url('bbm/hapus/' . $value['Id']) ?>" class="btn btn-danger btn-xs delete-btn">delete</a>
                                            </td>
                                    <?php
                                        $no++;
                                    }
                                }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row text-center">
                        <!-- <?= $pagination ?> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".delete-btn").click(function(e) {
            e.preventDefault(); // Prevent the default action of the anchor tag

            var deleteUrl = $(this).attr('href'); // Get the URL from the href attribute
            var formData = new FormData($("form#form-bbm")[0]); // Get form data

            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl, // Use the URL from the <a> tag
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
        });
    });
</script>