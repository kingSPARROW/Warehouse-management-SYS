<?php
$page_title = 'Manage Palets';
require_once('includes/load.php');
page_require_level(2);

// Fetch all palets
$all_palets = find_all('palet');

// Predefined locations
$predefined_locations = array("Location1", "Location2", "Location3"); // Add more if needed

// Add new palet
if (isset($_POST['add_palet'])) {
  $palet_number = remove_junk($db->escape($_POST['palet-number']));
  $location = remove_junk($db->escape($_POST['location']));
  
  $query = "INSERT INTO palet (palet_number, location) VALUES ('$palet_number', '$location')";
  if ($db->query($query)) {
    $session->msg('s', "Palet added");
    redirect('palet.php', false);
  } else {
    $session->msg('d', 'Failed to add palet');
    redirect('palet.php', false);
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Palet</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="palet.php">
          <div class="form-group">
            <label for="palet-number">Palet Number</label>
            <input type="text" class="form-control" name="palet-number" required>
          </div>
          <div class="form-group">
            <label for="location">Location</label>
            <select class="form-control" name="location" required>
              <option value="">Select Location</option>
              <?php foreach ($predefined_locations as $loc) : ?>
                <option value="<?php echo $loc; ?>"><?php echo $loc; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <button type="submit" name="add_palet" class="btn btn-primary">Add Palet</button>
          </div>
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
          <span>Palet List</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Palet Number</th>
              <th>Location</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_palets as $palet): ?>
              <tr>
                <td><?php echo $palet['id']; ?></td>
                <td><?php echo remove_junk($palet['palet_number']); ?></td>
                <td><?php echo remove_junk($palet['location']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
