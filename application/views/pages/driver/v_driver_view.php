<style>
  .col-xs-3 {
    width: 25%;
    background-color: #008080;
  }

  .row {
    margin-left: 0px;
  }

  .container-fluid {
    padding-right: 0px;
    padding-left: 0px
  }

  .btn_footer_panel .tag_ {
    padding-top: 37px;
  }

  body {}

  table,
  th,
  td {
    border: 0px solid black;
  }

  table.center {
    margin-left: auto;
    margin-right: auto;
  }

  .button1 {
    background-color: #4CAF50;
  }

  table,
  table {
    border-collapse: separate;
    border-spacing: 0 1em;
  }

  /* Green */
</style>
<div class="right_col" role="main">
  <div class="clearfix"></div>

  <div class="x_panel card">
    <div align="center">
      <font style="font-size:17px;">
        <?php if ($this->uri->segment(4) == 'e') {
          echo 'User Edit';
        } else {
          echo 'User View';
        } ?>
        <hr />
      </font>
    </div>
    <font style="font-size:14px;">
      <?php if ($this->uri->segment(4) != 'e' && $this->uri->segment(3) == true) { ?>
        </br>
        <table>
          <tr>
            <th>Nama</th>
            <td>: <?= $user->nama ?></td>

          </tr>
          <tr>
            <th>Status</th>
            <td>:<?php if ($user->status == 1) { ?>
              <span style="cursor: default;" class="btn btn-primary">Active</span>
            <?php } else { ?>
              <span style="cursor: default;" class="btn btn-danger">Not Active</span>
            <?php } ?>
            </td>
          </tr>
          <tr>
            <th>Phone</th>
            <td>: <?= $user->phone ?></td>
          </tr>
          <tr>
            <th>Nip</th>
            <td>: <?= $user->nip ?></td>
          </tr>
          <tr>
            <th><a href="<?= base_url('app/user') ?>" class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a></th>
          </tr>
        </table>
        <br>
      <?php } elseif ($this->uri->segment(3) == false) { ?> <!-- add user -->
        <?= $this->session->flashdata('msg') ?>
        <form action="<?= base_url('driver/add_driver') ?>" method="POST">
          <input type="hidden" value="add" name="add">
          <input type="hidden" value="<?= $this->uri->segment('3') ?>" name="id">
          <table>
            <tr>
              <th width="200">Name</th>
              <td> <input type="text" name="nama" class="form-control">
              </td>
            </tr>
            <tr>
              <th width="200">Date of birth</th>
              <td>
                <div class='input-group date' id='myDatepicker2'>
                  <input type='text' id='date_pic' name='tgl_lahir' class="form-control" placeholder="yyyy-mm-dd" data-validate-words="1" required="required" />
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </td>
            </tr>
            <tr>
              <th>Status</th>
              <td>
                <input name="status" value="1" type="radio" id="active">
                <label for="active">Active</label>
                <input name="status" value="0" type="radio" id="noactive">
                <label for="noactive">Not Active</label>
              </td>
            </tr>
            <tr>
              <th>Phone</th>
              <td><input type="text" name="phone" class="form-control"></td>
            </tr>
            <tr>
              <th>Nip</th>
              <td><input type="text" name="nip" class="form-control"></td>
            </tr>
            <tr>
              <th>
                <a class="btn btn-warning" href="<?= base_url('driver/list') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
              </th>
              <td><button type="submit" class="btn btn-primary">Submit</button></td>
            </tr>
          </table>
        </form>
      <?php  } else if ($this->uri->segment(4) == 'e') { ?>
        </br>
        <?= $this->session->flashdata('msg') ?>
        <form action="<?= base_url('driver/driver_edit/' . $this->uri->segment('3')) ?>" method="POST">
          <input type="hidden" value="edit" name="edit">
          <input type="hidden" value="<?= $this->uri->segment('3') ?>" name="id">
          <table>
            <tr>
              <th width="200">Name</th>
              <td> <input type="text" name="nama" class="form-control" value="<?= $user->nama ?>">
              </td>
            </tr>
            <tr>
              <th width="200">Date of Birth</th>
              <td> <input type="date" name="tgl_lahir" class="form-control" value="<?= $user->tgl_lahir ?>">
              </td>
            </tr>
            <tr>
              <th>Status</th>
              <td>
                <input <?= $user->status ? 'checked' : '' ?> name="status" type="radio" value="1" id="active">
                <label for="active">Active</label>
                <input <?= $user->status ? '' : 'checked' ?> name="status" type="radio" value="0" id="noactive">
                <label for="noactive">Not Active</label>
              </td>
            </tr>
            <tr>
              <th>Phone</th>
              <td><input type="text" name="phone" class="form-control" value="<?= $user->phone ?>"></td>
            </tr>
            <tr>
              <th>Nip</th>
              <td><input readonly type="text" name="nip" class="form-control" value="<?= $user->nip ?>"></td>
            </tr>
            <tr>
              <th>
                <a class="btn btn-warning" href="<?= base_url('driver/list') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                <button type="submit" class="btn btn-primary">Update</button>
              </th>
            </tr>
          </table>
        </form>
        <br>
      <?php } ?>
    </font>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('select.js-example-basic-multiple').select2();
    $('div#myDatepicker2').datetimepicker({
      format: 'YYYY-MM-DD',
      maxDate: Date.now() + 90000000
    });
  });

  window.setTimeout(function() {
    $(".alert-success").fadeTo(500, 0).slideUp(500, function() {
      $(this).remove();
    });
  }, 3000);

  window.setTimeout(function() {
    $(".alert-danger").fadeTo(500, 0).slideUp(500, function() {
      $(this).remove();
    });
  }, 3000);
</script>