<?php
$page_title = 'Employee Management';
require_once('includes/load.php');
page_require_level(2);

// Handle adding new employee
if (isset($_POST['add_employee'])) {
    $employee_name = remove_junk($db->escape($_POST['employee_name']));
    $first_name = remove_junk($db->escape($_POST['first_name']));
    $last_name = remove_junk($db->escape($_POST['last_name']));
    
    // Generate employee ID
    $employee_id = generate_employee_id();
    
    $query = "INSERT INTO employees (employee_id, employee_name, first_name, last_name) VALUES ('$employee_id', '$employee_name', '$first_name', '$last_name')";
    
    if ($db->query($query)) {
        $session->msg('s', "Employee added successfully");
        redirect('employee.php', false);
    } else {
        $session->msg('d', 'Error adding employee');
        redirect('employee.php', false);
    }
}

// Function to generate employee ID
function generate_employee_id() {
    global $db;
    $query = "SELECT MAX(CAST(SUBSTRING(employee_id, 4) AS UNSIGNED)) AS max_id FROM employees";
    $result = $db->query($query);
    $row = $db->fetch_assoc($result);
    
    $max_id = $row['max_id'];
    if ($max_id === null) {
        $new_id = 1;
    } else {
        $new_id = $max_id + 1;
    }
    
    return 'FAC' . str_pad($new_id, 3, '0', STR_PAD_LEFT);
}

// Fetch all employees
$all_employees = find_all('employees');

?>

<?php include_once('layouts/header.php'); ?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-6 col-sm-12"> <!-- Adjust columns for different screen sizes -->
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Employee</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="employee.php">
          <div class="form-group">
            <label for="employee_name">Employee Name</label>
            <input type="text" class="form-control" name="employee_name" required>
          </div>
          <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" name="first_name">
          </div>
          <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" name="last_name">
          </div>
          <button type="submit" name="add_employee" class="btn btn-primary">Add Employee</button>
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
          <span>Employee List</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="table-responsive"> <!-- Add responsive class for tables -->
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>First Name</th>
                <th>Last Name</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_employees as $employee): ?>
                <tr>
                  <td><?php echo $employee['employee_id']; ?></td>
                  <td><?php echo remove_junk($employee['employee_name']); ?></td>
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
</div>

<?php include_once('layouts/footer.php'); ?>

