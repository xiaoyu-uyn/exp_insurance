<?php

require_once('../../../private/initialize.php');
require_customer_login();
$id = $_GET['id'];
$policy_id = $id;
if(is_post_request()) {
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
  $temp_error = [];
  $temp_error = validate_home($home);
  if(!empty($temp_error)) {
    $errors = $temp_error;
  } else {
    $amount = (int)$home['home_area'] * (int)$home['purchase_value'] * $_SESSION['month_number'] / 1000000;
    start_transaction();
    $lock_result = updatelock_prem_amount_by_policy_id($policy_id);
    if($lock_result !== True) {
      rollback();
      $_SESSION['message'] = "System Busy";
      redirect_to(url_for('/staff/policys/new_more_home.php?id=' . h(u($policy_id))));
    }
    update_prem_amount_by_policy_id($policy_id, $amount);
    commit();

    insert_home($home);
    $home_id = mysqli_insert_id($db);
    // $home_id = auto_increment_home_id();
    $home_h_policy = [];
    $home_h_policy['home_id'] = $home_id;
    $home_h_policy['policy_id'] = $policy_id;
    insert_home_h_policy($home_h_policy);
    commit();
    $_SESSION['message'] = 'The home was added successfully.';
    redirect_to(url_for('/staff/policys/new_more_home.php?id=' . h(u($policy_id))));
  }
}
?>

<?php $page_title = 'Add more homes'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/invoices/new.php?id=' . h(u($policy_id))); ?>">&laquo; Don't need add more home to this policy</a>
  <br />
  <a class="back-link" href="<?php echo url_for('/staff/policys/new_old_home.php?id=' . h(u($policy_id))); ?>">&laquo; Add a home which was insurenced</a>

  <div class="home new">
    <h1>add home</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/new_more_home.php?id=' . h(u($policy_id))); ?>" method="post">
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