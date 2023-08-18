<?php
$page_title = 'Manage Employees';
require_once('includes/load.php');
page_require_level(2);

// Fetch all employees
$all_employees = find_all('employees');

?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Manage Employees</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_employees as $employee): ?>
                            <tr>
                                <td><?php echo $employee['id']; ?></td>
                                <td><?php echo remove_junk($employee['employee_id']); ?></td>
                                <td><?php echo remove_junk($employee['first_name']); ?></td>
                                <td><?php echo remove_junk($employee['last_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
