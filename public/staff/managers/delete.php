<?php

require_once('../../../private/initialize.php');
require_manager_login();
if(!isset($_GET['id'])) {
  redirect_to(url_for('/staff/managers/index.php'));
}
$id = $_GET['id'];

if(is_post_request()) {
  $policy_id_sets = [];
  $home_id_sets = [];
  $home_id_sets = [];
  $policy_id_sets = find_policy_id_by_customer_id($id); // row

  while($policy_id = mysqli_fetch_row($policy_id_sets)) {
    $home_id_sets = find_home_id_by_policy_id($policy_id[0]);
    while($home_id = mysqli_fetch_row($home_id_sets)) {
      start_transaction();
      $lock_result = updatelock_home_by_home_id($home_id[0]);
      if($lock_result !== True) {
        rollback();
        $_SESSION['message'] = "System Busy" . $lock_result;
        redirect_to(url_for('/staff/managers/index.php'));
      }
      delete_home($home_id[0]);
      commit();
    } 
    mysqli_free_result($home_id_sets);
    }
  mysqli_free_result($policy_id_sets);
  $result = delete_customer($id);
  commit();
  $_SESSION['message'] = 'The customer was deleted successfully.';
  redirect_to(url_for('/staff/managers/index.php'));

} else {
  $customer = find_customer_by_id($id);
}

?>

<?php $page_title = 'Delete Customer'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/managers/index.php'); ?>">&laquo; Back to List</a>

  <div class="admin delete">
    <h1>Delete Admin</h1>
    <p>Are you sure you want to delete this customer?</p>
    <p class="item"><?php echo h($customer['USERNAME']); ?></p>

    <form action="<?php echo url_for('/staff/managers/delete.php?id=' . $id); ?>" method="post">
      <div id="operations">
        <input type="submit" name="commit" value="Delete Customer" />
      </div>
    </form>
  </div>

</div>

<?php include(SHARED_PATH . '/manager_footer.php'); ?>
