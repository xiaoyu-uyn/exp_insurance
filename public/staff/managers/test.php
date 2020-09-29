<?php require_once('../../../private/initialize.php'); ?>

<?php
  $startd = strtotime('2001,03,01');
  $endd = strtotime('2003,01,01');
  // $startdate = new DateTime();
  // $startdate->setTimestamp($startd);
  $enddate = new DateTime();
  $enddate->setTimestamp($endd);
  $startdate = new DateTime();
  $startdate->setTimestamp($startd);
  $res = diffInMonths($startdate, $enddate);
  echo $startd - $endd;

?>