<?php require_once('../../../private/initialize.php'); ?>

<?php
require_customer_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id']; // PHP > 7.0
$license_number_set = find_license_number_by_combine_id($id);
$policy_id = find_policy_id_by_combine_id($id);
?>

<?php $page_title = 'Show Covered drivers'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>
<br />
<br />
<div id="content">
<a class="back-link" href="<?php echo url_for('/staff/policys/show_vehicles.php?id=' . h(u($policy_id))); ?>">&laquo; Back to Vehicles</a>
  <div class="vehicles show">

    <table class="list">
      <tr>
        <th>License Number</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Birthday</th>
      </tr>

      <?php while($license_number = mysqli_fetch_row($license_number_set)) { ?>
        <?php 
          start_transaction();
          $lock_result = sharelock_driver_by_license_number($license_number[0]);
          if($lock_result !== True) {
            rollback();
            $_SESSION['message'] = "System Busy" . $lock_result;
            redirect_to(url_for('/staff/policys/show_vehicles.php?id=' . h(u($policy_id))));
          }
          $driver = find_driver_by_license_number($license_number[0]);
          commit();
        ?>
        <tr>
          <td><?php echo h($driver['license_number']); ?></td>
          <td><?php echo h($driver['first_name']); ?></td>
          <td><?php echo h($driver['last_name']); ?></td>
          <td><?php echo h($driver['birthday']); ?></td>

        </tr>
      <?php } ?>
    </table>
    <?php mysqli_free_result($license_number_set); ?>
  </div>

</div>