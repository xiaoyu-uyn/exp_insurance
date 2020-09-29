<?php

require_once('../../../private/initialize.php');
require_manager_login();
$manager = [];

if(is_post_request()) {

  $manager = [];
  $manager['first_name'] = $_POST['first_name'] ?? '';
  $manager['last_name'] = $_POST['last_name'] ?? '';
  $manager['email'] = $_POST['email'] ?? '';
  $manager['username'] = $_POST['username'] ?? '';
  $manager['password'] = $_POST['password'] ?? '';
  $manager['check_password'] = $_POST['check_password'] ?? '';

  $result = insert_manager($manager);
  if($result === true) {
    $new_id = mysqli_insert_id($db);
    $_SESSION['message'] = 'The manager was created successfully.';
    redirect_to(url_for('/staff/managers/index.php'));
  } else {
    $errors = $result;
  }

} else {
  $manager = [];
  $manager['first_name'] = $_POST['first_name'] ?? '';
  $manager['last_name'] = $_POST['last_name'] ?? '';
  $manager['email'] = $_POST['email'] ?? '';
  $manager['username'] = $_POST['username'] ?? '';
  $manager['password'] = $_POST['password'] ?? '';
  $manager['check_password'] = $_POST['check_password'] ?? '';
}

?>

<?php $page_title = 'Create Manager'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/managers/index.php'); ?>">&laquo; Back to List</a>

  <div class="manager new">
    <h1>Create Admin</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/managers/new.php'); ?>" method="post">
      <dl>
        <dt>first_name</dt>
        <dd><input type="text" name="first_name" value="" ></dd>
        <dt>2-255</dt>
      </dl>
      <dl>
        <dt>last_name</dt>
        <dd><input type="text" name="last_name" value="" ></dd>
        <dt>2-255</dt>
      </dl>
      <dl>
        <dt>email</dt>
        <dd><input type="text" name="email" value="" ></dd>
        <dt>valid email</dt>
      </dl>
      <dl>
        <dt>username</dt>
        <dd><input type="text" name="username" value="" ></dd>
        <dt>8-255</dt>
      </dl>
      <dl>
        <dt>password</dt>
        <dd><input type="text" name="password" value="" ></dd>
        <dt>Aa1* length>8</dt>
      </dl>
      <dl>
        <dt>password for check</dt>
        <dd><input type="text" name="check_password" value=""></dd>
        <dt>type again</dt>
      </dl>
      <div id="operations">
        <input type="submit" value="Create Manager" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/manager_footer.php'); ?>
