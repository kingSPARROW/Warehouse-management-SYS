<?php
$page_title = 'Add Box';
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(2);
$all_products = find_all('products');

// Predefined storage areas
$storage_areas = array("Rack 1", "Rack 2", "Rack 3"); // You can add more if needed

// Function to generate and save barcode
function generate_barcode($box_name) {
  require('vendor/autoload.php');
  $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
  $barcodeData = $generator->getBarcode($box_name, $generator::TYPE_CODE_128);
  $barcodeFilePath = 'barcode/' . $box_name . '.png';
  file_put_contents($barcodeFilePath, $barcodeData);
  return $barcodeFilePath;
}

if (isset($_POST['add_box'])) {
  $product_id = remove_junk($db->escape($_POST['product-id']));
  $quantity = remove_junk($db->escape($_POST['quantity']));
  $box_name = generate_unique_box_number();
  $storage_area = remove_junk($db->escape($_POST['storage-area'])); // Get selected storage area
  $query = "INSERT INTO boxes (product_id, quantity, box_name, storage_area) VALUES ('{$product_id}', '{$quantity}', '{$box_name}', '{$storage_area}')";
  if ($db->query($query)) {
    $session->msg('s', "Box added ");
    $added_box = find_box_by_name($box_name); // Use the correct function

    // Generate and save barcode
    $barcodeFilePath = generate_barcode($box_name);
    
    // Fetch the product name for the given product ID
    $product_name = find_product_name_by_id($product_id);
  } else {
    $session->msg('d', ' Sorry failed to add box!');
    redirect('add_box.php', false);
  }
}
// Add this function to get product name by product ID
function find_product_name_by_id($product_id) {
  global $db;
  $query = "SELECT name FROM products WHERE id = '{$product_id}' LIMIT 1";
  $result = $db->query($query);
  if ($db->num_rows($result) > 0) {
    $product = $db->fetch_assoc($result);
    return $product['name'];
  } else {
    return null;
  }
}

?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="panel panel-default">
    <div class="panel-heading">
      <strong>
        <span class="glyphicon glyphicon-th"></span>
        <span>Add New Box</span>
      </strong>
    </div>
    <div class="panel-body">
      <div class="col-md-7">
        <form method="post" action="add_box.php">
          <div class="form-group">
            <label for="product-id">Product</label>
            <select class="form-control" name="product-id" required>
              <option value="">Select Product</option>
              <?php foreach ($all_products as $product) : ?>
                <option value="<?php echo (int)$product['id']; ?>">
                  <?php echo remove_junk($product['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" name="quantity" placeholder="Quantity">
          </div>
          <div class="form-group">
            <label for="storage-area">Storage Area</label>
            <select class="form-control" name="storage-area">
              <?php foreach ($storage_areas as $area) : ?>
                <option value="<?php echo $area; ?>"><?php echo $area; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <button type="submit" name="add_box" class="btn btn-primary">Generate</button>
          </div>
        </form>
        <?php if (!empty($added_packed_box)) : ?>
          <div class="alert alert-success">
            <!-- Display added packed box details and barcode -->
            <img src="<?php echo $barcodeFilePath; ?>" alt="Barcode" id="printBarcode">
            <br>
            <?php if (isset($added_packed_box['product_name'])) : ?>
              Product Name: <?php echo $added_packed_box['product_name']; ?><br>
            <?php endif; ?>
            Box Code: <?php echo $added_packed_box['box_code']; ?><br>
            Employee ID: <?php echo $added_packed_box['employee_id']; ?><br>
            Quantity: <?php echo $added_packed_box['quantity']; ?><br>
            Packed Month: <?php echo $added_packed_box['packed_month']; ?><br>
            Packet Quantity: <?php echo $added_packed_box['packet_quantity']; ?><br>
            Print: <?php echo $added_packed_box['print']; ?><br>
            Seal: <?php echo $added_packed_box['seal']; ?><br>
            Packed Date: <?php echo $added_packed_box['packed_date']; ?><br>
            <button onclick="printBarcode()" class="btn btn-primary">Print Barcode</button>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>

<script>
  function printBarcode() {
    var printWindow = window.open('', '', 'width=300,height=500');
    printWindow.document.open();
    printWindow.document.write('<html><head><title>Print Barcode</title></head><body>');
    printWindow.document.write('<div style="text-align: center;">');
    printWindow.document.write('<img src="<?php echo $barcodeFilePath; ?>" alt="Barcode" style="width: 200px;">');
    printWindow.document.write('<br>');
    printWindow.document.write('<?php if (isset($product_name)) : ?>');
    printWindow.document.write('Product Name: <?php echo $product_name; ?>');
    printWindow.document.write('<br>');
    printWindow.document.write('<?php endif; ?>');
    printWindow.document.write('Box Number: <?php echo $added_box["box_name"]; ?>');
    printWindow.document.write('<br>');
    printWindow.document.write('Storage Area: <?php echo $added_box["storage_area"]; ?>');
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
  }
</script>
