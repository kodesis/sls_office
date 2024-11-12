<!-- page content -->
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <!-- Start content-->
    <!-- tes -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_title">
                    <h2>Cari Detail Nomor Lambung</h2>
                </div>
                <div class="x_content">
                    <div class="col-md-8 col-sm-12 col-xs-12">
                        <form class="form-horizontal form-label-left" id="form-detail">
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-md-6 col-sm-4 col-xs-12">
                                    <label for="metode" class="form-label">ID Asset</label>
                                    <!-- <select class="form-control" name="id_asset" id="id_asset">
                                    <option selected value="All">All</option>
                                    <?php
                                    foreach ($asset as $a) {
                                    ?>
                                        <option value="<?= $a->Id ?>"><?= $a->Id ?> - <?= $a->nama_asset ?></option>
                                    <?php
                                    }
                                    ?>
                                </select> -->
                                    <select class="form-control" name="id_asset" id="id_asset">
                                        <option value="ALL">ALL</option>
                                        <?php foreach ($asset as $a) : ?>
                                            <option value="<?= $a->Id ?>"><?= $a->Id ?> - <?= $a->nama_asset ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 30px">
                                <div class="col-md-3 col-sm-4 col-xs-12">
                                    <label for="no_rekening" class="form-label">Tahun</label>
                                    <input type="number" class="form-control" name="tahun_cari" id="tahun_cari" placeholder="Pilih Tahun" min="2000" max="2500">
                                </div>
                                <div class="col-md-3 col-sm-4 col-xs-12">
                                    <label for="metode" class="form-label">Bulan</label>
                                    <!-- <input type="text" class="form-control" name="total_harga" id="total_harga"> -->
                                    <select class="form-control" id="bulan_cari" name="bulan_cari">
                                        <option selected disabled>-- Pilih Bulan --</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="total_semua" class="col-md-4 col-sm-12 col-xs-12 hidden">
                        <h2><b>HM : <span id="hm_title">0</span></b></h2>
                        <br>

                        <h2><b>KM : <span id="km_title">0</span></b></h2>
                        <br>

                        <h2><b>Total Biaya : <span id="total_biaya_title">0</span></b></h2>
                        <br>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end">
                            <!-- <a href="<?= base_url('dashboard') ?>" class="btn btn-warning">Back</a> -->
                            <button type="submit" onclick="cariData()" class="btn btn-primary" id="btn-save">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_title">
                    <h2>List Detail</h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <!-- <a href="<?= base_url('bbm/create') ?>" class="btn btn-primary">Create Bbm</a>
                        <a href="<?= base_url('bbm/ubah_harga_bbm') ?>" class="btn btn-primary">Ubah Harga Bbm</a> -->
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table1">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">No. Lambung</th>
                                    <th scope="col">Nama Asset</th>
                                    <!-- <th scope="col">RIT</th> -->
                                    <th scope="col">Total Part</th>
                                    <!-- <th scope="col">KM</th> -->
                                    <th scope="col">Total Harga</th>
                                    <!-- <th scope="col">Harga/Liter</th> -->
                                    <!-- <th scope="col">Tanggal</th> -->
                                    <th scope="col">HM Awal</th>
                                    <th scope="col">HM Akhir</th>
                                    <th scope="col">HM</th>
                                    <th scope="col">KM Awal</th>
                                    <th scope="col">KM Akhir</th>
                                    <th scope="col">KM</th>
                                    <th scope="col">Total Harga BBM</th>
                                    <th scope="col">Total BBM (Liter)</th>
                                </tr>
                            </thead>
                            <tbody>

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

<link rel="stylesheet" href="<?= base_url(); ?>assets/select2/css/select2.min.css">
<script type="text/javascript" src="<?= base_url(); ?>assets/select2/js/select2.min.js"></script>
<script>
    function matchStart(params, data) {
        // If there are no search terms, return all data
        if ($.trim(params.term) === '') {
            return data;
        }

        // Convert both option text and search term to uppercase for case-insensitive comparison
        if (data.text.toUpperCase().includes(params.term.toUpperCase())) {
            return data;
        }

        // Return `null` if the term does not match any part of the text
        return null;
    }

    $(document).ready(function() {
        $("#id_asset").select2({
            matcher: matchStart,
            placeholder: "Select an asset", // Optional: adds a placeholder
            allowClear: true // Optional: adds a clear option
        });
    });

    function cariData() {
        const ttlbulanValue = $('#bulan_cari').val();
        const ttltahunValue = $('#tahun_cari').val();
        const ttlassetValue = $('#id_asset').val();

        if (!ttlbulanValue) {
            swal.fire({
                customClass: 'slow-animation',
                icon: 'error',
                showConfirmButton: false,
                title: 'Kolom Bulan Tidak Boleh Kosong',
                timer: 1500
            });
        } else if (!ttltahunValue) {
            swal.fire({
                customClass: 'slow-animation',
                icon: 'error',
                showConfirmButton: false,
                title: 'Kolom Tahun Tidak Boleh Kosong',
                timer: 1500
            });
        } else {
            const monthNames = [
                "Januari", "Februari", "Maret", "April",
                "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];

            const monthName = monthNames[ttlbulanValue - 1];
            const tanggal_hasil = monthName + ' ' + ttltahunValue;

            console.log('bisa 1')
            $.ajax({
                url: "<?php echo site_url('Detail_No_Lambung/cari/') ?>" + ttlbulanValue + '/' + ttltahunValue + '/' + ttlassetValue,
                type: "POST",
                dataType: "JSON",
                success: function(response) {
                    if (response.status === "Success") {
                        const data = response.data;
                        const tableBody = $('table tbody');
                        tableBody.empty();

                        let total_hm = 0;
                        let total_km = 0;
                        let total_biaya_semua = 0;

                        const requests = data.map((item, index) => {
                            return new Promise((resolve, reject) => {
                                $.ajax({
                                    url: "<?php echo site_url('Detail_No_Lambung/cari_ritasi_sparepart/'); ?>" + item.Id + '/' + ttlbulanValue + '/' + ttltahunValue,
                                    type: "POST",
                                    dataType: "json",
                                    success: function(additionalResponse) {
                                        if (additionalResponse.status === "Success") {
                                            const additionalData_sparepart = additionalResponse.data;

                                            $.ajax({
                                                url: "<?php echo site_url('Detail_No_Lambung/cari_ritasi_tonase/'); ?>" + item.Id + '/' + ttlbulanValue + '/' + ttltahunValue,
                                                type: "POST",
                                                dataType: "json",
                                                success: function(additionalResponse) {
                                                    if (additionalResponse.status === "Success") {
                                                        const additionalData_tonase = additionalResponse.data;

                                                        $.ajax({
                                                            url: "<?php echo site_url('Detail_No_Lambung/cari_ritasi_bbm/'); ?>" + item.Id + '/' + ttlbulanValue + '/' + ttltahunValue,
                                                            type: "POST",
                                                            dataType: "json",
                                                            success: function(additionalResponse) {
                                                                if (additionalResponse.status === "Success") {
                                                                    const additionalData_bbm = additionalResponse.data;

                                                                    const row = `
                                                                    <tr>
                                                                        <td>${index + 1}</td>
                                                                        <td>${item.Id || 'N/A'}</td>
                                                                        <td>${item.nama_asset || 'N/A'}</td>
                                                                        <td>${additionalData_sparepart.jumlah_part !== null ? Math.floor(additionalData_sparepart.jumlah_part).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>Rp. ${additionalData_sparepart.harga_part !== null ? Math.floor(additionalData_sparepart.harga_part).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>${additionalData_tonase.hm_awal !== null ? Math.floor(additionalData_tonase.hm_awal).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>${additionalData_tonase.hm_akhir !== null ? Math.floor(additionalData_tonase.hm_akhir).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>${additionalData_tonase.hm_difference !== null ? Math.floor(additionalData_tonase.hm_difference).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>${additionalData_tonase.km_awal !== null ? Math.floor(additionalData_tonase.km_awal).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>${additionalData_tonase.km_akhir !== null ? Math.floor(additionalData_tonase.km_akhir).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>${additionalData_tonase.km_difference !== null ? Math.floor(additionalData_tonase.km_difference).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>Rp. ${additionalData_bbm.total_harga !== null ? Math.floor(additionalData_bbm.total_harga).toLocaleString('id-ID') : 'N/A'}</td>
                                                                        <td>${additionalData_bbm.total_liter !== null ? Math.floor(additionalData_bbm.total_liter).toLocaleString('id-ID') + ' Liter' : 'N/A'}</td>
                                                                    </tr>
                                                                `;

                                                                    $('table tbody').append(row);

                                                                    // Accumulate values, ensuring they are rounded and avoiding `NaN` with `|| 0`
                                                                    total_hm += Math.round(additionalData_tonase.hm_difference || 0);
                                                                    total_km += Math.round(additionalData_tonase.km_difference || 0);

                                                                    // Calculate `total_biaya` and ensure it’s rounded
                                                                    const total_biaya = Math.round((parseFloat(additionalData_sparepart.harga_part) || 0) + (parseFloat(additionalData_bbm.total_harga) || 0));
                                                                    // Add `total_biaya` to `total_biaya_semua` and round to prevent floating-point issues
                                                                    total_biaya_semua += total_biaya;

                                                                    resolve(); // Resolve the promise after calculations
                                                                } else {
                                                                    reject("Additional data not found for item ID: " + item.Id);
                                                                }
                                                            },
                                                            error: reject
                                                        });
                                                    } else {
                                                        reject("Additional data not found for item ID: " + item.Id);
                                                    }
                                                },
                                                error: reject
                                            });
                                        } else {
                                            reject("Additional data not found for item ID: " + item.Id);
                                        }
                                    },
                                    error: reject
                                });
                            });
                        });

                        Promise.all(requests).then(() => {

                            $('#hm_title').text(total_hm.toLocaleString('id-ID') || 'N/A');
                            $('#km_title').text(total_km.toLocaleString('id-ID') || 'N/A');

                            $('#total_biaya_title').text(Math.round(total_biaya_semua).toLocaleString('id-ID') || 'N/A');
                            $('#total_semua').removeClass('hidden');
                            $('#total_semua').addClass('show');

                            $('#tab_ketemu').addClass('show');
                            swal.fire({
                                customClass: 'slow-animation',
                                icon: 'success',
                                showConfirmButton: false,
                                title: 'Berhasil Mencari Data Saldo',
                                timer: 1500
                            });
                        }).catch(error => {
                            console.error(error);
                            swal.fire({
                                customClass: 'slow-animation',
                                icon: 'error',
                                showConfirmButton: false,
                                title: 'Terjadi kesalahan saat mengambil data',
                                timer: 1500
                            });
                        });
                    } else {
                        $('#tab_ketemu').removeClass('show');
                        swal.fire({
                            customClass: 'slow-animation',
                            icon: 'warning',
                            showConfirmButton: false,
                            title: 'Data Tidak Ditemukan',
                            timer: 1500
                        });
                    }
                },
                error: function() {
                    alert('Error get data from ajax');
                }
            });
        }
    }
</script>