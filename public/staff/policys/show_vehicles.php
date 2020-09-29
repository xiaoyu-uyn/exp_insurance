<?php require_once('../../../private/initialize.php'); ?>

<?php
require_customer_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id']; // PHP > 7.0
$vin_set = find_vin_by_policy_id($id);

?>

<?php $page_title = 'Show Covered Vehicles'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>
<br />
<br />
<div id="content">
<a class="back-link" href="<?php echo url_for('/staff/policys/show.php'); ?>">&laquo; Storage</a>
  <div class="vehicles show">

    <table class="list">
      <tr>
        <th>Vin</th>
        <th>Model Year</th>
        <th>Status</th>
        <th>&nbsp;</th>
        

      </tr>

      <?php while($vin = mysqli_fetch_row($vin_set)) { ?>
        <?php 
          start_transaction();
          $lock_result = sharelock_vehicle_by_vin($vin[0]);
          if($lock_result !== True) {
            rollback();
            $_SESSION['message'] = "System Busy" . $lock_result;
            redirect_to(url_for('/staff/policys/show.php'));
          }
          $vehicle = find_vehicle_by_vin($vin[0]);
          commit();
        ?>
        <?php $combine_id = find_combine_id_by_vin_and_policy_id($vin[0], $id);?>
        <tr>
          <td><?php echo h($vehicle['vin']); ?></td>
          <td><?php echo h($vehicle['model_year']); ?></td>

          <td><?php 
          if($vehicle['vstatus'] === 'L'){
            echo h('Leased');
          } elseif($vehicle['vstatus'] === 'F'){
            echo h('Financed');
          } elseif($vehicle['vstatus'] === 'O'){
            echo h('Owned');
          }
          ?></td>
          <td><a class="action" href="<?php echo url_for('/staff/policys/showdrivers.php?id=' . h(u($combine_id)));?>">Covered drivers</a></td>
          
        </tr>
      <?php } ?>
    </table>
    <?php mysqli_free_result($vin_set); ?>
  </div>

</div>