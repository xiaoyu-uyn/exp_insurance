<?php

require_once('../../../private/initialize.php');
require_customer_login();

$id = $_GET['id'];
$combine_id = $id;


$policy_id = find_policy_id_by_combine_id($id);

$vin = find_vin_by_combine_id($id);


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
    insert_driver($driver);
    $a_vehicles_drivers = [];
    $a_vehicles_drivers['combine_id'] = $id;
    $a_vehicles_drivers['license_number'] = $driver['license_number'];
    insert_a_vehicles_drivers($a_vehicles_drivers);
    commit();
    $_SESSION['message'] = 'The driver was created successfully.';
    redirect_to(url_for('/staff/policys/new_driver.php?id=' . h(u($combine_id))));
  }
}

?>

<?php $page_title = 'Shopping'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/policys/new_vehicle.php?id=' . h(u($policy_id))); ?>">&laquo; No more drivers for this vehicle</a>
  <br />
  <a class="back-link" href="<?php echo url_for('/staff/policys/new_old_driver.php?id=' . h(u($combine_id))); ?>">&laquo; Add a driver which is in your stock</a>

  <div class="customer new">
    <h1>Create Driver to Vehicle <?php echo $vin;?></h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/new_driver.php?id=' . h(u($combine_id))); ?>" method="post">
      <dl>
        <dt>License Number</dt>
        <dd><input type="text" name="license_number" value=''></dd>
        <dt>8 digits</dt>
      </dl>
      <dl>
        <dt>First Name</dt>
        <dd><input type="text" name="first_name" value=''></dd>
        <dt>2-30</dt>
      </dl>
      <dl>
        <dt>Last Name</dt>
        <dd><input type="text" name="last_name" value=''></dd>
        <dt>2-30</dt>
      </dl>
      <dl>
        <dt>Birthday</dt>
        <dd><input type="text" name="birthday" value='' ></dd>
        <dt>m,d,y</dt>
      </dl>
      <div id="operations">
        <input type="submit" value="Submit" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/customer_footer.php'); ?>