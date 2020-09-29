<?php
  if(!isset($page_title)) { $page_title = 'Customer Area'; }
?>

<!doctype html>

<html lang="en">
  <head>
    <title>EI - <?php echo h($page_title); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/staff.css'); ?>" />
  </head>

  <body>
    <header>
      <h1>EI Customer Area</h1>
    </header>

    <navigation>
      <ul>
        <li>Customer: <?php echo $_SESSION['cusername'] ?? ''; ?></li>
        <li><a href="<?php echo url_for('/index.php'); ?>">Home Page</a></li>
        <li><a href="<?php echo url_for('/staff/policys/logout.php'); ?>">Logout</a></li>
      </ul>
    </navigation>

    <?php echo display_session_message(); ?>
