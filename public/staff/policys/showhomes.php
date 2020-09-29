<?php require_once('../../../private/initialize.php'); ?>

<?php
require_customer_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id']; // PHP > 7.0

$home_id_set = find_home_ids_by_policy_id($id);

?>

<?php $page_title = 'Show Covered Homes'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>
<br />
<br />
<div id="content">
<a class="back-link" href="<?php echo url_for('/staff/policys/show.php'); ?>">&laquo; Storage</a>
  <div class="home show">

    <table class="list">
      <tr>
        <th>home_id</th>
        <th>purchase_date</th>
        <th>purchase_value</th>
        <th>home_area</th>
        <th>home_type</th>
        <th>AutoFireNotification</th>
        <th>HomeSecuritySystem</th>
        <th>SwimmingPool</th>
        <th>Basement</th>
      </tr>

      <?php while($home_id = mysqli_fetch_row($home_id_set)) { ?>
        <?php 
          start_transaction();
          $lock_result = sharelock_home_by_home_id($home_id[0]);
          if($lock_result !== True) {
            rollback();
            $_SESSION['message'] = "System Busy" . $lock_result;
            redirect_to(url_for('/staff/policys/show.php'));
          }
          $home = find_home_by_home_id($home_id[0]);
          commit();
        ?>
        <tr>
          <td><?php echo h($home['home_id']); ?></td>
          <td><?php echo h($home['purchase_date']); ?></td>
          <td><?php echo h($home['purchase_value']); ?></td>
          <td><?php echo h($home['home_area']); ?></td>

          <td><?php 
          if($home['home_type'] === 'S'){
            echo h('Single Family');
          } elseif($home['home_type'] === 'M'){
            echo h('Multi Family');
          } elseif($home['home_type'] === 'C'){
            echo h('Condominimum');
          } elseif($home['home_type'] === 'T'){
            echo h('Town House');
          }
          ?></td>
          <td><?php 
          if($home['homesecuritysystem'] == 1){
            echo h('Yes');
          } elseif($home['homesecuritysystem'] == 0){
            echo h('No');
          }
          ?></td>
          <td><?php 
          if($home['autofirenotification'] == 1){
            echo h('Yes');
          } elseif($home['autofirenotification'] == 0){
            echo h('No');
          }
          ?></td>
          <td><?php 
          if($home['swimmingpool'] === 'U'){
            echo h('Underground swimming pool');
          } elseif($home['swimmingpool'] === 'O'){
            echo h('Overground swimming pool');
          } elseif($home['swimmingpool'] === 'I'){
            echo h('Indoor swimming pool');
          } elseif($home['swimmingpool'] === 'M'){
            echo h('Multiple swimming pool');
          } else {
            echo h('No swimmingpool');
          }
          ?></td>
          <td><?php 
          if($home['basement'] == 1){
            echo h('Yes');
          } elseif($home['basement'] == 0){
            echo h('No');
          }
          ?></td>
        </tr>
      <?php } ?>
    </table>
    <?php mysqli_free_result($home_id_set); ?>
  </div>

</div>