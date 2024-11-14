<div class="right_col" role="main">
    <div class="container">
        <!-- <div class="row justify-content-center">
            <div class="title_left">
                <h3>
                    <a href="http://103.252.51.17:8787/fornax/user_login.php" class="btn btn-warning">Fornax</a>
                </h3>
            </div>
        </div> -->
        <div class="clearfix"></div>
        <div class="row justify-content-center">
            <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="owl-carousel owl-theme">
                    <?php $bg = $this->db->get_where('utility', ['Id' => 1])->row_array() ?>
                    <div class="item">
                        <img style="height: 400px;" src="<?= base_url('upload/banner/' . $bg['banner1']) ?>" alt="">
                    </div>
                    <div class="item">
                        <img style="height: 400px;" src="<?= base_url('upload/banner/' . $bg['banner2']) ?>" alt="">
                    </div>
                    <div class="item">
                        <img style="height: 400px;" src="<?= base_url('upload/banner/' . $bg['banner3']) ?>" alt="">
                    </div>

                </div>
            </div>
        </div>
        <?php
        $a = $this->session->userdata('level');
        if (strpos($a, '40') !== false) { ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Add Banner
                    </a>
                    <div class="collapse" id="collapseExample">
                        <div class="cardmb-2">
                            <div class="card-body">
                                <div class="item group row">
                                    <div class="col-md-4">
                                        <form method="POST" action="<?= base_url('home/banner') ?>" enctype="multipart/form-data">
                                            <span>Banner 1</span>
                                            <input type="file" onchange="this.form.submit()" class="form-control" name="banner1">
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <form method="POST" action="<?= base_url('home/banner') ?>" enctype="multipart/form-data">
                                            <span>Banner 2</span>
                                            <input type="file" onchange="this.form.submit()" class="form-control" name="banner2">
                                        </form>
                                    </div>
                                    <form method="POST" action="<?= base_url('home/banner') ?>" enctype="multipart/form-data">
                                        <div class="col-md-4">
                                            <span>Banner 3</span>
                                            <input type="file" onchange="this.form.submit()" class="form-control" name="banner3">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <br>
        <div class="row">
            <div class="col-md-8 col-sm-8 col-xs-12">

                <div class="row">
                    <!-- <div class="col-md-6 col-sm-6 col-xs-12">
									<div class="x_panel tile fixed_height_300">
										<div class="x_title">
											<h2><i class="fa fa-envelope-o"></i> Inbox</h2>
											<div class="clearfix"></div>
										</div>
										<div class="x_content">
											<h4>Total Inbox</h4><br>
										
											<div class="widget_summary">
												<div class="w_left w_25">
													<span>In</span>
												</div>
												<div class="w_center w_55">
													<div class="progress">
														<div class="progress-bar bg-green" role="progressbar" aria-valuenow="46.288209606987"
															aria-valuemin="0" aria-valuemax="100" style="width: 46.288209606987%;">
															<span class="sr-only">46.288209606987%</span>
														</div>
													</div>
												</div>
												<div class="w_right w_20">
													<span>106</span>
												</div>
												<div class="clearfix"></div>
											</div>
											<div class="widget_summary">
												<div class="w_left w_25">
													<span>Out</span>
												</div>
												<div class="w_center w_55">
													<div class="progress">
														<div class="progress-bar bg-green" role="progressbar" aria-valuenow="20.960698689956"
															aria-valuemin="0" aria-valuemax="100" style="width: 20.960698689956%;">
															<span class="sr-only">20.960698689956%</span>
														</div>
													</div>
												</div>
												<div class="w_right w_20">
													<span>48</span>
												</div>
												<div class="clearfix"></div>
											</div>
										</div>
									</div>
								</div> -->
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="x_panel tile fixed_height_300">
                            <div class="x_title">
                                <h2><i class="fa fa-envelope-o"></i> Memo</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <h4>Total Memo <?= $total ?></h4><br>
                                <a href="<?= base_url('app/inbox') ?>">
                                    <div class="widget_summary">
                                        <div class="w_left w_25">
                                            <span>Unread</span>
                                        </div>
                                        <div class="w_center w_55">
                                            <div class="progress">
                                                <div class="progress-bar bg-blue-sky" role="progressbar" aria-valuenow="46.288209606987" aria-valuemin="0" aria-valuemax="100" style="width: <?= $count_inbox ?>%;">
                                                    <span class="sr-only">46.288209606987%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w_right w_20">
                                            <span><?= $count_inbox ?></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                                <a href="<?= base_url('app/inbox') ?>">
                                    <div class="widget_summary">
                                        <div class="w_left w_25">
                                            <span>Read</span>
                                        </div>
                                        <div class="w_center w_55">
                                            <div class="progress">
                                                <div class="progress-bar bg-blue-sky" role="progressbar" aria-valuenow="20.960698689956" aria-valuemin="0" aria-valuemax="100" style="width: <?= $read_inbox ?>%;">
                                                    <span class="sr-only">20.960698689956%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w_right w_20">
                                            <span><?= $read_inbox ?></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="x_panel tile fixed_height_300">
                            <div class="x_title">
                                <h2><i class="fa fa-envelope-o"></i> Tello</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <h4>Total Tello <?= $total_tello['id'] ?></h4><br>
                                <a href="<?= base_url('task/task') ?>">
                                    <div class="widget_summary">
                                        <div class="w_left w_25">
                                            <span>Open</span>
                                        </div>
                                        <div class="w_center w_55">
                                            <div class="progress">
                                                <div class="progress-bar bg-blue-sky" role="progressbar" aria-valuenow="46.288209606987" aria-valuemin="0" aria-valuemax="100" style="width: <?= $open_tello['id'] ?>%;">
                                                    <!-- <span class="sr-only">46.288209606987%</span> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w_right w_20">
                                            <span><?= $open_tello['id'] ?></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                                <a href="<?= base_url('task/task') ?>">
                                    <div class="widget_summary">
                                        <div class="w_left w_25">
                                            <span>Closed</span>
                                        </div>
                                        <div class="w_center w_55">
                                            <div class="progress">
                                                <div class="progress-bar bg-blue-sky" role="progressbar" aria-valuenow="20.960698689956" aria-valuemin="0" aria-valuemax="100" style="width: <?= $closed_tello['id'] ?>%;">
                                                    <span class="sr-only">20.960698689956%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w_right w_20">
                                            <span><?= $closed_tello['id'] ?></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                                <a href="<?= base_url('task/task') ?>">
                                    <div class="widget_summary">
                                        <div class="w_left w_25">
                                            <span>Pending</span>
                                        </div>
                                        <div class="w_center w_55">
                                            <div class="progress">
                                                <div class="progress-bar bg-blue-sky" role="progressbar" aria-valuenow="20.960698689956" aria-valuemin="0" aria-valuemax="100" style="width: <?= $pending_tello['id'] ?>%;">
                                                    <span class="sr-only">20.960698689956%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w_right w_20">
                                            <span><?= $pending_tello['id'] ?></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                                <a href="<?= base_url('task/task') ?>">
                                    <div class="widget_summary">
                                        <div class="w_left w_25">
                                            <span>Over Due Date</span>
                                        </div>
                                        <div class="w_center w_55">
                                            <div class="progress">
                                                <div class="progress-bar bg-blue-sky" role="progressbar" aria-valuenow="20.960698689956" aria-valuemin="0" aria-valuemax="100" style="width: <?= $over_due_card['id'] ?>%;">
                                                    <span class="sr-only">20.960698689956%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w_right w_20">
                                            <span><?= $over_due_card['id'] ?></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>



                </div>
            </div>

        </div>
        <!-- Start content-->

        <!-- Finish content-->
    </div>
    <br><br>
    <br><br>
</div>