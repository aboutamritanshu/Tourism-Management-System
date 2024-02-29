<?php

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

define('INDUSTRY_TYPE_ID', 'Retail');
define('CHANNEL_ID', 'WEB');
define('PAYTM_MERCHANT_MID', 'tk0skL16930420179530');

date_default_timezone_set("Asia/Kolkata");

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('destinations.php');
}

if (isset($_POST['pay_now'])) {

    //insert payment data into database
    $frm_data = filteration($_POST);

    $checkSum = "";

    $ORDER_ID = 'ORD_' . $_SESSION['uId'] . random_int(11111, 9999999);
    $CUST_ID = $_SESSION['uId'];
    $INDUSTRY_TYPE_ID = INDUSTRY_TYPE_ID;
    $CHANNEL_ID = CHANNEL_ID;
    $TXN_AMOUNT = $_SESSION['destination']['payment'] * $frm_data['no_person'];
    $TXN_ID = $_SESSION['uId'] . time() . uniqid(mt_rand(), true);
    $TXN_STATUS = 'TXN_SUCCESS';

    $paramList = array();
    $paramList["MID"] = PAYTM_MERCHANT_MID;
    $paramList["ORDDER_ID"] = $ORDER_ID;
    $paramList["CUST_ID"] = $CUST_ID;
    $paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
    $paramList["CHANNEL_ID"] = $CHANNEL_ID;
    $paramList["TXN_AMOUNT"] = $TXN_AMOUNT;

    $query1 = "INSERT INTO `booking_order`(`user_id`, `destination_id`, `ddate`, `booking_status`, `order_id`, `trans_id`, `trans_amt`, `trans_status`, `trans_resp_msg`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    insert($query1, [$CUST_ID, $_SESSION['destination']['id'], $frm_data['ddate'], 'booked', $ORDER_ID, $TXN_ID, $TXN_AMOUNT, $TXN_STATUS, 'transaction has been done'], 'isssssiss');

    $booking_id = mysqli_insert_id($con);

    $query2 = "INSERT INTO `booking_details`(`booking_id`, `destination_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`, `days`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    insert($query2, [$booking_id, $_SESSION['destination']['name'], $_SESSION['destination']['price'], $TXN_AMOUNT, $frm_data['name'], $frm_data['phonenum'], $frm_data['address'], $_SESSION['destination']['days']], 'isssssss');

    redirect('pay_status.php?order='.$ORDER_ID);
}
