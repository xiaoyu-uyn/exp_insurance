<?php require_once('../../../private/initialize.php'); ?>

<?php
  require_manager_login();

  if(is_post_request()) {
    if(!$_POST['first_name'] || !$_POST['last_name']) {
      $customers_set = find_all_customers();
    } else {
      $customers_set = find_customers_by_name($_POST['first_name'], $_POST['last_name']);
      $test2 = find_customers_by_name($_POST['first_name'], $_POST['last_name']);
      if(!$test = mysqli_fetch_assoc($test2)) {
        $_SESSION['message'] = 'No such customer ' . $_POST['first_name'] . ' ' . $_POST['last_name'];
        redirect_to(url_for('/staff/managers/index.php'));
      }
    }
  } else {
    $customers_set = find_all_customers();
  }

?>

<?php $page_title = 'Manger homepage'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">
  <div class="listing">
    <h1>Find Customer by lastName and FirstName</h1>
     <form action="<?php echo url_for('/staff/managers/index.php'); ?>" method="post">
      <dl>
        <dt>First Name</dt>
        <dd><input type="text" name="first_name" value=''></dd>
      </dl>
      <dl>
        <dt>Last Name</dt>
        <dd><input type="text" name="last_name" value=''></dd>
      </dl>
      <div id="operations">
        <input type="submit" value="Search" />
      </div>
    </form>
    <h1>Customers</h1>

    <div class="actions">
      <a class="action" href="<?php echo url_for('/staff/managers/new.php'); ?>">Create New Manager</a>
    </div>

    <table class="list">
      <tr>
        <th>customer_id</th>
        <th>first_name</th>
        <th>last_name</th>
        <th>st_address</th>
        <th>city</th>
        <th>state</th>
        <th>zipcode</th>
        <th>gender</th>
        <th>martial_status</th>
        <th>username</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>

      <?php while($customer = mysqli_fetch_assoc($customers_set)) { ?>
        <tr>
          <td><?php echo h($customer['CUSTOMER_ID']); ?></td>
          <td><?php echo h($customer['FIRST_NAME']); ?></td>
          <td><?php echo h($customer['LAST_NAME']); ?></td>
          <td><?php echo h($customer['ST_ADDRESS']); ?></td>
          <td><?php echo h($customer['CITY']); ?></td>
          <td><?php echo h($customer['STATE']); ?></td>
          <td><?php echo h($customer['ZIPCODE']); ?></td>
          <td><?php echo $customer['GENDER']=='M'? 'Male' : 'Female' ; ?></td>
          <td><?php 
          if($customer['MARTIAL_STATUS'] == 'M'){
            echo 'Married';
          } elseif($customer['MARTIAL_STATUS'] == 'S'){
            echo 'Single';
          } elseif($customer['MARTIAL_STATUS'] == 'W'){
            echo 'Windower';
          }
          ; ?></td>
          <td><?php echo h($customer['USERNAME']); ?></td>         
          <td><a class="action" href="<?php echo url_for('/staff/managers/manage_policy.php?id=' . h(u($customer['CUSTOMER_ID']))); ?>">View</a></td>
          <td><a class="action" href="<?php echo url_for('/staff/managers/delete.php?id=' . h(u($customer['CUSTOMER_ID']))); ?>">Delete</a></td>
        </tr>
      <?php } ?>
    </table>

    <?php
      mysqli_free_result($customers_set);
    ?>
  </div>
  <div class="actions">
      <a class="action" href="<?php echo url_for('/staff/managers/show.php'); ?>">Check all Managers</a>
    </div>

</div>

<?php include(SHARED_PATH . '/manager_footer.php'); ?>