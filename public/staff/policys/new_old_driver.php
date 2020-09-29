<?php

require_once('../../../private/initialize.php');
require_customer_login();

$id = $_GET['id'];
$combine_id = $id;


$policy_id = find_policy_id_by_combine_id($id);

$vin = find_vin_by_combine_id($id);


$policy_ids_set = find_policy_id_by_customer_id($_SESSION['customer_id']);
$license_numbers = [];
$counter = 0;
while($policy_id_t = mysqli_fetch_row($policy_ids_set)) {
  $combine_id_sets = find_combine_id_by_policy_id($policy_id_t[0]);
  while($combine_id_t = mysqli_fetch_row($combine_id_sets)) {
    $license_number_sets = find_license_number_by_combine_id($combine_id_t[0]);
    while($license_number_t = mysqli_fetch_row($license_number_sets)) {
      $license_numbers[] = $license_number_t[0];
      $counter++;
    }
  }
}

if(is_post_request()) {

  $license_number = $license_numbers[$_POST['license_number']];


  $a_vehicles_drivers = [];
  $a_vehicles_drivers['combine_id'] = $id;
  $a_vehicles_drivers['license_number'] = $license_number;
  insert_a_vehicles_drivers($a_vehicles_drivers);
  commit();
  $_SESSION['message'] = 'The driver was created successfully.';
  redirect_to(url_for('/staff/policys/new_driver.php?id=' . h(u($combine_id))));

}

?>

<?php $page_title = 'Shopping'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/policys/new_vehicle.php?id=' . h(u($policy_id))); ?>">&laquo; No more drivers for this vehicle</a>

  <div class="customer new">
    <h1>Create Driver to Vehicle <?php echo $vin;?></h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/new_old_driver.php?id=' . h(u($combine_id))); ?>" method="post">
      <dl>
        <dt>License Number</dt>
        <dd>
          <select name="license_number">
            <?php
              if($counter == 0) {
                echo "You have no insurenced driver in stock.";
              } else {
                for($i=0; $i < $counter; $i++) {
                  if($i == 0) {
                    echo "<option value=\"0\" selected>" . $license_numbers[0] . "</option>";
                  } else {
                    echo "<option value=\"" . $i . "\">" . $license_numbers[$i] . "</option>";
                  }
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