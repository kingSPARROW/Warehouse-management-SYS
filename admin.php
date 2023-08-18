<?php
$page_title = 'Admin Home Page';
require_once('includes/load.php');
page_require_level(1);

$c_categorie = count_by_id('categories');
$c_product = count_by_id('products');
$c_user = count_by_id('users');
// $products_sold = find_highest_saleing_product('10');
$recent_products = find_recent_product_added('5');
$product_inventory = get_product_inventory(); // Add this line for pie chart data

// Function to check low stock products
function check_low_stock($product_inventory) {
  $low_stock_products = array();
  foreach ($product_inventory as $product) {
    if ($product['total_quantity'] <= 50) {
      $low_stock_products[] = $product;
    }
  }
  return $low_stock_products;
}

// Get low stock products
$low_stock_products = check_low_stock($product_inventory);
// Function to check drying alerts
function check_drying_alert($drying_time, $created_at) {
  $current_time = time();
  $created_at_timestamp = strtotime($created_at);
  $time_diff = $current_time - $created_at_timestamp;

  $alert_threshold = 43200;

  return $time_diff < $drying_time * 43200 && $time_diff > ($drying_time * 43200 - $alert_threshold);
}
?>
?>

<?php include_once('layouts/header.php'); ?>
<style>
  /* Customize background colors for panels */
  .panel-custom-green {
    background-color: #27ae60; /* Dark green */
  }
  .panel-custom-yellow {
    background-color: #f39c12; /* Dark yellow */
  }
  .panel-custom-red {
    background-color: #c0392b; /* Dark red */
  }
  .panel-custom-blue {
    background-color: #2980b9; /* Dark blue */
  }
  
  /* Increase the size of the logos */
  .panel-custom .panel-heading i {
    font-size: 48px;
  }
  
  /* Change text and link colors to light */
  body {
    color: #ffffff; /* Light text color */
  }
  a {
    color: #3498db; /* Link color */
  }
</style>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <!-- User Section (Green) -->
  <div class="col-md-2">
    <div class="panel panel-custom-green text-center">
      <div class="panel-heading">
        <i class="glyphicon glyphicon-user"></i>
      </div>
      <div class="panel-body">
        <h2 class="margin-top"><?php echo $c_user['total']; ?></h2>
        <p class="text-muted">Users</p>
      </div>
    </div>
  </div>
  <!-- Category Section (Yellow) -->
  <div class="col-md-2">
    <div class="panel panel-custom-yellow text-center">
      <div class="panel-heading">
        <i class="glyphicon glyphicon-list"></i>
      </div>
      <div class="panel-body">
        <h2 class="margin-top"><?php echo $c_categorie['total']; ?></h2>
        <p class="text-muted">Categories</p>
      </div>
    </div>
  </div>
  <!-- Product Section (Red) -->
  <div class="col-md-2">
    <div class="panel panel-custom-red text-center">
      <div class="panel-heading">
        <i class="glyphicon glyphicon-shopping-cart"></i>
      </div>
      <div class="panel-body">
        <h2 class="margin-top"><?php echo $c_product['total']; ?></h2>
        <p>Total Products</p>
      </div>
    </div>
  </div>
  <!-- Low Stock Section (Blue) -->
  <div class="col-md-2">
    <div class="panel panel-custom-blue text-center">
      <div class="panel-heading">
        <i class="glyphicon glyphicon-warning-sign"></i>
      </div>
      <div class="panel-body">
        <?php if (count($low_stock_products) > 0) : ?>
          <h2 class="margin-top"><?php echo count($low_stock_products); ?></h2>
          <p>Low Stock Products</p>
        <?php else : ?>
          <h2 class="margin-top">0</h2>
          <p>All products have sufficient stock.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
<!-- Drying Alerts Section -->
<div class="row">
  <div class="col-md-2">
    <?php
    $drying_records = find_all('drying');
    $has_alerts = false;
    $alert_count = 0; // Initialize the alert count
    foreach ($drying_records as $record) {
      if (check_drying_alert($record['drying_time'], $record['created_at'])) {
        $has_alerts = true;
        $alert_count++; // Increment the alert count
      }
    }
    if ($has_alerts) {
      echo '<div class="panel panel-custom-blue text-center" style="background-color: #2196f3;">';
      echo '<div class="panel-heading">';
      echo '<i class="glyphicon glyphicon-bell" style="color: #fff;"></i>';
      echo '</div>';
      echo '<div class="panel-body" style="color: #fff; cursor: pointer;" data-toggle="collapse" data-target="#notificationContent">';
      echo '<h2 class="margin-top">Alerts(' . $alert_count . ')</h2>';
      echo '</div>';
      echo '</div>';
    }
    ?>
  </div>
</div>

<!-- Collapsible Notification Content -->
<div id="notificationContent" class="row collapse">
  <div class="col-md-12">
    <?php
    $has_alerts = false;
    foreach ($drying_records as $record) {
      if (check_drying_alert($record['drying_time'], $record['created_at'])) {
        echo '<div class="panel panel-warning text-center" style="background-color: #ff9800;">';
        echo '<div class="panel-heading">';
        echo '<i class="glyphicon glyphicon-warning-sign" style="color: #fff;"></i>';
        echo '</div>';
        echo '<div class="panel-body" style="color: #fff;">';
        echo 'Drying for Gummie: ' . find_gummie_name_by_id($record['gummie_id']) . ' is almost complete for Tray ID: ' . $record['tray_id'];
        echo '</div>';
        echo '</div>';
        $has_alerts = true;
      }
    }
    if (!$has_alerts) {
      echo '<div class="panel panel-custom-green text-center" style="background-color: #ff9800;">';
      echo '<div class="panel-heading">';
      echo '<i class="glyphicon glyphicon-warning-sign" style="color: #fff;"></i>';
      echo '</div>';
      echo '<div class="panel-body" style="color: #fff;">';
      echo 'No drying alerts.';
      echo '</div>';
      echo '</div>';
    }
    ?>
  </div>
</div>

<!-- Completed Drying Section -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-custom-blue text-center" style="background-color: #2196f3;">
      <div class="panel-heading">
        <i class="glyphicon glyphicon-ok-circle" style="color: #fff;"></i>
      </div>
      <div class="panel-body" style="color: #fff;">
        <h2 class="margin-top">Completed Drying Processes</h2>
        <?php
        $completed_drying_records = array();
        $current_time = time(); // Define the $current_time variable
        foreach ($drying_records as $record) {
          $created_at_timestamp = strtotime($record['created_at']);
          if ($current_time >= $created_at_timestamp + ($record['drying_time'] * 3600)) {
            $completed_drying_records[] = $record;
          }
        }
        if (!empty($completed_drying_records)) {
          foreach ($completed_drying_records as $record) {
            $gummie_name = find_gummie_name_by_id($record['gummie_id']);
            echo '<p>Drying for Gummie: ' . $gummie_name . ' is completed for Tray ID: ' . $record['tray_id'] . '</p>';
          }
        } else {
          echo '<p>No completed drying processes.</p>';
        }
        ?>
      </div>
    </div>
  </div>
</div>
  <!-- Product Chart Section -->
  <div class="col-md-4">
    <div id="productChart" style="height: 800px;"></div>
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
