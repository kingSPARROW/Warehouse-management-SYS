<?php
$page_title = 'Completed Drying';
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
                    <span>Completed Drying</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gummie Name</th>
                            <th>Tray ID</th>
                            <th>Marked Completed Time</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM completed_drying ORDER BY id DESC";
                        $result = $db->query($query);

                        while ($completed_drying = $db->fetch_assoc($result)) :
                            $gummieName = find_gummie_name_by_id($completed_drying['gummie_id']);
                            $trayID = array_key_exists('tray_id', $completed_drying) ? $completed_drying['tray_id'] : 'N/A';
                            $markedCompletedTime = array_key_exists('completed_time', $completed_drying) ? $completed_drying['completed_time'] : 'N/A';
                            $type = array_key_exists('type', $completed_drying) ? $completed_drying['type'] : 'N/A';
                        ?>
                            <tr>
                                <td><?php echo $completed_drying['id']; ?></td>
                                <td><?php echo $gummieName; ?></td>
                                <td><?php echo $trayID; ?></td>
                                <td><?php echo $markedCompletedTime; ?></td>
                                <td><?php echo $type; ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-xs" onclick="printBarcode('<?php echo $gummieName; ?>', '<?php echo $trayID; ?>', '<?php echo $markedCompletedTime; ?>', '<?php echo $type; ?>')">Print Barcode</button>
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

<script>
  function printBarcode(gummieName, trayID, markedCompletedTime, type) {
    var printWindow = window.open('', '', 'width=400,height=600');
    printWindow.document.open();
    printWindow.document.write('<html><head><title>Print Barcode</title></head><body>');
    printWindow.document.write('<div style="text-align: center;">');
    printWindow.document.write('<br>');
    printWindow.document.write('<br>');
    // Add the barcode image
    printWindow.document.write('<img src="barcode/' + trayID + '.png" alt="Barcode" style="width: 400px;">');
    printWindow.document.write('<br>');
    printWindow.document.write('Gummie Name: ' + gummieName);
    printWindow.document.write('<br>');
    printWindow.document.write('Tray ID: ' + trayID);
    printWindow.document.write('<br>');
    printWindow.document.write('Marked Completed Time: ' + markedCompletedTime);
    printWindow.document.write('<br>');
    printWindow.document.write('Type: ' + type);
    printWindow.document.write('<br>');
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
  }
</script>
