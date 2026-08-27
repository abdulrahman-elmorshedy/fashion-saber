<div class="tab-pane fade" id="tab-setting-supervision">
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('panel/setting.supervision_settings') }}</h5>
      <p class="text-muted small mb-0">{{ __('panel/setting.supervision_settings_desc') }}</p>
    </div>
    <div class="card-body">
      <table class="table table-bordered align-middle" id="table-supervision">
        <thead>
          <tr>
            <th width="20%">{{ __('panel/common.image') }}</th>
            <th width="35%">{{ __('panel/setting.supervision_name') }}</th>
            <th width="35%">{{ __('panel/setting.supervision_telephone') }}</th>
            <th class="text-end" width="10%"></th>
          </tr>
        </thead>
        <tbody>
          @php
            $supervisionList = old('supervision_list', system_setting('supervision_list', []));
            if (!is_array($supervisionList)) {
                $supervisionList = json_decode($supervisionList, true) ?: [];
            }
          @endphp
          @foreach ($supervisionList as $index => $supervision)
            <tr>
              <td>
                <x-common-form-image 
                  title="" 
                  name="supervision_list[{{ $index }}][image]" 
                  :value="$supervision['image'] ?? ''"
                />
              </td>
              <td>
                @foreach($locales as $locale)
                  @php
                    $code = $locale['code'];
                    $name = $locale['name'];
                  @endphp
                  <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-light border-end-0"><img src="{{ asset('images/flag/'. $code . '.png') }}" width="16"></span>
                    <input type="text" class="form-control border-start-0" name="supervision_list[{{ $index }}][name][{{ $code }}]" value="{{ $supervision['name'][$code] ?? (is_string($supervision['name'] ?? '') ? $supervision['name'] : '') }}" placeholder="{{ $name }}">
                  </div>
                @endforeach
              </td>
              <td>
                <input type="text" class="form-control form-control-sm" name="supervision_list[{{ $index }}][telephone]" value="{{ $supervision['telephone'] ?? '' }}" placeholder="{{ __('panel/setting.supervision_telephone_placeholder') }}">
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">{{ __('common/base.delete') }}</button>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" class="text-end">
              <button type="button" class="btn btn-primary btn-sm" onclick="addSupervision()">إضافة جهة إشراف</button>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

@push('footer')
  <script>
    let supervisionIndex = {{ count($supervisionList) }};
    const activeLocales = @json($locales);
    function addSupervision() {
      let nameInputsHtml = '';
      activeLocales.forEach(locale => {
        const code = locale.code;
        const name = locale.name;
        nameInputsHtml += `
          <div class="input-group input-group-sm mb-1">
            <span class="input-group-text bg-light border-end-0"><img src="/images/flag/${code}.png" width="16"></span>
            <input type="text" class="form-control border-start-0" name="supervision_list[${supervisionIndex}][name][${code}]" value="" placeholder="${name}">
          </div>
        `;
      });

      const html = `
        <tr>
          <td>
             <div class="form-image-container" data-name="supervision_list[${supervisionIndex}][image]">
                <input type="hidden" name="supervision_list[${supervisionIndex}][image]" value="">
                <div class="image-preview border rounded d-flex align-items-center justify-content-center bg-light" style="width: 40px; height: 40px; cursor: pointer;" onclick="inno.fileManagerIframe((file) => { $(this).find('img').remove(); $(this).append('<img src=\"' + file.url + '\" style=\"width:100%;height:100%;object-fit:cover;\">'); $(this).prev('input').val(file.url); })">
                   <i class="bi bi-plus text-muted"></i>
                </div>
             </div>
          </td>
          <td>
            ${nameInputsHtml}
          </td>
          <td>
            <input type="text" class="form-control form-control-sm" name="supervision_list[${supervisionIndex}][telephone]" value="" placeholder="{{ __('panel/setting.supervision_telephone_placeholder') }}">
          </td>
          <td class="text-end">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">{{ __('common/base.delete') }}</button>
          </td>
        </tr>
      `;
      document.querySelector('#table-supervision tbody').insertAdjacentHTML('beforeend', html);
      supervisionIndex++;
    }
  </script>
@endpush
