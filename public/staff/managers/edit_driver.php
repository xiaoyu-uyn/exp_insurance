<?php

require_once('../../../private/initialize.php');
require_manager_login();

$id = $_GET['id'];

if(is_post_request()) {

  $driver = [];
  $driver['license_number'] = $_POST['license_number'];
  $driver['first_name'] = $_POST['first_name'];
  $driver['last_name'] = $_POST['last_name'];
  $driver['birthday'] = $_POST['birthday'];

  $temp_error = validate_driver($driver);
  if(!empty($temp_error)) {
    $errors = $temp_error;
  } else {
    start_transaction();
    $lock_result = updatelock_driver_by_license_number($id);
    if($lock_result !== True) {
      rollback();
      $_SESSION['message'] = "System Busy" . $lock_result;
      redirect_to(url_for('/staff/managers/edit_driver.php?id=' . h(u($id))));
    }
    update_driver_by_license_number($driver, $id);
    commit();

    $_SESSION['message'] = 'The driver was updated successfully.';
    redirect_to(url_for('/staff/managers/manage_driver.php?id=' . h(u($_SESSION['combine_id']))));
  }

} else {
  start_transaction();
  $lock_result = sharelock_driver_by_license_number($id);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/managers/manage_driver.php?id=' . h(u($_SESSION['combine_id']))));
  }
  $driver = find_driver_by_license_number($id);
  commit();

}


?>

<?php $page_title = 'Edit Driver'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/managers/manage_driver.php?id=' . h(u($_SESSION['combine_id']))); ?>">&laquo; Back to List</a>

  <div class="home edit">
    <h1>Edit Driver</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/managers/edit_driver.php?id=' . h(u($id))); ?>" method="post">
      <dl>
        <dt>License Number</dt>
        <dd><input type="text" name="license_number" value='<?php echo $driver['license_number'];?>'></dd>
        <dt>8 digits</dt>
      </dl>
      <dl>
        <dt>First Name</dt>
        <dd><input type="text" name="first_name" value='<?php echo $driver['first_name'];?>'></dd>
        <dt>2-30</dt>
      </dl>
      <dl>
        <dt>Last Name</dt>
        <dd><input type="text" name="last_name" value='<?php echo $driver['last_name'];?>'></dd>
        <dt>2-30</dt>
      </dl>
      <dl>
        <dt>Birthday</dt>
        <dd><input type="text" name="birthday" value='<?php echo $driver['birthday'];?>' ></dd>
        <dt>m,d,y</dt>
      </dl>
      <div id="operations">
        <input type="submit" value="Submit" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/manager_footer.php'); ?>