<?php

require_once('../../../private/initialize.php');
if(is_post_request()) {

  $customer = [];
  $customer['first_name'] = $_POST['first_name'] ?? '';
  $customer['last_name'] = $_POST['last_name'] ?? '';
  $customer['username'] = $_POST['username'] ?? '';
  $customer['password'] = $_POST['password'] ?? '';
  $customer['check_password'] = $_POST['check_password'] ?? '';
  $customer['st_address'] = $_POST['st_address'] ?? '';
  $customer['city'] = $_POST['city'] ?? '';
  $customer['state'] = $_POST['state'] ?? '';
  $customer['zipcode'] = $_POST['zipcode'] ?? '';
  if(isset($_POST['gender'])) {
    $customer['gender'] = $_POST['gender'];
  }
  $customer['martial_status'] = $_POST['martial_status'] ?? '';
  $result = insert_customer($customer);

  if($result === true) {
    commit();
    $_SESSION['message'] = 'The customer was created successfully.';
    redirect_to(url_for('/staff/policys/login.php'));
  } else {
    $errors = $result;
  }

} else {
  $customer = [];
}

?>

<?php $page_title = 'Create customer'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/policys/login.php'); ?>">&laquo; Back to List</a>

  <div class="customer new">
    <h1>Create customer</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/new.php'); ?>" method="post">
      <dl>
        <dt>first_name</dt>
        <dd><input type="text" name="first_name" value=''> ></dd>
        <dt>2-30</dt>
      </dl>
      <dl>
        <dt>last_name</dt>
        <dd><input type="text" name="last_name" value=''></dd>
        <dt>2-30</dt>
      </dl>
      <dl>
        <dt>st_address</dt>
        <dd><input type="text" name="st_address" value='' ></dd>
        <dt>2-50</dt>
      </dl>
      <dl>
        <dt>city</dt>
        <dd><input type="text" name="city" value=''></dd>
        <dt>2-50</dt>
      </dl>
      <dl>
        <dt>state</dt>
        <dd><input type="text" name="state" value=''></dd>
        <dt>2-20</dt>
      </dl>
      <dl>
        <dt>zipcode</dt>
        <dd><input type="text" name="zipcode" value=''></dd>
        <dt>5</dt>
      </dl>
      <dl>
        <dt>gender</dt>
        <dd><input type="text" name="gender" value=''></dd>
        <dt>M/F</dt>
      </dl>
      <dl>
        <dt>martial_status</dt>
        <dd><input type="text" name="martial_status" value=''></dd>
        <dt>M/S/W</dt>
      </dl>
      <dl>
        <dt>username</dt>
        <dd><input type="text" name="username" value=''></dd>
        <dt>8-50</dt>
      </dl>
      <dl>
        <dt>password</dt>
        <dd><input type="text" name="password" value=''></dd>
        <dt>8-50</dt>
      </dl>
      <dl>
        <dt>password confirm</dt>
        <dd><input type="text" name="check_password" value=''></dd>
        <dt>8-50</dt>
      </dl>
      <div id="operations">
        <input type="submit" value="Create customer" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/customer_footer.php'); ?>
