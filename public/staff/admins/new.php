<?php

require_once('../../../private/initialize.php');
require_login();
$admin = [];

if(is_post_request()) {

  $admin = [];
  $admin['first_name'] = $_POST['first_name'] ?? '';
  $admin['last_name'] = $_POST['last_name'] ?? '';
  $admin['email'] = $_POST['email'] ?? '';
  $admin['username'] = $_POST['username'] ?? '';
  $admin['password'] = $_POST['password'] ?? '';
  $admin['check_password'] = $_POST['check_password'] ?? '';

  $result = insert_admin($admin);

  if($result === true) {
    $new_id = mysqli_insert_id($db);
    commit();
    $_SESSION['message'] = 'The admin was created successfully.';
    redirect_to(url_for('/staff/admins/show.php?id=' . $new_id));
  } else {
    $errors = $result;
  }

} else {
  $admin = [];
  $admin['first_name'] = $_POST['first_name'] ?? '';
  $admin['last_name'] = $_POST['last_name'] ?? '';
  $admin['email'] = $_POST['email'] ?? '';
  $admin['username'] = $_POST['username'] ?? '';
  $admin['password'] = $_POST['password'] ?? '';
  $admin['check_password'] = $_POST['check_password'] ?? '';
}

?>

<?php $page_title = 'Create Admin'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/admins/index.php'); ?>">&laquo; Back to List</a>

  <div class="admin new">
    <h1>Create Admin</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/admins/new.php'); ?>" method="post">
      <dl>
        <dt>first_name</dt>
        <dd><input type="text" name="first_name" value=<?php echo $admin['first_name']; ?> ></dd>
        <dt>2-255</dt>
      </dl>
      <dl>
        <dt>last_name</dt>
        <dd><input type="text" name="last_name" value=<?php echo $admin['last_name']; ?> ></dd>
        <dt>2-255</dt>
      </dl>
      <dl>
        <dt>email</dt>
        <dd><input type="text" name="email" value=<?php echo $admin['email'] ?? "valid email"; ?> ></dd>
        <dt>valid email</dt>
      </dl>
      <dl>
        <dt>username</dt>
        <dd><input type="text" name="username" value=<?php echo $admin['username']; ?> ></dd>
        <dt>8-255</dt>
      </dl>
      <dl>
        <dt>password</dt>
        <dd><input type="text" name="password" value=<?php echo $admin['password']; ?> ></dd>
        <dt>Aa1* length>8</dt>
      </dl>
      <dl>
        <dt>password for check</dt>
        <dd><input type="text" name="check_password" value=<?php echo $admin['check_password']; ?>></dd>
        <dt>type again</dt>
      </dl>
      <div id="operations">
        <input type="submit" value="Create Admin" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
