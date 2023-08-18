<?php
$page_title = 'Manage Drying';
require_once('includes/load.php');
page_require_level(2);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Manage Drying</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="get" action="manage_drying.php" class="form-inline">
                    <div class="form-group">
                        <label for="search-tray-id">Search by Tray ID:</label>
                        <input type="text" class="form-control" id="search-tray-id" name="search-tray-id" placeholder="Enter Tray ID">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                <br>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gummie Name</th>
                            <th>Drying Time (hrs)</th>
                            <th>Created At</th>
                            <th>Tray ID</th>
                            <th>Time Remaining</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $search_tray_id = '';

                        if (isset($_GET['search-tray-id'])) {
                            $search_tray_id = $db->escape($_GET['search-tray-id']);
                        }

                        $query = "SELECT * FROM drying";
                        $conditions = array();

                        if (!empty($search_tray_id)) {
                            $conditions[] = "tray_id LIKE '%$search_tray_id%'";
                        }

                        if (!empty($conditions)) {
                            $query .= " WHERE " . implode(" AND ", $conditions);
                        }

                        $query .= " ORDER BY id DESC";

                        $result = $db->query($query);

                        while ($drying = $db->fetch_assoc($result)) :
                            $gummie_name = find_gummie_name_by_id($drying['gummie_id']);
                            $created_at_timestamp = strtotime($drying['created_at']);
                            $end_timestamp = $created_at_timestamp + ($drying['drying_time'] * 3600);
                            $current_timestamp = time();
                            $remaining_time = max(0, $end_timestamp - $current_timestamp);
                            $remaining_days = floor($remaining_time / (24 * 3600));
                            $remaining_hours = gmdate("H", $remaining_time);
                            $remaining_minutes = gmdate("i", $remaining_time);
                            $remaining_seconds = gmdate("s", $remaining_time);
                        ?>
                            <tr>
                                <td><?php echo $drying['id']; ?></td>
                                <td><?php echo $gummie_name; ?></td>
                                <td><?php echo $drying['drying_time']; ?></td>
                                <td><?php echo $drying['created_at']; ?></td>
                                <td><?php echo $drying['tray_id']; ?></td>
                                <td>
                                    <?php
                                    echo $remaining_days . " days ";
                                    echo $remaining_hours . " hrs ";
                                    echo $remaining_minutes . " mins ";
                                    echo $remaining_seconds . " secs";
                                    ?>
                                </td>
                                <td>
    <?php
    if ($remaining_time <= 0) {
        echo '<a href="delete_dryingnopass.php?id=' . $drying['id'] . '" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to mark this drying process as complete?\')">Mark Complete</a>';
    } else {
        echo '<button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#passwordModal' . $drying['id'] . '">Mark Complete (Password)</button>';

        // Password Modal
        echo '<div class="modal fade" id="passwordModal' . $drying['id'] . '" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel' . $drying['id'] . '">';
        echo '<div class="modal-dialog" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h4 class="modal-title" id="passwordModalLabel' . $drying['id'] . '">Enter Password</h4>';
        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span>';
        echo '</button>';
        echo '</div>';
        echo '<div class="modal-body">';
        echo '<form action="delete_drying.php" method="post">';
        echo '<input type="hidden" name="id" value="' . $drying['id'] . '">';
        echo '<input type="password" class="form-control" name="password" placeholder="Enter Password" required>';
        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
        echo '<button type="submit" class="btn btn-primary">Submit</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    ?>
</td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
