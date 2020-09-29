
<?php
require_once('../../../private/initialize.php');
?>


<?php
$errors = [];
$cusername = '';
$cpassword = '';

if(is_post_request()) {

  $cusername = $_POST['username'] ?? '';
  $cpassword = $_POST['password'] ?? '';

  if(is_blank($cusername)) {
    $errors[] = "Username cannot be blank";
  }
  if(is_blank($cpassword)) {
    $errors[] = "Password cannot be blank";
  }
  // if there were no errors, try to login
  if(empty($errors)) {
    $customer = find_customer_by_username($cusername);
    $login_failure_msg = "Log in was unseccessful.";
    if($customer) {
      if(password_verify($cpassword, $customer['PASSWORD'])) {
        // password matches
        log_in_customer($customer);
        redirect_to(url_for('/staff/policys/show.php'));
      } else {
        // username found, but password does not match
        $errors[] = $login_failure_msg;
      }
    } else {
      $errors[] = $login_failure_msg;
    }
  }

  
}

?>

<?php $page_title = 'Log in (Customer)'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>
<navigation>
  <ul>
    <li><a href="<?php echo url_for('/staff/policys/new.php'); ?>">Register</a></li>
  </ul>
</navigation>
<div id="content">
  <h1>Log in for customer</h1>

  <?php echo display_errors($errors); ?>

  <form action="login.php" method="post">
    Username:<br />
    <input type="text" name="username" value="<?php echo h($cusername); ?>" /><br />
    Password:<br />
    <input type="password" name="password" value="" /><br />
    <input type="submit" name="submit" value="Submit"  />
  </form>

</div>

<?php include(SHARED_PATH . '/customer_footer.php'); ?>
