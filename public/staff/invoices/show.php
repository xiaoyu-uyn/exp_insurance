<?php require_once('../../../private/initialize.php'); ?>

<?php
require_customer_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id']; // PHP > 7.0
$policy_id = $id;

$uninvoiced_amount = calculate_uninvoiced_amount($id);
if($uninvoiced_amount != 0) {
  $_SESSION['message'] = 'Please make more invoice to pay your remainning $' . $uninvoiced_amount . ' fees.';
}

$invoices_set = find_invoices_by_policy_id($id);

?>

<?php $page_title = 'Show Invoices'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>
<br />
<br />
<div id="content">
  <a class="back-link" href="<?php echo url_for('/staff/policys/show.php'); ?>">&laquo; Back to Storage</a>
  <div class="invoice show">

    <table class="list">
      <tr>
        <th>invoice_id</th>
        <th>invoice_date</th>
        <th>due_date</th>
        <th>invoice_amount</th>
        <th>policy_id</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>

      <?php while($invoice = mysqli_fetch_assoc($invoices_set)) { ?>
        <tr>
          <td><?php echo h($invoice['invoice_id']); ?></td>
          <td><?php echo h($invoice['i_date']); ?></td>
          <td><?php echo h($invoice['due_date']); ?></td>
          <td><?php echo h($invoice['i_amount']); ?></td>
          <td><?php echo h($invoice['policy_id']); ?></td>
          <td><a class="action" href="<?php echo url_for('/staff/payment/new.php?id=' . h(u($invoice['invoice_id']))); ?>">Make more payments</a></td>
          <td><a class="action" href="<?php 
              echo url_for('/staff/payment/show.php?id=' . h(u($invoice['invoice_id'])));
          ?>">Payments</a></td>
        </tr>
      <?php } ?>
    </table>
    
    <?php mysqli_free_result($invoices_set); ?>
  </div>
<navigation>
  <ul>
    <li><a href="<?php echo url_for('/staff/invoices/new.php?id=' . h(u($policy_id))); ?>">Make another invoice</a></li>
  </ul>
</navigation>
</div>