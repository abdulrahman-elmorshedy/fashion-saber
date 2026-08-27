<!-- Payment Icons Settings -->
<div class="tab-pane fade" id="tab-setting-payment-icons">
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('panel/setting.payment_icons_settings') }}</h5>
      <p class="text-muted small mb-0">{{ __('panel/setting.payment_icons_settings_desc') }}</p>
    </div>
    <div class="card-body">
      @php
        $paymentIcons = system_setting('payment_icons');
        if (is_string($paymentIcons)) {
            $paymentIcons = json_decode($paymentIcons, true) ?: [];
        }
        $paymentIcons = is_array($paymentIcons) ? $paymentIcons : [];
      @endphp
      
      <div class="row">
        @foreach(range(0, 11) as $index)
          <div class="col-6 col-md-2 mb-4 text-center">
            <x-common-form-image 
              :title="__('panel/setting.payment_icon_title', ['index' => $index + 1])" 
              name="payment_icons[{{ $index }}]" 
              :value="$paymentIcons[$index] ?? ''"
            />
          </div>
        @endforeach
      </div>
      <p class="text-muted small mt-2"><i class="bi bi-info-circle"></i> {{ __('panel/setting.payment_icons_hint') }}</p>
    </div>
  </div>
</div>
