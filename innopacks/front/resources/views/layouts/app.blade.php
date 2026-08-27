<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ front_locale_direction() }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="{{ front_route('home.index') }}">
  <title>@yield('title', system_setting_locale('meta_title', 'MZ Fashion - ' . (app()->getLocale() == 'ar' ? 'نظام تجارة إلكترونية مبتكر' : 'Innovative Open Source Ecommerce System')))</title>
  <meta name="description" content="@yield('description', system_setting_locale('meta_description', 'MZ Fashion - ' . (app()->getLocale() == 'ar' ? 'منصة تجارة إلكترونية مبتكرة قائمة على Laravel' : 'An innovative e-commerce platform based on Laravel')))">
  <meta name="keywords" content="@yield('keywords', system_setting_locale('meta_keywords', 'MZ Fashion, Ecommerce, Laravel, Multilingual, Multi-currency'))">
  <meta name="generator" content="MZ Fashion {{ innoshop_version() }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="api-token" content="{{ session('front_api_token') }}">
  <link rel="shortcut icon" href="{{ image_origin(system_setting('favicon', 'images/favicon.png')) }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset(mix('build/front/css/bootstrap.css')) }}">
  <link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}">
  <script src="{{ asset(mix('build/front/js/app.js')) }}"></script>
  <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('vendor/layer/3.5.1/layer.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset(mix('build/front/css/app.css')) }}">
  <script>
    const urls = {
      front_api: '{{ route('api.home.base') }}',
      front_base: '{{ front_route('home.index') }}',
      front_upload: '{{ front_root_route('upload.images') }}',
      front_cart_add: '{{ front_route('carts.store') }}',
      front_cart_mini: '{{ front_route('carts.mini') }}',
      front_cart: '{{ front_route('carts.index') }}',
      front_checkout: '{{ front_route('checkout.index') }}',
      front_login: '{{ front_route('login.index') }}',
      front_favorites: '{{ account_route('favorites.index') }}',
      front_favorite_cancel: '{{ account_route('favorites.cancel') }}',
    };

    const config = {
      isLogin: !!{{ current_customer()->id ?? 'null' }},
      currency: {
        code: '{{ current_currency_code() }}',
        symbol_left: '{{ current_currency()?->symbol_left ?? "$" }}',
        symbol_right: '{{ current_currency()?->symbol_right ?? "" }}',
        decimal_place: {{ current_currency()?->decimal_place ?? 2 }},
        rate: {{ current_currency()?->value ?? 1 }}
      }
    };

    const asset_url = '{{ asset('') }}';
  </script>
  <style>
    /* RTL Dropdown alignment fix */
    html[dir="rtl"] .dropdown-menu-end {
      right: auto !important;
      left: 0 !important;
    }
  </style>
  @stack('header')
  @include('common::components.echo')
  <style>
    /* Preloader Styles */
    .page-preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #ffffff;
      z-index: 99999;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    .logo-loader-wrap {
      position: relative;
      width: 150px;
    }
    .logo-base {
      width: 100%;
      filter: grayscale(100%) opacity(20%);
    }
    .logo-fill {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 0%;
      overflow: hidden;
      animation: fillUp 1.5s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
    }
    .logo-fill img {
      width: 150px;
      position: absolute;
      bottom: 0;
      left: 0;
    }
    @keyframes fillUp {
      0% { height: 0%; }
      100% { height: 100%; }
    }

    /* Back to Top Button */
    .back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 45px;
      height: 45px;
      background-color: var(--bs-primary, #0d6efd);
      color: #fff;
      border: none;
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      font-size: 20px;
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transform: translateY(20px);
      transition: all 0.3s ease;
      cursor: pointer;
    }
    html[dir="rtl"] .back-to-top {
      right: auto;
      left: 30px;
    }
    .back-to-top.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .back-to-top:hover {
      background-color: #0b5ed7; /* dark mode fallback if var not present */
      background-color: var(--bs-primary-dark, #0b5ed7);
      color: #fff;
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    }
    
    /* Mobile Filter Sidebar Fix */
    @media (max-width: 767.98px) {
      #filterSidebar {
        position: fixed !important;
        top: 0 !important;
        width: 280px !important;
        height: 100vh !important;
        height: 100dvh !important;
        z-index: 10001 !important;
        background: #fff !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        display: block !important;
        padding: 1.5rem !important;
        overflow-y: auto !important;
      }
      
      html[dir="rtl"] #filterSidebar {
        right: auto !important;
        left: -280px !important;
        transform: none !important;
      }
      html[dir="rtl"] #filterSidebar.show {
        left: 0 !important;
      }
      
      html[dir="ltr"] #filterSidebar {
        left: auto !important;
        right: -280px !important;
        transform: none !important;
      }
      html[dir="ltr"] #filterSidebar.show {
        right: 0 !important;
      }
    }
    
    #overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 10000;
      display: none;
    }

    /* Mobile Search Modal Enhancements */
    #searchModal .modal-content {
      overflow: hidden;
    }
    #searchModal .search-box input {
      height: 55px;
      font-size: 1.1rem;
      border-color: #eee;
    }
    #searchModal .search-box input:focus {
      box-shadow: none;
      border-color: var(--bs-primary);
    }
    #searchModal .search-box .btn-primary {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
    }
    html[dir="rtl"] #searchModal .search-box .btn-primary {
      left: 0;
      right: auto;
      border-radius: 50px 0 0 50px;
    }
    html[dir="ltr"] #searchModal .search-box .btn-primary {
      right: 0;
      left: auto;
      border-radius: 0 50px 50px 0;
    }
    html[dir="rtl"] #searchModal .search-box input {
      padding-left: 70px !important;
      padding-right: 25px !important;
    }
    html[dir="ltr"] #searchModal .search-box input {
      padding-right: 70px !important;
      padding-left: 25px !important;
    }

  </style>
  @hookinsert('front.layout.app.head.bottom')
</head>

<body class="@yield('body-class')">
  <!-- Preloader -->
  <div id="page-preloader" class="page-preloader">
      <div class="logo-loader-wrap">
          <img src="{{ image_origin(system_setting('front_logo', 'images/logo.svg')) }}" alt="Logo" class="logo-base">
          <div class="logo-fill">
              <img src="{{ image_origin(system_setting('front_logo', 'images/logo.svg')) }}" alt="Logo">
          </div>
      </div>
  </div>

  @if (!request('iframe'))
    <x-front-header />
  @endif

  <div class="m-0 p-0" id="appContent">
      @include('components.flash')
      @yield('content')
  </div>

  @if (!request('iframe'))
    <x-front-footer />
  @endif

  @if (!request('iframe'))
    @include('components.mini-cart')
    @include('components.newsletter-popup')
    
    <!-- Back to top button -->
    <button id="backToTop" class="back-to-top" title="{{ __('front/common.back_to_top') ?? 'العودة للأعلى' }}">
        <i class="bi bi-arrow-up"></i>
    </button>
  @endif

  @stack('footer')

  <script>
    // Preloader JS
    window.addEventListener('load', function() {
      const preloader = document.getElementById('page-preloader');
      if (preloader) {
        preloader.style.opacity = '0';
        setTimeout(() => { preloader.style.visibility = 'hidden'; }, 500);
      }
    });

    // Back to Top JS
    document.addEventListener('DOMContentLoaded', function() {
      const backToTop = document.getElementById('backToTop');
      if (backToTop) {
        window.addEventListener('scroll', function() {
          if (window.scrollY > 300) {
            backToTop.classList.add('show');
          } else {
            backToTop.classList.remove('show');
          }
        });
        backToTop.addEventListener('click', function() {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
      }
    });
  </script>
</body>

</html>
