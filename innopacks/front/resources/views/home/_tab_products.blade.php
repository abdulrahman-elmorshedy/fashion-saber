@foreach ($products as $product)
  <div class="col-6 col-md-4 col-lg-3">
    @include('shared.product')
  </div>
@endforeach
