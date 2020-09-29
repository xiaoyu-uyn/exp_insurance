<?php

require_once('../../../private/initialize.php');
require_customer_login();
$id = $_GET['id'];
$policy_id = $id;
$policy_ids_set = find_policy_id_by_customer_id($_SESSION['customer_id']);
$home_ids = [];
$counter = 0;
while($policy_id_t = mysqli_fetch_row($policy_ids_set)) {
  $home_ids_sets = find_home_ids_by_policy_id($policy_id_t[0]);
  while($home_id_t = mysqli_fetch_row($home_ids_sets)) {
    $home_ids[] = $home_id_t[0];
    $counter++;
  }
}

if(is_post_request()) {
  $home_id = $home_ids[$_POST['home_id']];

  start_transaction();
  $lock_result = sharelock_home_by_home_id($home_id);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/policys/new_old_home.php?id=' . h(u($policy_id))));
  }
  $home = find_home_by_home_id($home_id);

  $amount = (int)$home['home_area'] * (int)$home['purchase_value'] * $_SESSION['month_number'] / 1000000;
  $lock_result = updatelock_prem_amount_by_policy_id($policy_id);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/policys/new_old_home.php?id=' . h(u($policy_id))));
  }
  update_prem_amount_by_policy_id($policy_id, $amount);
  commit();

  $home_h_policy = [];
  $home_h_policy['home_id'] = $home_id;
  $home_h_policy['policy_id'] = $policy_id;
  insert_home_h_policy($home_h_policy);
  commit();
  $_SESSION['message'] = 'The home was added successfully.';
  redirect_to(url_for('/staff/policys/new_more_home.php?id=' . h(u($policy_id))));
}
?>

<?php $page_title = 'Select a home'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/invoices/new.php?id=' . h(u($policy_id))); ?>">&laquo; Don't need add more home to this policy</a>
  <br />
  <a class="back-link" href="<?php echo url_for('/staff/policys/new_old_home.php?id=' . h(u($policy_id))); ?>">&laquo; Add a home which was insurenced</a>

  <div class="home new">
    <h1>add home</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/new_old_home.php?id=' . h(u($policy_id))); ?>" method="post">
      <dl>
        <dt>home_id</dt>
        <dd>
          <select name="home_id">
            <?php
              if($counter == 0) {
                echo "You have no insurenced home in stock.";
              } else {
                for($i=0; $i < $counter; $i++) {
                  if($i == 0) {
                    echo "<option value=\"0\" selected>" . $home_ids[0] . "</option>";
                  } else {
                    echo "<option value=\"" . $i . "\">" . $home_ids[$i] . "</option>";
                  }
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