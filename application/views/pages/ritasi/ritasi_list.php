<style>
    .clickable_table {
        cursor: pointer;
    }
</style>

<!-- page content -->
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <!-- Start content-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_title">
                    <h2>List Shift</h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <a href="<?= base_url('ritasi/create') ?>" class="btn btn-primary">Create Shift</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">No. Lambung</th>
                                    <th scope="col">Nama Driver</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Shift</th>
                                    <th scope="col">Jam</th>
                                    <th scope="col">KM Awal</th>
                                    <th scope="col">KM Akhir</th>
                                    <th scope="col">Total KM</th>
                                    <th scope="col">Harga KM</th>
                                    <th scope="col">Total Harga KM</th>
                                    <th scope="col">Total Tonase</th>
                                    <th scope="col">Harga Tonase</th>
                                    <th scope="col">Total Harga Tonase</th>
                                    <th scope="col">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$ritasi) { ?>
                                    <tr align="center">
                                        <td colspan="7">Belum ada data</td>
                                    </tr>
                                    <?php } else {
                                    $no = 1;
                                    foreach ($ritasi as $value) {
                                        $this->db->select('nama_asset');
                                        $this->db->from('asset_list');
                                        $this->db->where('Id', $value['nomor_lambung']);
                                        $get_aset = $this->db->get()->row();
                                        $nama_asset = $get_aset->nama_asset;


                                        $this->db->select('nama');
                                        $this->db->from('driver');
                                        $this->db->where('id', $value['id_driver']);
                                        $get_driver = $this->db->get()->row();
                                        $nama_driver = null;
                                        if ($get_driver) {
                                            $nama_driver = $get_driver->nama;
                                        }
                                    ?>
                                        <tr>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= $no ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= $nama_asset ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= $nama_driver ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= $value['tanggal'] ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= $value['shift'] ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= $value['jam_awal'] ?> - <?= $value['jam_akhir'] ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= number_format($value['km_awal']) ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= number_format($value['km_akhir']) ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= number_format($value['total_km']) ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= number_format($value['harga_km']) ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= number_format($value['total_km'] * $value['harga_km']) ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= number_format($value['total_tonase']) ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= number_format($value['harga_tonase']) ?></a></td>
                                            <td scope="row"><a class="clickable_table" onclick="Lihat_Detail(<?= $value['Id'] ?>)"><?= number_format($value['total_tonase'] * $value['harga_tonase']) ?></a></td>
                                            <td scope="row">
                                                <a href="<?= base_url('ritasi/ubah/' . $value['Id']) ?>" class="btn btn-success btn-xs">Update</a>
                                                <a href="<?= base_url('ritasi/hapus/' . $value['Id']) ?>" class="btn btn-danger btn-xs delete-btn">delete</a>
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
    <div class="clearfix"></div>
    <!-- Start content-->
    <div id="lihat_table_detail" class="row hidden">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_title">
                    <h2>Detail Retasi&nbsp;
                        <h2 id="id_retasi_in_detail"> </h2>
                    </h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <a onclick="oncreate_detail()" class="btn btn-primary">Create Ritasi</a>
                    </div>
                    <div id="create_detail" class="row hidden">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_content">
                                <!-- <form id="add_detail_form" class="form-horizontal form-label-left" method="POST" action="<?= base_url('ritasi/detail_insert') ?>" enctype="multipart/form-data" id="form-ritasi"> -->
                                <form class="form-horizontal form-label-left" enctype="multipart/form-data" id="form-detail-ritasi">
                                    <div class="row" style="margin-bottom: 30px">
                                        <!-- <div class="col-md-2 col-sm-4 col-xs-12">
                                    <label for="tanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?php echo date('Y-m-d'); ?>">
                                </div> -->
                                        <input type="hidden" class="form-control" name="id_header" id="id_header">
                                        <div class="col-md-3 col-sm-4 col-xs-12">
                                            <label for="no_rekening" class="form-label">Lokasi Loading</label>
                                            <input type="text" class="form-control" name="lokasi_loading" id="lokasi_loading">
                                        </div>
                                        <div class="col-md-3 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label">Tujuan</label>
                                            <input type="text" class="form-control" name="tujuan" id="tujuan">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label">Jam</label>
                                            <input type="time" class="form-control" name="jam" id="jam">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label">Berat Awal</label>
                                            <input type="number" class="form-control" name="berat_awal" id="berat_awal">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label">Berat Akhir</label>
                                            <input type="number" class="form-control" name="berat_akhir" id="berat_akhir">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12" style="margin-top: 5px;">
                                            <label for="metode" class="form-label">KM</label>
                                            <input type="number" class="form-control" name="km" id="km">
                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-lg-12 text-end">
                                        <a onclick="tutup_create()" class="btn btn-warning">Back</a>
                                        <button onclick="" type="submit" class="btn btn-primary" id="btn-save-detail">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="edit_detail" class="row hidden">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_content">
                                <form class="form-horizontal form-label-left" enctype="multipart/form-data" id="form-edit-detail-ritasi">
                                    <div class="row" style="margin-bottom: 30px">
                                        <input type="hidden" class="form-control" name="id_header_edit" id="id_header_edit">
                                        <div class="col-md-3 col-sm-4 col-xs-12">
                                            <label for="no_rekening" class="form-label">Lokasi Loading</label>
                                            <input type="text" class="form-control" name="lokasi_loading" id="lokasi_loading_edit">
                                        </div>
                                        <div class="col-md-3 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label">Tujuan</label>
                                            <input type="text" class="form-control" name="tujuan" id="tujuan_edit">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label">Jam</label>
                                            <input type="time" class="form-control" name="jam" id="jam_edit">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label">Berat Awal</label>
                                            <input type="number" class="form-control" name="berat_awal" id="berat_awal_edit">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label">Berat Akhir</label>
                                            <input type="number" class="form-control" name="berat_akhir" id="berat_akhir_edit">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label" style="margin-top: 5px;">Total Tonase</label>
                                            <input type="number" class="form-control" name="total_tonase" id="total_tonase_edit">
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label" style="margin-top: 5px;">KM</label>
                                            <input type="number" class="form-control" name="km" id="km_edit">
                                        </div>

                                        <div class="col-md-2 col-sm-4 col-xs-12">
                                            <label for="metode" class="form-label" style="margin-top: 5px;">total_KM</label>
                                            <input type="number" class="form-control" name="total_km" id="total_km_edit">
                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-lg-12 text-end">
                                        <a onclick="tutup_edit()" class="btn btn-warning">Back</a>
                                        <button onclick="save_edit()" type="submit" class="btn btn-primary" id="btn-save-detail_edit">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="table_detail" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Lokasi Loading</th>
                                    <th scope="col">Tujuan</th>
                                    <th scope="col">Jam</th>
                                    <th scope="col">Berat Awal</th>
                                    <th scope="col">Berat Akhir</th>
                                    <th scope="col">Total Tonase</th>
                                    <th scope="col">KM</th>
                                    <th scope="col">Total KM</th>
                                    <th scope="col">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <!-- <a href="<?= base_url('ritasi/ubah/') ?>" class="btn btn-success btn-xs">Update</a>
                                        <a href="<?= base_url('ritasi/hapus/') ?>" class="btn btn-danger btn-xs delete-btn">delete</a> -->
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5"></th>
                                    <th scope="col">Total</th>
                                    <th scope="col" id="total_tonase_table">0</th>
                                    <th scope="col">Total</th>
                                    <th scope="col" id="total_km_table">0</th>
                                </tr>
                            </tfoot>
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
            var formData = new FormData($("form#form-ritasi")[0]); // Get form data

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


    function Lihat_Detail(id) {
        const rowElement = document.getElementById('lihat_table_detail');
        var url = "<?= base_url('ritasi/detail_list/') ?>" + id;

        $.ajax({
            url: url,
            method: "GET",
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
                    populateTable(res.data);
                    rowElement.classList.remove('hidden');
                    rowElement.classList.add('show');
                    $('#id_retasi_in_detail').text(id);
                    $('#id_header').val(id);

                    $('#btn-save-detail').on('click', function() {
                        add_detail(id);
                    });
                    Swal.close();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: `${res.msg}`,
                        showConfirmButton: false,
                        timer: 1500,
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: `Error: ${error}`,
                    showConfirmButton: false,
                    timer: 1500,
                });
            }
        });
    }

    function populateTable(data) {
        const tableBody = document.querySelector("#table_detail tbody");
        tableBody.innerHTML = ""; // Clear existing rows
        var no = 1;
        let totalKm = 0; // Variable to store the sum of 'km'
        let totalTonase = 0; // Variable to store the sum of 'km'
        data.forEach(item => {
            const row = document.createElement("tr");

            row.innerHTML = `
            <td>${no}</td>
            <td>${item.lokasi_loading}</td>
            <td>${item.tujuan}</td>
            <td>${item.jam}</td>
            <td>${item.berat_awal}</td>
            <td>${item.berat_akhir}</td>
            <td>${item.total_tonase}</td>
            <td>${item.km}</td>
            <td>${item.total_km}</td>
            <td>
            <a onclick="update_detail(${item.Id})" class="btn btn-success btn-xs">Update</a>
            <a onclick="hapus_detail(${item.Id})" class="btn btn-danger btn-xs delete-btn">delete</a>
            </td>
        `;

            totalKm += parseFloat(item.total_km) || 0;
            totalTonase += parseFloat(item.total_tonase) || 0;

            no++;
            tableBody.appendChild(row);
        });
        $('#total_tonase_table').text(totalTonase); // Update the total km value in the table
        $('#total_km_table').text(totalKm); // Update the total km value in the table
    }

    function oncreate_detail() {
        // Select the element by its ID
        const rowElement = document.getElementById('create_detail');

        // Check if the element exists and has the 'hidden' class
        if (rowElement.classList.contains('hidden')) {
            // Remove the 'hidden' class
            rowElement.classList.remove('hidden');

            // Add the 'show' class
            rowElement.classList.add('show');
        } else {
            rowElement.classList.remove('show');

            // Add the 'show' class
            rowElement.classList.add('hidden');
        }
    }

    function tutup_create() {
        const rowElement = document.getElementById('create_detail');
        rowElement.classList.remove('show');
        rowElement.classList.add('hidden');
    }

    function add_detail(id) {
        var url;
        var formData;
        url = "<?php echo site_url('ritasi/insert_detail') ?>";

        // window.location = url_base;
        var formData = new FormData($("#form-detail-ritasi")[0]);

        Swal.fire({
            title: "Are you sure?",
            text: "You want to Add this?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url, // Use the URL from the <a> tag
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
    }

    function update_detail(id) {
        $('#form-detail-ritasi')[0].reset(); // reset form on modals
        // $('.form-group').removeClass('has-error'); // clear error class
        // $('.help-block').empty(); // clear error string
        // $('.modal-title').text('Edit Poster');
        console.log('bisa 1')
        $.ajax({
            url: "<?php echo site_url('ritasi/detail_edit/') ?>" + id,
            type: "POST",
            dataType: "JSON",
            success: function(data) {

                JSON.stringify(data.Id);
                // alert(JSON.stringify(data));
                $('#id_header_edit').val(data.Id);
                $('#lokasi_loading_edit').val(data.lokasi_loading);
                $('#tujuan_edit').val(data.tujuan);
                $('#jam_edit').val(data.jam);
                $('#berat_awal_edit').val(data.berat_awal);
                $('#berat_akhir_edit').val(data.berat_akhir);
                $('#total_tonase_edit').val(data.total_tonase);
                $('#km_edit').val(data.km);
                $('#total_km_edit').val(data.total_km);
                $('#edit_detail').removeClass('hidden');
                $('#edit_detail').addClass('show');
                // $('#halaman_page_edit').val(data.halaman_page);
                console.log(data)


            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function save_edit() {
        var url;
        var formData;
        url = "<?php echo site_url('ritasi/edit_detail') ?>";

        // window.location = url_base;
        var formData = new FormData($("#form-edit-detail-ritasi")[0]);

        Swal.fire({
            title: "Are you sure?",
            text: "You want to Edit this?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url, // Use the URL from the <a> tag
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
                                // location.reload();
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
    }

    function hapus_detail(id) {
        var url;
        var formData;
        url = "<?php echo site_url('ritasi/delete_detail/') ?>" + id;

        Swal.fire({
            title: "Are you sure?",
            text: "You want to Delete this?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url, // Use the URL from the <a> tag
                    method: "POST",
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
    }

    function tutup_edit() {
        const rowElement = document.getElementById('edit_detail');
        rowElement.classList.remove('show');
        rowElement.classList.add('hidden');
    }
</script>