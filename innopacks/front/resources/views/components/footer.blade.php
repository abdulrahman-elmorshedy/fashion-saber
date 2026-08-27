@hookinsert('layout.footer.top')

{{-- Newsletter Section --}}
@php
  $showFooterNewsletter = system_setting('newsletter_footer_enabled', true);
@endphp
@if($showFooterNewsletter)
  @php
    $bgColorStart = system_setting('newsletter_bg_color', '#1c1c1c');
    $bgColorEnd   = system_setting('newsletter_bg_color_end', '#000000');
    $textColor    = system_setting('newsletter_text_color', '#D4AF37');
  @endphp
  <div class="footer-newsletter-wrapper"
       style="background: linear-gradient(135deg, {{ $bgColorStart }} 0%, {{ $bgColorEnd }} 100%);">
    <div class="footer-newsletter" style="color: {{ $textColor }};">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 col-lg-5">
            <h4 class="newsletter-title" style="color: {{ $textColor }};">{{ __('front/newsletter.newsletter') }}</h4>
            <p class="newsletter-desc" style="color: {{ $textColor }}; opacity: 0.9;">{{ __('front/newsletter.newsletter_desc') }}</p>
          </div>
          <div class="col-12 col-lg-7">
            <form class="newsletter-form" action="{{ front_route('newsletter.subscribe') }}" method="POST">
              @csrf
              <input type="hidden" name="source" value="footer">
              <div class="input-group newsletter-input-group">
                <input type="email" name="email" class="form-control newsletter-email-input" 
                       placeholder="{{ __('front/newsletter.email_placeholder') }}" 
                       required>
                <button type="submit" class="btn btn-primary newsletter-submit-btn">
                  {{ __('front/newsletter.subscribe') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif

@hookupdate('layouts.footer.main')
<footer id="appFooter">
  <div class="footer-box">
    <div class="container">
      <div class="footer-top-links">
        <div class="row">
          <div class="col-12 col-md-4 footer-item">
            <div class="about">
              <div class="footer-link-title">
                <img src="{{ image_origin(system_setting('front_logo', 'images/logo.svg')) }}" style="height: 100px; filter: brightness(0) invert(1);" alt="Logo">
                <div class="footer-link-icon"><i class="bi bi-plus-lg"></i></div>
              </div>
              <div class="about-text footer-item-content">
                <p>
                  <b>{{ system_setting_locale('footer_about', system_setting_locale('meta_description', '')) }}</b>
                </p>
              </div>

              @php
                $supervisionList = system_setting('supervision_list', []);
                if (is_string($supervisionList)) {
                    $supervisionList = json_decode($supervisionList, true) ?: [];
                }
                $supervisionList = array_filter(is_array($supervisionList) ? $supervisionList : []);
              @endphp
              
              @if(count($supervisionList) > 0)
                <div class="supervision-container mt-4 pt-3 border-top border-secondary border-opacity-10">
                  <div class="small mb-2 text-uppercase letter-spacing-1" style="opacity: 0.5; font-weight: 700; font-size: 0.7rem;">{{ __('front/common.under_supervision') }}</div>
                  <div class="d-flex flex-wrap gap-2">
                    @foreach($supervisionList as $supervision)
                      @if(!empty($supervision['name']) || !empty($supervision['telephone']))
                        <div class="supervision-item px-2 py-1 rounded d-flex align-items-center" style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.2); transition: all 0.3s;">
                          @if(!empty($supervision['image']))
                            <div class="me-2" style="width: 30px; height: 30px; flex-shrink: 0;">
                              <img src="{{ image_origin($supervision['image']) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(0.5) contrast(1.1);" alt="Supervision">
                            </div>
                          @endif
                           <div>
                             @php
                               $supervisionName = $supervision['name'] ?? '';
                               if (is_array($supervisionName)) {
                                   $supervisionName = $supervisionName[front_locale_code()] ?? ($supervisionName[setting_locale_code()] ?? (array_values($supervisionName)[0] ?? ''));
                               }
                             @endphp
                             <div class="fw-bold" style="color: var(--bs-primary, #D4AF37); font-size: 0.85rem; line-height: 1.2;">{{ $supervisionName }}</div>
                             @if(!empty($supervision['telephone']))
                               <div class="small opacity-75" style="font-family: monospace; font-size: 0.75rem;" dir="ltr">
                                 <i class="bi bi-telephone-fill me-1" style="font-size: 0.7rem;"></i>{{ $supervision['telephone'] }}
                               </div>
                             @endif
                          </div>
                        </div>
                      @endif
                    @endforeach
                  </div>
                </div>
              @endif
              
              @hookinsert('layout.footer.about.after')
            </div>
          </div>
          <div class="col-12 col-md-8">
            <div class="row">
              <div class="col-12 col-md-3 footer-item">
                <div class="footer-links">
                  <div class="footer-link-title">
                    <span>{{ __('front/common.products') }}</span>
                    <div class="footer-link-icon"><i class="bi bi-plus-lg"></i></div>
                  </div>
                  <ul class="footer-item-content">
                    @foreach($footerMenus['categories'] as $item)
                      <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </div>
              @if(count($footerMenus['catalogs'] ?? []) > 0)
              <div class="col-12 col-md-3 footer-item">
                <div class="footer-links">
                  <div class="footer-link-title">
                    <span>{{ __('front/common.news') }}</span>
                    <div class="footer-link-icon"><i class="bi bi-plus-lg"></i></div>
                  </div>
                  <ul class="footer-item-content">
                    @foreach($footerMenus['catalogs'] as $item)
                      <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </div>
              @endif
              <div class="col-12 col-md-3 footer-item">
                <div class="footer-links">
                  <div class="footer-link-title">
                    <span>{{ __('front/common.pages') }}</span>
                    <div class="footer-link-icon"><i class="bi bi-plus-lg"></i></div>
                  </div>
                  <ul class="footer-item-content">
                    @foreach($footerMenus['pages'] as $item)
                      <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </div>
              <div class="col-12 col-md-3 footer-item">
                <div class="footer-links">
                  <div class="footer-link-title">
                    <span>{{ __('front/common.specials') }}</span>
                    <div class="footer-link-icon"><i class="bi bi-plus-lg"></i></div>
                  </div>
                  <ul class="footer-item-content">
                    @foreach($footerMenus['specials'] as $item)
                      <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bottom-box">
        <div class="row">
          <div class="col-md-6">
            <div class="left-links">
              <span class="copyright-text">
                @php
                  $frontCopyright = system_setting_locale('front_copyright_text', 'All Rights Reserved');
                  $frontLink = system_setting('front_copyright_link');
                @endphp
                @if($frontLink)
                  <a href="{{ $frontLink }}" target="_blank">{{ $frontCopyright }}</a>
                @else
                  {{ $frontCopyright }}
                @endif
                @if(system_setting('icp_number', ''))
                  <a href="https://beian.miit.gov.cn" class="ms-2" target="_blank">{{ system_setting('icp_number', '') }}</a>
                @endif
              </span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-md-end align-items-center gap-4 flex-wrap">
              @php
                $social_links = system_setting('social_media_links', []);
                if (!is_array($social_links)) {
                    $social_links = json_decode($social_links, true) ?: [];
                }
              @endphp
              @if(!empty($social_links))
                <div class="social-icons fs-5">
                  @foreach($social_links as $social)
                    @if(!empty($social['url']))
                      <a href="{{ $social['url'] }}" target="_blank" class="text-secondary ms-3 text-decoration-none" style="transition: color 0.3s;" onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color=''">
                        <i class="bi {{ $social['icon'] ?? 'bi-link' }}"></i>
                      </a>
                    @endif
                  @endforeach
                </div>
              @endif
              @php
                $payment_icons = system_setting('payment_icons', []);
                if (is_string($payment_icons)) {
                    $payment_icons = json_decode($payment_icons, true) ?: [];
                }
                $payment_icons = array_filter(is_array($payment_icons) ? $payment_icons : []);
              @endphp
              @if(!empty($payment_icons))
                <div class="payment-icon m-0">
                  @foreach($payment_icons as $icon)
                    <img src="{{ image_origin($icon) }}" class="img-fluid" style="max-height: 25px; margin-left: 10px; filter: grayscale(1) opacity(0.8); transition: all 0.3s;" onmouseover="this.style.filter='grayscale(0) opacity(1)'" onmouseout="this.style.filter='grayscale(1) opacity(0.8)'">
                  @endforeach
                </div>
              @endif
            </div>
          </div>
      </div>
      </div>
    </div>
  </div>
</footer>
@endhookupdate

@hookinsert('layout.footer.bottom')

@push('footer')
<script>
  $(document).ready(function() {
    // Newsletter subscription form handler
    $('.newsletter-form').on('submit', function(e) {
      e.preventDefault();
      
      const form = $(this);
      const emailInput = form.find('input[name="email"]');
      const submitBtn = form.find('button[type="submit"]');
      const originalBtnText = submitBtn.html();
      
      // Basic validation
      if (!emailInput.val() || !emailInput[0].checkValidity()) {
        emailInput[0].reportValidity();
        return false;
      }
      
      // Disable button and show loading
      submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
      
      // Prepare form data
      const formData = new URLSearchParams();
      formData.append('email', emailInput.val());
      formData.append('source', form.find('input[name="source"]').val() || 'footer');
      formData.append('_token', form.find('input[name="_token"]').val());
      
      // Submit via AJAX
      axios.post(form.attr('action'), formData.toString())
        .then(function(res) {
          if (res.success) {
            inno.alert({
              msg: res.message || '{{ __('front/newsletter.subscribe_success') }}',
              type: 'success'
            });
            // Clear form
            emailInput.val('');
          } else {
            inno.alert({
              msg: res.message || '{{ __('front/newsletter.subscribe_failed') }}',
              type: 'danger'
            });
          }
        })
        .catch(function(error) {
          const errorMsg = error.response?.data?.message || '{{ __('front/newsletter.subscribe_failed') }}';
          inno.alert({
            msg: errorMsg,
            type: 'danger'
          });
        })
        .finally(function() {
          // Re-enable button
          submitBtn.prop('disabled', false).html(originalBtnText);
        });
      
      return false;
    });
  });
</script>
@endpush

@if (system_setting('js_code', ''))
  {!! system_setting('js_code', '') !!}
@endif