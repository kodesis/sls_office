<!-- Start content-->
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <div class="x_panel card">
        <div class="x_title">
            <h2>Item List</h2>
        </div>

        <div class="row">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal1"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Item</button>
            <!-- <a href="<?= base_url('asset/export_item') ?>" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel</a> -->
        </div>
        <!-- <div class="row">
            <div class="col-md-3 sol-sm-6 col-xs-12" style="padding: 0 !important; margin: 0 !important">
                <div class="panel panel-default">
                    <div class="panel-heading">Total Nilai All Spare Part</div>
                    <div class="panel-body">
                        <p style="font-weight: bolder; font-size: 20px"><?= "Rp." . number_format($total['total'] - $total_repair['total']) ?></p>
                    </div>
                </div>
            </div>
        </div> -->

        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12" style="padding: 0 !important; margin: 0 !important">
                <form action="<?= base_url('asset/item_list') ?>" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Cari nama atau kode item..." value="<?= $this->input->get('search') ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Search!</button>
                            <a href="<?= base_url('asset/item_list') ?>" class="btn btn-warning">RESET</a>
                            <a href="<?= base_url('asset/export_itemList') ?>" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export</a>
                        </span>
                    </div><!-- /input-group -->
                </form>
            </div>
        </div>
        <!-- search -->
        <!-- <div class="row" style="margin-left: auto; width: 40%;">

        </div> -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Stok</th>
                        <th>Jumlah Detail</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                        <th>Jenis</th>
                        <th>#</th>
                    </tr>
                </thead>
                <?php
                if (empty($users_data)) {
                ?>
                    <tbody>
                        <tr align="center">
                            <td colspan="9"><b>Tidak ada data</b></td>
                        </tr>
                    </tbody>
                <?php
                } else { ?>
                    <tbody>
                        <?php
                        foreach ($users_data as $data) :
                            $sql = "SELECT * FROM item_detail WHERE kode_item = '$data->Id' AND (status = 'A' or status = 'R')";
                            $detail = $this->db->query($sql);

                            $stok_repair = $this->db->get_where('item_detail', ['kode_item' => $data->Id, 'status' => 'R'])->num_rows();
                        ?>
                            <!--content here-->
                            <tr>
                                <td class="fit"><?php echo ++$page; ?></td>
                                <td><?php echo $data->nomor; ?></td>
                                <td><?php echo $data->nama; ?></td>
                                <td class="fit"><?php echo $data->stok; ?></td>
                                <td><?php echo $detail->num_rows(); ?></td>
                                <td><?= $data->harga_sat ? number_format($data->harga_sat) : "" ?></td>
                                <td><?php echo number_format(($data->harga_sat * $data->stok) - ($stok_repair * $data->harga_sat)); ?></td>
                                <td><?php echo $data->nama_jenis; ?></td>
                                <td width="80px">
                                    <a href="<?= base_url('asset/ubah_item/' . $data->Id) ?>" class="btn btn-success btn-xs"><i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>
                                    <a href="<?= base_url('asset/detail/' . $data->Id) ?>" class="btn btn-warning btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                    <?php
                        endforeach;
                    }
                    ?>
                    </tbody>
            </table>
        </div>
        <div class="clearfix"></div>

        <!--pagination-->
        <div class="row col-12 text-center">
            <?php echo $pagination; ?>
        </div>
    </div>

    <!-- Finish content-->


    <!-- /page content -->
    <form data-parsley-validate enctype="multipart/form-data" action="<?php echo base_url(); ?>asset/add_item" method="post" name="form-item" id="form-item" class="form-horizontal form-label-left">
        <div class="modal fade" id="myModal1" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h2 class="modal-title">Tambah Data Item</h2>
                    </div>
                    <div class="modal-body">
                        <h4>
                            <font color="Grey"><Strong>
                        </h4><br>
                        <div class="form-group">
                            <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align" for="last-name">Kode <span class="required">*</span>
                                </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input id="kode" class="form-control col-md-12 col-xs-12" name="kode" id="kode" placeholder="Masukkan kode item" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align" for="last-name">Nama Item <span class="required">*</span>
                                </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input id="kode" class="form-control col-md-12 col-xs-12" name="name" placeholder="Masukkan nama item" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align" for="last-name">Jenis Item <span class="required">*</span>
                                </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select name="jenis_item" id="jenis_item" class="form-control select2" style="width: 100%;">
                                        <option value="">Pilih Jenis Item</option>
                                        <?php
                                        $jenis = $this->db->get('item_jenis')->result_array();
                                        foreach ($jenis as $value) {
                                        ?>
                                            <option value="<?= $value['Id'] ?>"><?= $value['nama_jenis'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align" for="last-name">Coa Item <span class="required">*</span>
                                </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select name="coa" id="coa" class="form-control select2" style="width: 100%;">
                                        <option value="">Pilih coa Item</option>
                                        <?php
                                        foreach ($coa as $c) {
                                        ?>
                                            <option value="<?= $c['no_sbb'] ?>"><?= $c['no_sbb'] . ' - ' . $c['nama_perkiraan'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align" for="last-name">Catatan</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <textarea name="catatan" id="catatan" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div style="text-align: center;">
                                <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        var selects = document.querySelectorAll('.select2');
        for (let index = 0; index < selects.length; index++) {
            $("#" + selects[index].id + "").select2({
                dropdownParent: $("#myModal1"),
                width: "100%"
            })
        }
    })
</script>