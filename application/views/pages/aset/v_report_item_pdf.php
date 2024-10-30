<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Working Supply</title>


  <style>
    * {
      margin: 0;
      padding: 0;
      font-family: Arial, Helvetica, sans-serif;
      font-size: 8pt;
    }

    body {
      margin: 30px;
    }

    #item {
      border-collapse: collapse;
      width: 100%;
    }

    #item td,
    #item th {
      border: 1px solid #ddd;
      padding: 5px;
    }

    #item tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    #item tr:hover {
      background-color: #ddd;
    }

    #item th {
      padding-top: 5px;
      padding-bottom: 5px;
      text-align: left;
      background-color: #615e5e;
      color: white;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .judul {
      margin-bottom: 30px;
      text-align: center;
      font-weight: 800;
      text-transform: uppercase;
    }
  </style>
</head>

<body>
  <?php if ($jenis == 'all') { ?>
    <div class="judul">Working Supply All Item</div>
    <table width="100%" id="item">
      <thead>
        <tr>
          <th>No.</th>
          <th>Tanggal</th>
          <th>Jenis</th>
          <th>Keterangan</th>
          <th>Item</th>
          <th>Serial Number</th>
          <th>Unit</th>
          <th>Stok Awal</th>
          <th>IN</th>
          <th>OUT</th>
          <th>Stok Akhir</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $total = 0;
        foreach ($report as $r) {
          $asset = $this->db->get_where('asset_list', ['Id' => $r['asset_id']])->row_array();
          $total += $r['harga'] * $r['jml'];

        ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= tgl_indo(date('Y-m-d', strtotime($r['tanggal']))); ?></td>
            <td><?= $r['jenis']; ?></td>
            <td><?= $r['keterangan'] ?></td>
            <td><?= $r['nama']; ?></td>
            <td>
              <?php
              if ($r['serial_number']) {
                foreach (json_decode($r['serial_number']) as $s) {
                  if ($s != 0) {
                    $serial = $this->db->get_where('item_detail', ['Id' => $s])->row_array();
                    echo $serial['serial_number'] . '<br>';
              ?>

              <?php } else {
                    echo '-';
                  }
                }
              } else {
                echo '-';
              } ?>
            </td>
            <td><?= $r['asset_id'] ? $asset['nama_asset'] : "-"; ?></td>
            <td><?= $r['stok_awal'] ?></td>
            <td>
              <?php
              if ($r['jenis'] == 'IN' or $r['jenis'] == 'REPAIR IN') {
                echo $r['jml'];
              } else {
                echo "-";
              }
              ?>
            </td>
            <td>
              <?php
              if ($r['jenis'] == 'OUT' or $r['jenis'] == 'REPAIR OUT') {
                echo $r['jml'];
              } else {
                echo "-";
              }
              ?>
            </td>
            <td><?= $r['stok_akhir'] ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  <?php } else { ?>
    <div class="judul">Working Supply Item <?= $item_list['nama'] ?></div>
    <table width="100%" id="item">
      <thead>
        <tr>
          <th>No.</th>
          <th>Tanggal</th>
          <th>Serial Number</th>
          <th>Keterangan</th>
          <th>Jenis</th>
          <th>Unit</th>
          <th>Stok Awal</th>
          <th>IN</th>
          <th>OUT</th>
          <th>Stok Akhir</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $total = 0;
        foreach ($report as $r) {
          $asset = $this->db->get_where('asset_list', ['Id' => $r['asset_id']])->row_array();
          $total += $r['harga'] * $r['jml'];
        ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= tgl_indo(date('Y-m-d', strtotime($r['tanggal']))); ?></td>
            <td>
              <?php
              if ($r['serial_number']) {
                foreach (json_decode($r['serial_number']) as $s) {
                  if ($s != 0) {
                    $serial = $this->db->get_where('item_detail', ['Id' => $s])->row_array();
                    echo $serial['serial_number'] . '<br>';
              ?>

              <?php } else {
                    echo '-';
                  }
                }
              } else {
                echo '-';
              } ?>
            </td>
            <td><?= $r['keterangan'] ?></td>
            <td><?= $r['jenis']; ?></td>
            <td><?= $r['asset_id'] ? $asset['nama_asset'] : "-"; ?></td>
            <td><?= $r['stok_awal'] ?></td>
            <td>
              <?php
              if ($r['jenis'] == 'IN' or $r['jenis'] == 'REPAIR IN') {
                echo $r['jml'];
              } else {
                echo "-";
              }
              ?>
            </td>
            <td>
              <?php
              if ($r['jenis'] == 'OUT' or $r['jenis'] == 'REPAIR OUT') {
                echo $r['jml'];
              } else {
                echo "-";
              }
              ?>
            </td>
            <td><?= $r['stok_akhir'] ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  <?php } ?>
</body>

</html>