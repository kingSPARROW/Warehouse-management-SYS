<?php
require_once('includes/load.php');
page_require_level(2);

// Check if the ID parameter is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Get the drying record
    $drying_record = find_by_id('drying', $id);

    // Insert the record into the completed_drying table with type 'Normal'
    $completed_time = date('Y-m-d H:i:s');
    $query = "INSERT INTO completed_drying (id, gummie_id, drying_time, created_at, tray_id, completed_time, type) ";
    $query .= "VALUES ('{$db->escape($drying_record['id'])}', '{$db->escape($drying_record['gummie_id'])}', ";
    $query .= "'{$db->escape($drying_record['drying_time'])}', '{$db->escape($drying_record['created_at'])}', ";
    $query .= "'{$db->escape($drying_record['tray_id'])}', '{$db->escape($completed_time)}', 'Normal')";
    
    // Perform the insertion
    if ($db->query($query)) {
        // Delete the drying record by ID
        $delete_query = "DELETE FROM drying WHERE id='{$id}'";
        if ($db->query($delete_query)) {
            $session->msg('s', "Drying information Mark Completed successfully.");
        } else {
            $session->msg('d', 'Failed to delete drying record.');
        }
    } else {
        $session->msg('d', 'Failed to Mark Completed drying information.');
    }
} else {
    $session->msg('d', 'Invalid request.');
}

redirect('manage_drying.php');
?>
