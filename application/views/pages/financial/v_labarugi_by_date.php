<div class="right_col" role="main">
    <div class="clearfix"></div>

    <!-- Start content-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">

                <div class="x_title">
                    <h2>Neraca per tanggal <?= format_indo($per_tanggal) ?> </h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-4 col-xs-12">
                            <h5>
                                Laba berjalan: <strong>Rp <?= number_format($total_pendapatan) ?></strong>
                            </h5>
                        </div>
                        <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/reportByDate') ?>">
                            <div class="col-md-2 col-xs-12">

                                <div class="form-group row">
                                    <input type="date" name="per_tanggal" id="per_tanggal" class="form-control" value="<?= $per_tanggal ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">

                                <div class="form-group row">
                                    <select name="jenis_laporan" id="jenis_laporan" class="form-control">
                                        <option <?= ($this->input->post('jenis_laporan') == "neraca") ? "selected" : "" ?> value="neraca">Neraca SBB</option>
                                        <option <?= ($this->input->post('jenis_laporan') == "laba_rugi") ? "selected" : "" ?> value="laba_rugi">Laba Rugi SBB</option>
                                        <option <?= ($this->input->post('jenis_laporan') == "neraca_bb") ? "selected" : "" ?> value="neraca_bb">Neraca BB</option>
                                        <option <?= ($this->input->post('jenis_laporan') == "lr_bb") ? "selected" : "" ?> value="lr_bb">Laba Rugi BB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 text-right">
                                <div class="form-group row">
                                    <button type="submit" name="button_sbm" class="btn btn-primary btn-sm" value="lihat">Lihat</button>
                                    <button type="submit" name="button_sbm" class="btn btn-success btn-sm" value="excel"><i class='fa fa-file'></i> Excel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <h2 class="text-center">Biaya</h2>
                            <p class="text-right">Total: <strong><?= number_format($sum_biaya) ?></strong></p>
                            <div class="table-responsive">
                                <table id="" class="table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No. Coa</th>
                                            <th>Nama Coa</th>
                                            <th>Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($biaya as $a) :
                                            $coa = $this->m_coa->getCoa($a->no_sbb);

                                            if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'AKTIVA') { ?>
                                                <tr>
                                                    <td><button class="bg-blue arus_kas" data-id="<?= $a->no_sbb ?>"><?= $a->no_sbb ?></td>
                                                    <td><?= $coa['nama_perkiraan'] ?></td>
                                                    <td class="text-right"><?= number_format($a->saldo_awal) ?></td>
                                                </tr>
                                        <?php
                                            }
                                        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <div class="row justify-content-between">
                                <h2 class="text-center">Pendapatan</h2>
                                <p class="text-right">Total: <strong><?= number_format($sum_pendapatan) ?></strong></p>
                            </div>
                            <div class="table-responsive">
                                <table id="" class="table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No. Coa</th>
                                            <th>Nama Coa</th>
                                            <th>Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($pendapatan as $a) :
                                            $coa = $this->m_coa->getCoa($a->no_sbb);

                                            if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'PASIVA') { ?>
                                                <tr>
                                                    <td><button class="bg-blue arus_kas" data-id="<?= $a->no_sbb ?>"><?= $a->no_sbb ?></td>
                                                    <td><?= $coa['nama_perkiraan'] ?></td>
                                                    <td class="text-right"><?= number_format($a->saldo_awal) ?></td>
                                                </tr>
                                        <?php
                                            }
                                        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="detailModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel2">Lacak arus kas</h4>
            </div>
            <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>" target="_blank">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" class="form-control" name="no_coa">
                        <div class="col-md-6 col-xs-12">
                            <label for="tgl_dari" class="form-label">Dari</label>
                            <input type="date" class="form-control" name="tgl_dari" required>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <label for="tgl_sampai" class="form-label">Sampai</label>
                            <input type="date" class="form-control" name="tgl_sampai" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Lihat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('click', '.arus_kas', function() {
            var id = $(this).data('id');

            $('#detailModal2 .modal-title').text('Arus kas ' + id);
            // $('#detailModal2 .modal-body').html(id);
            $('#detailModal2 input[name="no_coa"]').val(id);
            $('#detailModal2').modal('show');
        });
    });
</script>