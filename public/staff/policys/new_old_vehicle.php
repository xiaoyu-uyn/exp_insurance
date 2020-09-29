<?php

require_once('../../../private/initialize.php');
require_customer_login();
$id = $_GET['id'];
$policy_id = $id;
$policy_ids_set = find_policy_id_by_customer_id($_SESSION['customer_id']);
$vins = [];
$counter = 0;
while($policy_id_t = mysqli_fetch_row($policy_ids_set)) {
  $vin_sets = find_vin_by_policy_id($policy_id_t[0]);
  while($vin_t = mysqli_fetch_row($vin_sets)) {
    $vins[] = $vin_t[0];
    $counter++;
  }
}

if(is_post_request()) {

  $vin = $vins[$_POST['vin']];
  start_transaction();
  $lock_result = sharelock_vehicle_by_vin($vin);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/policys/new_old_vehicle.php?id=' . h(u($policy_id))));
  }
  $vehicle = find_vehicle_by_vin($vin);

  $prem_amount =  80 * $_SESSION['month_number'];
  $lock_result = updatelock_prem_amount_by_policy_id($policy_id);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/policys/new_old_vehicle.php?id=' . h(u($policy_id))));
  }
  update_prem_amount_by_policy_id($policy_id, $prem_amount);
  commit();


  $a_policy_vehicles = [];
  $a_policy_vehicles['vin'] =  $vehicle['vin'];
  $a_policy_vehicles['policy_id'] = $policy_id;
  insert_a_policy_vehicles($a_policy_vehicles);

  $combine_id = mysqli_insert_id($db);
  commit();
  $_SESSION['message'] = 'The vehicle was created successfully.';
  redirect_to(url_for('/staff/policys/new_driver.php?id=' . h(u($combine_id))));
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

    <form action="<?php echo url_for('/staff/policys/new_old_vehicle.php?id=' . h(u($policy_id))); ?>" method="post">
      <dl>
        <dt>VIN</dt>
        <dd>
          <select name="vin">
            <?php
              if($counter == 0) {
                echo "You have no insurenced vehicle in stock.";
              } else {
                for($i=0; $i < $counter; $i++) {
                  if($i == 0) {
                    echo "<option value=\"0\" selected>" . $vins[0] . "</option>";
                  } else {
                    echo "<option value=\"" . $i . "\">" . $vins[$i] . "</option>";
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