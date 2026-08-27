<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset=utf-8/>
  <meta name="generator" content="InnoShop {{ innoshop_version() }}">
  <title>@yield('title')</title>
  <style>
      body {
          background-color: #f8f9fa;
          font-family: 'Open Sans', sans-serif;
          font-size: 14px;
          color: #333;
      }

      .demo {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          min-height: 100vh;
      }

      .demo p:first-child {
          text-align: center;
          font-family: inherit;
          font-size: 150px;
          font-weight: bold;
          line-height: 120px;
          letter-spacing: 5px;
          color: #dee2e6;
          margin-bottom: 50px;
      }

      .demo p:not(:first-child) {
          text-align: center;
          color: #6c757d;
          font-family: inherit;
          font-size: 24px;
          text-shadow: none;
          letter-spacing: 1px;
          line-height: 2em;
          margin-top: -30px;
      }

      .btn-back-home {
          display: block;
          width: 160px;
          height: 45px;
          line-height: 45px;
          text-align: center;
          background: #212529;
          color: #fff;
          text-decoration: none;
          border-radius: 5px;
          margin: 30px auto;
          box-shadow: 0 4px 6px rgba(0,0,0,0.1);
          font-weight: 600;
          transition: all 0.2s ease;
      }

      .btn-back-home:hover {
          background: #343a40;
          box-shadow: 0 6px 8px rgba(0,0,0,0.15);
          transform: translateY(-2px);
      }

      .btn-back-home:active {
          box-shadow: none;
          transform: translateY(1px);
      }
  </style>
</head>
<body>
<div class="demo">
  {{--@yield('code')--}}
  @php
    $status = \Illuminate\Support\Facades\View::getSection('code');
    $digits = str_split($status);
  @endphp

  <p>
    @foreach($digits as $digit)<span>{{ $digit }}</span>@endforeach
  </p>

  <p>@yield('message')</p>
  <a href="javascript:history.back()" class="btn-back-home">{{ __('front/common.back_page') }}</a>
</div>
</body>
</html>