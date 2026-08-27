<div class="custom-payment card w-max-700 m-auto h-min-300 shadow-sm border-0">
  <div class="card-body p-4">
    <div class="text-center mb-4">
      <div class="display-6 text-success mb-2">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <div class="fs-4 fw-bold text-dark">{{ __('front/checkout.order_success') }}</div>
      <p class="text-muted">{{ __('front/checkout.order_number') }}: <span class="fw-bold">#{{ $order->number }}</span></p>
    </div>

    <div class="alert alert-info border-0 bg-light-subtle mb-4">
      <div class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-2"></i>{{ __('front/checkout.billing_methods') }}</div>
      <div class="fs-5 text-primary mb-1">{{ $order->billing_method_name }}</div>
      @if(isset($custom_payment['description']) && $custom_payment['description'])
        <div class="text-muted white-space-pre-line mt-2">
          {!! nl2br(e($custom_payment['description'])) !!}
        </div>
      @endif
    </div>

    <div class="table-responsive mb-4">
      <table class="table table-sm table-borderless bg-light rounded-3">
        <tbody>
          <tr>
            <td class="text-muted ps-3 py-2">{{ __('front/order.order_time') }}</td>
            <td class="text-end pe-3 py-2 fw-medium">{{ $order->created_at->format('Y-m-d H:i') }}</td>
          </tr>
          <tr>
            <td class="text-muted ps-3 py-2">{{ __('front/order.order_total') }}</td>
            <td class="text-end pe-3 py-2 fw-bold text-primary">{{ currency_format($order->total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="d-grid gap-2">
      <button type="button" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm btn-confirm-payment">
        {{ __('common/base.confirm') }}
      </button>
      <a href="{{ front_route('orders.numberShow', ['number' => $order->number]) }}" class="btn btn-link text-decoration-none text-muted">
        {{ __('front/order.view_order') }}
      </a>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('.btn-confirm-payment').click(function () {
      location.href = '{{ front_route('payment.success') }}?order_number={{ $order->number }}';
    });
  });
</script>

<style>
  .white-space-pre-line {
    white-space: pre-line;
  }
</style>
