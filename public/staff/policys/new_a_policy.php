<?php

require_once('../../../private/initialize.php');
require_customer_login();
if(is_post_request()) {

  $policy = [];
  $policy['type'] = 'A';
  $policy['start_date'] = $_POST['start_date'];
  $policy['end_date'] = $_POST['end_date'];
  $policy['customer_id'] = $_SESSION['customer_id'];
  $startd = strtotime(str_replace(',', '-', $policy['start_date']));
  $endd = strtotime(str_replace(',', '-', $policy['end_date']));
  $startdate = new DateTime();
  $startdate->setTimestamp($startd);
  $enddate = new DateTime();
  $enddate->setTimestamp($endd);
  $curdate = new DateTime();
  $curdate->setTimestamp(time());
  $Interval = $enddate->diff($startdate);
  $month_diff = $Interval->m;
  $year_diff = $Interval->y;
  $month_number = $year_diff * 12 + $month_diff;
  $_SESSION['month_number'] = $month_number;

  if($enddate > $curdate) {
    $policy['status'] = 'C';
  } else {
    $policy['status'] = 'P';
  }
  // Q
  $vehicle = [];
  $vehicle['vin'] = $_POST['vin'];
  $vehicle['model_year'] = $_POST['model_year'];
  $vehicle['vstatus'] = $_POST['vstatus'];


  $policy['prem_amount'] =  80 * $month_number;
  
  $temp_error1 = validate_vehicle($vehicle);
  $temp_error2 = validate_policy($policy);
  $temp_error = array_merge($temp_error1, $temp_error2);
  if(!empty($temp_error)) {
    $errors = $temp_error;
  } else {
    insert_policy($policy);
    $policy_id = mysqli_insert_id($db);

    insert_vehicle($vehicle);
    $_SESSION['policy_id'] = $policy_id;

    $a_policy = [];
    $a_policy['policy_id'] = $policy_id;
    $a_policy['a_policy_type'] = 'A';
    insert_a_policy($a_policy);
    $a_policy_vehicles = [];
    $a_policy_vehicles['vin'] =  $vehicle['vin'];
    $a_policy_vehicles['policy_id'] = $policy_id;
    insert_a_policy_vehicles($a_policy_vehicles);
    $combine_id = mysqli_insert_id($db);
    commit();
    $_SESSION['message'] = 'The policy was created successfully.';
    redirect_to(url_for('/staff/policys/new_driver.php?id=' . h(u($combine_id))));
  }
}

?>

<?php $page_title = 'Shopping'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/policys/show.php'); ?>">&laquo; Back to Storage</a>

  <div class="customer new">
    <h1>Create Auto Policy</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/new_a_policy.php'); ?>" method="post">
      <dl>
        <dt>start_date</dt>
        <dd><input type="text" name="start_date" value=''></dd>
        <dt>m,d,y</dt>
      </dl>
      <dl>
        <dt>end_date</dt>
        <dd><input type="text" name="end_date" value='' ></dd>
        <dt>m,d,y</dt>
      </dl>
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