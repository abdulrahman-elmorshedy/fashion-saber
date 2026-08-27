@extends('layouts.app')
@section('body-class', 'page-categories')

@section('title', \InnoShop\Common\Libraries\MetaInfo::getInstance($category)->getTitle())
@section('description', \InnoShop\Common\Libraries\MetaInfo::getInstance($category)->getDescription())
@section('keywords', \InnoShop\Common\Libraries\MetaInfo::getInstance($category)->getKeywords())

@section('content')
  <x-front-breadcrumb type="category" :value="$category" :showFilter="true"/>

  @hookinsert('category.show.top')

  <div class="container">
    <div class="row">
      <div class="col-12 col-md-3">
        @include('shared.filter_sidebar')
      </div>

      <div class="col-12 col-md-9">
        @include('categories.partials._intro', ['category' => $category])
        @include('categories.partials._subcategories', ['category' => $category])
        @include('categories.partials._controls', ['products' => $products, 'per_page_items' => $per_page_items])
        <div id="products-container">
          @include('categories.partials._products', ['products' => $products])
        </div>
        @include('categories.partials._description', ['category' => $category])
      </div>
    </div>

    @hookinsert('category.show.bottom')

  </div>
@endsection


@push('footer')
  <script>
    $('.form-select, input[name="style_list"]').change(function(event) {
      filterProductData();
    });

    function filterProductData() {
      let url = new URL(window.location.href);
      let params = new URLSearchParams(url.search);
      let order = $('.order-select').val();
      let perPage = $('.per-page-select').val();
      let styleList = $('input[name="style_list"]:checked').val();

      if (order) {
        let orderKeys = order.split('|');
        params.set('sort', orderKeys[0]);
        params.set('order', orderKeys[1]);
      } else {
        params.delete('sort');
        params.delete('order');
      }

      if (perPage) {
        params.set('per_page', perPage);
      }

      if (styleList) {
        params.set('style_list', styleList);
      }

      if (typeof filterSidebar !== 'undefined') {
        filterSidebar.updateUrl(params);
      } else {
        url.search = params.toString();
        location = url.toString();
      }
    }

    function filterAttrChecked(data) {
      let filterAtKey = [];
      data.forEach((item) => {
        let checkedAtValues = [];
        item.values.forEach((val) => val.selected ? checkedAtValues.push(val.id) : '')
        if (checkedAtValues.length) {
          filterAtKey.push(`${item.id}:${checkedAtValues.join(',')}`)
        }
      })

      return filterAtKey.join('|')
    }
  </script>
@endpush