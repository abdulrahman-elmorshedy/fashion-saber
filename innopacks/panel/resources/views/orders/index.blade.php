@php
  $routePrefix       = $routePrefix ?? panel_name();
  $layoutView        = $layoutView ?? 'panel::layouts.app';
  $showExport        = $showExport ?? true;
  $showCustomerLink  = $showCustomerLink ?? true;
@endphp
@extends($layoutView)
@section('body-class', '')

@section('title', __('panel/menu.orders'))

@section('page-title-right')
  <button type="button" class="btn btn-danger btn-sm" id="btn-destroy-all">
    <i class="bi bi-trash"></i> {{ app()->getLocale() == 'ar' ? 'حذف جميع الطلبات' : 'Delete All Orders' }}
  </button>
  @hookinsert('panel.orders.index.title.right')
@endsection

@section('content')
  <div class="card h-min-600">
    <div class="card-body">
      <x-panel-data-data-search
        :action="route($routePrefix . '.orders.index')"
        :searchFields="$searchFields ?? []"
        :filters="$filterButtons ?? []"
        :enableDateRange="true"
        :ajax="true"
      >
        <x-slot name="toolbarRight">
          <button type="button" class="btn btn-sm btn-outline-danger me-2 d-none" id="btn-bulk-destroy">
            <i class="bi bi-trash"></i> {{ app()->getLocale() == 'ar' ? 'حذف المحدد' : 'Delete Selected' }}
            <span class="badge bg-danger ms-1" id="selected-count">0</span>
          </button>
          @if($showExport)
          <a href="{{ route($routePrefix . '.orders.export.batch') }}?{{ http_build_query(request()->query()) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-file-earmark-excel"></i> {{ __('panel/common.export') }}
          </a>
          @endif
        </x-slot>
      </x-panel-data-data-search>
      @hookinsert('panel.orders.index.criteria.after')

      @if ($orders->count())
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
               @hookinsert('panel.orders.index.header.top')
                <td width="30"><input type="checkbox" id="check-all" class="form-check-input"></td>
                <td>{{ __('common/base.id') }}</td>
                <td>{{ __('panel/order.number') }}</td>
                <td>{{ __('panel/order.order_items') }}</td>
                <td>{{ __('panel/order.customer_name') }}</td>
                <td>{{ __('panel/order.shipping_method_name') }}</td>
                <td>{{ __('panel/order.billing_method_name') }}</td>
                <td>{{ __('panel/order.total') }}</td>
                <td>{{ __('panel/order.status') }}</td>
                @hookinsert('panel.orders.index.header.extra')
                <td>{{ __('panel/order.created_at') }}</td>
                <td>{{ __('panel/common.actions') }}</td>
              </tr>
            </thead>
            <tbody>
              @foreach ($orders as $item)
                <tr>
                 @hookinsert('panel.orders.index.row.top', $item)
                  <td><input type="checkbox" class="form-check-input order-checkbox" value="{{ $item->id }}"></td>
                  <td>{{ $item->id }}</td>
                  <td>{{ $item->number }} {{ $item->id == $item->parent_id ? 'M' : '' }}</td>
                  <td>
                    <div class="d-flex">
                      @foreach ($item->items->take(5) as $product)
                        <div class="wh-30 overflow-hidden border border-1 me-1">
                          <img src="{{ image_resize($product->image) }}" alt="{{ $product->name }}" class="img-fluid">
                        </div>
                      @endforeach
                    </div>
                  </td>
                  <td>
                    @if ($item->customer_id > 0 && $showCustomerLink)
                      <a href="{{ route($routePrefix . '.customers.edit', $item->customer_id) }}" class="text-decoration-none"
                      target="_blank">
                        {{ $item->customer_name }}
                      </a>
                    @else
                      {{ $item->customer_name }}
                    @endif
                  </td>
                  <td>{{ $item->shipping_method_name }}</td>
                  <td>{{ $item->billing_method_name }}</td>
                  <td>{{ $item->total_format }}</td>
                  <td><span class="badge bg-{{ $item->status_color }}">{{ $item->status_format }}</span></td>
                  @hookinsert('panel.orders.index.row.extra', $item)
                  <td>{{ $item->created_at }}</td>
                  <td>
                    <a href="{{ route($routePrefix . '.orders.show', [$item->id]) }}"
                      class="btn btn-sm btn-outline-primary">{{ __('common/base.view') }}</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        {{ $orders->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
      @else
        <x-common-no-data />
      @endif
    </div>
  </div>
@endsection

@push('footer')
  <script>
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const bulkBtn = document.getElementById('btn-bulk-destroy');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkBtn() {
        const checked = document.querySelectorAll('.order-checkbox:checked');
        selectedCountSpan.innerText = checked.length;
        if (checked.length > 0) {
            bulkBtn.classList.remove('d-none');
        } else {
            bulkBtn.classList.add('d-none');
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkBtn();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBtn);
    });

    // Bulk Destroy
    $(document).on('click', '#btn-bulk-destroy', function() {
        const checked = $('.order-checkbox:checked');
        const ids = checked.map(function() { return this.value; }).get();
        const msg = "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من حذف الطلبات المحددة؟' : 'Are you sure you want to delete selected orders?' }}";
        
        if (confirm(msg)) {
            const loading = layer.load(1);
            axios.delete("{{ route($routePrefix . '.orders.bulk_destroy') }}", {
                data: { ids: ids }
            })
            .then(response => {
                const res = response.data;
                layer.close(loading);
                if (res.success) {
                    layer.msg(res.message, {icon: 1});
                    // Refresh data via AJAX if possible, or reload
                    if (typeof ajaxLoad === 'function') {
                        ajaxLoad(window.location.href);
                    } else {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } else {
                    layer.msg(res.message || 'Error', {icon: 2});
                }
            })
            .catch(error => {
                layer.close(loading);
                layer.msg(error.response?.data?.message || 'Error', {icon: 2});
            });
        }
    });

    $(document).on('click', '#btn-destroy-all', function() {
        const msg = "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من حذف جميع الطلبات نهائياً؟ لا يمكن التراجع عن هذا الإجراء.' : 'Are you sure you want to delete ALL orders permanently? This action cannot be undone.' }}";
        if (confirm(msg)) {
            const loading = layer.load(1);
            axios.delete("{{ route($routePrefix . '.orders.destroy_all') }}")
            .then(response => {
                const res = response.data;
                layer.close(loading);
                if (res.success) {
                    layer.msg(res.message, {icon: 1});
                    if (typeof ajaxLoad === 'function') {
                        ajaxLoad(window.location.href);
                    } else {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } else {
                    layer.msg(res.message || 'Error', {icon: 2});
                }
            })
            .catch(error => {
                layer.close(loading);
                layer.msg(error.response?.data?.message || 'Error', {icon: 2});
            });
        }
    });
  </script>
  @hookinsert('panel.orders.footer.script.bottom')
@endpush
