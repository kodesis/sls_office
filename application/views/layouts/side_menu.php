<div class="menu_section">
    <h3>General</h3>
    <ul class="nav side-menu">
        <li>
            <a><i class="fa fa-home"></i>Home<span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                <li><a href="<?php echo base_url(); ?>home">Dashboard</a></li>
                <!-- <li><a href="<?php echo base_url(); ?>home/banner">Banner</a></li> -->
                <li><a href="<?php echo base_url(); ?>Detail_No_Lambung/list">Detail No Lambung</a></li>
            </ul>
        </li>
        <?php $a = $this->session->userdata('level');
        if (strpos($a, '40') !== false) { ?>
            <li>
                <a><i class="fa fa-edit"></i>BOC Digital Memo<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '401') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/create_memo">Create</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '401') !== false) {
                    ?>
                        <li><a href="<?php echo base_url(); ?>app/inbox">Inbox</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '401') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/send_memo">Outbox</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php $a = $this->session->userdata('level');
        if (strpos($a, '70') !== false) { ?>
            <li>
                <a><i class="fa fa-edit"></i>External Letter<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '701') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/letter_in">Letter In</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '701') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/x">Letter Out</a></li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>
        <?php $a = $this->session->userdata('level');
        if (strpos($a, '60') !== false) { ?>
            <li>
                <a><i class="fa fa-edit"></i>Tello<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '601') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>task/task">Task List</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '601') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>task/create_task">Create</a></li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>
        <?php $a = $this->session->userdata('level');
        if (strpos($a, '50') !== false) { ?>
            <li>
                <a><i class="fa fa-edit"></i>Management Asset<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '501') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/asset_list">Asset List</a></li>
                        <li><a href="<?php echo base_url(); ?>asset/item_list">Item List</a></li>
                        <li><a href="<?php echo base_url(); ?>asset/item_out">Item Out</a></li>
                        <li><a href="<?php echo base_url(); ?>asset/vendors">Vendors</a></li>
                    <?php }
                    if (strpos($a, '502') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>asset/po_list">Purchase Order</a></li>
                        <li><a href="<?php echo base_url(); ?>asset/ro_list">Release Order</a></li>
                    <?php }
                    if (strpos($a, '504') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>asset/report_asset">Report Asset</a></li>
                    <?php }
                    if (strpos($a, '505') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>asset/working_supply">Working Supply</a></li>
                    <?php }
                    if (strpos($a, '506') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>asset/repair">Repair In</a></li>
                    <?php }
                    if (strpos($a, '507') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>asset/repair_out">Repair Out</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <!-- <?php $a = $this->session->userdata('level');
                if (strpos($a, '10') !== false) { ?>
            <li>
                <a><i class="fa fa-edit"></i>Queue<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '101') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/antrian_input">Create Queue</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '102') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/antrian_panggil">Queue Management</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '103') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/antrian_monitor">Queue Monitor </a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?> -->
        <!-- <?php $a = $this->session->userdata('level');
                if (strpos($a, '20') !== false) { ?>
            <li>
                <a><i class="fa fa-edit"></i>Mit-E<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '201') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/quotation">Quotation</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '202') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/">Invoice</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '203') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/">Report</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?> -->
        <?php $a = $this->session->userdata('level');
        if (strpos($a, '30') !== false) { ?>
            <li>
                <a><i class="fa fa-edit"></i>Human Resources<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '301') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>app/import">Upload Gaji</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '302') !== false) { ?>
                        <!--li><a href="<?php echo base_url(); ?>app/slip_gaji_pdf">Slip Gaji</a></li-->
                        <li><a href="<?php echo base_url(); ?>app/cetak_gaji">Slip Gaji</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '302') !== false) { ?>
                        <!--li><a href="<?php echo base_url(); ?>app/slip_gaji_pdf">Slip Gaji</a></li-->
                        <li><a href="<?php echo base_url(); ?>app/absen_wfh">Absen WFH</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '303') !== false) { ?>
                        <!--li><a href="<?php echo base_url(); ?>app/slip_gaji_pdf">Slip Gaji</a></li-->
                        <li><a href="<?php echo base_url(); ?>app/user">User</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '302') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>cuti/view">Cuti</a></li>
                    <?php } ?>
                    <?php $a = $this->session->userdata('level');
                    if (strpos($a, '302') !== false) { ?>
                        <li><a href="<?php echo base_url(); ?>absensi">Absensi</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php $a = $this->session->userdata('level');
        if (strpos($a, '80') !== false) { ?>
            <li>
                <a><i class="fa fa-edit"></i>Financial<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php
                    if (strpos($a, '805') !== false) { ?>
                        <li>
                            <a href="<?= base_url(); ?>financial/financial_entry">Financial Entry</a>
                        </li>
                    <?php }
                    if (strpos($a, '806') !== false) { ?>
                        <li>
                            <a href="<?= base_url(); ?>financial/fe_pending">FE Pending</a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>financial/approved_fe">Approved FE</a>
                        </li>
                    <?php }
                    if (strpos($a, '807') !== false) { ?>
                        <li>
                            <a href="<?= base_url(); ?>financial/invoice">Invoice</a>
                        </li>
                    <?php }
                    if (strpos($a, '808') !== false) { ?>
                        <li>
                            <a href="<?= base_url(); ?>customer">Customer</a>
                        </li>
                    <?php }
                    if (strpos($a, '809') !== false) { ?>
                        <li>
                            <a href="<?= base_url(); ?>financial/showreport">Neraca L/R (current)</a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>financial/reportByDate">Neraca L/R (by date)</a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>financial/coa_report">Arus Kas</a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>financial/list_coa">List CoA</a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>financial/closing">Closing EoM</a>
                        </li>
                    <?php } ?>
                    <?php
                    if (strpos($a, '801') !== false) { ?>
                        <li>
                            <a href="<?= base_url(); ?>pengajuan/list">List Pengajuan</a>
                        </li>
                    <?php }
                    if (strpos($a, '802') !== FALSE) {
                    ?>
                        <li>
                            <a href="<?= base_url(); ?>pengajuan/approval_spv">Approval Kepala Divisi</a>
                        </li>
                    <?php }
                    if (strpos($a, '803') !== FALSE) {
                    ?>
                        <li>
                            <a href="<?= base_url(); ?>pengajuan/approval_keuangan">Approval Keuangan</a>
                        </li>
                    <?php }
                    if (strpos($a, '804') !== FALSE) {
                    ?>
                        <li>
                            <a href="<?= base_url(); ?>pengajuan/approval_direksi">Approval Direksi</a>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="<?= base_url(); ?>ritasi/list">Ritasi</a>
                    </li>
                    <li>
                        <a href="<?= base_url(); ?>bbm/list">BBM</a>
                    </li>
                </ul>
            </li>
        <?php } ?>
        <li>
            <a><i class="fa fa-key"></i>Password<span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                <li><a href="<?php echo base_url(); ?>login/password">Change Password</a></li>
            </ul>
        </li>
    </ul>
</div>

<!--footer menu-->
<div class="sidebar-footer hidden-small">
    <a data-toggle="tooltip" data-placement="top" title="Settings">
        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="Lock">
        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="Logout" href="<?php echo base_url(); ?>login/logout">
        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
    </a>
</div>
<!--footer menu-->

<div class="menu_section">
</div>

<script>
    function clickdb1() {
        return confirm('Are you sure to Back Up Data Base? ');
    }

    function clickdb2() {
        return confirm('Are you sure to clear Data Base? ');
    }
</script>