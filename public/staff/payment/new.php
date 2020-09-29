<?php

require_once('../../../private/initialize.php');

$id = $_GET['id'];
$invoice_id = $id;
$policy_id = find_policy_id_by_invoice_id($invoice_id);
$unpaid_amount = calculate_unpaid_amount($invoice_id);
$_SESSION['message'] = ' Remaining amount is ' . $unpaid_amount;
if(is_post_request()) {
  
  $curdate_reg = date('m,d,Y');
  // $curdate_reg = $curdate->format('Y/m/d');

  $payment = [];
  $payment['p_date'] = $curdate_reg;
  $payment['method'] = $_POST['method'];
  $payment['amount'] = $_POST['amount'];
  $payment['invoice_id'] = $invoice_id;

  $temp_error = validate_payment($payment, $unpaid_amount);
  if(!empty($temp_error)) {
    $errors = $temp_error;
  } else {
    insert_payment($payment);
    commit();
    $unpaid_amount -= $payment['amount'];
    $_SESSION['message'] = 'The payment was created successfully. Remaining amount is ' . $unpaid_amount;
    if($unpaid_amount == 0) {
      redirect_to(url_for('/staff/invoices/new.php?id=' . h(u($policy_id))));
    } else {
      redirect_to(url_for('/staff/payment/new.php?id=' . h(u($invoice_id))));
    }
    
  }
}

?>

<?php $page_title = 'Create Payment'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">


  <a class="back-link" href="<?php echo url_for('/staff/invoices/show.php?id=' . h(u($policy_id))); ?>">&laquo; back to invoices</a>

  <div class="payment new">
    <h1>Create Payment</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/payment/new.php?id=' . h(u($invoice_id))); ?>" method="post">
      <dl>
        <dt>Method</dt>
        <dd>
          <select name="method">
            <?php
              for($i=1; $i < 5; $i++) {
                if($i == 1) {
                  echo "<option value=\"Paypal\" selected>Paypal</option>";
                } elseif($i == 2) {
                  echo "<option value=\"Credit\">Credit</option>";
                } elseif($i == 3) {
                  echo "<option value=\"Check\">Check</option>";
                } elseif($i == 4) {
                  echo "<option value=\"Debit\">Debit</option>";
                }
              }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>Amount</dt>
        <dd><input type="text" name="amount" value='<?php echo $unpaid_amount ;?>'></dd>
        <dt>$ *.??</dt>
      </dl>
      <div id="operations">
        <input type="submit" value="Submit" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/customer_footer.php'); ?>