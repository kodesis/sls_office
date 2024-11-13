<div class="right_col" role="main">
    <div class="clearfix"></div>

    <!-- Start content-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_content">
                    <div class="row">
                        <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/save_saldo_awal') ?>">
                            <div class="col-md-3 col-xs-12 mt-3">
                                <input type="text" class="form-control datepicker" data-date-format="mm/yyyy" id="periode" name="periode" value="<?= $this->input->post('periode') ?>">
                            </div>
                            <div class="col-md-1 col-xs-12 mt-3 text-right">
                                <button type="submit" class="btn btn-primary">Closing EoM</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive mt-3">
                        <table id="" class="table table-stripped" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Closing Periode</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($saldo) :
                                    $no = 1;
                                    foreach ($saldo as $c) : ?>
                                        <tr>
                                            <td class="text-right"><?= $no++ ?>.</td>
                                            <td><?= format_indo($c->periode) ?></td>
                                            <td><?= $c->keterangan ?></td>
                                            <td class="text-center"><a href="<?= base_url('financial/closing/' . $c->periode) ?>" class="btn btn-primary btn-xs">Detail</a></td>
                                        </tr>
                                    <?php
                                    endforeach;
                                else :  ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No data available</td>
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

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

<script>
    $('#periode').datepicker({
        format: "yyyy-mm", // Format to show month and year only
        minViewMode: "months", // Show only month and year in the picker
        autoclose: true // Close the picker after selection
    });
</script>