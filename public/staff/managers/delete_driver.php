<?php

require_once('../../../private/initialize.php');
require_manager_login();

$id = $_GET['id'];


if(is_post_request()) {
  start_transaction();
  $lock_result = updatelock_driver_by_license_number($id);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/managers/delete_driver.php?id=' . $id));
  }
  delete_driver($id);
  commit();

  $_SESSION['message'] = 'The driver was deleted successfully.';
  redirect_to(url_for('/staff/managers/manage_driver.php?id=' . h(u($_SESSION['combine_id']))));

}

?>

<?php $page_title = 'Delete Driver'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/managers/manage_driver.php?id=' . h(u($_SESSION['combine_id']))); ?>">&laquo; Back to List</a>

  <div class="policy delete">
    <h1>Delete Driver</h1>
    <p>Are you sure you want to delete this driver?</p>

    <form action="<?php echo url_for('/staff/managers/delete_driver.php?id=' . $id); ?>" method="post">
      <div id="operations">
        <input type="submit" name="commit" value="Delete Driver" />
      </div>
    </form>
  </div>

</div>

<?php include(SHARED_PATH . '/manager_footer.php'); ?>