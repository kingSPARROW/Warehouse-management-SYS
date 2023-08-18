<?php
$page_title = 'Manage Boxes';
require_once('includes/load.php');
page_require_level(2);

// Fetch all boxes with product information
$all_boxes = find_all_boxes_with_product();

// Fetch all products that can be put into boxes
$boxable_products = find_all('products', "WHERE is_box = 0");

// Delete Box Logic
if (isset($_GET['delete_id'])) {
  $delete_id = (int)$_GET['delete_id'];
  if (delete_box_by_id($delete_id)) {
    $session->msg('s', "Box deleted");
    redirect('manage_boxes.php');
  } else {
    $session->msg('d', 'Failed to delete box');
    redirect('manage_boxes.php');
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
          <span class="glyphicon glyphicon-th"></span>
          <span>Manage Boxes</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Box Name</th>
              <th>Product Inside</th>
              <th>Quantity</th>
              <th>Storage Area</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_boxes as $box): ?>
              <tr>
                <td><?php echo $box['id']; ?></td>
                <td><?php echo remove_junk($box['box_name']); ?></td>
                <td><?php echo remove_junk($box['product_name']); ?></td>
                <td><?php echo remove_junk($box['quantity']); ?></td>
                <td><?php echo remove_junk($box['storage_area']); ?></td>
                <td>
                  <a href="generate_barcode.php?box_name=<?php echo urlencode($box['box_name']); ?>" class="btn btn-success btn-xs" title="Generate Barcode and Download">
                    Generate Barcode
                  </a>
                  <a href="edit_box.php?id=<?php echo (int)$box['id']; ?>" class="btn btn-warning btn-xs" title="Edit">
                    <span class="glyphicon glyphicon-edit"></span>
                  </a>
                  <a href="?delete_id=<?php echo (int)$box['id']; ?>" class="btn btn-danger btn-xs" title="Delete">
                    <span class="glyphicon glyphicon-trash"></span>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
