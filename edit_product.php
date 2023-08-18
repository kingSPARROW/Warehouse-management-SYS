<?php
$page_title = 'Edit product';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(2);
?>
<?php
$product = find_by_id('products', (int)$_GET['id']);
// Fetch all products that are not boxes themselves
$all_products = find_all('products', "WHERE is_box = 0 AND id != {$product['id']}");
$all_categories = find_all('categories');
$all_photo = find_all('media');
if (!$product) {
  $session->msg("d", "Missing product id.");
  redirect('product.php');
}
?>
<?php
if (isset($_POST['product'])) {
  $req_fields = array(
    'product-title', 'product-categorie', 'product-quantity',
    'buying-price', 'saleing-price', 'is-box', 'box-product'
  );
  validate_fields($req_fields);

  if (empty($errors)) {
    $p_name = remove_junk($db->escape($_POST['product-title']));
    $p_cat = (int)$_POST['product-categorie'];
    $p_qty = remove_junk($db->escape($_POST['product-quantity']));
    $p_buy = remove_junk($db->escape($_POST['buying-price']));
    $p_sale = remove_junk($db->escape($_POST['saleing-price']));
    if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
      $media_id = '0';
    } else {
      $media_id = remove_junk($db->escape($_POST['product-photo']));
    }
    $query = "UPDATE products SET";
    $query .= " name ='{$p_name}', quantity ='{$p_qty}',";
    $query .= " buy_price ='{$p_buy}', sale_price ='{$p_sale}', categorie_id ='{$p_cat}',media_id='{$media_id}'";

    // New box-related fields
    $is_box = (int)$_POST['is-box'];
    $box_product_id = null;
    $box_quantity = null;
    $storage_area = null;
    $barcode = null;

    if ($is_box === 1) {
      $box_product_id = remove_junk($db->escape($_POST['box-product']));
      $box_quantity = remove_junk($db->escape($_POST['box-quantity']));
      $storage_area = remove_junk($db->escape($_POST['storage-area']));
      // You can update the barcode here if needed
      // $barcode = generate_updated_barcode();
      $query .= ", is_box='{$is_box}', box_product_id='{$box_product_id}', box_quantity='{$box_quantity}', storage_area='{$storage_area}', barcode='{$barcode}'";
    } else {
      $query .= ", is_box='0', box_product_id=NULL, box_quantity=NULL, storage_area=NULL, barcode=NULL";
    }

    $query .= " WHERE id ='{$product['id']}'";
    $result = $db->query($query);
    if ($result && $db->affected_rows() === 1) {
      $session->msg('s', "Product updated ");
      redirect('product.php', false);
    } else {
      $session->msg('d', ' Sorry failed to update!');
      redirect('edit_product.php?id=' . $product['id'], false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('edit_product.php?id=' . $product['id'], false);
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
        <span>Edit Product</span>
      </strong>
    </div>
    <div class="panel-body">
      <div class="col-md-7">
        <form method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>">
          <div class="form-group">
            <label for="product-title">Product Title</label>
            <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']); ?>">
          </div>
          <div class="form-group">
            <label for="box-product">Box Product</label>
               <select class="form-control" name="box-product">
                 <option value="">Select Box Product</option>
                   <?php foreach ($all_products as $box_product): ?>
                    <option value="<?php echo (int)$box_product['id']; ?>" <?php if ($product['box_product_id'] === $box_product['id']) echo "selected"; ?>>
                      <?php echo remove_junk($box_product['name']); ?></option>
                        <?php endforeach; ?>
                </select>
          </div>

              <div class="col-md-6">
                <label for="product-photo">Product Photo</label>
                <select class="form-control" name="product-photo">
                  <option value="">No image</option>
                  <?php foreach ($all_photo as $photo): ?>
                    <option value="<?php echo (int)$photo['id']; ?>" <?php if ($product['media_id'] === $photo['id']) echo "selected"; ?>>
                      <?php echo $photo['file_name']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4">
                <label for="box-checkbox">Is Box</label>
                <input type="checkbox" name="is-box" id="box-checkbox" value="1" <?php if ($product['is_box'] === '1') echo "checked"; ?>>
              </div>
              <div class="col-md-4">
                <label for="box-product">Box Product</label>
                <select class="form-control" name="box-product">
                  <option value="">Select a product for the box</option>
                  <?php foreach ($all_products as $prod): ?>
                    <option value="<?php echo (int)$prod['id']; ?>" <?php if ($product['box_product_id'] === $prod['id']) echo "selected"; ?>>
                      <?php echo remove_junk($prod['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="box-quantity">Box Quantity</label>
                <input type="number" class="form-control" name="box-quantity" value="<?php echo remove_junk($product['box_quantity']); ?>">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="storage-area">Storage Area</label>
            <input type="text" class="form-control" name="storage-area" value="<?php echo remove_junk($product['storage_area']); ?>">
          </div>
          <!-- ... rest of the fields ... -->
          <button type="submit" name="product" class="btn btn-danger">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
