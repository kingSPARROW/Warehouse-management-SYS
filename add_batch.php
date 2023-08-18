<?php
$page_title = 'Add Batch';
require_once('includes/load.php');
page_require_level(2);

$all_gummies = find_all('gummies');

if (isset($_POST['add_batch'])) {
    $gummie_id = remove_junk($db->escape($_POST['gummie-id']));
    $batch_number = remove_junk($db->escape($_POST['batch-number']));
    $batch_size = remove_junk($db->escape($_POST['batch-size']));
    $mfg_date = remove_junk($db->escape($_POST['mfg-date']));

    // Fetch the gummie name based on the selected gummie ID
    $gummie_name = find_gummie_name_by_id($gummie_id);

    $query = "INSERT INTO batches_gummies (gummies_name, batch_number, batch_size, mfg_date) 
              VALUES ('{$gummie_name}', '{$batch_number}', '{$batch_size}', '{$mfg_date}')";

    if ($db->query($query)) {
        $session->msg('s', "Batch added ");
        redirect('add_batch.php', false);
    } else {
        $session->msg('d', 'Failed to add batch: ' . $db->error());
    }
}

include_once('layouts/header.php');
?>

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
                <span>Add Batch</span>
            </strong>
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <form method="post" action="add_batch.php">
                    <div class="form-group">
                        <label for="gummie-id">Gummie Product</label>
                        <select class="form-control" name="gummie-id" required>
                            <option value="">Select Gummie Product</option>
                            <?php foreach ($all_gummies as $gummie) : ?>
                                <option value="<?php echo (int)$gummie['id']; ?>">
                                    <?php echo remove_junk($gummie['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="batch-number">Batch Number</label>
                        <input type="text" class="form-control" name="batch-number" placeholder="Batch Number" required>
                    </div>
                    <div class="form-group">
                        <label for="batch-size">Batch Size (kgs)</label>
                        <input type="number" step="0.01" class="form-control" name="batch-size" placeholder="Batch Size" required>
                    </div>
                    <div class="form-group">
                        <label for="mfg-date">Manufacturing Date</label>
                        <input type="date" class="form-control" name="mfg-date" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="add_batch" class="btn btn-primary">Add Batch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
