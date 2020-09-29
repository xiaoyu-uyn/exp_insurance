<?php require_once('../../../private/initialize.php'); ?>

<?php
require_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id'] ?? '1'; // PHP > 7.0

$admin = find_admin_by_id($id);

?>

<?php $page_title = 'Show Admin'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/admins/index.php'); ?>">&laquo; Back to List</a>

  <div class="admin show">

    <h1>Admin: <?php echo h($admin['username']); ?></h1>

    <div class="attributes">
      <dl>
        <dt>id</dt>
        <dd><?php echo h($admin['id']); ?></dd>
      </dl>
      <dl>
        <dt>first_name</dt>
        <dd><?php echo h($admin['first_name']); ?></dd>
      </dl>
      <dl>
        <dt>last_name</dt>
        <dd><?php echo h($admin['last_name']); ?></dd>
      </dl>
      <dl>
        <dt>email</dt>
        <dd><?php echo h($admin['email']); ?></dd>
      </dl>
      <dl>
        <dt>username</dt>
        <dd><?php echo h($admin['username']); ?></dd>
      </dl>
      <dl>
        <dt>password</dt>
        <dd><?php echo h($admin['hashed_password']); ?></dd>
      </dl>
    </div>

  </div>

</div>
