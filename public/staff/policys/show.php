<?php require_once('../../../private/initialize.php'); ?>

<?php
require_customer_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_SESSION['customer_id']; // PHP > 7.0

start_transaction();
$lock_result = sharelock_policy_by_customer_id($id);
if($lock_result !== true) {
  rollback();
  $_SESSION['message'] = "System Busy" . $lock_result;
  redirect_to(url_for('/staff/policys/login.php'));
}
$policy_set = find_policys_by_customer_id($id);
commit();

?>

<?php $page_title = 'Show Your Storage'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>
<br />
<br />
<div id="content">

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
      </tr>

      <?php while($policy = mysqli_fetch_assoc($policy_set)) { ?>
        <tr>
          <td><?php echo h($policy['policy_id']); ?></td>
          <td><?php echo $policy['type'] == 'H'? 'Home ins' : 'Auto ins' ;?></td>
          <td><?php echo h($policy['start_date']); ?></td>
          <td><?php echo h($policy['end_date']); ?></td>
          <td><?php echo $policy['status'] == 'C'? 'Current' : 'Expired' ; ?></td>
          <td><?php echo h($policy['prem_amount']); ?></td>
          <td><a class="action" href="<?php echo url_for('/staff/invoices/show.php?id=' . h(u($policy['policy_id']))); ?>">Invoices</a></td>
          <td><a class="action" href="<?php 
            if($policy['type'] == 'H') {
              echo url_for('/staff/policys/showhomes.php?id=' . h(u($policy['policy_id'])));
            } else {
              echo url_for('/staff/policys/show_vehicles.php?id=' . h(u($policy['policy_id'])));
            }
          ?>">Covered Item</a></td>
        </tr>
      <?php } ?>
    </table>
    
    <?php mysqli_free_result($policy_set); ?>
  </div>
  <navigation>
  <ul>
    <li><a href="<?php echo url_for('/staff/policys/type_select.php'); ?>">Keep Shopping</a></li>
  </ul>
</navigation>

</div>
