<x-panel::form.row :title="$title" :required="$required">
  <div class="autocomplete-group-wrapper autocomplete-wrapper-{{ $id }}">
    <div class="mb-3">
      <select class="form-control select2-autocomplete-{{ $id }}" multiple="multiple" 
              name="{{ $name }}" @if ($required) required @endif style="width: 100%;">
        {{-- Existing items --}}
        @if(!empty($selectedItems))
          @foreach($selectedItems as $item)
            @if($item['id'] ?? '')
              <option value="{{ $item['id'] }}" selected="selected" 
                      data-image="{{ $item['image_small'] ?? $item['image'] ?? asset('images/placeholder.png') }}"
                      data-name="{{ $item['name'] ?? $item['title'] ?? '' }}">
                {{ $item['name'] ?? $item['title'] ?? '' }} (ID: {{ $item['id'] }})
              </option>
            @endif
          @endforeach
        @endif
      </select>
      <div class="invalid-feedback text-start">{{ $placeholder }}</div>
    </div>
  </div>
</x-panel::form.row>

@push('header')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  .select2-container .select2-selection--multiple {
    min-height: 40px;
    border: 1px solid #dcdfe6;
    border-radius: 4px;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #f4f4f5;
    border: 1px solid #e9e9eb;
    border-radius: 4px;
    color: #909399;
    padding: 3px 6px;
    margin-top: 5px;
    display: flex;
    align-items: center;
  }
  .select2-choice-image {
    width: 24px;
    height: 24px;
    border-radius: 3px;
    margin-right: 8px;
    object-fit: cover;
  }
  [dir="rtl"] .select2-choice-image {
    margin-right: 0;
    margin-left: 8px;
  }
  .select2-result-item {
    display: flex;
    align-items: center;
  }
  .select2-result-image {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    margin-right: 12px;
    object-fit: cover;
  }
  [dir="rtl"] .select2-result-image {
    margin-right: 0;
    margin-left: 12px;
  }
  .select2-result-details {
    display: flex;
    flex-direction: column;
  }
  .select2-result-name {
    font-weight: 600;
    font-size: 14px;
  }
  .select2-result-id {
    font-size: 11px;
    color: #888;
  }
</style>
@endpush

@push('footer')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(function () {
    const $select = $('.select2-autocomplete-{{ $id }}');

    function formatResult(item) {
      if (item.loading) {
        return item.text;
      }

      var imgUrl = item.image_small || item.image || item.element?.dataset?.image || '{{ asset("images/placeholder.png") }}';
      var name = item.name || item.title || item.element?.dataset?.name || item.text;
      var id = item.id || item.value;

      var $container = $(
        '<div class="select2-result-item">' +
          '<img src="' + imgUrl + '" class="select2-result-image" onerror="this.src=\'{{ asset("images/placeholder.png") }}\'" />' +
          '<div class="select2-result-details">' +
            '<div class="select2-result-name"></div>' +
            '<div class="select2-result-id"></div>' +
          '</div>' +
        '</div>'
      );

      $container.find('.select2-result-name').text(name);
      if (id && id !== name) {
        $container.find('.select2-result-id').text('ID: ' + id);
      }

      return $container;
    }

    function formatSelection(item) {
      if (!item.id) {
        return item.text;
      }
      var imgUrl = item.image_small || item.image || item.element?.dataset?.image || '{{ asset("images/placeholder.png") }}';
      var name = item.name || item.title || item.element?.dataset?.name || item.text.split(' (ID:')[0];
      
      var $container = $(
        '<div style="display: flex; align-items: center;">' +
          '<img src="' + imgUrl + '" class="select2-choice-image" onerror="this.src=\'{{ asset("images/placeholder.png") }}\'" />' +
          '<span></span>' +
        '</div>'
      );
      
      $container.find('span').text(name);
      return $container;
    }

    $select.select2({
      placeholder: "{{ $placeholder }}",
      allowClear: true,
      minimumInputLength: 0,
      dir: document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr',
      ajax: {
        url: '{{ $api }}/autocomplete',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            keyword: params.term || ''
          };
        },
        processResults: function (data) {
          var items = data.data || data;
          return {
            results: $.map(items, function(item) {
              return {
                id: item.id || item.value,
                text: item.name || item.title || '',
                name: item.name || item.title || '',
                image_small: item.image_small || item.image || '',
                image: item.image || item.image_small || ''
              }
            })
          };
        },
        cache: true
      },
      templateResult: formatResult,
      templateSelection: formatSelection
    });

    {{-- Fallback: Load names by IDs if selectedItems was not provided --}}
    @if(empty($selectedItems))
      var initialValues = @json($value);
      if (initialValues && initialValues.length > 0) {
        var baseApi = '{{ $api }}'.replace(/\/autocomplete$/, '');
        axios.get(baseApi + '/names?ids=' + initialValues.join(','))
          .then(function(res) {
            var data = res.data.data || res.data;
            $.each(data, function(idx, item) {
              var id = item.id || item.value;
              var name = item.name || item.title || '';
              var img = item.image_small || item.image || '';
              
              if ($select.find("option[value='" + id + "']").length) {
                return;
              }
              
              var newOption = new Option(name + ' (ID: ' + id + ')', id, true, true);
              newOption.dataset.image = img;
              newOption.dataset.name = name;
              $select.append(newOption).trigger('change');
            });
          })
          .catch(function(err) {
            console.warn('Failed to load initial selected items:', err);
          });
      }
    @endif
  });
</script>
@endpush
