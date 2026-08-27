<div class="tab-pane fade" id="tab-setting-social-media">
  <table class="table table-bordered align-middle" id="table-social-media">
    <thead>
      <tr>
        <th width="30%">{{ __('panel/setting.social_media_icon') }}</th>
        <th width="50%">{{ __('panel/setting.social_media_url') }}</th>
        <th class="text-end" width="20%"></th>
      </tr>
    </thead>
    <tbody>
      @php
        $social_links = old('social_media_links', system_setting('social_media_links', [])) ?: [];
      @endphp
      @foreach ($social_links as $index => $social)
        <tr>
          <td>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi {{ $social['icon'] ?? 'bi-link' }}"></i></span>
              <input type="text" class="form-control" name="social_media_links[{{ $index }}][icon]" value="{{ $social['icon'] ?? '' }}" placeholder="bi-facebook">
            </div>
          </td>
          <td>
            <input type="url" class="form-control form-control-sm" name="social_media_links[{{ $index }}][url]" value="{{ $social['url'] ?? '' }}" placeholder="https://facebook.com/...">
          </td>
          <td class="text-end">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">{{ __('common/base.delete') }}</button>
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="text-end">
          <button type="button" class="btn btn-primary btn-sm" onclick="addSocialMedia()">{{ __('panel/setting.social_media_add') }}</button>
        </td>
      </tr>
    </tfoot>
  </table>
</div>

@push('footer')
  <script>
    let socialMediaIndex = {{ count($social_links) }};
    function addSocialMedia() {
      const html = `
        <tr>
          <td>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-link"></i></span>
              <input type="text" class="form-control" name="social_media_links[${socialMediaIndex}][icon]" value="" placeholder="bi-facebook">
            </div>
          </td>
          <td>
            <input type="url" class="form-control form-control-sm" name="social_media_links[${socialMediaIndex}][url]" value="" placeholder="https://...">
          </td>
          <td class="text-end">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">{{ __('common/base.delete') }}</button>
          </td>
        </tr>
      `;
      document.querySelector('#table-social-media tbody').insertAdjacentHTML('beforeend', html);
      socialMediaIndex++;
    }
  </script>
@endpush
