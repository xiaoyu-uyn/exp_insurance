<!doctype html>

<html lang="en">
  <head>
    <title>WDS Insurance <?php if(isset($page_title)) { echo '- ' . h($page_title); } ?><?php if(isset($preview) && $preview) { echo ' [PREVIEW]'; } ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/public.css'); ?>" />
  <style>
  img {
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
  </style>
  </head>

  <body>

    <header>
      <h1>
        <a href="<?php echo url_for('/index.php'); ?>">
          <img src="<?php echo url_for('/images/logo.jpeg'); ?>" width="1000" height="110" alt="" />
        </a>
      </h1>
    </header>