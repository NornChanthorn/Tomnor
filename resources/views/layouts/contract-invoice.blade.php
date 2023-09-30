<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ $generalSetting->site_title }} @hasSection('title') - @endif @yield('title')</title>
  <meta charset="utf-8">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" sizes="32x32">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="{{ asset('css/invoice.css') }}">
</head>

<body>
  <div class="invoice">
    <div class="header">
      <div class="row">
          <div class="col-12">
              <img src="{{ file_exists($invoice_head->logo) ? asset($invoice_head->logo) : asset('images/contract-phone-3.jpg') }}" alt="" class="img-fluid logo">
          </div>
          <div class="col-12 text-center">
              <div class="title">
                  <h3>{{ $invoice_head->location_kh }}</h3>
                  <h3><b>{{ $invoice_head->location }}</b></h3>
              </div>
             
          </div>
      </div>
    </div>

     <div class="content">
        @yield('content')
     </div>
     <div class="row text-center mt-5">
      <div class="col-6">
        អ្នកទិញ / <b>BUYER</b> 
      </div>
      <div class="col-6">
        អ្នកលក់  / <b>SELLER</b> 
     </div>
    </div>
    <div class="footer">
      <div class="row box-footer">
        <div class="col-4 border-right">
          <img class="img-fluid" src="{{ asset('phone.png') }}" alt="" srcset="">
          <p>{{ $invoice_head->phone_1 }}</p>
          <p>{{ $invoice_head->phone_2 }}</p>
        </div>
        <div class="col-4 border-right">
          <img src="{{ asset('email.png') }}" alt="" srcset="">
          <p>{{ $invoice_head->email_1 }}</p>
          <p>{{ $invoice_head->email_2 }}</p>
        </div>
        <div class="col-4">
          <img class="img-fluid" src="{{ asset('map.png') }}" alt="" srcset="">
          <p>{{ $invoice_head->address }}</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
