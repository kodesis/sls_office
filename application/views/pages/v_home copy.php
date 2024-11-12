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

        <!-- <div class="header" style="margin-left: 1%">
            <h2>Total penggunaan spare part</h2>
            <hr>
        </div> -->
        <div class="row justify-content-center">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div id="chart_div_spare_part"></div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div id="chart_div_tonase"></div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div id="chart_div_bbm"></div>
            </div>
        </div>
        <br>
        <div class="row">
            <!-- <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="x_panel tile fixed_height_300">
                    <div class="x_title">
                        <h2><i class="fa fa-envelope-o"></i> Total Penggunaan Spare part</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <h4>Total Sparepart : <?php
                                                if (!empty($total_sparepart->total_sum)) {
                                                    echo $total_sparepart->total_sum;
                                                } else {
                                                    echo '0';
                                                }
                                                ?></h4><br>
                        <?php
                        if (!empty($total_sparepart_perbulan)) {
                            foreach ($total_sparepart_perbulan as $tsp) {
                        ?>
                                <a href="<?= base_url('task/task') ?>">
                                    <div class="widget_summary">
                                        <div class="w_left w_25">
                                            <span><?= $tsp->month_year ?></span>
                                        </div>
                                        <div class="w_center w_55">
                                            <div class="progress">
                                                <div class="progress-bar bg-blue-sky" role="progressbar" aria-valuenow="46.288209606987" aria-valuemin="0" aria-valuemax="100" style="width: <?= $tsp->total_sum ?>%;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w_right w_20">
                                            <span><?= $tsp->total_sum ?></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            <?php
                            }
                        } else {
                            ?>
                            <span>Tidak Ada Yang Keluar</span>

                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div> -->
        </div>
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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php
// Initialize the data array with headers for Google Charts
$chart_data_spare_part = [['Month', 'Biaya Total Spare Parts']];

// Loop through the database results and populate the chart data array
foreach ($total_sparepart_perbulan as $row) {
    $chart_data_spare_part[] = [$row->month_year, (int)$row->total_sum];
}

$chart_data_tonase = [['Month', 'Total Tonase']];

// Loop through the database results and populate the chart data array
foreach ($total_tonase as $row) {
    $chart_data_tonase[] = [$row->month_year, (int)$row->total_sum];
}

$chart_data_bbm = [['Month', 'Total Biaya BBM']];

// Loop through the database results and populate the chart data array
foreach ($total_bbm as $row) {
    $chart_data_bbm[] = [$row->month_year, (int)$row->total_sum];
}
?>
<script type="text/javascript">
    // Load the Google Charts API
    google.charts.load('current', {
        'packages': ['corechart']
    });

    // Callback function to draw the chart
    google.charts.setOnLoadCallback(drawChart_sparepart);

    function drawChart_sparepart() {
        // Create the data table using PHP
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($chart_data_spare_part); ?>);

        // Set chart options
        var options = {
            title: 'Biaya Total Spare Parts Per Month',
            chartArea: {
                width: '50%'
            },
            hAxis: {
                title: 'Biaya Total Spare Parts',
                minValue: 0
            },
            vAxis: {
                title: 'Month'
            }
        };

        // Instantiate and draw the chart
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_spare_part'));
        chart.draw(data, options);
    }

    google.charts.setOnLoadCallback(drawChart_tonase);

    function drawChart_tonase() {
        // Create the data table using PHP
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($chart_data_tonase); ?>);

        // Set chart options
        var options = {
            title: 'Total Tonase Per Month',
            chartArea: {
                width: '50%'
            },
            hAxis: {
                title: 'Total Tonase',
                minValue: 0
            },
            vAxis: {
                title: 'Month'
            }
        };

        // Instantiate and draw the chart
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_tonase'));
        chart.draw(data, options);
    }

    google.charts.setOnLoadCallback(drawChart_bbm);

    function drawChart_bbm() {
        // Create the data table using PHP
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($chart_data_bbm); ?>);

        // Set chart options
        var options = {
            title: 'Biaya Total BBM Per Month',
            chartArea: {
                width: '50%'
            },
            hAxis: {
                title: 'Biaya Total BBM',
                minValue: 0
            },
            vAxis: {
                title: 'Month'
            }
        };

        // Instantiate and draw the chart
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_bbm'));
        chart.draw(data, options);
    }
</script>