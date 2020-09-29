<?php
require_once('../../../private/initialize.php');

$errors = [];
$username = '';
$password = '';

if(is_post_request()) {

  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  if(is_blank($username)) {
    $errors[] = "Username cannot be blank";
  }
  if(is_blank($password)) {
    $errors[] = "Password cannot be blank";
  }
  // if there were no errors, try to login
  if(empty($errors)) {
    $manager = find_manager_by_username($username);
    $login_failure_msg = "Log in was unseccessful.";
    if($manager) {
      if(password_verify($password, $manager['hashed_password'])) {
        // password matches
        log_in_manager($manager);
        redirect_to(url_for('/staff/managers/index.php'));
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

<?php $page_title = 'Log in'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">
  <h1>Log in</h1>

  <?php echo display_errors($errors); ?>

  <form action="login.php" method="post">
    Username:<br />
    <input type="text" name="username" value="<?php echo h($username); ?>" /><br />
    Password:<br />
    <input type="password" name="password" value="" /><br />
    <input type="submit" name="submit" value="Submit"  />
  </form>

</div>

<?php include(SHARED_PATH . '/manager_footer.php'); ?>