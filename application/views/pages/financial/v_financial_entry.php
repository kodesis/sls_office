<style>
    .btn-warning:hover {
        color: #fff !important;
        background-color: #ec971f !important;
        border-color: #d58512 !important;
    }
</style>

<div class="right_col" role="main">
    <div class="clearfix"></div>

    <!-- Start content-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel card">
                <div class="x_title">
                    <h2>Financial entry
                        <small>Please fill below</small>
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li class="dropdown">
                            <a class="btn btn-warning btn-sm" href="<?= base_url('upload/format_data.xlsx') ?>" download style="font-size: 12px;padding: 5px 10px;color: white;">
                                Download Format Data
                            </a>
                        </li>
                        <li class="dropdown">
                            <button class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#upload_modal" type="button" style="color: white;">
                                Upload Data
                            </button>
                        </li>
                        <li class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color: white;">
                                Input Multiple
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="<?= base_url('financial/financial_entry/debit') ?>">Multi Kredit
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= base_url('financial/financial_entry/kredit') ?>">Multi Debit
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="x_content">
                    <!-- <br> -->
                    <form class="form-label-left input_mask" method="POST" action="<?= base_url('financial/store_financial_entry') ?>" enctype="multipart/form-data">
                        <div class="col-md-6 col-xs-12 form-group has-feedback">
                            <label for="" class="form-label">Debit</label>
                            <select name="neraca_debit" id="neraca_debit" class="form-control select2" style="width: 100%" required>
                                <option value="">:: Pilih pos neraca debit</option>
                                <?php foreach ($coa as $c) : ?>
                                    <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>">
                                        <?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 col-xs-12 form-group has-feedback">
                            <label for="" class="form-label">Kredit</label>
                            <select name="neraca_kredit" id="neraca_kredit" class="form-control select2" style="width: 100%" required>
                                <option value="">:: Pilih pos neraca kredit</option>
                                <?php
                                foreach ($coa as $c) :
                                ?>
                                    <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?> </option>
                                <?php
                                endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-12 form-group has-feedback">
                            <label for="" class="form-label">Nominal</label>
                            <input type="text" class="form-control uang" name="input_nominal" id="input_nominal" placeholder="Nominal" autofocus required>
                        </div>
                        <div class="col-md-6 col-xs-12 form-group has-feedback">
                            <label for="" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6 col-xs-12 form-group has-feedback">
                            <label for="" class="form-label">Keterangan</label>
                            <textarea name="input_keterangan" id="input_keterangan" class="form-control" placeholder="Keterangan" oninput="this.value = this.value.toUpperCase()" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 col-xs-12 form-group has-feedback">
                            <label for="file_upload" class="form-label">Upload file (opsional)</label>
                            <input type="file" name="file_upload" id="file_upload" class="form-control">
                        </div>
                        <div class="form-group row">
                            <div class="col-md-9 col-sm-9  offset-md-3 mt-3">
                                <button class="btn btn-primary" type="reset">Reset</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Upload Financial Entry</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="upload_file_fe">
                        <div class="col-md-12 col-sm-12  offset-md-3 mt-3">
                            <label for="" class="form-label">File Format Data</label>
                            <input class="form-control" type="file" name="format_data" id="format_data">
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="upload_fe()">Save</button>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/vendors/jquery/dist/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="<?= base_url(); ?>assets/select2/css/select2.min.css">
<script type="text/javascript" src="<?= base_url(); ?>assets/select2/js/select2.min.js"></script>

<script src="<?= base_url(); ?>assets/js/jquery.mask.js"></script>
<script>
    $(document).ready(function() {
        $('.uang').mask('000.000.000.000.000', {
            reverse: true
        });
        $('.select2').select2();

        $("form").on("submit", function() {
            Swal.fire({
                title: "Loading...",
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });
        });

        function formatState(state, colorAktiva, colorPasiva, signAktiva, signPasiva) {
            if (!state.id) {
                return state.text;
            }

            var color = state.element.dataset.posisi == "AKTIVA" ? colorAktiva : colorPasiva;
            var sign = state.element.dataset.posisi == "AKTIVA" ? signAktiva : signPasiva;

            var $state = $('<span style="background-color: ' + color + ';"><strong>' + state.text + ' ' + sign + '</strong></span>');

            return $state;
        };

        function formatStateDebit(state) {
            return formatState(state, '#2ecc71', '#ff7675', '(+)', '(-)');
        }

        function formatStateKredit(state) {
            return formatState(state, '#ff7675', '#2ecc71', '(-)', '(+)');
        }

        $('#neraca_debit').select2({
            // templateResult: formatStateDebit,
            templateSelection: formatStateDebit
        });

        $('#neraca_kredit').select2({
            // templateResult: formatStateKredit,
            templateSelection: formatStateKredit
        });

        $('#neraca_debit, #neraca_kredit').change(function() {
            var debit = $('#neraca_debit').find(":selected").val();
            var kredit = $('#neraca_kredit').find(":selected").val();
            disabledSubmit(debit, kredit);
        });

        function disabledSubmit(debit, kredit) {
            if (debit && kredit) {
                if (debit == kredit) {
                    console.log('sama');
                    $('.btn-success').prop('disabled', true);
                } else {
                    console.log('tidak sama');
                    $('.btn-success').prop('disabled', false);
                }
            }
        }
    });

    function upload_fe() {
        const ttlnamaValue = $('#format_data').val();


        if (!ttlnamaValue) {
            swal.fire({
                customClass: 'slow-animation',
                icon: 'error',
                showConfirmButton: false,
                title: 'Kolom File Tidak Boleh Kosong',
                timer: 1500
            });
        } else {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    InputEvent: 'form-control',
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Ingin Menambahkan Data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambahkan',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {

                if (result.isConfirmed) {

                    var url;
                    var formData;
                    url = "<?php echo site_url('Financial/upload_financial_entry') ?>";

                    // window.location = url_base;
                    var formData = new FormData($("#upload_file_fe")[0]);
                    let accumulatedResponse = ""; // Variable to accumulate the response

                    $.ajax({
                        url: url,
                        type: "POST",
                        dataType: "text", // Change to 'text' to handle server-sent events
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            // Show the progress dialog before sending the request
                            Swal.fire({
                                title: 'Uploading...',
                                html: `
                <progress id="progressBar" value="0" max="100" style="width: 100%;"></progress>
                <div id="progressText" style="margin-top: 10px; font-weight: bold;">0/0 Data</div>
            `,
                                allowOutsideClick: false,
                                showConfirmButton: false
                            });
                        },
                        xhrFields: {
                            onprogress: function(e) {
                                // Read the response text for progress updates
                                accumulatedResponse += e.currentTarget.responseText; // Accumulate responses

                                var response = e.currentTarget.responseText.trim().split('\n');

                                // Loop through each line to find progress data
                                response.forEach(function(line) {
                                    try {
                                        var progressData = JSON.parse(line.replace("data: ", ""));
                                        if (progressData.progress) {
                                            $("#progressBar").val(progressData.progress);
                                            $("#progressText").text(`${progressData.currentRow}/${progressData.totalRows} Data`);
                                        }
                                    } catch (error) {
                                        console.error("Error parsing progress data:", error);
                                    }
                                });
                            },
                        },
                        success: function(data) {
                            try {
                                // Attempt to parse the final response
                                var finalResponse = JSON.parse(accumulatedResponse.trim().split('\n').pop()); // Get the last line which should be the status
                                console.log("Response data:", finalResponse); // Log final response to see its structure
                                if (!finalResponse.status) swal.fire('Gagal menyimpan data', 'error');
                                else {

                                    // document.getElementById('rumahadat').reset();
                                    // $('#add_modal').modal('hide');
                                    (JSON.stringify(data));
                                    // alert(data)
                                    swal.fire({
                                        customClass: 'slow-animation',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        title: 'Berhasil Menambahkan Data',
                                        timer: 3000
                                    });
                                    document.getElementById('upload_file_fe').reset(); // Reset the form
                                    $('#upload_modal').modal('hide'); // Hide the modal
                                    // location.reload();

                                }
                            } catch (error) {
                                // If parsing fails, log the error
                                console.error("Error parsing final response:", error);
                                swal.fire('Gagal menyimpan data', 'error');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            swal.fire('Operation Failed!', errorThrown, 'error');
                        },
                        complete: function() {
                            console.log('Editing job done');
                        }
                    });


                }

            })
        }
    }
</script>