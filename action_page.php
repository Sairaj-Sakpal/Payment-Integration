<?php
require ("connect.php");
require ("config.php");
require_once('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

if ($_SERVER['REQUEST_METHOD'] == "POST")
    {
        //contact form fields
        $first_name = $_POST['firstName'];
        $last_name = $_POST['lastName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        if (isset($_POST['saveInfo'])) {
            $save_info_sw = $_POST['saveInfo'];
        } else {
            $save_info_sw = 0;
        }
        $payment_mode = $_POST['paymentMethod'];
        $tatal_amount = $_POST['totalAmount'];
        $query = "INSERT INTO `user_details` (`first_name`, `last_name`, `email`, `phone`, `save_user_data`,`payment_mode`, `tatal_amount`) VALUES ('" . $first_name . "','" . $last_name . "', '". $email . "','". $phone . "','". $save_info_sw . "','" . $payment_mode . "','" . $tatal_amount . "')";
        mysqli_query($conn,$query);
            if(mysqli_affected_rows($conn)>0)
            {
                $razorpayOrder = create_order($tatal_amount,$keyId,$keySecret,$email,$conn);
                $currency = $razorpayOrder['currency'];
                $status = $razorpayOrder['status'];
                $receipt = $razorpayOrder['receipt'];
                $razorpayOrderId = $razorpayOrder['id'];
                $amount = $razorpayOrder['amount']/100;
                // $razorpayFetchOrder = fetch_order($keyId,$keySecret,$razorpayOrderId);
                // $razorpayFetchOrderPayment = fetch_order_payment($keyId,$keySecret,$razorpayOrderId);
                $data = array("Status"=>1,'Message'=>"Success",'currency'=>$currency,'status'=>$status,'receipt'=>$receipt,'RazorpayID'=>$razorpayOrderId, 'Amount'=> $amount);
                print_r(json_encode($data));
                // $razorpayFetchPostPaymentData = fetch_post_payment_data($keyId,$keySecret,$razorpayOrderId);
            }
            else{
                $data = array("Status"=>0,'Message'=>"Record Not Found");
                print_r(json_encode($data));
            }
    }

    //First Step to create the order
    function create_order($tatal_amount,$keyId,$keySecret,$email,$conn){
    $key_id = $keyId;
    $secret = $keySecret;
    $api = new Api($key_id, $secret);
    $user_id = 0;

    $razorpayOrder = $api->order->create(array('receipt' => '123', 'amount' => ($tatal_amount*100), 'currency' => 'INR', 'notes'=> array('key1'=> 'value3','key2'=> 'value2')));
    
    $razorpayOrderId = $razorpayOrder['id'];
    $razorpayTotalAmount = $razorpayOrder['amount'];

    $query1 = "SELECT id FROM user_details WHERE tatal_amount = '" . $tatal_amount . "' ";
        $result = mysqli_query($conn, $query1);
        while ($res = mysqli_fetch_assoc($result)) {
            $user_id = $res['id'];
            // echo $user_id;   
        }

        $job_query = "INSERT INTO `order_details`(`user_id`,`order_id`,`total_amount`) VALUE('" . $user_id . "','" . $razorpayOrderId . "','".$tatal_amount."')";
        // echo $job_query;
        $job_result = mysqli_query($conn, $job_query);
        if ($job_result) {
            // echo "Job Data Entered!<br>";
        } else {
            // echo "Job Data Failed!<br>";
        }
        return $razorpayOrder;
    }

    // function fetch_order($keyId, $keySecret,$orderId){
    function fetch_order($keyId, $keySecret,$razorpayOrderId){
        $key_id = $keyId;
        $secret = $keySecret;
        $orderId = $razorpayOrderId;
        // $orderId = 'order_LZmS060p3uSV0c';
        $api = new Api($key_id, $secret);
        $razorpayFetchOrder = $api->order->fetch($orderId);
        return $razorpayFetchOrder;
    }

    function fetch_order_payment($keyId, $keySecret,$razorpayOrderId){
        $key_id = $keyId;
        $secret = $keySecret;
        $orderId = $razorpayOrderId;
        $api = new Api($key_id, $secret);
        $razorpayFetchOrderPayment = $api->order->fetch($orderId)->payments();
        return $razorpayFetchOrderPayment;
    }

    function fetch_post_payment_data($keyId, $keySecret,$razorpayOrderId){
        $key_id = $keyId;
        $secret = $keySecret;
        $orderId = $razorpayOrderId;
        $api = new Api($key_id, $secret);
        $razorpayFetchOrderPayment = $api->order->fetch($orderId)->payments();
        return $razorpayFetchOrderPayment;
    }

    
?>