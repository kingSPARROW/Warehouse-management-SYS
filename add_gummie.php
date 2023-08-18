<?php
$page_title = 'Add Gummie';
require_once('includes/load.php');
page_require_level(2);

// Check if the form is submitted
if (isset($_POST['add_gummie'])) {
  $req_fields = array('gummie-name', 'gummie-media');

  // Validate required fields
  validate_fields($req_fields);

  if (empty($errors)) {
    $g_name = remove_junk($db->escape($_POST['gummie-name']));
    $g_media = remove_junk($db->escape($_POST['gummie-media']));
    $date = make_date();

    // Insert the gummie into the database
    $query  = "INSERT INTO gummies (";
    $query .= " name, media_id, date";
    $query .= ") VALUES (";
    $query .= " '{$g_name}', '{$g_media}', '{$date}'";
    $query .= ")";

    if ($db->query($query)) {
      $session->msg('s', "Gummie added successfully.");
      redirect('add_gummie.php', false);
    } else {
      $session->msg('d', 'Sorry, failed to add the gummie.');
      redirect('add_gummie.php', false);
    }
  } else {
    $session->msg('d', $errors);
    redirect('add_gummie.php', false);
  }
}

$all_media = find_all('media');
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Gummie</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="add_gummie.php" class="clearfix">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="gummie-name" placeholder="Gummie Name">
              </div>
            </div>
            <div class="form-group">
              <label for="gummie-media">Select Gummie Media:</label>
              <select class="form-control" name="gummie-media">
                <option value="">Select Gummie Media</option>
                <?php foreach ($all_media as $media) : ?>
                  <option value="<?php echo (int)$media['id']; ?>">
                    <?php echo $media['file_name']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" name="add_gummie" class="btn btn-danger">Add Gummie</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
