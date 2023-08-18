<?php
$page_title = 'Product Transfer';
require_once('includes/load.php');
page_require_level(2);

// Fetch all products for the dropdown
$products = find_all('products', "WHERE is_box = 0");
$product_names = array();
foreach ($products as $product) {
  $product_names[] = $product['name'];
}

// Destination options (predefined)
$destination_options = array('Amazon', 'Flipkart', 'Ebay', 'Walmart'); // Update this with your desired destination options

if (isset($_POST['transfer'])) {
  $product_name = $db->escape($_POST['product_name']);
  $destination = $db->escape($_POST['destination']);
  $quantity = (int)$_POST['quantity'];
  $transfer_date = $db->escape($_POST['transfer_date']);

  // Validate input data
  if (empty($product_name) || empty($destination) || empty($quantity) || empty($transfer_date)) {
    $session->msg('d', 'Please fill all required fields.');
    redirect('product_transfer.php', false);
  }

  // Get the current product quantity in the boxes
  $product_id = find_product_id_by_name($product_name);
  if (!$product_id) {
    $session->msg('d', 'Invalid product name. Please select a valid product.');
    redirect('product_transfer.php', false);
  }

  $sql_get_quantity = "SELECT SUM(quantity) AS total_quantity FROM boxes WHERE product_id = '{$product_id}'";
  $result_get_quantity = $db->query($sql_get_quantity);
  $product_quantity_in_boxes = $db->fetch_assoc($result_get_quantity)['total_quantity'];

  if ($product_quantity_in_boxes < $quantity) {
    $session->msg('d', 'Insufficient quantity in the boxes for transfer.');
    redirect('product_transfer.php', false);
  }

  // Deduct the transferred quantity from the boxes
  $sql_update_quantity = "UPDATE boxes SET quantity = quantity - '{$quantity}' WHERE product_id = '{$product_id}' ORDER BY id ASC LIMIT 1";
  $result_update_quantity = $db->query($sql_update_quantity);

  if (!$result_update_quantity) {
    $session->msg('d', 'Failed to deduct quantity from the boxes. Please try again.');
    redirect('product_transfer.php', false);
  }

  // Record the product transfer
  $sql = "INSERT INTO product_transfers (box_id, product_id, destination, quantity, transfer_date) SELECT id, '{$product_id}', '{$destination}', '{$quantity}', '{$transfer_date}' FROM boxes WHERE product_id = '{$product_id}' ORDER BY id ASC LIMIT 1";
  if ($db->query($sql)) {
    $session->msg('s', 'Product transfer recorded successfully.');
    redirect('product_transfer.php', false);
  } else {
    $session->msg('d', 'Failed to record product transfer. Please try again.');
    redirect('product_transfer.php', false);
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
  <div class="col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-transfer"></span>
          <span>Product Transfer</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="product_transfer.php">
          <div class="form-group">
            <label for="product_name">Product Name</label>
            <select class="form-control" name="product_name" id="product_name" required>
              <option value="">Select a product</option>
              <?php foreach ($product_names as $name) : ?>
                <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="destination">Destination</label>
            <select class="form-control" name="destination" id="destination" required>
              <option value="">Select a destination</option>
              <?php foreach ($destination_options as $option) : ?>
                <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" name="quantity" id="quantity" required>
          </div>
          <div class="form-group">
            <label for="transfer_date">Transfer Date</label>
            <input type="date" class="form-control" name="transfer_date" id="transfer_date" required>
          </div>
          <div class="form-group">
            <button type="submit" name="transfer" class="btn btn-primary">Record Transfer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
