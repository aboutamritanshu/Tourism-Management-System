<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');


if(isset($_POST['check_availability'])){
    $frm_data = filteration($_POST);
    
    $status = "";
    $result = "";

    //departure date validation   
    $today_date = new DateTime(date("Y-m-d"));
    $ddate_date = new DateTime($frm_data['ddate']);

    if($status!=''){
        echo $result;
    } else {
        session_start();

        //run query to check departure destination is available or not
        $payment = $_SESSION['destination']['price'] * $frm_data['no_person'];
        $_SESSION['destination']['payment'] = $_SESSION['destination']['price'];
        $_SESSION['destination']['available'] = true;

        $result = json_encode(["status"=>'available', "days"=>$frm_data['duration'], "payment"=> $payment]);
        echo $result;
    }
}
?>