const events = ['mousemove', 'touchmove']
var totalamount;
$.each(events, function (k, v) {
  $('#customRange3').on(v, function () {
    $('#amount').text($('#customRange3').val());
    var amount = $('#customRange3').val();
    var tax = ((amount / 100) * 18).toFixed(0);
    totalamount = eval(amount) + eval(tax);
    $('#total-amount').text(totalamount);
    $('#totalAmount').val(totalamount);
  });
})

$(document).ajaxSend(function () {
  $("#overlay").fadeIn(300);
});

$('#payment_details_form').submit(function () {
  event.preventDefault();
  // $razorpay_payment_id = "response.razorpay_payment_id";
  //     $razorpay_order_id = "response.razorpay_order_id";
  //     $razorpay_signature = "response.razorpay_signature";
  // $razorpay_payment_id = response.razorpay_payment_id;
  // $razorpay_order_id = response.razorpay_order_id;
  // $razorpay_signature = response.razorpay_signature;
  // var razorpay_payment_reponse = { razorpay_payment_id: $razorpay_payment_id, razorpay_order_id: $razorpay_order_id, razorpay_signature: $razorpay_signature }

  $.ajax({
    type: 'post',
    url: 'action_page.php',
    data: $(this).serialize(),
    dataType: "json",
    success: function (res) {
      if (res.Status == 1) {
        // console.log(res);
        // alert(res);
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: true
        })
        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          html: "RazorPay Id : " + res.RazorpayID + "<br> Amount : " + res.Amount + "<br> Status : Order " + res.status,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Pay it!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            setData(res)
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Your payment is cancelled',
              'error'
            )
          }
        })
      } else {
        alert("Oops ! something wrong. 3");
      }
    }, error: function (err) {
      alert('Oops ! something wrong. 4');
    }
  }).done(function () {
    setTimeout(function () {
      $("#overlay").fadeOut(300);
    }, 500);
  });
});

function setData(res) {
  var RazorPayTotalAmount = Number(res.Amount * 100);
  var RazorpayID = res.RazorpayID;
  // alert(RazorpayID + " & " + RazorPayTotalAmount);
  var options = {
    "key": "rzp_test_CswR4etBlQd0ha", // Enter the Key ID generated from the Dashboard
    "amount": RazorPayTotalAmount,
    "order_id": RazorpayID, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
    "currency": "INR",
    "name": "Acme Corp",
    "description": "Test Transaction",
    "image": "https://example.com/your_logo",
    //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
    "handler": function (response) {
      // alert(response.razorpay_payment_id);
      // alert(response.razorpay_order_id);
      // alert(response.razorpay_signature)
      $razorpay_payment_id = response.razorpay_payment_id;
      $razorpay_order_id = response.razorpay_order_id;
      $razorpay_signature = response.razorpay_signature;
      var razorpay_payment_reponse = { razorpay_payment_id: $razorpay_payment_id, razorpay_order_id: $razorpay_order_id, razorpay_signature: $razorpay_signature }
      $.ajax({
        type: 'post',
        url: 'verify_payment.php',
        // url: 'action_page.php/verify_payment_signature',
        // data: JSON.stringify(razorpay_payment_reponse),
        data: razorpay_payment_reponse,
        dataType: "json",
        // contentType: 'application/json; charset=utf-8',
        success: function (res) {
          if (res.Status == 1) {
            Swal.fire(
              'Congratulations !',
              'You Payment is done Successfully !',
              'success'
            )
          } else {
            alert("Oops ! something wrong. 1");
          }
        }, error: function (err) {
          alert('Oops ! something wrong. 2');
        }
      }).done(function () {
        setTimeout(function () {
          $("#overlay").fadeOut(300);
        }, 500);
      });

    },
    "prefill": {
      "name": $('#firstName').val() +" "+ $('#lastName').val(),
      "email": $('#email').val(),
      "contact": $('#mobile').val()
    },
    "notes": {
      "address": "Razorpay Corporate Office"
    },
    "theme": {
      "color": "#3399cc"
    }
  };
  // console.log(options);
  var rzp1 = new Razorpay(options);
  rzp1.on('payment.failed', function (response) {
    Swal.fire({
      ErrorCode: `${response.error.code}`,
      ErrorDescription: `${response.error.description}`,
      ErrorSource: `${response.error.source}`,
      ErrorStep: `${response.error.step}`,
      Errorreason: `${response.error.reason}`,
      Order_id: `${response.error.metadata.order_id}`,
      Payment_id: `${response.error.metadata.payment_id}`})
    // response.error.code+ " " + response.error.description + " " + response.error.source + " " + response.error.step + " " + response.error.reason + " " + response.error.metadata.order_id + " " + response.error.metadata.payment_id)  
    // + " " + response.error.description + " " + response.error.source + " " + " " + "" +) 
    // alert(response.error.code);
    // alert(response.error.description);
    // alert(response.error.source);
    // alert(response.error.step);
    // alert(response.error.reason);
    // alert(response.error.metadata.order_id);
    // alert(response.error.metadata.payment_id);
  });

  rzp1.open();
  event.preventDefault();
}
