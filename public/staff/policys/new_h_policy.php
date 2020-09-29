<?php

require_once('../../../private/initialize.php');
require_customer_login();
if(is_post_request()) {

  $policy = [];
  $policy['type'] = 'H';
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
  $home = [];
  $home['purchase_date'] = $_POST['purchase_date'];
  $home['purchase_value'] = $_POST['purchase_value'];
  $home['home_area'] = $_POST['home_area'];
  $home['home_type'] = $_POST['home_type'];
  $home['AutoFireNotification'] = $_POST['AutoFireNotification'];
  $home['HomeSecuritySystem'] = $_POST['HomeSecuritySystem'];
  if($_POST['SwimmingPool'] !== 'nul') {
    $home['SwimmingPool'] = $_POST['SwimmingPool'];
  }
  $home['Basement'] = $_POST['Basement'];

  $policy['prem_amount'] =  (int)$home['home_area'] * (int)$home['purchase_value'] * (int)$month_number / 1000000;
  
  $temp_error1 = validate_home($home);
  $temp_error2 = validate_policy($policy);
  $temp_error = array_merge($temp_error1, $temp_error2);
  if(!empty($temp_error)) {
    $errors = $temp_error;
  } else {
    insert_policy($policy);
    $policy_id = mysqli_insert_id($db);
    insert_home($home);
    $home_id = mysqli_insert_id($db);
    $_SESSION['policy_id'] = $policy_id;
    $h_policy = [];
    $h_policy['policy_id'] = $policy_id;
    $h_policy['h_policy_type'] = 'H';
    $home_h_policy = [];
    $home_h_policy['home_id'] = $home_id;
    $home_h_policy['policy_id'] = $policy_id;
    insert_h_policy($h_policy);
    insert_home_h_policy($home_h_policy);
    commit();
    $_SESSION['message'] = 'The policy was created successfully.';
    redirect_to(url_for('/staff/policys/new_more_home.php?id=' . h(u($policy_id))));
  }
}

?>

<?php $page_title = 'Shopping'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/policys/show.php'); ?>">&laquo; Back to Storage</a>

  <div class="customer new">
    <h1>Create Home policy</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/new_h_policy.php'); ?>" method="post">
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
        <dt>purchase_date</dt>
        <dd><input type="text" name="purchase_date" value=''></dd>
        <dt>m,d,y</dt>
      </dl>
      <dl>
        <dt>purchase_value</dt>
        <dd><input type="text" name="purchase_value" value=''></dd>
        <dt>$</dt>
      </dl>
      <dl>
        <dt>home_area</dt>
        <dd><input type="text" name="home_area" value=''></dd>
        <dt>in square meter</dt>
      </dl>
      <dl>
        <dt>home_type</dt>
        <dd>
          <select name="home_type">
            <?php
              for($i=1; $i < 5; $i++) {
                if($i == 1) {
                  echo "<option value=\"S\" selected>Single Family</option>";
                } elseif($i == 2) {
                  echo "<option value=\"M\">Multi Family</option>";
                } elseif($i == 3) {
                  echo "<option value=\"C\">Condominimum</option>";
                } elseif($i == 4) {
                  echo "<option value=\"T\">Town House</option>";
                }
              }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>AutoFireNotification</dt>
        <dd>
          <select name="AutoFireNotification">
            <?php
              for($i=1; $i < 3; $i++) {
                if($i == 1) {
                  echo "<option value=\"1\" selected>Yes</option>";
                } elseif($i == 2) {
                  echo "<option value=\"0\">No</option>";
                }
              }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>HomeSecuritySystem</dt>
        <dd>
          <select name="HomeSecuritySystem">
            <?php
              for($i=1; $i < 3; $i++) {
                if($i == 1) {
                  echo "<option value=\"1\" selected>Yes</option>";
                } elseif($i == 2) {
                  echo "<option value=\"0\">No</option>";
                }
              }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>SwimmingPool</dt>
        <dd>
          <select name="SwimmingPool">
            <?php
              for($i=1; $i < 5; $i++) {
                if($i == 1) {
                  echo "<option value=\"U\" selected>Underground swimming pool</option>";
                } elseif($i == 2) {
                  echo "<option value=\"M\">Overground swimming pool</option>";
                } elseif($i == 3) {
                  echo "<option value=\"C\">Indoor swimming pool</option>";
                } elseif($i == 4) {
                  echo "<option value=\"T\">Multiple swimming pool</option>";
                } elseif($i == 5) {
                  echo "<option value=\"Nul\">No swimming pool</option>";
                }    
              }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>Basement</dt>
        <dd>
          <select name="Basement">
            <?php
              for($i=1; $i < 3; $i++) {
                if($i == 1) {
                  echo "<option value=\"1\" selected>Yes</option>";
                } elseif($i == 2) {
                  echo "<option value=\"0\">No</option>";
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