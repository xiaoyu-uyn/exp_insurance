<?php require_once('../../../private/initialize.php'); ?>

<?php
require_manager_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id']; // PHP > 7.0
$_SESSION['combine_id'] = $id;
$license_number_set = find_license_number_by_combine_id($id);
$policy_id = find_policy_id_by_combine_id($id);

?>

<?php $page_title = 'Show Drivers'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>
<br />
<br />
<div id="content">
<a class="back-link" href="<?php echo url_for('/staff/managers/manage_vehicle.php?id=' . h(u($policy_id))); ?>">&laquo; Back</a>
  <div class="v show">

    <table class="list">
      <tr>
        <th>License Number</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Birthday</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>

      <?php while($license_number = mysqli_fetch_row($license_number_set)) { ?>
        <?php 
          start_transaction();
          $lock_result = sharelock_driver_by_license_number($license_number[0]);
          if($lock_result !== True) {
            rollback();
            $_SESSION['message'] = "System Busy" . $lock_result;
            redirect_to(url_for('/staff/managers/manage_vehicle.php?id=' . h(u($policy_id))));
          }
          $driver = find_driver_by_license_number($license_number[0]);
          commit();
        ?>
        <tr>
          <td><?php echo h($driver['license_number']); ?></td>
          <td><?php echo h($driver['first_name']); ?></td>
          <td><?php echo h($driver['last_name']); ?></td>
          <td><?php echo h($driver['birthday']); ?></td>
          <td><a class="action" href="<?php echo url_for('/staff/managers/delete_driver.php?id=' . h(u($driver['license_number']))); ?>">Delete</a></td>
          <td><a class="action" href="<?php echo url_for('/staff/managers/edit_driver.php?id=' . h(u($driver['license_number']))); ?>">Edit</a></td>

        </tr>
      <?php } ?>
    </table>
    <?php mysqli_free_result($license_number_set); ?>
  </div>

</div>