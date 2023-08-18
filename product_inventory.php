<?php
$page_title = 'Product Inventory';
require_once('includes/load.php');
page_require_level(2);

// Fetch the total quantity of products in boxes
$product_inventory = get_product_inventory();
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Product Inventory</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Total Quantity</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($product_inventory as $product): ?>
              <?php
              // Compare the quantity against the threshold (50 in this example)
              $threshold = 50;
              $quantity = $product['total_quantity'];
              $isLowStock = $quantity <= $threshold;
              ?>
              <tr <?php echo $isLowStock ? 'class="warning"' : ''; ?>>
                <td><?php echo remove_junk($product['name']); ?></td>
                <td><?php echo remove_junk($product['total_quantity']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-stats"></span>
          <span>Product Inventory Chart</span>
        </strong>
      </div>
      <div class="panel-body">
        <div id="productChart" style="height: 400px;"></div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  // Load the Google Charts API
  google.charts.load('current', { 'packages': ['corechart'] });
  google.charts.setOnLoadCallback(drawChart);
  // Function to draw the 3D pie chart
  function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Product Name');
    data.addColumn('number', 'Total Quantity');
    data.addRows([
      <?php
        foreach ($product_inventory as $product) {
          echo "['" . $product['name'] . "', " . $product['total_quantity'] . "],";
        }
      ?>
    ]);
    var options = {
      title: 'Product Inventory',
      is3D: true,
      height: 400,
      legend: { position: 'right' },
      backgroundColor: { fill: 'transparent' }
    };
    var chart = new google.visualization.PieChart(document.getElementById('productChart'));
    chart.draw(data, options);
  }
</script>
