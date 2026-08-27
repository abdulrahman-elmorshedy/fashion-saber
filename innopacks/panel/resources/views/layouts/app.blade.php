<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ panel_locale_direction() }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="{{ panel_route('home.index') }}">
  <title>@yield('title'){{ View::hasSection('title') ? ' - ' : '' }}{{ system_setting_locale('store_name', 'MZ Fashion') }}</title>
  <meta name="keywords" content="@yield('keywords', 'MZ Fashion, Ecommerce, Open Source, CMS, Laravel 11, Multilingual, Multicurrency, Hook, Plugin Architecture, Flexible, Powerful')">
  <meta name="generator" content="MZ Fashion {{ innoshop_version() }}">
  <meta name="asset" content="{{ asset('/') }}">
  <meta name="description" content="@yield('description', 'MZ Fashion')">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="api-token" content="{{ session('panel_api_token') }}">
  <meta name="storage-base-url" content="{{ storage_url('') }}">
  <link rel="shortcut icon" href="{{ image_origin(system_setting('favicon', 'images/favicon.png')) }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/element-plus/index.css') }}">
  @if(panel_locale_direction() == 'rtl')
    <link rel="stylesheet" href="{{ asset(mix('build/panel/css/bootstrap-rtl.css')) }}">
  @else
    <link rel="stylesheet" href="{{ asset(mix('build/panel/css/bootstrap.css')) }}">
  @endif
  @if(panel_locale_direction() == 'rtl')
    <link rel="stylesheet" href="{{ asset(mix('build/panel/css/app-rtl.css')) }}">
  @else
    <link rel="stylesheet" href="{{ asset(mix('build/panel/css/app.css')) }}">
  @endif
  <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('vendor/vue/3.5/vue.global' . (config('app.debug') ? '' : '.prod') . '.js') }}"></script>
  <script src="{{ asset('vendor/element-plus/index.full.js') }}"></script>
  <script src="{{ asset('vendor/element-plus/icons.min.js') }}"></script>
  @if(str_starts_with(panel_locale_code(), 'zh'))
  <script src="{{ asset('vendor/element-plus/zh-cn.js') }}"></script>
  @endif
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/layer/3.5.1/layer.js') }}"></script>
  <script src="{{ asset('vendor/laydate/laydate.js') }}"></script>
  <script src="{{ asset(mix('build/panel/js/app.js')) }}"></script>
  <script src="{{ asset(mix('build/panel/js/panel-standalone.js')) }}"></script>
  <script src="{{ asset('vendor/locale-modal/locale-modal.js') }}"></script>
  <script>
    const urls = {
      panel_api: '{{ route('api.panel.base.index') }}',
      panel_base: '{{ panel_route('home.index') }}',
      panel_upload: '{{ panel_route('upload.images') }}',
      panel_ai: '{{ panel_route('content_ai.generate') }}',
      file_manager_title: '{{ __("panel/menu.file_manager") }}',
    };

    const lang = {
      hint: '{{ __('common/base.hint') }}',
      delete_confirm: '{{ __('common/base.delete_confirm') }}',
      confirm: '{{ __('common/base.confirm') }}',
      cancel: '{{ __('common/base.cancel') }}',
    }

    const panelLocaleMessages = {
      frontRequired: '{{ __("panel/common.front_locale_required", ["locale" => setting_locale_code(), "field" => ":field"]) }}',
    };

    window._lmConfig = {
      defaultLocale: '{{ panel_locale_code() }}',
      locales: @json(locales()),
      hasTranslator: @json(has_translator()),
      translateUrl: '{{ panel_route("home.index") }}/translations/translate-text',
      messages: {
        sourceEmpty: '{{ __("panel/common.source_empty") }}',
        noEmpty: '{{ __("panel/common.no_empty_languages") }}',
        translated: '{{ __("panel/common.translated_languages", ["count" => ":count"]) }}',
        copied: '{{ __("panel/common.copied_languages", ["count" => ":count"]) }}',
      },
    };
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
</head>

<body class="@yield('body-class')">
  @include('panel::layouts.header')
  <div class="main-content">
    <aside id="sidebar-box" class="sidebar-box navbar-expand-xs border-radius-xl">
      <div class="sidebar-body">
        <x-panel-layout-sidebar></x-panel-layout-sidebar>
      </div>
      <div class="mb-menu-close"><i class="bi bi-chevron-left"></i></div>
    </aside>

    <div id="content">
      <div class="page-title-box py-1 d-flex align-items-center justify-content-between">
        <div class="d-flex">
          <h4 class="page-title mb-0">@yield('title')</h4>
          <div class="ms-4 text-danger">@yield('page-title-after')</div>
        </div>
        <div class="text-nowrap">
          @yield('page-title-right')
          @hookinsert('panel.layout.right.button.after')
        </div>
      </div>

      <div class="container-fluid p-0 mt-2">
        <div class="content-info">
          @if (session()->has('errors'))
            <x-common-alert type="danger" msg="{{ session('errors')->first() }}" class="mt-4"/>
          @endif
          @if (session('success'))
            <x-common-alert type="success" msg="{{ session('success') }}" class="mt-4"/>
          @endif
          @if (session('error'))
            <x-common-alert type="danger" msg="{{ session('error') }}" class="mt-4"/>
          @endif
          @yield('content')
        </div>

        <div class="page-bottom-btns">
          @yield('page-bottom-btns')
        </div>

        <p class="text-center text-secondary mt-5">
          @php
            $panelCopyright = system_setting_locale('panel_copyright_text', 'Admin Panel');
            $panelLink = system_setting('panel_copyright_link');
          @endphp
          @if($panelLink)
            <a href="{{ $panelLink }}" class="text-secondary text-decoration-none" target="_blank">{{ $panelCopyright }}</a>
          @else
            {{ $panelCopyright }}
          @endif
        </p>
      </div>
    </div>
  </div>

  @include('panel::layouts.footer')

  @stack('footer')
</body>

</html>
