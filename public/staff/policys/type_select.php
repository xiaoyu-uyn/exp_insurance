<?php

require_once('../../../private/initialize.php');
require_customer_login();
if(is_post_request()) {
  if($_POST['type'] === 'Home Insurance') {
    redirect_to(url_for('/staff/policys/new_h_policy.php'));
  } elseif($_POST['type'] === 'Auto Insurance') {
    redirect_to(url_for('/staff/policys/new_a_policy.php'));
  }

}

?>

<?php $page_title = 'Type selection'; ?>
<?php include(SHARED_PATH . '/customer_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/policys/show.php'); ?>">&laquo; Back to Storage</a>

  <div class="customer new">
    <h1>Select a policy type</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/policys/type_select.php'); ?>" method="post">
      <dl>
        <dt>Type</dt>
        <dd>
          <select name="type">
            <?php
              for($i=1; $i < 3; $i++) {
                if($i == 1) {
                  echo "<option value=\"Home Insurance\" selected>Home Insurance</option>";
                } else {
                  echo "<option value=\"Auto Insurance\">Auto Insurance</option>";
                }
      
              }
            ?>
          </select>
        </dd>
      </dl>
      <div id="operations">
        <input type="submit" value="select" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/customer_footer.php'); ?>