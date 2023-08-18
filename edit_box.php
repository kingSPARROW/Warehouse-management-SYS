<?php
$page_title = 'Edit Box';
require_once('includes/load.php');
page_require_level(2);

if(isset($_GET['id'])) {
  $box_id = (int)$_GET['id'];
  $box = find_box_by_id($box_id);

  if(!$box) {
    $session->msg("d", "Box not found!");
    redirect('manage_boxes.php');
  }
} else {
  $session->msg("d", "Missing box ID.");
  redirect('manage_boxes.php');
}

// Fetch all products that can be put into boxes
$boxable_products = find_all('products', "WHERE is_box = 0");

if(isset($_POST['edit_box'])) {
  $new_quantity = remove_junk($db->escape($_POST['new-quantity']));
  $new_storage_area = remove_junk($db->escape($_POST['new-storage-area']));
  $new_product_id = remove_junk($db->escape($_POST['new-product-id']));
  
  // Update box details
  $query = "UPDATE boxes SET quantity='{$new_quantity}', storage_area='{$new_storage_area}', product_id='{$new_product_id}' WHERE id='{$box_id}'";
  $result = $db->query($query);
  if($result) {
    $session->msg("s", "Box details updated.");
    redirect("edit_box.php?id={$box_id}");
  } else {
    $session->msg("d", "Failed to update box details.");
    redirect("edit_box.php?id={$box_id}");
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
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-edit"></span>
          <span>Edit Box</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_box.php?id=<?php echo $box_id; ?>">
          <div class="form-group">
            <label for="new-quantity">Quantity</label>
            <input type="number" class="form-control" name="new-quantity" value="<?php echo $box['quantity']; ?>">
          </div>
          <div class="form-group">
            <label for="new-storage-area">Storage Area</label>
            <input type="text" class="form-control" name="new-storage-area" value="<?php echo $box['storage_area']; ?>">
          </div>
          <div class="form-group">
            <label for="new-product-id">Product Name</label>
            <select class="form-control" name="new-product-id">
              <option value="">Select Product</option>
              <?php foreach ($boxable_products as $product) : ?>
                <option value="<?php echo (int)$product['id']; ?>" <?php if($box['product_id'] === $product['id']) echo 'selected'; ?>>
                  <?php echo remove_junk($product['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <button type="submit" name="edit_box" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
