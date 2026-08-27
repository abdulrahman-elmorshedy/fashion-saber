@php
  $routePrefix      = $routePrefix ?? panel_name();
  $layoutView       = $layoutView  ?? 'panel::layouts.app';
  $showBulkActions  = $showBulkActions ?? true;
  $showCopyAction   = $showCopyAction ?? true;
@endphp
@extends($layoutView)
@section('body-class', 'page-product')
@section('title', __('panel/menu.products'))

@section('page-title-right')
  <a href="{{ route($routePrefix . '.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-square"></i> {{
  __('common/base.create') }}</a>
@endsection

@section('content')
  <div class="card h-min-600" id="app">
    <div class="card-body">

      <x-panel-data-data-search
        :action="route($routePrefix . '.products.index')"
        :searchFields="$searchFields ?? []"
        :filters="$filterButtons ?? []"
        :enableDateRange="true"
        :ajax="true"
      />

      <div class="mb-3 p-3 bg-light rounded border" id="products-toolbar">
        <div class="d-flex d-md-flex flex-column flex-md-row justify-content-md-between align-items-start gap-3 w-100">
          <div class="d-flex align-items-center gap-2">
            @if($showBulkActions)
              @include('panel::products.bulk.actions')
            @endif
            <button type="button" class="btn btn-danger btn-sm" @click="destroyAllProducts">
              <i class="bi bi-trash"></i> {{ app()->getLocale() == 'ar' ? 'حذف جميع المنتجات' : 'Delete All Products' }}
            </button>
          </div>

          <div class="toolbar-right d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 gap-md-3">
            <x-panel-data-info :paginator="$products ?? null"/>
            <x-panel-data-sorter :options="$sortOptions ?? []"/>
          </div>
        </div>
      </div>

      @if ($products->count())
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
            <tr>
              @if($showBulkActions)<th><input class="form-check-input" @click="checkAll" type="checkbox" ref="checkAllBox"></th>@endif
              <th>{{ __('common/base.id') }}</th>
              <th class="wp-100">{{ __('common/base.image') }}</th>
              <th>{{ __('common/base.name') }}</th>
              <th>{{ __('panel/product.price') }}</th>
              <th>{{ __('panel/product.quantity') }}</th>
              <th>{{ __('panel/common.created_and_updated') }}</th>
              <th>{{ __('panel/common.active') }}</th>
              @hookinsert('panel.product.list.table.header.after')
              <th>{{ __('panel/common.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
              <tr>
                @if($showBulkActions)<td><input class="form-check-input" type="checkbox" :value="{{ $product->id }}" v-model="checkedIds">
                </td>@endif
                <td>{{ $product->id }}</td>
                <td>
                  <div class="d-flex align-items-center justify-content-center wh-50 border">
                    <a href="{{ $product->url }}" target="_blank">
                      <img src="{{ $product->image_url }}" class="img-fluid"
                           alt="{{ $product->fallbackName() }}">
                    </a>
                  </div>
                </td>
                <td>
                  <a href="{{ $product->url }}" class="text-decoration-none" target="_blank" data-bs-toggle="tooltip"
                     title="{{ $product->fallbackName() }}">
                    {{ sub_string($product->fallbackName(),20) }}
                  </a>
                  @if($product->isMultiple())
                    &nbsp;<span class="text-bg-success px-1">M</span>
                  @endif
                </td>
                <td>{{ currency_format($product->masterSku->price ?? 0) }}</td>
                <td>{{ $product->totalQuantity() }}</td>
                <td>
                    <div class="d-flex flex-column">
                        <div class="small">{{ \Carbon\Carbon::parse($product->created_at)->format('Y-m-d') }}</div>
                        <div class="small text-muted">{{ \Carbon\Carbon::parse($product->updated_at)->format('Y-m-d') }}</div>
                    </div>
                </td>
                <td>@include('panel::shared.list_switch', ['value' => $product->active, 'url' => route($routePrefix . '.products.active', $product->id)])</td>
                @hookinsert('panel.product.list.table.row.after', $product)
                <td>
                  <div class="d-flex gap-2">
                    <div>
                      <a href="{{ route($routePrefix . '.products.edit', [$product->id]) }}">
                        <el-button size="small" plain type="primary">{{
                        __('common/base.edit')}}</el-button>
                      </a>
                    </div>
                    @if($showCopyAction)
                    <div>
                      <a href="{{ route($routePrefix . '.products.copy', [$product->id]) }}">
                        <el-button size="small" plain type="warning">{{
                        __('common/base.copy')}}</el-button>
                      </a>
                    </div>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        {{ $products->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
      @else
        <x-common-no-data/>
      @endif

      {{-- Bulk Actions Modals --}}
      @if($showBulkActions)
        @include('panel::products.bulk.modals')
      @endif

    </div>
  @endsection

@push('footer')

  <script>
    const {createApp, ref, reactive, watch} = Vue;
    const {ElMessageBox, ElMessage, ElLoading} = ElementPlus;

    function initProductVueApp() {
      if (!document.getElementById('app')) return;
      
      const app = createApp({
        setup() {
          // ... (existing setup code)
          const deleteForm = ref(null);
          const checkedIds = ref([]);
          const checkAllBox = ref(null);
          const categoryCascaderOptions = @json($categoryOptions);

          const dialogVisible = ref({
            price: false,
            categories: false,
            quantity: false,
            publish: false,
            unpublish: false,
          });

          const bulkFormData = ref({
            price: {mode: 'reset', value: null},
            categories: [],
            quantity: {mode: 'reset', value: null},
          });

          const open = (index) => {
            ElMessageBox.confirm(
              '{{ __("common/base.hint_delete") }}',
              '{{ __("common/base.cancel") }}',
              {
                confirmButtonText: '{{ __("common/base.confirm")}}',
                cancelButtonText: '{{ __("common/base.cancel")}}',
                type: 'warning',
              }
            ).then(() => {
              deleteForm.value.action = '{{ url('/') }}/' + '{{ $routePrefix }}' + '/products/' + index;
              deleteForm.value.submit();
            }).catch(() => {
            });
          };

          const deleteAll = () => {
            if (checkedIds.value.length === 0) {
              ElMessage({
                type: 'warning',
                message: '{{ __("panel/common.select_items") }}',
              });
              return;
            }

            ElMessageBox.confirm(
              `{{ __("panel/product.bulk_delete_confirm") }}（${checkedIds.value.length} {{ __("panel/product.items") }}）`,
              '{{ __("common/base.hint_delete") }}',
              {
                confirmButtonText: '{{ __("common/base.confirm")}}',
                cancelButtonText: '{{ __("common/base.cancel")}}',
                type: 'warning',
                dangerouslyUseHTMLString: true,
              }
            ).then(() => {
              const loading = ElLoading.service({
                lock: true,
                text: '{{ __("panel/product.deleting") }}',
                background: 'rgba(0, 0, 0, 0.7)'
              });

              axios.delete("{{ $showBulkActions ? route($routePrefix . '.products.destroy.batch') : '' }}", {
                data: {
                  ids: checkedIds.value
                }
              }).then(response => {
                const res = response.data;
                ElMessage({
                  type: 'success',
                  message: res.message,
                });
                if (typeof ajaxLoad === 'function') {
                    ajaxLoad(window.location.href);
                } else {
                    setTimeout(() => window.location.reload(), 1000);
                }
              }).catch(error => {
                let errorMessage = '{{ __("common/base.error") }}';
                if (error.response?.data?.message) {
                  errorMessage = error.response.data.message;
                }
                ElMessage({
                  type: 'error',
                  message: errorMessage,
                });
              }).finally(() => {
                loading.close();
              });
            }).catch(() => {});
          };

          const destroyAllProducts = () => {
            ElMessageBox.confirm(
              '{{ app()->getLocale() == "ar" ? "هل أنت متأكد من حذف جميع المنتجات نهائياً من المتجر؟ لا يمكن التراجع عن هذا الإجراء." : "Are you sure you want to delete ALL products from the store permanently? This action cannot be undone." }}',
              '{{ app()->getLocale() == "ar" ? "تحذير خطير" : "Critical Warning" }}',
              {
                confirmButtonText: '{{ app()->getLocale() == "ar" ? "نعم، احذف الكل" : "Yes, Delete All" }}',
                cancelButtonText: '{{ __("common/base.cancel")}}',
                type: 'error',
                confirmButtonClass: 'el-button--danger',
              }
            ).then(() => {
              const loading = ElLoading.service({
                lock: true,
                text: '{{ app()->getLocale() == "ar" ? "جاري حذف جميع المنتجات..." : "Deleting all products..." }}',
                background: 'rgba(0, 0, 0, 0.7)'
              });

              axios.delete("{{ route($routePrefix . '.products.destroy_all') }}")
                .then(response => {
                  const res = response.data;
                  ElMessage({
                    type: 'success',
                    message: res.message,
                  });
                  if (typeof ajaxLoad === 'function') {
                      ajaxLoad(window.location.href);
                  } else {
                      setTimeout(() => window.location.reload(), 1000);
                  }
                }).catch(error => {
                  ElMessage({
                    type: 'error',
                    message: error.response?.data?.message || 'Error',
                  });
                }).finally(() => {
                  loading.close();
                });
            }).catch(() => {});
          };

          const bulkAction = (action) => {
            if (checkedIds.value.length === 0) {
              ElMessage({
                type: 'warning',
                message: '{{ __("panel/common.select_items") }}',
              });
              return;
            }
            if (dialogVisible.value.hasOwnProperty(action)) {
              dialogVisible.value[action] = true;
            }
          };

          const submitBulkUpdate = (action) => {
            if (checkedIds.value.length === 0) return;

            const payload = {
              action: action,
              ids: checkedIds.value,
              data: bulkFormData.value[action] || {}
            };

            const loading = ElLoading.service({
              lock: true,
              text: '{{ __("panel/product.processing") }}',
              background: 'rgba(0, 0, 0, 0.7)'
            });

            axios.post("{{ $showBulkActions ? route($routePrefix . '.products.bulk.update') : '' }}", payload).then(response => {
                const res = response.data;
                if (res && res.success) {
                  ElMessage({ type: 'success', message: res.message });
                  dialogVisible.value[action] = false;
                  if (typeof ajaxLoad === 'function') {
                      ajaxLoad(window.location.href);
                  } else {
                      setTimeout(() => window.location.reload(), 1000);
                  }
                }
              }).catch(error => {
                ElMessage({ type: 'error', message: error.response?.data?.message || 'Error' });
              })
              .finally(() => {
                loading.close();
              });
          };

          const checkAll = () => {
            if (checkAllBox.value.checked) {
              checkedIds.value = Array.from(document.querySelectorAll('input[type="checkbox"][value]')).map(el => parseInt(el.value));
            } else {
              checkedIds.value = [];
            }
          };

          watch(checkedIds, (newVal) => {
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"][value]');
            if (!allCheckboxes.length) return;
            if (newVal.length === allCheckboxes.length) {
              checkAllBox.value.checked = true;
              checkAllBox.value.indeterminate = false;
            } else if (newVal.length > 0) {
              checkAllBox.value.indeterminate = true;
              checkAllBox.value.checked = false;
            } else {
              checkAllBox.value.indeterminate = false;
              checkAllBox.value.checked = false;
            }
          });

          return {
            open, deleteForm, checkAll, checkedIds, checkAllBox, deleteAll,
            bulkAction, dialogVisible, bulkFormData, submitBulkUpdate,
            categoryCascaderOptions, destroyAllProducts
          };
        }
      });
      app.use(ElementPlus);
      app.mount('#app');
    }

    initProductVueApp();
    $(document).on('ajaxPageLoaded', initProductVueApp);
    
    // Initialize Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
  </script>

@endpush
