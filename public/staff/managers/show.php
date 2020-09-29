<?php require_once('../../../private/initialize.php'); ?>

<?php
require_manager_login();
// $id = isset($_GET['id']) ? $_GET['id'] : '1';
$id = $_GET['id'] ?? '1'; // PHP > 7.0

$managers_set = find_all_managers($id);

?>

<?php $page_title = 'Show Managers'; ?>
<?php include(SHARED_PATH . '/manager_header.php'); ?>

<div id="content">
  <div class="Managers listing">
    <h1>Managers</h1>

    <div class="actions">
      <a class="action" href="<?php echo url_for('/staff/managers/index.php'); ?>">Back to list</a>
    </div>

    <table class="list">
      <tr>
        <th>id</th>
        <th>first_name</th>
        <th>last_name</th>
        <th>email</th>
        <th>username</th>
      </tr>

      <?php while($manager = mysqli_fetch_assoc($managers_set)) { ?>
        <tr>
          <td><?php echo h($manager['id']); ?></td>
          <td><?php echo h($manager['first_name']); ?></td>
          <td><?php echo h($manager['last_name']); ?></td>
          <td><?php echo h($manager['email']); ?></td>
          <td><?php echo h($manager['username']); ?></td>
        </tr>
      <?php } ?>
    </table>

    <?php
      mysqli_free_result($managers_set);
    ?>
  </div>

</div>

<?php include(SHARED_PATH . '/manager_footer.php'); ?>