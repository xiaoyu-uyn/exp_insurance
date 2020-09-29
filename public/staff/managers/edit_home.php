<?php

require_once('../../../private/initialize.php');
require_manager_login();

$id = $_GET['id'];
$policy_id = find_policy_id_by_home_id($id);

if(is_post_request()) {

  $home = [];
  $home['home_id'] = $id;
  $home['purchase_date'] = $_POST['purchase_date'];
  $home['home_type'] = $_POST['home_type'];
  $home['AutoFireNotification'] = $_POST['AutoFireNotification'];
  $home['HomeSecuritySystem'] = $_POST['HomeSecuritySystem'];
  if($_POST['SwimmingPool'] !== 'nul') {
    $home['SwimmingPool'] = $_POST['SwimmingPool'];
  }
  $home['Basement'] = $_POST['Basement'];
  start_transaction();
  $lock_result = updatelock_home_by_home_id($home['home_id']);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/managers/edit_home.php?id=' . h(u($id))));
  }
  update_home($home);
  commit();
  $_SESSION['message'] = 'The home was updated successfully.';
  redirect_to(url_for('/staff/managers/manage_home.php?id=' . h(u($policy_id))));
} else {
  start_transaction();
  $lock_result = sharelock_home_by_home_id($id);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/managers/manage_home.php?id=' . h(u($policy_id))));
  }
  $home = find_home_by_home_id($id);
  commit();

}


?>

<?php $page_title = 'Edit Admin'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/managers/manage_home.php?id=' . h(u($policy_id))); ?>">&laquo; Back to Home</a>

  <div class="home edit">
    <h1>Edit Home</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/managers/edit_home.php?id=' . h(u($id))); ?>" method="post">
      <dl>
        <dt>purchase_date</dt>
        <dd><input type="text" name="purchase_date" value='<?php echo $home['purchase_date']; ?>'></dd>
        <dt>m,d,y (<?php echo $home['purchase_date']; ?>)</dt>
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

<?php include(SHARED_PATH . '/manager_footer.php'); ?>