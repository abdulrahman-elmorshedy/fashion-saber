@extends('layouts.app')
@section('body-class', 'page-home')

@push('header')
  <script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}">
@endpush

@section('content')

  @hookinsert('home.content.top')

  <section class="module-content">
    @if ($slideshow)
      <section class="module-line">
        <div class="swiper" id="module-swiper-1">
          <div class="module-swiper swiper-wrapper">
            @foreach ($slideshow as $slide)
              <div class="swiper-slide">
                <a href="{{ $slide['link'] ?: 'javascript:void(0)' }}"
                   class="home-slideshow-link">
                  @if(!empty($slide['is_video']))
                    <video autoplay muted loop playsinline class="home-slideshow-video">
                      <source src="{{ image_origin($slide['image'][$slide['locale']]) }}" type="video/mp4">
                    </video>
                  @else
                    <img src="{{ image_origin($slide['image'][$slide['locale']]) }}"
                         class="img-fluid"
                         alt="{{ $slide['image_alt'] }}">
                  @endif
                  @if (! empty($slide['has_slideshow_caption']))
                    <div class="home-slideshow-caption">
                      @if ($slide['display_title'] !== '')
                        <h2 class="home-slideshow-title">{{ $slide['display_title'] }}</h2>
                      @endif
                      @if ($slide['display_subtitle'] !== '')
                        <p class="home-slideshow-subtitle">{!! nl2br(e($slide['display_subtitle'])) !!}</p>
                      @endif
                      @if ($slide['link'] && $slide['link'] !== 'javascript:void(0)')
                        <span class="home-slideshow-btn">{{ __('front/common.shop_now') }}</span>
                      @endif
                    </div>
                  @endif
                </a>
              </div>
            @endforeach
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </section>
      <script>
        var swiper = new Swiper('#module-swiper-1', {
          loop: true,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          autoplay: {
            delay: 2500,
            disableOnInteraction: true,
          },
        });
      </script>
    @endif

    @hookinsert('home.swiper.after')

    @if (0)
      <section class="module-line">
        <div class="module-banner-2">
          <div class="container">
            <div class="row">
              <div class="col-12 col-md-4 mb-2 mb-lg-0">
                <a href=""><img src="{{ asset('images/demo/banner/banner-3.jpg') }}" class="img-fluid"></a>
              </div>
              <div class="col-12 col-md-8">
                <a href=""><img src="{{ asset('images/demo/banner/banner-4.jpg') }}" class="img-fluid"></a>
              </div>
            </div>
          </div>
        </div>
      </section>
    @endif

    @if (! empty($home_categories))
      <section class="module-line">
        <div class="module-categories">
          <div class="container">
            <div class="swiper" id="categories-swiper">
              <div class="swiper-wrapper">
                @foreach ($home_categories as $cat)
                  <div class="swiper-slide">
                    <a href="{{ $cat['url'] }}" class="home-category-item text-center d-block">
                      <div class="home-category-img-wrap mb-2 mx-auto">
                        <img src="{{ $cat['image'] ?: asset('vendor/innoshop/images/placeholder.png') }}" 
                             alt="{{ $cat['name'] }}" 
                             class="img-fluid">
                      </div>
                      <div class="home-category-name fw-bold">{{ $cat['name'] }}</div>
                    </a>
                  </div>
                @endforeach
              </div>
              <div class="swiper-pagination"></div>
            </div>
          </div>
        </div>
      </section>
      <script>
        var categoriesSwiper = new Swiper('#categories-swiper', {
          slidesPerView: 2,
          spaceBetween: 15,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          autoplay: {
            delay: 3000,
            disableOnInteraction: true,
          },
        });
      </script>
      <style>
        .home-category-item { text-decoration: none; color: inherit; transition: transform 0.2s; padding: 10px 0; }
        .home-category-item:hover { transform: translateY(-5px); }
        .home-category-img-wrap { 
          aspect-ratio: 16/9; 
          overflow: hidden; 
          background: #f8f9fa; 
          border-radius: 6px; 
          width: 100%;
        }
        .home-category-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        #categories-swiper { padding-bottom: 35px; }
      </style>
    @endif

    <section class="module-line">
      <div class="module-product-tab">
        <div class="container">
          <div class="module-title-wrap">
            <div class="module-title">{{ system_setting_locale('home_feature_product_title', __('front/home.feature_product')) }}</div>
            <div class="module-sub-title">{{ system_setting_locale('home_feature_product_subtitle', __('front/home.feature_product_text')) }}</div>
          </div>

          <ul class="nav nav-tabs">
            @foreach ($tab_products as $item)
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                  data-bs-target="#module-product-tab-x-{{ $loop->iteration }}"
                  type="button">{{ $item['tab_title'] }}</button>
              </li>
            @endforeach
          </ul>

          <div class="tab-content">
            @foreach ($tab_products as $item)
              <div class="tab-pane fade show {{ $loop->first ? 'active' : '' }}"
                id="module-product-tab-x-{{ $loop->iteration }}">
                <div class="row gx-3 gx-lg-4">
                  @foreach ($item['products'] as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                      @include('shared.product')
                    </div>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </section>

    @if (! empty($hot_products) && ! empty($hot_products['floors']))
      <section class="module-line">
        <div class="module-hot-products">
          <div class="container">
            @if ($hot_products['display_mode'] === 'tab')
              <ul class="nav nav-pills mb-4 justify-content-{{ $hot_products['title_align'] === 'center' ? 'center' : 'start' }}" id="hot-products-tabs" role="tablist">
                @foreach ($hot_products['floors'] as $fIdx => $floor)
                  <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $fIdx === 0 ? 'active' : '' }}" 
                            id="floor-tab-{{ $fIdx }}" 
                            data-bs-toggle="pill" 
                            data-bs-target="#floor-content-{{ $fIdx }}" 
                            type="button">{{ $floor['name'] }}</button>
                  </li>
                @endforeach
              </ul>
              <div class="tab-content">
                @foreach ($hot_products['floors'] as $fIdx => $floor)
                  <div class="tab-pane fade {{ $fIdx === 0 ? 'show active' : '' }}" id="floor-content-{{ $fIdx }}">
                    <div class="row gx-3 gx-lg-4">
                      @foreach ($floor['products'] as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                          @include('shared.product')
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              @foreach ($hot_products['floors'] as $floor)
                <div class="module-floor mb-5">
                  <div class="module-title-wrap text-{{ $hot_products['title_align'] }}">
                    <div class="module-title">{{ $floor['name'] }}</div>
                    @if ($floor['subtitle'])
                      <div class="module-sub-title">{{ $floor['subtitle'] }}</div>
                    @endif
                  </div>
                  <div class="row gx-3 gx-lg-4">
                    @foreach ($floor['products'] as $product)
                      <div class="col-6 col-md-4 col-lg-3">
                        @include('shared.product')
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            @endif
          </div>
        </div>
      </section>
    @endif

    @if (0)
      <section class="module-line">
        <div class="module-banner-1">
          <div class="container">
            <a href=""><img src="{{ asset('images/demo/banner/banner-5.jpg') }}" class="img-fluid"></a>
          </div>
        </div>
      </section>
    @endif

    <section class="module-line">
      <div class="module-product-tab">
        <div class="container">
          <div class="module-title-wrap">
            <div class="module-title">{{ system_setting_locale('home_news_blog_title', __('front/home.news_blog')) }}</div>
            <div class="module-sub-title">{{ system_setting_locale('home_news_blog_subtitle', __('front/home.news_blog_text')) }}</div>
          </div>

          <div class="row gx-3 gx-lg-4">
            @foreach ($news as $new)
              <div class="col-6 col-md-4 col-lg-3">
                @include('shared.article', ['item' => $new])
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </section>
  </section>

  @hookinsert('home.content.bottom')

@endsection
