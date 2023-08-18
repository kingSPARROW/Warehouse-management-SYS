<?php
require_once('includes/load.php');
page_require_level(2);

if (isset($_POST['drying-id']) && isset($_POST['add-hours'])) {
    $drying_ids = $_POST['drying-id'];
    $add_hours = (float)$_POST['add-hours'];

    foreach ($drying_ids as $drying_id) {
        $drying_id = (int)$drying_id;
        $query = "SELECT * FROM drying WHERE id = {$drying_id}";
        $result = $db->query($query);
        $drying = $db->fetch_assoc($result);

        if ($drying) {
            $new_drying_time = $drying['drying_time'] + $add_hours;
            $update_query = "UPDATE drying SET drying_time = '{$new_drying_time}' WHERE id = {$drying_id}";
            $db->query($update_query);
        }
    }

    $session->msg('s', 'Drying Time updated successfully.');
    redirect('manage_drying.php');
} else {
    $session->msg('d', 'No records selected for updating.');
    redirect('manage_drying.php');
}
?>
