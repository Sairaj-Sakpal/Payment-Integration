<?php
require('./fpdf186/fpdf.php');
require ("config.php");
require_once('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

if (isset($_GET['razorpayOrderId'])) {
    $OrderId=$_GET['razorpayOrderId']; 
}

// $OrderId=$_GET['razorpayOrderId']; 


// $OrderId='order_MD16VmBj0JnL6R';
// echo $OrderId;
$razorpayFetchOrderPayment = fetch_order($OrderId,$keyId,$keySecret);
// echo "<pre>";
// print_r($razorpayFetchOrderPayment["items"][0]);
// print_r($razorpayFetchOrderPayment["items"][0]->amount);die();

function fetch_order($OrderId,$keyId,$keySecret){
    $key_id = $keyId;
    $secret = $keySecret;
    $orderId = $OrderId;
    // $orderId = 'order_MD16VmBj0JnL6R';
    $api = new Api($key_id, $secret);
    $razorpayFetchOrder = $api->order->fetch($orderId)->payments();
    return $razorpayFetchOrder;
}

class PDF extends FPDF
{
    function Header()
    {
        $this->SetTextColor(255,138,0);
        $this->SetFont('Arial','B',25);
        $this->Cell(80);
        $this->Cell(30,10,'Payment Recieved',0,0,'C');
        $this->Ln(1);
    }

    function Content()
    {
        $this->SetTextColor(133,133,133);
        $this->SetFont('Arial','B',13);
        $this->Cell(75);
        $this->Cell(40,50,'A receipt copied has been mailed to your email id !',0,0,'C');
    }

    function DrawLine(){
        $this->SetLineWidth(1.5);
        $this->SetDrawColor(200,200,200);
        $this->Line(50,55,160,55);
    }

    function PrintChapter($OrderId,$razorpayFetchOrderPayment)
    {
        $this->Content();
        $this->GetDateTime();
        $this->ChapterBody($OrderId,$razorpayFetchOrderPayment);
        $this->DrawLine();
    }

    function ChapterBody($OrderId,$razorpayFetchOrderPayment){
        $payment_id = $razorpayFetchOrderPayment["items"][0]->id;
        $amount = ($razorpayFetchOrderPayment["items"][0]->amount)/100;
        $payment_mode = $razorpayFetchOrderPayment["items"][0]->method;
        $created_at = $razorpayFetchOrderPayment["items"][0]->created_at;
        $payment_date = date("F j, Y, g:i a", $created_at);
        $this->SetTextColor(54,69,79);
        $this->SetFont('Arial','B',20);
        $this->SetXY(50,60);
        $this->Cell(110, 15, "Payment Details ", 0, 2, "C");
        $this->SetFont("Helvetica", "",10);
        $this->MultiCell(52, 5, "Amount :\nPayment Id :\nPayment Mode :\nPayment Date & Time :", "0", "R", false);
        $this->SetXY(101,75);
        $this->MultiCell(45.5, 5, "Rs $amount/- \n$payment_id \n$payment_mode \n$payment_date\n ", 0, "L", false);
        $this->SetLineWidth(1.5);
        $this->SetDrawColor(200,200,200);
        $this->Line(50,100,160,100);
        $this->SetTextColor(133,133,133);
        $this->SetFont('Arial','',10);
        $this->SetXY(85,85);
        $this->Cell(40,50,'This computer generated no signature required ',0,0,'C');
        $this->Image('icons/paid_stamp.png',140,85,30);
        // $this->Cell(50,70,'OrderId : '.$razorpayFetchOrderPayment["items"][0]->status,0,0,'C');
    }

    function GetDateTime(){
        $this->SetTextColor(200,200,200);
        $this->SetFont('Arial','B',13);
        setlocale(LC_TIME, "");
        $this->SetX(80);
        date_default_timezone_set('Asia/Kolkata');
        $date_and_time = utf8_encode(strftime('%d-%m-%Y %I:%M:%S %p'));
        $this->Cell(50,70,'Receipt Generated on '.$date_and_time,0,0,'C');
        
    
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// function fetch_order($OrderId){
//     // $key_id = $keyId;
//     // $secret = $keySecret;
//     $orderId = $OrderId;
//     // $orderId = 'order_LZmS060p3uSV0c';
//     $api = new Api($key_id, $secret);
//     $razorpayFetchOrder = $api->order->fetch($orderId);
//     return $razorpayFetchOrder;
// }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    // $pdf->SetFont('Times','',12);
    // $pdf->Cell(50,70,'OrderId : '.$OrderId,0,0,'C');
    $pdf->PrintChapter($OrderId,$razorpayFetchOrderPayment);
    $pdf->Output();
?>