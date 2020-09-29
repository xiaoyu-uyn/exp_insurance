<?php

require_once('../../../private/initialize.php');
require_manager_login();

$id = $_GET['id'];
$vin = $id;

if(is_post_request()) {

  $vehicle = [];
  $vehicle['vin'] = $_POST['vin'];
  $vehicle['vstatus'] = $_POST['vstatus'];

  $temp_error = validate_vehicle_no_model_year($vehicle);
  if(!empty($temp_error)) {
    $errors = $temp_error;
  } else {
    start_transaction();
    $lock_result = updatelock_vehicle_by_vin($vin);
    if($lock_result !== True) {
      rollback();
      $_SESSION['message'] = "System Busy" . $lock_result;
      redirect_to(url_for('/staff/managers/manage_vehicle.php?id=' . h(u($_SESSION['policy_id']))));
    }
    $res = update_vehicle_by_vin($vehicle, $vin);
    commit();
    if($res === True) {
      $_SESSION['message'] = 'The vehicle was updated successfully.';
      redirect_to(url_for('/staff/managers/manage_vehicle.php?id=' . h(u($_SESSION['policy_id']))));
    } else {
      $errors = $res;
    }
    
  }

} else {
  start_transaction();
  $lock_result = sharelock_vehicle_by_vin($vin);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/managers/manage_vehicle.php?id=' . h(u($_SESSION['policy_id']))));
  }
  $vehicle = find_vehicle_by_vin($vin);
  commit();

}


?>

<?php $page_title = 'Edit Vehicle'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/managers/manage_vehicle.php?id=' . h(u($_SESSION['policy_id']))); ?>">&laquo; Back to List</a>

  <div class="home edit">
    <h1>Edit Vehicle</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/managers/edit_vehicle.php?id=' . h(u($id))); ?>" method="post">
      <dl>
        <dt>VIN</dt>
        <dd><input type="text" name="vin" value='<?php echo $vehicle['vin'] ;?>'></dd>
        <dt>17 digits</dt>
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

<?php include(SHARED_PATH . '/manager_footer.php'); ?>