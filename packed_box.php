<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$page_title = 'Packed Boxes';
require_once('includes/load.php');
page_require_level(2);

$all_packed_boxes = find_all_packed_boxes();

$current_month = date('F');

$added_packed_box = array();

if (isset($_POST['add_packed_box'])) {
  $employee_id = remove_junk($db->escape($_POST['employee-id']));
  $product_id = remove_junk($db->escape($_POST['product-id']));
  $quantity = remove_junk($db->escape($_POST['quantity']));
  $packed_month = remove_junk($db->escape($_POST['packed-month']));
  $packed_date = date('Y-m-d H:i:s');
  $box_code = generate_unique_code();
  
  $product_name = find_product_name_by_id($product_id);
  
  $section_name = remove_junk($db->escape($_POST['section-name']));
  $packet_quantity = remove_junk($db->escape($_POST['packet-quantity']));
  $print = remove_junk($db->escape($_POST['print']));
  $seal = remove_junk($db->escape($_POST['seal']));

  $query = "INSERT INTO packed_box (employee_id, product_name, quantity, packed_date, section_name, packet_quantity, print, seal, box_code) 
            VALUES ('{$employee_id}', '{$product_name}', '{$quantity}', '{$packed_date}', '{$section_name}', '{$packet_quantity}', '{$print}', '{$seal}', '{$box_code}')";

  if ($db->query($query)) {
      $session->msg('s', "Packed box added $box_code ");
      $barcodeFilePath = generate_barcode($box_code);
      $added_packed_boxi = array(
          $box_code,
          $barcodeFilePath
      );
    
      echo "Debug - Box Code: " . $box_code . "<br>";
      echo "Debug - Barcode Image Path: " . $barcodeFilePath . "<br>";
  } else {
      $session->msg('d', ' Sorry failed to add packed box!');
  }
  redirect('packed_box.php', false);
}


$all_employees = find_all('employees');
$all_products = find_all('products');

function generate_unique_code() {
    global $db;

    $code = mt_rand(10000, 99999);

    $query = "SELECT id FROM packed_box WHERE box_code = '{$code}'";
    $result = $db->query($query);

    while ($db->num_rows($result) > 0) {
        $code = mt_rand(10000, 99999);
        $result = $db->query($query);
    }

    return $code;
}
// Function to find all packed boxes
function find_all_packed_boxes() {
  global $db;
  $query = "SELECT pb.id, e.employee_id, e.first_name, pb.quantity, MONTHNAME(pb.packed_date) AS packed_month, p.name AS product_name 
            FROM packed_box pb 
            INNER JOIN employees e ON pb.employee_id = e.id 
            INNER JOIN products p ON pb.product_name = p.name";
  return $db->query($query);
}
function generate_barcode($box_code) {
  require('vendor/autoload.php');
  $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
  $barcodeData = $generator->getBarcode($box_code, $generator::TYPE_CODE_128);
  $barcodeFilePath = 'barcode/' . $box_code . '.png';
  file_put_contents($barcodeFilePath, $barcodeData);
  return $barcodeFilePath;
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
        <span>Add Packed Box</span>
      </strong>
    </div>
    <div class="panel-body">
      <div class="col-md-12">
        <form method="post" action="packed_box.php">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="employee-id">Packed By</label>
                <select class="form-control" name="employee-id" required>
                  <option value="">Select Employee</option>
                  <?php foreach ($all_employees as $employee) : ?>
                    <option value="<?php echo remove_junk($employee['employee_id']); ?>">
                      <?php echo remove_junk($employee['employee_id']) . ' - ' . remove_junk($employee['employee_name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="product-id">Product</label>
                <select class="form-control" name="product-id" required>
                  <option value="">Select Product</option>
                  <?php foreach ($all_products as $product) : ?>
                    <option value="<?php echo (int)$product['id']; ?>" data-product-name="<?php echo remove_junk($product['name']); ?>">
                      <?php echo remove_junk($product['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" name="quantity" placeholder="Quantity">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="packet-quantity">Packet Quantity</label>
                <input type="number" class="form-control" name="packet-quantity" placeholder="Packet Quantity" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="print">Print</label>
                <select class="form-control" name="print" required>
                  <option value="">Select Option</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="seal">Seal</label>
                <select class="form-control" name="seal" required>
                  <option value="">Select Option</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="packed-month">Packed Month</label>
                <select class="form-control" name="packed-month" required>
                  <option value="">Select Month</option>
                  <?php foreach (range(1, 12) as $month) : ?>
                    <option value="<?php echo date('F', strtotime("2023-$month-01")); ?>" <?php if (date('F', strtotime("2023-$month-01")) === $current_month) echo 'selected'; ?>>
                      <?php echo date('F', strtotime("2023-$month-01")); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <button type="submit" name="add_packed_box" class="btn btn-primary">Add Packed Box</button>
              </div>
            </div>
          </div>
        </form>
        <?php if (isset($added_packed_box)) : ?>
<div class="col-md-12">
  <div class="alert alert-success">
    <strong>Debug Info:</strong>
    <pre>
      <?php print_r($added_packed_box); ?>
    </pre>
  </div>
</div>
<?php endif; ?>

      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>