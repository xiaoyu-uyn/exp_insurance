<?php

require_once('../../../private/initialize.php');
require_customer_login();
$id = $_GET['id'];
$policy_id = $id;

if(is_post_request()) {

  $vehicle = [];
  $vehicle['vin'] = $_POST['vin'];
  $vehicle['model_year'] = $_POST['model_year'];
  $vehicle['vstatus'] = $_POST['vstatus'];


  $prem_amount =  80 * $_SESSION['month_number'];
  
  $temp_error = validate_vehicle($vehicle);
  if(!empty($temp_error)) {
    $errors = $temp_error;
  } else {
    start_transaction();
    $lock_result = updatelock_prem_amount_by_policy_id($policy_id);
    if($lock_result !== True) {
      rollback();
      $_SESSION['message'] = "System Busy" . $lock_result;
      redirect_to(url_for('/staff/policys/new_vehicle.php?id=' . h(u($policy_id))));
    }
    update_prem_amount_by_policy_id($policy_id, $prem_amount);
    commit();

    insert_vehicle($vehicle);
    $a_policy_vehicles = [];
    $a_policy_vehicles['vin'] =  $vehicle['vin'];
    $a_policy_vehicles['policy_id'] = $policy_id;
    insert_a_policy_vehicles($a_policy_vehicles);
    $combine_id = mysqli_insert_id($db);
    commit();
    $_SESSION['message'] = 'The vehicle was created successfully.';
    redirect_to(url_for('/staff/policys/new_driver.php?id=' . h(u($combine_id))));
  }
}

?>

<?php $page_title = 'Shopping'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/invoices/new.php?id=' . h(u($policy_id))); ?>">&laquo; No more vehicles to add</a>
  <br />
  <a class="back-link" href="<?php echo url_for('/staff/policys/new_old_vehicle.php?id=' . h(u($policy_id))); ?>">&laquo; Add a vehicle which was insurenced</a>

  <div class="customer new">
    <h1>Create Vehicle</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/new_vehicle.php?id=' . h(u($policy_id))); ?>" method="post">
      <dl>
        <dt>VIN</dt>
        <dd><input type="text" name="vin" value=''></dd>
        <dt>17 digits</dt>
      </dl>
      <dl>
        <dt>Model_year</dt>
        <dd><input type="text" name="model_year" value=''></dd>
        <dt>4 digits</dt>
      </dl>
      <dl>
        <dt>Vehicle Status</dt>
        <dd>
          <select name="vstatus">
            <?php
              for($i=1; $i < 4; $i++) {
                if($i == 1) {
                  echo "<option value=\"L\" selected>Leased</option>";
                } elseif($i == 2) {
                  echo "<option value=\"F\">Finaced</option>";
                } elseif($i == 3) {
                  echo "<option value=\"O\">Owned</option>";
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