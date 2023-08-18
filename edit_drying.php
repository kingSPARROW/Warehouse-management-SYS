<?php
$page_title = 'Edit Drying Information';
require_once('includes/load.php');
page_require_level(2);

// Check if the ID parameter is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Fetch the drying record by ID
    $drying = find_by_id('drying', $id);

    if (!$drying) {
        $session->msg('d', 'Drying information not found.');
        redirect('manage_drying.php');
    }
} else {
    $session->msg('d', 'Invalid request.');
    redirect('manage_drying.php');
}

if (isset($_POST['update_drying'])) {
    $req_fields = array('drying-time');

    // Validate required fields
    validate_fields($req_fields);

    if (empty($errors)) {
        $drying_time = (float)$_POST['drying-time'];

        // Update the drying information
        $query = "UPDATE drying SET drying_time='{$drying_time}' WHERE id='{$id}'";
        if ($db->query($query)) {
            $session->msg('s', "Drying information updated successfully.");
            redirect('manage_drying.php', false);
        } else {
            $session->msg('d', 'Sorry, failed to update drying information.');
            redirect('edit_drying.php?id=' . $id, false);
        }
    } else {
        $session->msg('d', $errors);
        redirect('edit_drying.php?id=' . $id, false);
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
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-edit"></span>
                    <span>Edit Drying Information</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="edit_drying.php?id=<?php echo $id; ?>">
                    <div class="form-group">
                        <label for="drying-time">Drying Time (hrs):</label>
                        <input type="number" step="0.01" class="form-control" name="drying-time" value="<?php echo $drying['drying_time']; ?>" required>
                    </div>
                    <button type="submit" name="update_drying" class="btn btn-primary">Update Drying Information</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
