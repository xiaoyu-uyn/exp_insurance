<?php

require_once('../../../private/initialize.php');

$id = $_GET['id'];
$policy_id = $id;
$uninvoiced_amount = calculate_uninvoiced_amount($id);
if($uninvoiced_amount == 0) {
  $_SESSION['message'] = 'All fees has been paid successfully';
  redirect_to(url_for('/staff/policys/show.php'));
}
$_SESSION['message'] = 'The invoice was created successfully. Remaining amount is ' . $uninvoiced_amount;
if(is_post_request()) {

  $curdate_reg = date('m,d,Y');
  // $curdate_reg = $curdate->format('Y/m/d');

  $due_date = strtotime('+ ' . $_POST['due_date'] . ' days', time());
  $due_date_datetime = new DateTime();
  $due_date_datetime->setTimestamp($due_date);
  $due_date_reg = $due_date_datetime->format('m,d,Y');

  $invoice = [];
  $invoice['i_date'] = $curdate_reg;
  $invoice['due_date'] = $due_date_reg;
  $invoice['i_amount'] = $_POST['i_amount'];
  $invoice['policy_id'] = $policy_id;

  $temp_error = validate_invoice($invoice, $uninvoiced_amount);
  if(!empty($temp_error)) {
    $errors = $temp_error;
  } else {
    insert_invoice($invoice);
    $invoice_id = mysqli_insert_id($db);
    commit();
    $_SESSION['message'] = 'The invoice was created successfully. Remaining amount is ' . $uninvoiced_amount;
    redirect_to(url_for('/staff/payment/new.php?id=' . h(u($invoice_id))));
  }
}

?>

<?php $page_title = 'Create Invoice'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/policys/show.php'); ?>">&laquo; Back to Storage</a>

  <div class="customer new">
    <h1>Create Invoices</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/invoices/new.php?id=' . h(u($policy_id))); ?>" method="post">
      <dl>
        <dt>Invoice_Amount</dt>
        <dd><input type="text" name="i_amount" value='<?php echo $uninvoiced_amount; ?>'></dd>
        <dt>$ *.??</dt>
      </dl>
      <dl>
        <dt>due_date</dt>
        <dd>
          <select name="due_date">
            <?php
              for($i=1; $i < 5; $i++) {
                if($i == 1) {
                  echo "<option value=\"7\" selected>7 days</option>";
                } elseif($i == 2) {
                  echo "<option value=\"14\">14 days</option>";
                } elseif($i == 3) {
                  echo "<option value=\"30\">30 days</option>";
                } elseif($i == 4) {
                  echo "<option value=\"60\">60 days</option>";
                }
              }
            ?>
          </select>
        </dd>
      </dl>
      <div id="operations">
        <input type="submit" value="Submit" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/customer_footer.php'); ?>