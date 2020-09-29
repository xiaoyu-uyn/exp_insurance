<?php require_once('../../../private/initialize.php'); ?>

<?php
require_manager_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id']; // PHP > 7.0
$customer = find_customer_by_id($id);
$customer_name = $customer['FIRST_NAME'] . ' ' . $customer['LAST_NAME'];

// $policy_set = find_policys_by_customer_id($id);
start_transaction();
$lock_result = sharelock_policy_by_customer_id($id);
if($lock_result !== True) {
  rollback();
  $_SESSION['message'] = "System Busy";
  redirect_to(url_for('/staff/policys/manage_policy.php'));
}
$policy_set = find_policys_by_customer_id($id);
commit();

?>

<?php $page_title = 'Show ' . $customer_name . ' Storage'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>
<br />
<br />
<div id="content">
  <a class="back-link" href="<?php echo url_for('/staff/managers/index.php'); ?>">&laquo; back to customers</a>
  <div class="policy show">

    <table class="list">
      <tr>
        <th>policy_id</th>
        <th>type</th>
        <th>start_date</th>
        <th>end_date</th>
        <th>status</th>
        <th>pre_amount</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>

      <?php while($policy = mysqli_fetch_assoc($policy_set)) { ?>
        <tr>
          <td><?php echo h($policy['policy_id']); ?></td>
          <td><?php echo $policy['type'] == 'H'? 'Home ins' : 'Auto ins' ;?></td>
          <td><?php echo h($policy['start_date']); ?></td>
          <td><?php echo h($policy['end_date']); ?></td>
          <td><?php echo $policy['status'] == 'C'? 'Current' : 'Expired' ; ?></td>
          <td><?php echo h($policy['prem_amount']); ?></td>
          <td><a class="action" href="<?php echo url_for('/staff/managers/manage_invoice.php?id=' . h(u($policy['policy_id']))); ?>">Invoices</a></td>
          <td><a class="action" href="<?php 
            if($policy['type'] == 'H') {
              echo url_for('/staff/managers/manage_home.php?id=' . h(u($policy['policy_id'])));
            } else {
              echo url_for('/staff/managers/manage_vehicle.php?id=' . h(u($policy['policy_id'])));
            }
          ?>"><?php echo $policy['type'] == 'H'? 'Home' : 'Vehicle' ;?></a></td>
          <td><a class="action" href="<?php 
            if($policy['type'] == 'H') {
              echo url_for('/staff/managers/delete_h.php?id=' . h(u($policy['policy_id'])));
            } else {
              echo url_for('/staff/managers/delete_a.php?id=' . h(u($policy['policy_id'])));
            }
          ?>">Delete</a></td>
        </tr>
      <?php } ?>
    </table>
    
    <?php mysqli_free_result($policy_set); ?>
  </div>

</div>