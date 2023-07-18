<?php
require ("connect.php");
require ("config.php");
require_once('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

if ($_SERVER['REQUEST_METHOD'] == "POST")
  {
    header('Content-Type: application/json');
    // $json = json_decode(file_get_contents("php://input"));
    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $razorpay_signature = $_POST['razorpay_signature'];
    // var_dump(json_decode($personData, true));
    // $address = $personData->razorpay_payment_id;
    // $postalCode = $personData->razorpay_order_id;
    // $returnData = json_encode($personData);
    // echo $razorpay_payment_id;
    // echo $razorpay_order_id;
    // echo $razorpay_signature;

    class Payment {
      public $razorpay_payment_id = "";
      public $razorpay_order_id = "";
      public $razorpay_signature = "";
    }

    $payment = new Payment();
    $payment->razorpay_payment_id = $razorpay_payment_id;
    $payment->razorpay_order_id = $razorpay_order_id;
    $payment->razorpay_signature = $razorpay_signature;

    $result = json_encode($payment);
    // echo "The JSON representation is:".$result."\n";

    $success = true;
    $error = "Payment Failed";

    // echo $success;
    // if (empty($result) === false){
    //   $success = false;
    //   echo $success;
    // }
    if (empty($_POST['razorpay_payment_id']) === false)
        {
            $api = new Api($keyId, $keySecret);
            try
            {
                // Please note that the razorpay order ID must
                // come from a trusted source (session here, but
                // could be database or something else)
                $attributes = array(
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_payment_id' => $razorpay_payment_id,
                    'razorpay_signature' => $razorpay_signature
                );

              $api->utility->verifyPaymentSignature($attributes);
              
            }
            catch(SignatureVerificationError $e)
            {
              $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }
        }

        if ($success === true)
        {
          $query1 = "SELECT user_id,order_id,total_amount FROM order_details WHERE order_id = '" . $razorpay_order_id . "' ";
          $sql_result = mysqli_query($conn, $query1);
          while ($res = mysqli_fetch_assoc($sql_result)) {
              $user_id = $res['user_id'];
              $order_id = $res['order_id'];
              $total_amount = $res['total_amount'];
              // echo $user_id;   
          }

          $job_query = "INSERT INTO `payment_details`(`user_id`,`razorpay_payment_id`,`razorpay_order_id`,`razorpay_signature`,`payment_mode`,`amount`) VALUE('" . $user_id . "','" . $razorpay_payment_id . "','" . $razorpay_order_id . "','" . $razorpay_signature . "','" . $success . "','".$total_amount."')";
          // echo $job_query;
          // print_r($job_query);
          $job_result = mysqli_query($conn, $job_query);
          if ($job_result) {
            $data = array("Status"=>1,'Message'=>"Success","Razorpay_Payment_Id"=>$_POST['razorpay_payment_id'],
            "Razorpay_Order_Id"=>$_POST['razorpay_order_id']);
            print_r(json_encode($data)); 
          } else {
              // echo "Job Data Failed!<br>";
          }
        }
        else
        {
          // print_r($success);
          $data = array("Status"=>0,'Message'=>"Failed","Error"=>$error);
          print_r(json_encode($data));
            // $html = "<p>Your payment failed</p>
            //         <p>{$error}</p>";
        }
    // $data1 = json_encode();
    // die();

    // header('Content-Type: application/json');
    // $action = $_POST['razorpay_payment_id'];
    // $json = json_decode(file_get_contents("php://input"));
    // $params = json_decode($_GET['razorpay_payment_reponse']);
    // $data1 = json_decode(stripslashes($_POST['razorpay_payment_reponse']));
    // // json_encode($_POST['razorpay_payment_reponse']);
    // // var_dump($data1);
    // $obj = $_POST['razorpay_payment_reponse'];
    // // $newJsonString = json_encode($obj);
    // // var_dump($newJsonString);
    // $razorpay_order_id = $obj->razorpay_order_id;
    // $params = $action->razorpay_order_id;
    // $data = array("Status"=>1,'Message'=>"Success","Data"=>$data1,"Data1"=>$params);
    // print_r(json_encode($data));

    // echo "Hello";
  }
// if ($_SERVER['REQUEST_METHOD'] == "POST")
//     {
//         //contact form fields
//         $first_name = $_POST['firstName'];
//         $last_name = $_POST['lastName'];
//         $email = $_POST['email'];
//         $phone = $_POST['phone'];
//         if (isset($_POST['saveInfo'])) {
//             $save_info_sw = $_POST['saveInfo'];
//         } else {
//             $save_info_sw = 0;
//         }
//         $payment_mode = $_POST['paymentMethod'];
//         $tatal_amount = $_POST['totalAmount'];
//         $query = "INSERT INTO `user_details` (`first_name`, `last_name`, `email`, `phone`, `save_user_data`,`payment_mode`, `tatal_amount`) VALUES ('" . $first_name . "','" . $last_name . "', '". $email . "','". $phone . "','". $save_info_sw . "','" . $payment_mode . "','" . $tatal_amount . "')";
//         mysqli_query($conn,$query);
//             if(mysqli_affected_rows($conn)>0)
//             {
//                 $razorpayOrder = create_order($tatal_amount,$keyId,$keySecret,$email,$conn);
//                 $currency = $razorpayOrder['currency'];
//                 $status = $razorpayOrder['status'];
//                 $receipt = $razorpayOrder['receipt'];
//                 $razorpayOrderId = $razorpayOrder['id'];
//                 $amount = $razorpayOrder['amount']/100;
//                 // $razorpayFetchOrder = fetch_order($keyId,$keySecret,$razorpayOrderId);
//                 // $razorpayFetchOrderPayment = fetch_order_payment($keyId,$keySecret,$razorpayOrderId);
//                 $data = array("Status"=>1,'Message'=>"Success",'currency'=>$currency,'status'=>$status,'receipt'=>$receipt,'RazorpayID'=>$razorpayOrderId, 'Amount'=> $amount);
//                 print_r(json_encode($data));
//                 // $razorpayFetchPostPaymentData = fetch_post_payment_data($keyId,$keySecret,$razorpayOrderId);
//             }
//             else{
//                 $data = array("Status"=>0,'Message'=>"Record Not Found");
//                 print_r(json_encode($data));
//             }
//     }
// $data = array("Status"=>1,'Message'=>"Success");
//         print_r(json_encode($data));
//     function verify_payment_signature(){
        // if(isset($_POST['razorpay_payment_reponse']) && !empty($_POST['razorpay_payment_reponse'])) {
        //     $action = $_POST['razorpay_payment_reponse'];
        //     echo $action;
        //     print_r($action);
        //     die();
        // }
        // $data = array("Status"=>1,'Message'=>"Success");
        // print_r(json_encode($data));
        // echo "Hello";
        // if ($_SERVER['REQUEST_METHOD'] == "POST")
        // {
        //     $action = $_POST['razorpay_payment_reponse'];
        //     echo "Hello";
        //     print_r($action);
        //     die();
        // }
        // echo "Hello2";
        // $success = true;
        // $error = "Payment Failed";
        // // if (empty($_POST['razorpay_payment_id']) === false)
        // {
        //     $api = new Api($keyId, $keySecret);

        //     try
        //     {
        //         // Please note that the razorpay order ID must
        //         // come from a trusted source (session here, but
        //         // could be database or something else)
        //         $attributes = array(
        //             'razorpay_order_id' => $_SESSION['razorpay_order_id'],
        //             'razorpay_payment_id' => $_POST['razorpay_payment_id'],
        //             'razorpay_signature' => $_POST['razorpay_signature']
        //         );

        //         $api->utility->verifyPaymentSignature($attributes);
        //     }
        //     catch(SignatureVerificationError $e)
        //     {
        //         $success = false;
        //         $error = 'Razorpay Error : ' . $e->getMessage();
        //     }
        // }

        // if ($success === true)
        // {
        //     $html = "<p>Your payment was successful</p>
        //             <p>Payment ID: {$_POST['razorpay_payment_id']}</p>";
        // }
        // else
        // {
        //     $html = "<p>Your payment failed</p>
        //             <p>{$error}</p>";
        // }

    // }

    
    // echo $html;

?>