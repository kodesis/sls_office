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
                        <div class="col-md-5 col-xs-12">
                            <h5>
                                Neraca: <strong>Rp <?= (isset($neraca)) ? number_format($neraca) : 0 ?></strong>
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
                                        <!-- <option <?= ($this->input->post('jenis_laporan') == "invoice_nol") ? "selected" : "" ?> value="invoice_nol">Invoice Nol</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 col-xs-12 text-right">

                                <div class="form-group row">
                                    <button type="submit" class="btn btn-primary">Lihat</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <h2 class="text-center">Activa</h2>
                            <p class="text-right">Total: <strong><?= (isset($sum_activa)) ? number_format($sum_activa) : 0 ?></strong></p>
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
                                    if (isset($activa)) :
                                        foreach ($activa as $a) :
                                            $coa = $this->m_coa->getCoa($a->no_sbb);

                                            if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'AKTIVA' && $a->saldo_awal != '0') : ?>
                                                <tr>
                                                    <td><?= $a->no_sbb ?></td>
                                                    <td><?= $coa['nama_perkiraan'] ?></td>
                                                    <td class="text-right"><?= number_format($a->saldo_awal) ?></td>
                                                </tr>
                                        <?php
                                            endif;
                                        endforeach;
                                    else : ?>
                                        <tr>
                                            <td colspan="3">Tidak ada activa yang ditampilkan</td>
                                        </tr>
                                    <?php
                                    endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <div class="row justify-content-between">
                                <h2 class="text-center">Pasiva</h2>
                                <p class="text-right">Total: <strong><?= (isset($sum_pasiva)) ? number_format($sum_pasiva) : 0 ?></strong></p>
                            </div>
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
                                    if (isset($pasiva)) :
                                        foreach ($pasiva as $a) :
                                            $coa = $this->m_coa->getCoa($a->no_sbb);

                                            if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'PASIVA' && $a->saldo_awal != '0') : ?>
                                                <tr>
                                                    <td><?= $a->no_sbb ?></td>
                                                    <td><?= $coa['nama_perkiraan'] ?></td>
                                                    <td class="text-right"><?= number_format($a->saldo_awal) ?></td>
                                                </tr>
                                        <?php
                                            endif;
                                        endforeach; ?>
                                        <tr>
                                            <td>3103001</td>
                                            <td>LABA TAHUN BERJALAN</td>
                                            <td class="text-right"><?= number_format($laba) ?></td>
                                        </tr>
                                    <?php
                                    else : ?>
                                        <tr>
                                            <td colspan="3">Tidak ada pasiva yang ditampilkan</td>
                                        </tr>
                                    <?php
                                    endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
</script>