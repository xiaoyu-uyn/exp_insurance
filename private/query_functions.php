<?php

  // Subjects

  function find_all_subjects($options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;

    $sql = "SELECT * FROM subjects ";
    if($visible) {
      $sql .= "WHERE visible = true ";
    }
    $sql .= "ORDER BY position ASC";
    //echo $sql;
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }

  function find_subject_by_id($id, $options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;

    $sql = "SELECT * FROM subjects ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
    if($visible) {
      $sql .= "AND visible = true";
    }
    // echo $sql;
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $subject = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $subject; // returns an assoc. array
  }

  function validate_subject($subject) {
    $errors = [];

    // menu_name
    if(is_blank($subject['menu_name'])) {
      $errors[] = "Name cannot be blank.";
    } elseif(!has_length($subject['menu_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "Name must be between 2 and 255 characters.";
    }

    // position
    // Make sure we are working with an integer
    $postion_int = (int) $subject['position'];
    if($postion_int <= 0) {
      $errors[] = "Position must be greater than zero.";
    }
    if($postion_int > 999) {
      $errors[] = "Position must be less than 999.";
    }

    // visible
    // Make sure we are working with a string
    $visible_str = (string) $subject['visible'];
    if(!has_inclusion_of($visible_str, ["0","1"])) {
      $errors[] = "Visible must be true or false.";
    }

    return $errors;
  }

  function insert_subject($subject) {
    global $db;

    $errors = validate_subject($subject);
    if(!empty($errors)) {
      return $errors;
    }

    $sql = "INSERT INTO subjects ";
    $sql .= "(menu_name, position, visible) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $subject['menu_name']) . "',";
    $sql .= "'" . db_escape($db, $subject['position']) . "',";
    $sql .= "'" . db_escape($db, $subject['visible']) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function update_subject($subject) {
    global $db;

    $errors = validate_subject($subject);
    if(!empty($errors)) {
      return $errors;
    }

    $sql = "UPDATE subjects SET ";
    $sql .= "menu_name='" . db_escape($db, $subject['menu_name']) . "', ";
    $sql .= "position='" . db_escape($db, $subject['position']) . "', ";
    $sql .= "visible='" . db_escape($db, $subject['visible']) . "' ";
    $sql .= "WHERE id='" . db_escape($db, $subject['id']) . "' ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }

  }

  function delete_subject($id) {
    global $db;

    $sql = "DELETE FROM subjects ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
    $sql .= "LIMIT 1";
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  // Pages

  function find_all_pages() {
    global $db;

    $sql = "SELECT * FROM pages ";
    $sql .= "ORDER BY subject_id ASC, position ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }

  function find_page_by_id($id, $options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;

    $sql = "SELECT * FROM pages ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
    if($visible) {
      $sql .= "AND visible = true";
    }
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $page = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $page; // returns an assoc. array
  }

  function validate_page($page) {
    $errors = [];

    // subject_id
    if(is_blank($page['subject_id'])) {
      $errors[] = "Subject cannot be blank.";
    }

    // menu_name
    if(is_blank($page['menu_name'])) {
      $errors[] = "Name cannot be blank.";
    } elseif(!has_length($page['menu_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "Name must be between 2 and 255 characters.";
    }
    $current_id = $page['id'] ?? '0';
    if(!has_unique_page_menu_name($page['menu_name'], $current_id)) {
      $errors[] = "Menu name must be unique.";
    }


    // position
    // Make sure we are working with an integer
    $postion_int = (int) $page['position'];
    if($postion_int <= 0) {
      $errors[] = "Position must be greater than zero.";
    }
    if($postion_int > 999) {
      $errors[] = "Position must be less than 999.";
    }

    // visible
    // Make sure we are working with a string
    $visible_str = (string) $page['visible'];
    if(!has_inclusion_of($visible_str, ["0","1"])) {
      $errors[] = "Visible must be true or false.";
    }

    // content
    if(is_blank($page['content'])) {
      $errors[] = "Content cannot be blank.";
    }

    return $errors;
  }

  function insert_page($page) {
    global $db;

    $errors = validate_page($page);
    if(!empty($errors)) {
      return $errors;
    }

    $sql = "INSERT INTO pages ";
    $sql .= "(subject_id, menu_name, position, visible, content) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $page['subject_id']) . "',";
    $sql .= "'" . db_escape($db, $page['menu_name']) . "',";
    $sql .= "'" . db_escape($db, $page['position']) . "',";
    $sql .= "'" . db_escape($db, $page['visible']) . "',";
    $sql .= "'" . db_escape($db, $page['content']) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function update_page($page) {
    global $db;

    $errors = validate_page($page);
    if(!empty($errors)) {
      return $errors;
    }

    $sql = "UPDATE pages SET ";
    $sql .= "subject_id='" . db_escape($db, $page['subject_id']) . "', ";
    $sql .= "menu_name='" . db_escape($db, $page['menu_name']) . "', ";
    $sql .= "position='" . db_escape($db, $page['position']) . "', ";
    $sql .= "visible='" . db_escape($db, $page['visible']) . "', ";
    $sql .= "content='" . db_escape($db, $page['content']) . "' ";
    $sql .= "WHERE id='" . db_escape($db, $page['id']) . "' ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }

  }

  function delete_page($id) {
    global $db;

    $sql = "DELETE FROM pages ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
    $sql .= "LIMIT 1";
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function find_pages_by_subject_id($subject_id, $options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;

    $sql = "SELECT * FROM pages ";
    $sql .= "WHERE subject_id='" . db_escape($db, $subject_id) . "' ";
    if($visible) {
      $sql .= "AND visible = true ";
    }
    $sql .= "ORDER BY position ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function count_pages_by_subject_id($subject_id, $options=[]) {
    global $db;

    $visible = $options['visible'] ?? false;

    $sql = "SELECT COUNT(id) AS count FROM pages ";
    $sql .= "WHERE subject_id='" . db_escape($db, $subject_id) . "' ";
    if($visible) {
      $sql .= "AND visible = true";
    }
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    // $row = mysqli_fetch_row($result); // return a single array
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    $count = $row['count'];
    return $count;
  }


// admins
    function find_all_admins() {
      global $db;
      $sql = "SELECT * FROM admins ";
      $sql .= "ORDER BY id ASC";
      $result = mysqli_query($db, $sql);
      confirm_result_set($result);
      return $result;
    }

    function find_admin_by_id($admin_id) {
      global $db;
      $sql = "SELECT * FROM admins ";
      $sql .= "WHERE id='" . db_escape($db, $admin_id) . "'";
      $result = mysqli_query($db, $sql);
      confirm_result_set($result);
      $admin = mysqli_fetch_assoc($result);
      mysqli_free_result($result);
      return $admin;
    }

    function find_admin_by_username($admin_username) {
      global $db;
      $sql = "SELECT * FROM admins ";
      $sql .= "WHERE username='" . db_escape($db, $admin_username) . "'";
      $result = mysqli_query($db, $sql);
      confirm_result_set($result);
      $admin = mysqli_fetch_assoc($result);
      mysqli_free_result($result);
      return $admin;
    }

    function insert_admin($admin) {
      global $db;

      $errors = validate_admin($admin);
      if(!empty($errors)) {
        return $errors;
      }
      $hased_password = password_hash($admin['password'], PASSWORD_BCRYPT);

      $sql = "INSERT INTO admins ";
      $sql .= "(first_name, last_name, email, username, hashed_password) ";
      $sql .= "VALUES (";
      $sql .= "'" . db_escape($db, $admin['first_name']) . "',";
      $sql .= "'" . db_escape($db, $admin['last_name']) . "',";
      $sql .= "'" . db_escape($db, $admin['email']) . "',";
      $sql .= "'" . db_escape($db, $admin['username']) . "',";
      $sql .= "'" . db_escape($db, $hased_password) . "'";
      $sql .= ")";
      $result = mysqli_query($db, $sql);
      // For INSERT statements, $result is true/false
      if($result) {
        return true;
      } else {
        // INSERT failed
        echo mysqli_error($db);
        db_disconnect($db);
        exit;
      }
    }
    function validate_manager($manager) {
      $errors = [];

      $password_required = $manager['password_required'] ?? true;
      // first_name
      if(!has_length($manager['first_name'], ['min' => 2, 'max' => 255])) {
        $errors[] = "first_name must be between 2 and 255 characters.";
      }

      // last_name
      if(!has_length($manager['last_name'], ['min' => 2, 'max' => 255])) {
        $errors[] = "last_name must be between 2 and 255 characters.";
      }

      // email
      if(!has_valid_email_format($manager['email'])) {
        $errors[] = "Not a valid email.";
      }

      //username
      if(!has_length($manager['username'], ['min' => 8, 'max' => 255])) {
        $errors[] = "username must be between 8 and 255 characters.";
      }
      $current_id = $manager['id'] ?? '0';
      if(!has_unique_manager_username($manager['username'], $current_id)) {
        $errors[] = "Username must be unique";
      }
      if($password_required) {
        // password
        if (strlen($manager['password']) < '8') {
          $errors[] = "Your Password Must Contain At Least 8 Characters!";
        }
        if(!preg_match("#[0-9]+#",$manager['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Number!";
        }
        if(!preg_match("#[A-Z]+#",$manager['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Capital Letter!";
        }
        if(!preg_match("#[a-z]+#",$manager['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Lowercase Letter!";
        }
        if(!preg_match('/[^A-Za-z0-9\s]/',$manager['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Special Character !";
        }

        // check_password
        if (!$manager['password']===$manager['check_password']) {
          $errors[] = "Two times password inputs not same!";
        }
      }
      

      return $errors;
    }

    function validate_admin($admin, $options=[]) {
      $errors = [];

      $password_required = $options['password_required'] ?? true;
      // first_name
      if(!has_length($admin['first_name'], ['min' => 2, 'max' => 255])) {
        $errors[] = "first_name must be between 2 and 255 characters.";
      }

      // last_name
      if(!has_length($admin['last_name'], ['min' => 2, 'max' => 255])) {
        $errors[] = "last_name must be between 2 and 255 characters.";
      }

      // email
      if(!has_valid_email_format($admin['email'])) {
        $errors[] = "Not a valid email.";
      }

      //username
      if(!has_length($admin['username'], ['min' => 8, 'max' => 255])) {
        $errors[] = "username must be between 8 and 255 characters.";
      }
      $current_id = $admin['id'] ?? '0';
      if(!has_unique_admin_username($admin['username'], $current_id)) {
        $errors[] = "Username must be unique";
      }
      if($password_required) {
        // password
        if (strlen($admin['password']) < '8') {
          $errors[] = "Your Password Must Contain At Least 8 Characters!";
        }
        if(!preg_match("#[0-9]+#",$admin['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Number!";
        }
        if(!preg_match("#[A-Z]+#",$admin['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Capital Letter!";
        }
        if(!preg_match("#[a-z]+#",$admin['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Lowercase Letter!";
        }
        if(!preg_match('/[^A-Za-z0-9\s]/',$admin['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Special Character !";
        }

        // check_password
        if (!$admin['password']===$admin['check_password']) {
          $errors[] = "Two times password inputs not same!";
        }
      }
      

      return $errors;
    }
    function update_admin($admin) {
      global $db;

      $password_sent = !is_blank($admin['password']);

      $errors = validate_admin($admin, ['password_required' => $password_sent]);
      if(!empty($errors)) {
        return $errors;
      }
      $hased_password = password_hash($admin['password'], PASSWORD_BCRYPT);

      $sql = "UPDATE admins SET ";
      $sql .= "first_name='" . db_escape($db, $admin['first_name']) . "', ";
      $sql .= "last_name='" . db_escape($db, $admin['last_name']) . "', ";
      $sql .= "email='" . db_escape($db, $admin['email']) . "', ";
      if($password_sent) {
        $sql .= "hashed_password='" . db_escape($db, $hased_password) . "', ";
      }
      $sql .= "username='" . db_escape($db, $admin['username']) . "' ";
      $sql .= "WHERE id='" . db_escape($db, $admin['id']) . "' ";
      $sql .= "LIMIT 1";

      $result = mysqli_query($db, $sql);
      // For UPDATE statements, $result is true/false
      if($result) {
        return true;
      } else {
        // UPDATE failed
        echo mysqli_error($db);
        db_disconnect($db);
        exit;
      }
    }

    function delete_admin($id) {
      global $db;

      $sql = "DELETE FROM admins ";
      $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
      $sql .= "LIMIT 1";
      $result = mysqli_query($db, $sql);

      // For DELETE statements, $result is true/false
      if($result) {
        return true;
      } else {
        // DELETE failed
        echo mysqli_error($db);
        db_disconnect($db);
        exit;
      }
    }

    function find_customer_by_username($username) {
      global $db;
      $sql = "SELECT * FROM customers ";
      $sql .= "WHERE username='" . db_escape($db, $username) . "'";
      $result = mysqli_query($db, $sql);
      confirm_result_set($result);
      $customer = mysqli_fetch_assoc($result);
      mysqli_free_result($result);
      return $customer;
    }

    // customer
    function insert_customer($customer) {
      global $db;

      $errors = validate_customer($customer);
      if(!empty($errors)) {
        return $errors;
      }
      $hased_password = password_hash($customer['password'], PASSWORD_BCRYPT);
      if(!isset($customer['gender'])) {
        $sql = "INSERT INTO customers ";
        $sql .= "(first_name, last_name, username, password, st_address, city, state, zipcode, martial_status) ";
        $sql .= "VALUES (";
        $sql .= "'" . db_escape($db, $customer['first_name']) . "',";
        $sql .= "'" . db_escape($db, $customer['last_name']) . "',";
        $sql .= "'" . db_escape($db, $customer['username']) . "',";
        $sql .= "'" . db_escape($db, $hased_password) . "',";
        $sql .= "'" . db_escape($db, $customer['st_address']) . "',";
        $sql .= "'" . db_escape($db, $customer['city']) . "',";
        $sql .= "'" . db_escape($db, $customer['state']) . "',";
        $sql .= "'" . db_escape($db, $customer['zipcode']) . "',";
        $sql .= "'" . db_escape($db, $customer['martial_status']) . "'";
        $sql .= ")";
      } else {
        $sql = "INSERT INTO customers ";
        $sql .= "(first_name, last_name, username, password, st_address, city, state, zipcode, gender, martial_status) ";
        $sql .= "VALUES (";
        $sql .= "'" . db_escape($db, $customer['first_name']) . "',";
        $sql .= "'" . db_escape($db, $customer['last_name']) . "',";
        $sql .= "'" . db_escape($db, $customer['username']) . "',";
        $sql .= "'" . db_escape($db, $hased_password) . "',";
        $sql .= "'" . db_escape($db, $customer['st_address']) . "',";
        $sql .= "'" . db_escape($db, $customer['city']) . "',";
        $sql .= "'" . db_escape($db, $customer['state']) . "',";
        $sql .= "'" . db_escape($db, $customer['zipcode']) . "',";
        $sql .= "'" . db_escape($db, $customer['gender']) . "',";
        $sql .= "'" . db_escape($db, $customer['martial_status']) . "'";
        $sql .= ")";
      }
      
      $result = mysqli_query($db, $sql);
      // For INSERT statements, $result is true/false
      if($result) {
        return true;
      } else {
        // INSERT failed
        echo mysqli_error($db);
        db_disconnect($db);
        exit;
      }
    }

    function validate_customer($customer, $options = []) {
      $errors = [];

      $password_required = $options['password_required'] ?? true;
      // first_name
      if(!has_length($customer['first_name'], ['min' => 2, 'max' => 30])) {
        $errors[] = "first_name must be between 2 and 255 characters.";
      }

      // last_name
      if(!has_length($customer['last_name'], ['min' => 2, 'max' => 30])) {
        $errors[] = "last_name must be between 2 and 255 characters.";
      }

      // st_address
      if(!has_length($customer['st_address'], ['min' => 2, 'max' => 50])) {
        $errors[] = "st_address must be between 2 and 50 characters.";
      }

      // city
      if(!has_length($customer['city'], ['min' => 2, 'max' => 20])) {
        $errors[] = "city must be between 2 and 20 characters.";
      }

      // state
      if(!has_length($customer['state'], ['min' => 2, 'max' => 20])) {
        $errors[] = "state must be between 2 and 20 characters.";
      }

      // zipcode
      if(!has_length($customer['zipcode'], ['min' => 5, 'max' => 5])) {
        $errors[] = "zipcode must be 5 characters.";
      }


      //username
      if(!has_length($customer['username'], ['min' => 8, 'max' => 50])) {
        $errors[] = "username must be between 8 and 50 characters.";
      }
      $current_id = $customer['id'] ?? '0';
      if(!has_unique_customer_username($customer['username'], $current_id)) {
        $errors[] = "Username must be unique";
      }
      if($password_required) {
        // password
        if (strlen($customer['password']) < '8') {
          $errors[] = "Your Password Must Contain At Least 8 Characters!";
        }
        if (strlen($customer['password']) > '50') {
          $errors[] = "Your Password Must Contain At most 50 Characters!";
        }
        if(!preg_match("#[0-9]+#",$customer['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Number!";
        }
        if(!preg_match("#[A-Z]+#",$customer['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Capital Letter!";
        }
        if(!preg_match("#[a-z]+#",$customer['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Lowercase Letter!";
        }
        if(!preg_match('/[^A-Za-z0-9\s]/',$customer['password'])) {
          $errors[] = "Your Password Must Contain At Least 1 Special Character !";
        } 

        // check_password
        if (!$customer['password']===$customer['check_password']) {
          $errors[] = "Two times password inputs not same!";
        }
        // gender
        if (isset($customer['gender']) && $customer['gender'] !== 'F' && $customer['gender'] !== 'M') {
          $errors[] = "Gender must be F, M or blank!";
        }
        // martial_status
        if ($customer['martial_status'] !== 'S' && $customer['martial_status'] !== 'M' && $customer['martial_status'] !== 'W') {
          $errors[] = "martial_status must be S, M or W!";
        }
    }
    return $errors;
  }

  function find_policys_by_customer_id($id) {
    global $db;

    $sql = "SELECT * FROM policy ";
    $sql .= "WHERE customer_id='" . db_escape($db, $id) . "' ";
    $sql .= "ORDER BY policy_id ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }

  function validate_policy($policy) {
    $errors = [];
    if($policy['prem_amount'] > 100000000) {
      $errors[] = "Your prem_amount cannot be larger than $100000000!";
    }
    return $errors;
  }
  function validate_home($home) {
    $errors = [];
    if($home['home_area'] > 100000000) {
      $errors[] = "Your home_area cannot be larger than 100000000!";
    }
    if($home['home_area'] <= 0) {
      $errors[] = "Your home_area cannot be negative!";
    }
    return $errors;
  }

  function auto_increment_policy_id() {
    global $db;
    // $sql = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES ";
    // $sql .= "WHERE TABLE_SCHEMA = ". "'" . "globe_bank" . "' ";
    // $sql .= "AND TABLE_NAME =" . "'" . "policy" . "'";

    $sql = "SELECT MAX(policy_id) FROM policy ";
    $result = mysqli_query($db, $sql);
    $id = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $id[0];
  }

  function auto_increment_home_id() {
    global $db;
    // $sql = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES ";
    // $sql .= "WHERE TABLE_SCHEMA = ". "'" . "globe_bank" . "' ";
    // $sql .= "AND TABLE_NAME =" . "'" . "home" . "'";
    $sql = "SELECT MAX(home_id) FROM home ";
    $result = mysqli_query($db, $sql);
    $id = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $id[0];
  }
  function auto_increment_invoice_id() {
    global $db;
    $sql = "SELECT MAX(invoice_id) FROM invoices ";
    $result = mysqli_query($db, $sql);
    $id = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $id[0];
  }

  function insert_h_policy($h_policy) {
    global $db;
    $sql = "INSERT INTO h_policy ";
    $sql .= "(policy_id, h_policy_type) ";
    $sql .= "VALUES (";
    $sql .= " " . $h_policy['policy_id'] . ",";
    $sql .= "'" . db_escape($db, $h_policy['h_policy_type']) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function insert_home_h_policy($home_h_policy) {
    global $db;
    $sql = "INSERT INTO home_h_policy ";
    $sql .= "(home_id, policy_id) ";
    $sql .= "VALUES (";
    $sql .= " " . $home_h_policy['home_id'] . ",";
    $sql .= " " . $home_h_policy['policy_id'] . "";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function insert_home($home) {
    global $db;
    $sql = "INSERT INTO home ";
    $sql .= "(purchase_date, purchase_value, home_area, home_type, autofirenotification, homesecuritysystem, swimmingpool, basement) ";
    $sql .= "VALUES (";
    $sql .= "" . toSQLDate(db_escape($db, $home['purchase_date'])) . ",";
    $sql .= " " . $home['purchase_value'] . ",";
    $sql .= " " . $home['home_area'] . ",";
    $sql .= "'" . db_escape($db, $home['home_type']) . "',";
    $sql .= " " . $home['AutoFireNotification'] . ",";
    $sql .= " " . $home['HomeSecuritySystem'] . ",";
    $sql .= "'" . db_escape($db, $home['SwimmingPool']) . "',";
    $sql .= " " . $home['Basement'] . "";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function insert_policy($policy) {
    global $db;
    $sql = "INSERT INTO policy ";
    $sql .= "(type, start_date, end_date, status, prem_amount, customer_id) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $policy['type']) . "',";
    $sql .= "" . toSQLDate(db_escape($db, $policy['start_date'])) . ",";
    $sql .= "" . toSQLDate(db_escape($db, $policy['end_date'])) . ",";
    $sql .= "'" . db_escape($db, $policy['status']) . "',";
    $sql .= " " . $policy['prem_amount'] . ",";
    $sql .= " " . $policy['customer_id'] . "";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function find_prem_amount_by_policy_id($id) {
    global $db;

    $sql = "SELECT prem_amount FROM policy WHERE policy_id=" . $id;
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $res[0];
  }
  function update_prem_amount_by_policy_id($id, $amount) {
     global $db;
     $amount += find_prem_amount_by_policy_id($id);
      $sql = "SELECT * FROM policy WHERE policy_id=" . $id . " FOR UPDATE;";
      $sql = "UPDATE policy SET ";
      $sql .= "prem_amount='" . $amount . "' ";
      $sql .= "WHERE policy_id=" . $id . " ";
      $sql .= "LIMIT 1";

      $result = mysqli_query($db, $sql);
      // For UPDATE statements, $result is true/false
      if($result) {
        return true;
      } else {
        // UPDATE failed
        echo mysqli_error($db);
        db_disconnect($db);
        exit;
      }
  }
  function find_home_ids_by_policy_id($policy_id) {
    global $db;

    $sql = "SELECT home_id FROM home_h_policy ";
    $sql .= "WHERE policy_id=" . $policy_id . " ";
    $sql .= "ORDER BY home_id ASC";
    $result = mysqli_query($db, $sql);
    return $result;
  }
  function find_home_by_home_id($home_id) {
    global $db;

    $sql = "SELECT * FROM home ";
    $sql .= "WHERE home_id=" . $home_id . " ";
    $sql .= "ORDER BY home_id ASC";
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $res;
  }

  function calculate_uninvoiced_amount($policy_id) {
    global $db;

    $sql = "SELECT prem_amount FROM policy ";
    $sql .= "WHERE policy_id=" . $policy_id . "";
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_row($result);
    mysqli_free_result($result);
    $total_amount = $res[0];
    global $db;

    $sql = "SELECT SUM(i_amount) FROM invoices ";
    $sql .= "WHERE policy_id=" . $policy_id . "";
    $result2 = mysqli_query($db, $sql);
    $res2 = mysqli_fetch_row($result2);
    $m = $res2[0];
    
    mysqli_free_result($result2);
    return $total_amount - $m;
  }
  function validate_invoice($invoice, $uninvoiced_amount) {
    $errors = [];
    if($invoice['i_amount'] > $uninvoiced_amount) {
      $errors[] = "Input amount is larger than total amount!";
    }
    if($invoice['i_amount'] < 0) {
      $errors[] = "Input amount cannot be negative!";
    }
    return $errors;
  }
  function insert_invoice($invoice) {
    global $db;
    $sql = "INSERT INTO invoices ";
    $sql .= "(i_date, due_date, i_amount, policy_id) ";
    $sql .= "VALUES (";
    $sql .= "" . toSQLDate(db_escape($db, $invoice['i_date'])) . ",";
    $sql .= "" . toSQLDate(db_escape($db, $invoice['due_date'])) . ",";
    $sql .= " " . $invoice['i_amount'] . ",";
    $sql .= " " . $invoice['policy_id'] . "";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function find_i_amount_by_invoice_id($id) {
    global $db;

    $sql = "SELECT i_amount FROM invoices ";
    $sql .= "WHERE invoice_id=" . $id . "";
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $res[0];
  }
  function calculate_unpaid_amount($invoice_id) {
    global $db;
    $total_amount = find_i_amount_by_invoice_id($invoice_id);

    $sql = "SELECT SUM(amount) FROM payment ";
    $sql .= "WHERE invoice_id=" . $invoice_id . ";";
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_row($result);
    $m = $res[0];
    mysqli_free_result($result);
    return $total_amount - $m;
  }
  function validate_payment($payment, $unpaid_amount) {
    $errors = [];
    if((int)$payment['amount'] < 0) {
      $errors[] = "Input amount cannot be negative!";
    }
    if((int)$payment['amount'] > (int)$unpaid_amount) {
      $errors[] = "Input amount is larger than total invoice amount!";
    }
    return $errors;
  }
  function insert_payment($payment) {
    global $db;
    $sql = "INSERT INTO payment ";
    $sql .= "(method, amount, p_date, invoice_id) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $payment['method']) . "',";
    $sql .= " " . $payment['amount'] . ",";
    $sql .= "" . toSQLDate(db_escape($db, $payment['p_date'])) . ",";
    $sql .= " " . $payment['invoice_id'] . "";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      db_disconnect($db);
      exit;
    }
  }

  function find_invoices_by_policy_id($id) {
    global $db;

    $sql = "SELECT * FROM invoices ";
    $sql .= "WHERE policy_id=" . $id . " ";
    $sql .= "ORDER BY invoice_id ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function find_payments_by_invoice_id($id) {
    global $db;

    $sql = "SELECT * FROM payment ";
    $sql .= "WHERE invoice_id=" . $id . " ";
    $sql .= "ORDER BY id ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function find_policy_id_by_invoice_id($id) {
    global $db;

    $sql = "SELECT policy_id FROM invoices ";
    $sql .= "WHERE invoice_id=" . $id . "";
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $res[0];
  }

  function find_all_customers() {
      global $db;
      $sql = "SELECT * FROM customers ";
      $sql .= "ORDER BY customer_id ASC";
      $result = mysqli_query($db, $sql);
      confirm_result_set($result);
      return $result;
    }

  function insert_manager($manager) {
    global $db;

      $errors = validate_manager($manager);
      if(!empty($errors)) {
        return $errors;
      }
      $hased_password = password_hash($manager['password'], PASSWORD_BCRYPT);

      $sql = "INSERT INTO managers ";
      $sql .= "(first_name, last_name, email, username, hashed_password) ";
      $sql .= "VALUES (";
      $sql .= "'" . db_escape($db, $manager['first_name']) . "',";
      $sql .= "'" . db_escape($db, $manager['last_name']) . "',";
      $sql .= "'" . db_escape($db, $manager['email']) . "',";
      $sql .= "'" . db_escape($db, $manager['username']) . "',";
      $sql .= "'" . db_escape($db, $hased_password) . "'";
      $sql .= ")";
      $result = mysqli_query($db, $sql);
      // For INSERT statements, $result is true/false
      if($result) {
        return true;
      } else {
        // INSERT failed
        echo mysqli_error($db);
        db_disconnect($db);
        exit;
      }
  }
  function find_manager_by_username($username) {
    global $db;
      $sql = "SELECT * FROM managers ";
      $sql .= "WHERE username='" . db_escape($db, $username) . "'";
      $result = mysqli_query($db, $sql);
      confirm_result_set($result);
      $admin = mysqli_fetch_assoc($result);
      mysqli_free_result($result);
      return $admin;
  }
  function find_all_managers() {
    global $db;

    $sql = "SELECT * FROM managers ";
    $sql .= "ORDER BY id ASC";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function find_customers_by_name($first_name, $last_name) {
    global $db;

    $sql = "SELECT * FROM customers ";
    $sql .= "WHERE first_name='" . db_escape($db, $first_name) . "' ";
    $sql .= "AND last_name='" . db_escape($db, $last_name) . "' ";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function find_policy_id_by_customer_id($customer_id) {
    global $db;

    $sql = "SELECT policy_id FROM policy ";
    $sql .= "WHERE customer_id=" . $customer_id;
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function find_invoice_id_by_policy_id($policy_id) {
    global $db;

    $sql = "SELECT invoice_id FROM invoices ";
    $sql .= "WHERE policy_id=" . $policy_id;
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function find_home_id_by_policy_id($policy_id) {
    global $db;
    $sql = "SELECT home_id FROM home_h_policy ";
    $sql .= "WHERE policy_id=" . $policy_id;
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function delete_home_h_policy_by_policy_id($policy_id) {
    global $db;

    $sql = "DELETE FROM home_h_policy ";
    $sql .= "WHERE policy_id=" . $policy_id;
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_h_policy_by_policy_id($policy_id) {
    global $db;

    $sql = "DELETE FROM h_policy ";
    $sql .= "WHERE policy_id=" . $policy_id;
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_payment_by_invoice_id($invoice_id) {
    global $db;

    $sql = "DELETE FROM payment ";
    $sql .= "WHERE invoice_id=" . $invoice_id;
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_invoice_by_policy_id($policy_id) {
    global $db;

    $sql = "DELETE FROM invoices ";
    $sql .= "WHERE policy_id=" . $policy_id;
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_home($home_id) {
    global $db;

    $sql = "DELETE FROM home ";
    $sql .= "WHERE home_id=" . $home_id;
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_policy($policy_id) {
    global $db;

    $sql = "DELETE FROM policy ";
    $sql .= "WHERE policy_id=" . $policy_id;
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_policy_by_customer_id($id){
    global $db;

    $sql = "DELETE FROM policy ";
    $sql .= "WHERE customer_id=" . $id;
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_customer($id) {
    global $db;

    $sql = "DELETE FROM customers ";
    $sql .= "WHERE customer_id=" . $id;
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function find_customer_by_id($customer_id) {
    global $db;

    $sql = "SELECT * FROM customers ";
    $sql .= "WHERE customer_id=" . $customer_id;
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row;
  }
  function find_customer_id_by_policy_id($id) {
    global $db;
    $sql = "SELECT customer_id FROM policy ";
    $sql .= "WHERE policy_id=" . $id;
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $row[0];
  }
  function update_home($home) {
    global $db;

    $sql = "UPDATE home SET ";
    $sql .= "purchase_date=" . toSQLDate(db_escape($db, $home['purchase_date'])) . ", ";
    $sql .= "home_type='" . db_escape($db, $home['home_type']) . "', ";
    $sql .= "AutoFireNotification='" . db_escape($db, $home['AutoFireNotification']) . "', ";
    $sql .= "HomeSecuritySystem='" . db_escape($db, $home['HomeSecuritySystem']) . "', ";
    $sql .= "SwimmingPool='" . db_escape($db, $home['SwimmingPool']) . "', ";
    $sql .= "Basement=" . db_escape($db, $home['Basement']) . " ";
    $sql .= "WHERE home_id=" . db_escape($db, $home['home_id']) . " ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function find_policy_id_by_home_id($id) {
    global $db;
    $sql = "SELECT policy_id FROM home_h_policy ";
    $sql .= "WHERE home_id=" . $id;
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $row[0];
  }
  function validate_vehicle($vehicle) {
    $errors = [];
    if(!has_length($vehicle['vin'], ['exact' => 17])) {
      $errors[] = "Vin must have 17 digits.";
    }

    if(!has_unique_vehicles_vin($vehicle['vin'])) {
      $errors[] = "Vin " . $vehicle['vin'] . " has been registered.";
    }

    if($vehicle['model_year'] > 9999 || $vehicle['model_year'] < 1000) {
      $errors[] = "model_year must be 4 digits.";
    }

    return $errors;
  }

  function insert_vehicle($vehicle) {
    global $db;
    $sql = "INSERT INTO vehicles ";
    $sql .= "(vin, model_year, vstatus) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $vehicle['vin']) . "',";
    $sql .= "'" . db_escape($db, $vehicle['model_year']) . "',";
    $sql .= "'" . db_escape($db, $vehicle['vstatus']) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function insert_a_policy($a_policy) {
    global $db;
    $sql = "INSERT INTO a_policy ";
    $sql .= "(policy_id, a_policy_type) ";
    $sql .= "VALUES (";
    $sql .= " " . $a_policy['policy_id'] . ",";
    $sql .= "'" . db_escape($db, $a_policy['a_policy_type']) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function insert_a_policy_vehicles($a_policy_vehicles) {
    global $db;
    $sql = "INSERT INTO a_policy_vehicles ";
    $sql .= "(vin, policy_id) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $a_policy_vehicles['vin']) . "',";
    $sql .= "'" . db_escape($db, $a_policy_vehicles['policy_id']) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function find_policy_id_by_combine_id($id) {
    global $db;
    $sql = "SELECT policy_id FROM a_policy_vehicles ";
    $sql .= "WHERE combine_id='" . db_escape($db, $id) . "'";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $res = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $res[0];
  }
  function validate_driver($driver) {
    $errors = [];
    if(!has_length_exactly($driver['license_number'], 8)) {
      $errors[] = "license_number must be 8 digits.";
    }
    if(preg_match('#[^0-9]#',$driver['license_number'])) {
      $errors[] = "license_number contains non-numeric element.";
    }

    if(!has_length($driver['first_name'], ['min' => 2, 'max' => 30])) {
      $errors[] = "first_name must be between 2 to 30 characters.";
    }
    if(!has_length($driver['last_name'], ['min' => 2, 'max' => 30])) {
      $errors[] = "last_name must be between 2 to 30 characters.";
    }
    if(!has_unique_drivers_l($driver['license_number'])) {
      $errors[] = "License number " . $license_number . " has been registered.";
    }
    return $errors;
  }
  function insert_driver($driver) {
    global $db;
    $sql = "INSERT INTO drivers ";
    $sql .= "(license_number, first_name, last_name, birthday) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $driver['license_number']) . "',";
    $sql .= "'" . db_escape($db, $driver['first_name']) . "',";
    $sql .= "'" . db_escape($db, $driver['last_name']) . "',";
    $sql .= "" . toSQLDate(db_escape($db, $driver['birthday'])) . "";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function insert_a_vehicles_drivers($a_vehicles_drivers) {
      global $db;
    $sql = "INSERT INTO a_vehicles_drivers ";
    $sql .= "(combine_id, license_number) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $a_vehicles_drivers['combine_id']) . "',";
    $sql .= "'" . db_escape($db, $a_vehicles_drivers['license_number']) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function find_vin_by_combine_id($id) {
    global $db;
    $sql = "SELECT vin FROM a_policy_vehicles ";
    $sql .= "WHERE combine_id='" . db_escape($db, $id) . "'";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    $res = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $res[0];
  }
  function find_vin_by_policy_id($id) {
    global $db;

    $sql = "SELECT vin FROM a_policy_vehicles ";
    $sql .= "WHERE policy_id=" . $id . " ";
    $sql .= "ORDER BY vin ASC";
    $result = mysqli_query($db, $sql);
    return $result;
  }
  function find_vehicle_by_vin($vin) {
    global $db;

    $sql = "SELECT * FROM vehicles ";
    $sql .="WHERE vin='" . db_escape($db, $vin) . "'";
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $res;
  }
  function find_combine_id_by_vin_and_policy_id($vin, $policy_id) {
    global $db;

    $sql = "SELECT combine_id FROM a_policy_vehicles ";
    $sql .="WHERE vin='" . db_escape($db, $vin) . "' ";
    $sql .= "AND policy_id=" . $policy_id;
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $res[0];
  }
  function find_license_number_by_combine_id($id) {
    global $db;

    $sql = "SELECT license_number FROM a_vehicles_drivers ";
    $sql .="WHERE combine_id='" . db_escape($db, $id) . "'";
    $result = mysqli_query($db, $sql);
    return $result;
  }
  function find_driver_by_license_number($license_number) {
    global $db;

    $sql = "SELECT * FROM drivers ";
    $sql .="WHERE license_number='" . db_escape($db, $license_number) . "'";
    $result = mysqli_query($db, $sql);
    $res = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $res;
  }
  function delete_a_vehicles_drivers_by_license_number($l) {
    global $db;

    $sql = "DELETE FROM a_vehicles_drivers ";
    $sql .= "WHERE license_number='" . db_escape($db, $l) . "'";
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_driver($l) {
    global $db;

    $sql = "DELETE FROM drivers ";
    $sql .= "WHERE license_number='" . db_escape($db, $l) . "'";
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function update_driver_by_license_number($driver, $license_number) {
    global $db;

    $sql = "UPDATE drivers SET ";
    $sql .= "birthday=" . toSQLDate(db_escape($db, $driver['birthday'])) . ", ";
    $sql .= "license_number='" . db_escape($db, $driver['license_number']) . "', ";
    $sql .= "first_name='" . db_escape($db, $driver['first_name']) . "', ";
    $sql .= "last_name='" . db_escape($db, $driver['last_name']) . "' ";
    $sql .= "WHERE license_number='" . db_escape($db, $license_number) . "' ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function validate_vehicle_no_model_year($vehicle) {
    $errors = [];
    if(!has_length($vehicle['vin'], ['exact' => 17])) {
      $errors[] = "Vin must have 17 digits.";
    }
    return $errors;
  }
  function update_vehicle_by_vin($vehicle, $vin) {
    global $db;

    $sql = "UPDATE vehicles SET ";
    $sql .= "vin='" . db_escape($db, $vehicle['vin']) . "', ";
    $sql .= "vstatus='" . db_escape($db, $vehicle['vstatus']) . "' ";
    $sql .= "WHERE vin='" . db_escape($db, $vin) . "' ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function find_vins_by_policy_id($id) {
    global $db;

    $sql = "SELECT vin FROM a_policy_vehicles ";
    $sql .= "WHERE policy_id='" . db_escape($db, $id) . "'";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function delete_a_policy($id) {
     global $db;

    $sql = "DELETE FROM a_policy ";
    $sql .= "WHERE policy_id='" . db_escape($db, $id) . "'";
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function delete_vehicle($vin) {
    global $db;

    $sql = "DELETE FROM vehicles ";
    $sql .= "WHERE vin='" . db_escape($db, $vin) . "'";
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function find_combine_id_by_policy_id($id) {
    global $db;

    $sql = "SELECT combine_id FROM a_policy_vehicles ";
    $sql .= "WHERE policy_id='" . db_escape($db, $id) . "'";
    $result = mysqli_query($db, $sql);
    confirm_result_set($result);
    return $result;
  }
  function start_transaction() {
    global $db;
    $sql = "START TRANSACTION;";
    $result = mysqli_query($db, $sql);
  }
  function commit() {
    global $db;
    $sql = "COMMIT;";
    $result = mysqli_query($db, $sql);
  }
  function rollback() {
    global $db;
    $sql = "ROLLBACK;";
    $result = mysqli_query($db, $sql);
  }
  function sharelock_policy_by_customer_id($id) {
    global $db;
    $sql = "SELECT * FROM policy WHERE customer_id=" . $id . " FOR SHARE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

  function updatelock_prem_amount_by_policy_id($id) {
    global $db;
    $sql = "SELECT prem_amount FROM policy WHERE policy_id=" . $id . " FOR UPDATE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function sharelock_home_by_home_id($id) {
    global $db;
    $sql = "SELECT * FROM home WHERE home_id=" . $id . " FOR SHARE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function sharelock_vehicle_by_vin($vin) {
    global $db;
    $sql = "SELECT * FROM vehicles WHERE vin='" . $vin . "' FOR SHARE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function sharelock_driver_by_license_number($l) {
    global $db;
    $sql = "SELECT * FROM drivers WHERE license_number=" . $l . " FOR SHARE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function updatelock_home_by_home_id($id) {
    global $db;
    $sql = "SELECT * FROM home WHERE home_id=" . $id . " FOR UPDATE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function updatelock_vehicle_by_vin($vin) {
    global $db;
    $sql = "SELECT * FROM vehicles WHERE vin='" . $vin . "' FOR UPDATE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function updatelock_policy_by_policy_id($id) {
    global $db;
    $sql = "SELECT * FROM policy WHERE policy_id=" . $id . " FOR UPDATE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function updatelock_driver_by_license_number($l) {
     global $db;
    $sql = "SELECT * FROM drivers WHERE license_number=" . $l . " FOR UPDATE NOWAIT;";
    $result = mysqli_query($db, $sql);
    if($result) {
      return true;
    } else {
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
?>
