<!-- Manual Payment Methods Settings -->
<div class="tab-pane fade" id="tab-setting-payments">
  <div class="container">
    <div class="row">
      <div class="ml-4" id="payments-form">
        <div class="row col-10">
          <table class="table table-response align-middle table-bordered">
            <thead>
              <tr>
                <th class="col-4">{{ __('panel/common.name') }}</th>
                <th class="col-6">{{ __('panel/product.content') }}</th>
                <th class="col-1"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item,index) in text" :key="index">
                <td>
                  <el-input v-model="item.name" placeholder="{{ __('panel/common.name') }}" />
                </td>
                <td>
                  <el-input v-model="item.description" type="textarea" :rows="1" placeholder="{{ __('panel/product.content') }}" />
                </td>
                <td class="text-center">
                  <i class="bi bi-x-circle text-danger cursor-pointer" @click="removeInput(index)"></i>
                </td>
              </tr>
              <tr>
                <td colspan="2"></td>
                <td class="text-center"><i @click="addInput" class="bi bi-plus-circle-fill text-primary fs-4 cursor-pointer"></i></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <input type="hidden" name="custom_payments" :value="JSON.stringify(text)" />
</div>

@push('footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof Vue === 'undefined' || typeof ElementPlus === 'undefined') {
        return;
      }

      const {createApp, ref, watch, onMounted} = Vue;

      let paymentsData = [];
      try {
        const rawData = @json(system_setting('custom_payments'));
        paymentsData = Array.isArray(rawData) ? rawData : [];
      } catch (error) {
        paymentsData = [];
      }

      const paymentsApp = createApp({
        setup() {
          const text = ref(paymentsData.map(item => ({
            name: item.name || '',
            description: item.description || ''
          })));

          if (text.value.length === 0) {
            text.value.push({ name: '', description: '' });
          }

          const addInput = () => {
            text.value.push({ name: '', description: '' });
          };

          const removeInput = (index) => {
            if (text.value.length > 1) {
              text.value.splice(index, 1);
            } else {
              text.value[0] = { name: '', description: '' };
            }
          };

          watch(text, (newValue) => {
            const input = document.querySelector('input[name="custom_payments"]');
            if (input) {
              const filteredData = newValue.filter(item => item.name && item.name.trim() !== '');
              input.value = JSON.stringify(filteredData);
            }
          }, { deep: true });

          onMounted(() => {
            const input = document.querySelector('input[name="custom_payments"]');
            if (input) {
              const filteredData = text.value.filter(item => item.name && item.name.trim() !== '');
              input.value = JSON.stringify(filteredData);
            }
          });

          return { text, addInput, removeInput };
        }
      });

      paymentsApp.use(ElementPlus);
      paymentsApp.mount('#payments-form');
    });
  </script>
@endpush
