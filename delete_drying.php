<?php
require_once('includes/load.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];

    // Replace 'your_password' with your actual password
    if ($password === 'king') {
        // Get the drying record
        $drying_record = find_by_id('drying', $id);

        // Insert the record into the completed_drying table
        $completed_time = date('Y-m-d H:i:s');
        $query = "INSERT INTO completed_drying (id, gummie_id, drying_time, created_at, tray_id, completed_time, type) ";
        $query .= "VALUES ('{$db->escape($drying_record['id'])}', '{$db->escape($drying_record['gummie_id'])}', ";
        $query .= "'{$db->escape($drying_record['drying_time'])}', '{$db->escape($drying_record['created_at'])}', ";
        $query .= "'{$db->escape($drying_record['tray_id'])}', '{$db->escape($completed_time)}', 'Forced')";
        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            // Delete the record from the drying table
            $delete_query = "DELETE FROM drying WHERE id = '{$db->escape($id)}'";
            $delete_result = $db->query($delete_query);

            if ($delete_result && $db->affected_rows() === 1) {
                $session->msg('s', "Drying record marked as complete.");
                redirect('manage_drying.php', false);
            } else {
                $session->msg('d', "Failed to mark drying record as complete.");
                redirect('manage_drying.php', false);
            }
        } else {
            $session->msg('d', "Failed to mark drying record as complete.");
            redirect('manage_drying.php', false);
        }
    } else {
        $session->msg('d', "Password incorrect. Unable to mark drying record as complete.");
        redirect('manage_drying.php', false);
    }
} else {
    $session->msg('d', "Invalid request.");
    redirect('manage_drying.php', false);
}
?>
