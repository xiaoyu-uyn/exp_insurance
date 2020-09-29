<?php

  // Performs all actions necessary to log in an admin
  function log_in_admin($admin) {
  // Renerating the ID protects the admin from session fixation.
    session_regenerate_id();
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['last_login'] = time();
    $_SESSION['username'] = $admin['username'];

    return true;
  }
  function log_in_customer($customer) {
    session_regenerate_id();
    $_SESSION['customer_id'] = $customer['CUSTOMER_ID'];
    $_SESSION['clast_login'] = time();
    $_SESSION['cusername'] = $customer['USERNAME'];
  }

  function log_in_manager($manager) {
    session_regenerate_id();
    $_SESSION['manager_id'] = $manager['id'];
    $_SESSION['clast_login'] = time();
    $_SESSION['musername'] = $manager['username'];
  }

  function log_out_admin() {
  // Renerating the ID protects the admin from session fixation.

    unset($_SESSION['admin_id']);
    unset($_SESSION['last_login']);
    unset($_SESSION['username']);
    // session_destroy(); // optional: destroys the whole session

    return true;
  }

  function log_out_customer() {
  // Renerating the ID protects the admin from session fixation.

    unset($_SESSION['customer_id']);
    unset($_SESSION['clast_login']);
    unset($_SESSION['cusername']);
    // session_destroy(); // optional: destroys the whole session

    return true;
  }

  function log_out_manager() {
  // Renerating the ID protects the admin from session fixation.

    unset($_SESSION['manager_id']);
    unset($_SESSION['clast_login']);
    unset($_SESSION['musername']);
    // session_destroy(); // optional: destroys the whole session

    return true;
  }

  // is_logged_in() contains all the logic for determining if a
  // request should be considered a "logged in" request or not.
  // It is the core of require_login() but it can also be called
  // on its own in other contexts (e.g. display one link if an admin
  // is logged in and display another link if they are not)
  function is_logged_in() {
    // Having a admin_id in the session serves a dual-purpose:
    // - Its presence indicates the admin is logged in.
    // - Its value tells which admin for looking up their record.
    return isset($_SESSION['admin_id']);
  }

  // Call require_login() at the top of any page which needs to
  // require a valid login before granting acccess to the page.
  function require_login() {
    if(!is_logged_in()) {
      redirect_to(url_for('/staff/login.php'));
    } else {
      // Do nothing, let the rest of the page proceed
    }
  }

  function require_manager_login() {
    if(!is_manager_logged_in()) {
      redirect_to(url_for('/staff/managers/login.php'));
    }
  }

  function is_manager_logged_in() {
    return isset($_SESSION['manager_id']);
  }

  function require_customer_login() {
    if(!is_customer_logged_in()) {
      redirect_to(url_for('/staff/policys/login.php'));
    }
  }
  function is_customer_logged_in() {
    return isset($_SESSION['customer_id']);
  }

?>
