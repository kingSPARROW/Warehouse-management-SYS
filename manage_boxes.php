<?php
$page_title = 'Manage Boxes';
require_once('includes/load.php');
page_require_level(2);

// Fetch all products for filter dropdown
$all_products = find_all('products');

// Fetch all boxes with product information
$all_boxes = find_all_boxes_with_product();

// Fetch all products that can be put into boxes
$boxable_products = find_all('products', "WHERE is_box = 0");

// Process search filter
if (isset($_GET['search'])) {
  // Existing filter logic...

  // Additional search by box name
  $search_box_name = remove_junk($db->escape($_GET['search-box']));
  if (!empty($search_box_name)) {
    $all_boxes = search_boxes_by_name($search_box_name);
  }
}

?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Add barcode scanning markup -->
<div id="barcode-scanner">
  <div id="scanner-container"></div>
  <button id="scan-button" class="btn btn-primary">Scan Barcode</button>
</div>

<div class="row">
  <div class="col-md-12">
    <!-- Existing code... -->
  </div>
</div>


<div class="row">
  <div class="col-md-12">
    <div class="row" style="margin-bottom: 10px;">
      <div class="col-md-6">
        <?php echo display_msg($msg); ?>
      </div>
      <div class="col-md-6 text-right">
        <form class="form-inline" method="get" action="manage_boxes.php">
          <div class="form-group">
            <input type="text" class="form-control" name="search-box" placeholder="Search Box">
          </div>
          <button type="submit" class="btn btn-primary">Search</button>
        </form>
      </div>
    </div>
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
        <div class="filter-form">
          <form method="get" action="manage_boxes.php">
            <div class="form-group">
              <label for="product-id">Filter by Product:</label>
              <select class="form-control" name="product-id">
                <option value="">Select Product</option>
                <?php foreach ($all_products as $product) : ?>
                  <option value="<?php echo (int)$product['id']; ?>">
                    <?php echo remove_junk($product['name']); ?>
                

                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="storage-area">Filter by Storage Area:</label>
              <select class="form-control" name="storage-area">
                <option value="">Select Storage Area</option>
                <?php foreach ($storage_areas as $area) : ?>
                  <option value="<?php echo $area; ?>"><?php echo $area; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" name="search" class="btn btn-primary">Apply Filter</button>
          </form>
        </div>
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
                  <a href="delete_box.php?id=<?php echo (int)$box['id']; ?>" class="btn btn-danger btn-xs" title="Delete">
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
<!-- Add QuaggaJS script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
  $(document).ready(function() {
    // Barcode scanning
    function startBarcodeScanner() {
      Quagga.init({
        inputStream: {
          type: "LiveStream",
          constraints: {
            facingMode: "environment" // Use the rear camera
          },
          target: document.querySelector("#scanner-container")
        },
        decoder: {
          readers: ["ean_reader"] // Supported barcode format
        }
      }, function(err) {
        if (err) {
          console.error("Error initializing Quagga:", err);
          return;
        }
        Quagga.start();
      });

      Quagga.onDetected(function(result) {
        var barcode = result.codeResult.code;
        window.location.href = 'manage_boxes.php?search-box=' + encodeURIComponent(barcode);
      });
    }

    $("#scan-button").on("click", function() {
      startBarcodeScanner();
    });

    // Your other functions and code...
  });
</script>