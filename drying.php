<?php
$page_title = 'Drying Gummies';
require_once('includes/load.php');
page_require_level(2);

// Function to generate a unique tray ID
function generate_unique_tray_id() {
  $prefix = 'TRAY';
  $tray_id = uniqid($prefix);
  return strtoupper($tray_id);
}
function find_all_batches() {
  global $db;
  $query = "SELECT id, batch_number FROM batches_gummies";
  return $db->query($query);
}

// Function to generate and save barcode
function generate_barcode($tray_id) {
  require('vendor/autoload.php');
  $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
  $barcodeData = $generator->getBarcode($tray_id, $generator::TYPE_CODE_128);
  $barcodeFilePath = 'barcode/' . $tray_id . '.png';
  file_put_contents($barcodeFilePath, $barcodeData);
  return $barcodeFilePath;
}
if (isset($_POST['add_drying'])) {
  $req_fields = array('gummie-id', 'drying-time', 'batch-number');

  // Validate required fields
  validate_fields($req_fields);

  if (empty($errors)) {
    $gummie_id = (int)$_POST['gummie-id'];
    $drying_time = (float)$_POST['drying-time'];
    $date = make_date();
    $tray_id = generate_unique_tray_id();
    $batch_id = (int)$_POST['batch-number'];

    // Insert the drying information into the database
    $query  = "INSERT INTO drying (";
    $query .= " gummie_id, drying_time, created_at, tray_id, batch_id";
    $query .= ") VALUES (";
    $query .= " '{$gummie_id}', '{$drying_time}', '{$date}', '{$tray_id}', '{$batch_id}'";
    $query .= ")";

    if ($db->query($query)) {
      $created_id = $db->insert_id();
      // Update the gummie with the create_id
      $update_query = "UPDATE gummies SET create_id='{$created_id}' WHERE id='{$gummie_id}'";
      $db->query($update_query);

      // Get gummie name
      $gummie_name = find_gummie_name_by_id($gummie_id);

      // Generate and save barcode
      $barcodeFilePath = generate_barcode($tray_id);
      $batch_id = (int)$_POST['batch-number'];

    // Get batch information by batch_id
      $batch_info = find_batch_info_by_id($batch_id);
      if ($batch_info) {
        $mfg_date = $batch_info['mfg_date'];
        $batch_size = $batch_info['batch_size'];
      $added_drying = array(
        'tray_id' => $tray_id,
        'gummie_name' => $gummie_name,
        'drying_time' => $drying_time,
        'batch_size' => $mfg_date,

      );
    } else {
      $session->msg('d', 'Sorry, failed to add drying information.');
      redirect('drying.php', false);
    }
  } else {
    $session->msg('d', $errors);
    redirect('drying.php', false);
  }
}
}
$all_gummies = find_all('gummies');
$all_batches = find_all_batches();
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="panel panel-info">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Drying Gummies</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="drying.php" class="clearfix">
            <div class="form-group">
              <label for="gummie-id">Select Gummie:</label>
              <select class="form-control" name="gummie-id">
                <option value="">Select Gummie</option>
                <?php foreach ($all_gummies as $gummie) : ?>
                  <option value="<?php echo (int)$gummie['id']; ?>">
                    <?php echo $gummie['name']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="batch-number">Select Batch Number:</label>
              <select class="form-control" name="batch-number">
                <option value="">Select Batch Number</option>
                <?php foreach ($all_batches as $batch) : ?>
                  <option value="<?php echo (int)$batch['id']; ?>">
                    <?php echo $batch['batch_number']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="drying-time">Time Taken to Dry (hrs):</label>
              <input type="number" step="0.01" class="form-control" name="drying-time" placeholder="Drying Time (hrs)">
            </div>
            <button type="submit" name="add_drying" class="btn btn-warning">Add Drying Information</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
if (isset($added_drying)) {
    echo '<div class="alert alert-success">';
    echo 'Generated Tray ID: ' . $added_drying['tray_id'] . '<br>';
    echo '<div style="display: none;">'; // Hide these values from the page
    echo 'Gummie Name: ' . $added_drying['gummie_name'] . '<br>';
    echo 'Current Time: ' . date('Y-m-d H:i:s') . '<br>';
    echo 'Drying Time: ' . $added_drying['drying_time'] . ' hrs<br>';
    echo '</div>';
    echo '<img src="' . $barcodeFilePath . '" alt="Barcode">';
    echo '<br>';
    echo '<a href="' . $barcodeFilePath . '" download class="btn btn-success">Download Barcode</a>';
    echo '<br>';
    echo '<button onclick="printBarcode(\'' . $added_drying['gummie_name'] . '\', \'' . date('Y-m-d H:i:s') . '\', \'' . $added_drying['drying_time'] . '\')" class="btn btn-primary">Print Barcode</button>';
    echo '</div>';
  }  
?>

<?php include_once('layouts/footer.php'); ?>

<script>
  function printBarcode(gummieName, currentTime, dryingTime) {
    var printWindow = window.open('', '', 'width=300,height=500');
    printWindow.document.open();
    printWindow.document.write('<html><head><title>Print Barcode</title></head><body>');
    printWindow.document.write('<div style="text-align: center;">');
    printWindow.document.write('<img src="<?php echo $barcodeFilePath; ?>" alt="Barcode" style="width: 600px;">');
    printWindow.document.write('<br>');
    printWindow.document.write('Gummie Name: ' + gummieName);
    printWindow.document.write('<br>');
    printWindow.document.write('Current Time: ' + currentTime);
    printWindow.document.write('<br>');
    printWindow.document.write('Drying Time: ' + dryingTime + ' hrs');
    printWindow.document.write('<br>');
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
  }
</script>
