<?php

require_once('../../../private/initialize.php');
require_manager_login();
if(!isset($_GET['id'])) {
  redirect_to(url_for('/staff/managers/index.php'));
}
$id = $_GET['id'];
$customer_id = find_customer_id_by_policy_id($id);
$policy_id = $id;

if(is_post_request()) {
  $vin_sets = find_vins_by_policy_id($policy_id);
  while($vin = mysqli_fetch_row($vin_sets)) {
    start_transaction();
    $lock_result = updatelock_vehicle_by_vin($vin[0]);
    if($lock_result !== True) {
      rollback();
      $_SESSION['message'] = "System Busy" . $lock_result;
      redirect_to(url_for('/staff/managers/delete_a.php?id=' . $id));
    }
    delete_vehicle($vin[0]);
    commit();
  } 
  start_transaction();
  $lock_result = updatelock_policy_by_policy_id($policy_id);
  if($lock_result !== True) {
    rollback();
    $_SESSION['message'] = "System Busy" . $lock_result;
    redirect_to(url_for('/staff/managers/delete_a.php?id=' . $id));
  }
  delete_policy($policy_id);
  commit();
  mysqli_free_result($vin_sets);

  $_SESSION['message'] = 'The policy was deleted successfully.';
  redirect_to(url_for('/staff/managers/manage_policy.php?id=' . h(u($customer_id))));

}

?>

<?php $page_title = 'Delete Policy'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/managers/manage_policy.php?id=' . h(u($customer_id))); ?>">&laquo; Back to List</a>

  <div class="policy delete">
    <h1>Delete Policy</h1>
    <p>Are you sure you want to delete this policy?</p>

    <form action="<?php echo url_for('/staff/managers/delete_a.php?id=' . $id); ?>" method="post">
      <div id="operations">
        <input type="submit" name="commit" value="Delete Policy" />
      </div>
    </form>
  </div>

</div>

<?php include(SHARED_PATH . '/manager_footer.php'); ?>