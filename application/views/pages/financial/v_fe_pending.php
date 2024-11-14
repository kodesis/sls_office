<style>
    .modal {
        text-align: center;
        padding: 0 !important;
    }

    .modal:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px;
    }

    .modal-dialog {
        display: inline-block;
        text-align: left;
        vertical-align: middle;
    }

    .select2-container .select2-dropdown .select2-results__option {
        text-align: left;
    }
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_right" style="width: 100%;">
            <div class="col-md-12 col-xs-12 form-group pull-right top_search">
                <form class="form-horizontal form-label-left" method="post" action="<?= base_url('financial/fe_pending') ?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" placeholder="Search for..." value="<?= $keyword ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-secondary" type="submit">Go!</button>
                            <button class="btn btn-warning" onclick="document.location='<?= base_url('financial/reset/customer') ?>'" style="color:white;">Reset</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>

    <!-- Start content-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_content">
                    <div class="table-responsive">
                        <table id="" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>User input</th>
                                    <th>CoA Debit</th>
                                    <th>CoA Kredit</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($fes) {
                                    foreach ($fes as $i) :
                                        $status = ($i['status_approval'] == '0') ? "Butuh persetujuan" : (($i['status_approval'] == '1' ? "Disetujui" : "Ditolak"));
                                        $bg = ($i['status_approval'] == '0') ? "bg-blue" : (($i['status_approval'] == '1' ? "bg-green" : "bg-red")); ?>
                                        <tr>
                                            <td><a href="<?= base_url('financial/show/' . $i['slug']) ?>"><?= $i['slug'] ?></a></td>
                                            <td><?= format_indo($i['tanggal_transaksi']) ?></td>
                                            <td><?= isset($i['created_by_name']) ? $i['created_by_name'] : 'N/A' ?></td>
                                            <td><?= show_coa($i['coa_debit']) ?></td>
                                            <td><?= show_coa($i['coa_kredit']) ?></td>
                                            <td class="text-right"><?= number_format(sum_nominal($i['nominal'])) ?></td>
                                            <td style="word-wrap: break-word;"><?= $i['keterangan'] ?></td>
                                            <td>
                                                <?php
                                                if ($i['status_approval'] == '1') {
                                                ?>
                                                    <span class="badge bg-green">Disetujui</span>
                                                <?php
                                                } else if ($i['status_approval'] == '2') {
                                                ?>
                                                    <span class="badge bg-red" data-toggle="tooltip" data-placement="right" title="<?= $i['alasan_ditolak'] ?>">Ditolak</span>
                                                <?php
                                                } ?>

                                                <button class="badge bg-blue" data-toggle="modal" data-target="#modal<?= $i['Id'] ?>">Lihat</button>
                                                <!-- Modal Utama -->
                                                <div class="modal fade" id="modal<?= $i['Id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel"><?= $i['slug'] ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <?php if ($i['jenis_fe'] == "multi_kredit") { ?>
                                                                        <!-- CoA Debit Section -->
                                                                        <div class="col-md-12 col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="no_coa" class="form-label">CoA Debit</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-8">
                                                                            <div class="form-group">
                                                                                <?php
                                                                                $coa = $this->m_coa->cek_coa(json_decode($i['coa_debit']));
                                                                                $nominal_array = json_decode($i['nominal'], true);
                                                                                $total = array_sum($nominal_array);
                                                                                ?>
                                                                                <p><?= $coa['no_sbb'] . ' - ' . $coa['nama_perkiraan'] ?>:</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4 text-right">
                                                                            <p><?= number_format($total) ?></p>
                                                                        </div>

                                                                        <!-- CoA Kredit Section -->
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="coa_debit" class="form-label">CoA Kredit</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <?php
                                                                                $coa_kredit = json_decode($i['coa_kredit'], true);
                                                                                if (is_array($coa_kredit) && is_array($nominal_array)) { ?>
                                                                                    <table class="table table-striped">
                                                                                        <tbody>
                                                                                            <?php foreach ($coa_kredit as $index => $coa_id) :
                                                                                                $coa = $this->m_coa->cek_coa($coa_id); ?>
                                                                                                <tr>
                                                                                                    <td><?= $coa['no_sbb'] . ' - ' . $coa['nama_perkiraan'] ?></td>
                                                                                                    <td class="text-right"><?= number_format($nominal_array[$index]); ?></td>
                                                                                                </tr>
                                                                                            <?php endforeach; ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                <?php } else {
                                                                                    echo "Data tidak valid.";
                                                                                } ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php } else if ($i['jenis_fe'] == "multi_debit") {
                                                                        $nominal_array = json_decode($i['nominal'], true); ?>
                                                                        <!-- CoA Debit Section -->
                                                                        <div class="col-md-12 col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="coa_debit" class="form-label">CoA Debit</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12 col-sm-12">
                                                                            <div class="form-group">
                                                                                <?php
                                                                                $coa_debit = json_decode($i['coa_debit'], true);
                                                                                if (is_array($coa_debit) && is_array($nominal_array)) { ?>
                                                                                    <table class="table table-striped">
                                                                                        <tbody>
                                                                                            <?php foreach ($coa_debit as $index => $coa_id) :
                                                                                                $coa = $this->m_coa->cek_coa($coa_id); ?>
                                                                                                <tr>
                                                                                                    <td><?= $coa['no_sbb'] . ' - ' . $coa['nama_perkiraan'] ?></td>
                                                                                                    <td class="text-right"><?= number_format($nominal_array[$index]); ?></td>
                                                                                                </tr>
                                                                                            <?php endforeach; ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                <?php } else {
                                                                                    echo "Data tidak valid.";
                                                                                } ?>
                                                                            </div>
                                                                        </div>

                                                                        <!-- CoA Kredit Section -->
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="no_coa" class="form-label">CoA Kredit</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-8">
                                                                            <div class="form-group">
                                                                                <?php
                                                                                $coa = $this->m_coa->cek_coa(json_decode($i['coa_kredit'], true));
                                                                                $total_kredit = array_sum($nominal_array);
                                                                                ?>
                                                                                <p><?= $coa['no_sbb'] . ' - ' . $coa['nama_perkiraan'] ?>:</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4 text-right">
                                                                            <p><?= number_format($total_kredit) ?></p>
                                                                        </div>
                                                                    <?php } else if ($i['jenis_fe'] == "single") {

                                                                        $nominal_array = json_decode($i['nominal'], true); ?>
                                                                        <div class="row">
                                                                            <!-- CoA Debit Section -->
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                    <label for="no_coa" class="form-label">CoA Debit</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <?php
                                                                                    $coa = $this->m_coa->cek_coa(json_decode($i['coa_debit']));

                                                                                    ?>
                                                                                    <p><?= $coa['no_sbb'] . ' - ' . $coa['nama_perkiraan'] ?>:</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4 text-right">
                                                                                <p><?= number_format($nominal_array) ?></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <!-- CoA Kredit Section -->
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                    <label for="no_coa" class="form-label">CoA Kredit</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <?php
                                                                                    $coa = $this->m_coa->cek_coa(json_decode($i['coa_kredit'], true));
                                                                                    $nominal_array = json_decode($i['nominal'], true);
                                                                                    ?>
                                                                                    <p><?= $coa['no_sbb'] . ' - ' . $coa['nama_perkiraan'] ?>:</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4 text-right">
                                                                                <p><?= number_format($nominal_array) ?></p>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <div class="row mt-3">
                                                                    <!-- Tanggal Transaksi Section -->
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="tanggal_transaksi" class="form-label">Tanggal transaksi</label>
                                                                            <p><?= format_indo($i['tanggal_transaksi']) ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Lampiran Section -->
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="lampiran" class="form-label">Lampiran</label>
                                                                            <p><?= ($i['file_path']) ? '<a href="' . base_url($i['file_path']) . '">' . $i['slug'] . '</a>' : 'Tidak ada' ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Keterangan Section -->
                                                                    <div class="col-sm-12 mt-3">
                                                                        <div class="form-group">
                                                                            <label for="keterangan" class="form-label">Keterangan</label>
                                                                            <p><?= $i['keterangan'] ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Status Approval Section -->
                                                                    <div class="col-sm-12">
                                                                        <?php if ($i['status_approval'] == '1') { ?>
                                                                            <span class="badge bg-green">Disetujui</span>
                                                                        <?php } else if ($i['status_approval'] == '2') { ?>
                                                                            <span class="badge bg-red" data-toggle="tooltip" data-placement="right" title="<?= $i['alasan_ditolak'] ?>">Ditolak</span>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <?php if ($i['status_approval'] == '0') { ?>
                                                                    <button href="#" class="btn bg-green btn-process" data-url="<?= base_url('financial/approve_fe/' . $i['slug']) ?>">Setujui</button>
                                                                    <div class="btn btn-danger" data-toggle="modal" data-target="#reject<?= $i['Id'] ?>">Tolak</div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal Tolak -->
                                                <div class="modal fade" id="reject<?= $i['Id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel"><?= $i['slug'] ?></h4>
                                                            </div>
                                                            <form action="<?= base_url('financial/reject_fe/' . $i['slug']) ?>" method="post">
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="alasan_ditolak" class="form-label">Alasan ditolak</label>
                                                                                <textarea name="alasan_ditolak" id="alasan_ditolak" class="form-control" oninput="this.value = this.value.toUpperCase()" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Tolak</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="8">Tidak ada data yang ditampilkan</td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <?= $this->pagination->create_links() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?= base_url(); ?>assets/select2/css/select2.min.css">
<script type="text/javascript" src="<?= base_url(); ?>assets/select2/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('.select3').select2();
    })

    // const flashdata = $(".flash-data").data("flashdata");
    // if (flashdata) {
    //     Swal.fire({
    //         title: "Success!! ",
    //         text: '<?= $this->session->flashdata('message_name') ?>',
    //         type: "success",
    //         icon: "success",
    //     });
    // }
    // // const flashdata_error = $('<?= $this->session->flashdata("message_error") ?>').data("flashdata");
    // const flashdata_error = $(".flash-data-error").data("flashdata");
    // // const flashdata_error = $('.flash-data').data('flashdata');
    // if (flashdata_error) {
    //     Swal.fire({
    //         title: "Error!! ",
    //         text: flashdata_error,
    //         type: "error",
    //         icon: "error",
    //     });
    // }
    // $(document).ready(function() {
    $(".btn-process").on("click", function(e) {
        e.preventDefault();
        const url = $(this).data("url");
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, process it!",
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = url;
            }
        });
    });
    // });
</script>