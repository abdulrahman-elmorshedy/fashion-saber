@extends('panel::layouts.app')

@section('title', __('panel/menu.country'))

@section('content')
<div class="card h-min-600">
  <div class="card-header">
    <h5 class="card-title mb-0">{{ __('panel/menu.countries') }}</h5>
  </div>
  <div class="card-body">
    <form class="needs-validation mt-3" novalidate action="{{ $country->id ? panel_route('countries.update', [$country->id]) : panel_route('countries.store') }}" method="POST">
      @csrf
      @method($country->id ? 'PUT' : 'POST')

      <x-common-form-input title="{{ __('panel/common.name') }}" name="name" :value="old('name', $country->name ?? '')" required placeholder="{{ __('panel/common.name') }}" />
      <x-common-form-input title="{{ __('panel/common.code') }}" name="code" :value="old('code', $country->code ?? '')" required placeholder="{{ __('panel/common.code') }}" />
      <x-common-form-image title="{{ __('panel/country.image') }}" name="image" :value="old('image', $country->image ?? '')" placeholder="image" />
      <x-common-form-input title="{{ __('panel/common.position') }}" name="position" :value="old('position', $country->slug ?? '')" required placeholder="{{ __('panel/common.position') }}" />
      <x-common-form-input title="{{ __('panel/common.status') }}" name="active" :value="old('active', $country->active ?? '')" placeholder="{{ __('panel/common.status') }}" />

      <div class="form-row mt-5 d-flex">
        <div class="wp-200 pe-2"></div>
        <button class="btn btn-primary" type="submit">{{ __('panel/common.btn_save') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('footer')
  <script>
  </script>
@endpush