<div class="right_col" role="main">
    <div class="clearfix"></div>
    <!-- Start content-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_title">
                    <h2>List Pengajuan</h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <a href="<?= base_url('asset/purchaseorder') ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Create PO</a>
                        <?php $a = $this->session->userdata('level');
                        if (strpos($a, '503') !== false) { ?>
                            <a href="<?= base_url('asset/sarlog') ?>" class="btn btn-success btn-sm">Approval Sarlog</a>
                            <a href="<?= base_url('asset/sarlog_approve') ?>" class="btn btn-success btn-sm">Belum Proses <span class="badge bg-red"><?= $count_sarlog ?></span></a>
                        <?php }
                        if ($this->session->userdata('bagian') == 10) { ?>
                            <a href="<?= base_url('asset/direksi_ops') ?>" class="btn btn-success btn-sm">Approval Direktur Ops <span class="badge bg-red"><?= $count_dirops ?></span></a>
                        <?php }
                        if ($this->session->userdata('bagian') == 9) { ?>
                            <a href="<?= base_url('asset/dirut') ?>" class="btn btn-success btn-sm">Approval Direktur Utama <span class="badge bg-red"><?= $count_dirut ?></span></a>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 form-group" style="margin: 0; padding:0;">
                            <form class="form-horizontal form-label-left" method="get" action="<?= base_url('asset/po_list') ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="input no po atau nama vendor" name="keyword" id="keyword" value="<?= $this->input->get('keyword') ?>">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit">Search</button>
                                        <a href="<?= base_url('asset/po_list') ?>" class="btn btn-warning" style="color:white;">Reset</a>
                                    </span>
                                </div><!-- /input-group -->
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">PO</th>
                                    <th scope="col">Vendor</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Posisi</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($po->num_rows() < 1) { ?>
                                    <tr align="center">
                                        <td colspan="7">Tidak ada data</td>
                                    </tr>
                                    <?php } else {
                                    foreach ($po->result_array() as $value) {
                                    ?>
                                        <tr>
                                            <td scoope="row"><?= ++$page; ?></td>
                                            <td scope="row"><?= $value['no_po'] ?></td>
                                            <td scope="row"><?= $value['nama_vendor'] ?></td>
                                            <td scope="row"><?= tgl_indo(date('Y-m-d', strtotime($value['tgl_pengajuan']))) ?></td>
                                            <td scope="row"><?= $value['posisi'] ?></td>
                                            <td scope="row">
                                                <?php
                                                if ($value['ppn']) {
                                                    $ppn = $value['total'] * 0.11;
                                                } else {
                                                    $ppn = 0;
                                                }
                                                ?>
                                                <?= number_format($value['total'] + $ppn) ?>
                                            </td>
                                            </td>
                                            <td scope="row">
                                                <button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal<?= $value['Id'] ?>">View</button>
                                                <?php if ($value['status_sarlog'] == 1 and $value['status_direksi_ops'] == 1 or $value['status_dirut'] == 1) { ?>
                                                    <a href="<?= base_url('asset/print/' . $value['Id']) ?>" class="btn btn-primary btn-xs" target="_blank">Print</a>
                                                <?php } ?>
                                                <?php if ($value['status_sarlog'] == 0 or $value['status_sarlog'] == 2 or $value['status_direksi_ops'] == 2 or $value['status_dirut'] == 2) { ?>
                                                    <a href="<?= base_url('asset/update_po/' . $value['Id']) ?>" class="btn btn-success btn-xs">Update</a>
                                                <?php } ?>
                                                <?php if ($value['posisi'] == 'Sudah Dibayar' || $value['posisi'] == "Hutang") { ?>
                                                    <form action="<?= base_url('asset/add_item_in') ?>" method="post">
                                                        <input type="hidden" name="id_po" id="id_po" value="<?= $value['Id'] ?>">
                                                        <button class="btn btn-success btn-xs btn-submit">Add to list</button>
                                                    </form>
                                                <?php } ?>
                                                <!-- Modal Detail -->
                                                <div class="modal fade" id="myModal<?= $value['Id'] ?>" role="dialog">
                                                    <div class="modal-dialog modal-lg">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h2 class="modal-title">Purchase Order <?= $value['no_po'] ?></h2>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width="20px">No.</th>
                                                                                <th>Item</th>
                                                                                <th width="25px">Qty</th>
                                                                                <th>UOI</th>
                                                                                <th width="25px">Satuan</th>
                                                                                <th>Price</th>
                                                                                <th>Total</th>
                                                                                <th width="300px">Ket</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $detail = $this->cb->get_where('t_po_detail', ['no_po' => $value['Id']])->result_array();
                                                                            $no = 1;
                                                                            foreach ($detail as $row) {
                                                                                $item = $this->db->get_where('item_list', ['Id' => $row['item']])->row_array();
                                                                            ?>
                                                                                <tr>
                                                                                    <td><?= $no++ ?></td>
                                                                                    <td><?= $item['nama'] . ' | ' . $item['nomor'] ?></td>
                                                                                    <td><?= $row['qty'] ?></td>
                                                                                    <td><?= $row['uoi'] ?></td>
                                                                                    <td><?= $row['satuan'] ?></td>
                                                                                    <td><?= number_format($row['price'], 0) ?></td>
                                                                                    <td><?= number_format($row['total'], 0) ?></td>
                                                                                    <td><?= $row['keterangan'] ?></td>
                                                                                </tr>
                                                                            <?php } ?>
                                                                            <tr>
                                                                                <td colspan="6" align="right"><strong>SUB TOTAL</strong></td>
                                                                                <td><?= number_format($value['total']) ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="6" align="right"><strong>PPN 11%</strong></td>
                                                                                <td><?= number_format($ppn) ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="6" align="right"><strong>TOTAL</strong></td>
                                                                                <td><?= number_format($ppn + $value['total']) ?></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <table style="margin-top: 20px;" class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Catatan Sarlog</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><?= $value['catatan_sarlog'] ? $value['catatan_sarlog'] : "Tidak ada catatan" ?></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table style="margin-top: 20px;" class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Catatan Direktur Operasional</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><?= $value['catatan_direksi_ops'] ? $value['catatan_direksi_ops'] : "Tidak ada catatan" ?></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table style="margin-top: 20px;" class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Catatan Direktur Utama</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><?= $value['catatan_dirut'] ? $value['catatan_dirut'] : "Tidak ada catatan" ?></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table style="margin-top: 20px;" class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Attachment Bayar</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <?php if ($value['bukti_bayar']) { ?>
                                                                                    <a href="<?= base_url('upload/po/' . $value['bukti_bayar']) ?>" class="btn btn-success btn-xs" download="">Download</a>
                                                                                <?php } else { ?>
                                                                                    <span> -</span>
                                                                                <?php } ?>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
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
                    <div class="row text-center">
                        <?= $pagination ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>