<?php require_once('../../../private/initialize.php'); ?>

<?php
require_customer_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id']; // PHP > 7.0
$invoice_id = $id;
$policy_id = find_policy_id_by_invoice_id($invoice_id);
$unpaid_amount = calculate_unpaid_amount($invoice_id);
if($unpaid_amount != 0) {
  $_SESSION['message'] = 'Please make more payments to pay your remainning $' . $unpaid_amount . ' fees.';
}

$payments_set = find_payments_by_invoice_id($id);

?>

<?php $page_title = 'Show Payments'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>
<br />
<br />
<div id="content">
  <a class="back-link" href="<?php echo url_for('/staff/invoices/show.php?id=' . h(u($policy_id))); ?>">&laquo; back to invoices</a>

  <div class="Payments show">

    <table class="list">
      <tr>
        <th>payment_id</th>
        <th>method</th>
        <th>amount</th>
        <th>pay_date</th>
        <th>invoice_id</th>
      </tr>

      <?php while($payment = mysqli_fetch_assoc($payments_set)) { ?>
        <tr>
          <td><?php echo h($payment['id']); ?></td>
          <td><?php echo h($payment['method']); ?></td>
          <td><?php echo h($payment['amount']); ?></td>
          <td><?php echo h($payment['p_date']); ?></td>
          <td><?php echo h($payment['invoice_id']); ?></td>
        </tr>
      <?php } ?>
    </table>
    
    <?php mysqli_free_result($payments_set); ?>
  </div>
<navigation>
  <ul>
    <li><a href="<?php echo url_for('/staff/payment/new.php?id=' . h(u($invoice_id))); ?>">Make another payment</a></li>
  </ul>
</navigation>
</div>