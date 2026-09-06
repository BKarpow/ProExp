<template>
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="m-0 fw-bold text-primary">Список продажів взуття</h5>
    </div>
    <NavShoesMenu/>
    <div class="card-body p-0">
      <!-- Таблиця продажів -->
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Дата та час</th>
              <th>Модель</th>
              <th>Продавець (User)</th>
              <th>Розмір</th>
              <th>Ціна</th>
              <!-- Дії показуємо, якщо є можливість редагування або дозволено видалення -->
              <th class="text-end">Дії</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
              </td>
            </tr>
            <tr v-else-if="sales.length === 0">
              <td colspan="7" class="text-center py-4 text-muted">Записи про продажі відсутні</td>
            </tr>
            <tr v-else v-for="item in sales" :key="item.id">
              <td>#{{ item.id }}</td>
              <td>
                <small class="text-muted fw-semibold">
                  {{ formatDate(item.created_at) }}
                </small>
              </td>
              <td>
                <span class="fw-semibold">{{ item.model?.name || `Модель #${item.models_id}` }}</span>
              </td>
              <td>
                <span class="badge bg-secondary">{{ item.user?.name || `User ID: ${item.user_id}` }}</span>
              </td>
              <td><span class="badge bg-info text-dark">{{ item.size }}</span></td>
              <td class="fw-bold text-success">{{ item.price }} ₴</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-warning me-1" @click="openModal(item)">
                  Редагувати
                </button>

                <!-- Кнопка Видалити показується ТІЛЬКИ якщо allowDelete = true -->
                <button
                  v-if="allowDelete"
                  class="btn btn-sm btn-outline-danger"
                  @click="deleteItem(item.id)"
                >
                  Видалити
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Пагінація Laravel -->
    <div class="card-footer bg-white d-flex justify-content-between align-items-center" v-if="pagination.total > 0">
      <small class="text-muted">
        Показано {{ pagination.from }} - {{ pagination.to }} з {{ pagination.total }}
      </small>
      <ul class="pagination pagination-sm m-0">
        <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
          <button class="page-link" @click="fetchSales(pagination.current_page - 1)">Попередня</button>
        </li>
        <li
          v-for="page in pagination.last_page"
          :key="page"
          class="page-item"
          :class="{ active: page === pagination.current_page }"
        >
          <button class="page-link" @click="fetchSales(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
          <button class="page-link" @click="fetchSales(pagination.current_page + 1)">Наступна</button>
        </li>
      </ul>
    </div>

    <!-- Модальне вікно (Редагування) -->
    <div v-if="showModal" class="modal d-block" style="background: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Редагувати продаж #{{ currentId }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="saveForm">
            <div class="modal-body">
              <!-- Помилки валідації -->
              <div v-if="Object.keys(errors).length" class="alert alert-danger py-2">
                <ul class="mb-0 ps-3">
                  <li v-for="(err, key) in errors" :key="key">{{ err[0] }}</li>
                </ul>
              </div>

              <!-- Вибір Моделі -->
              <div class="mb-3">
                <label class="form-label">Модель взуття</label>
                <select v-model="form.models_id" class="form-select" required>
                  <option :value="null" disabled>Оберіть модель...</option>
                  <option v-for="m in shoesModels" :key="m.id" :value="m.id">
                    {{ m.name || `Модель ID #${m.id}` }}
                  </option>
                </select>
              </div>

              <!-- Розмір -->
              <div class="mb-3">
                <label class="form-label">Розмір</label>
                <input v-model="form.size" type="text" class="form-control" placeholder="напр. 41" required />
              </div>

              <!-- Ціна -->
              <div class="mb-3">
                <label class="form-label">Ціна (₴)</label>
                <input v-model.number="form.price" type="number" min="0" class="form-control" required />
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Скасувати</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Збереження...' : 'Оновити' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import NavShoesMenu from './NavShoesMenu.vue';

// Оголошення пропсів
const props = defineProps({
  userId: {
    type: Number,
    default: null
  },
  // Прапорець дозволу на видалення (за замовчуванням false)
  allowDelete: {
    type: Boolean,
    default: false
  }
});

// Ендпоінти
const API_BASE = '/rapi/sales-shoes';
const MODELS_API = '/shoes/models/all';

// Реактивні змінні
const sales = ref([]);
const shoesModels = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const currentId = ref(null);
const errors = ref({});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0,
  prev_page_url: null,
  next_page_url: null
});

const form = reactive({
  models_id: null,
  size: '',
  price: 0,
  user_id: props.userId
});

// Форматування дати та часу (напр. "01.09.2026 14:30")
const formatDate = (dateString) => {
  if (!dateString) return '—';
  const date = new Date(dateString);
  return date.toLocaleString('uk-UA', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Завантаження списку продажів
const fetchSales = async (page = 1) => {
  loading.value = true;
  try {
    const response = await axios.get(`${API_BASE}?page=${page}`);
    sales.value = response.data.data;

    pagination.current_page = response.data.current_page;
    pagination.last_page = response.data.last_page;
    pagination.from = response.data.from;
    pagination.to = response.data.to;
    pagination.total = response.data.total;
    pagination.prev_page_url = response.data.prev_page_url;
    pagination.next_page_url = response.data.next_page_url;
  } catch (error) {
    console.error('Помилка завантаження продажів:', error);
  } finally {
    loading.value = false;
  }
};

// Завантаження списку моделей взуття для випадаючого списку
const fetchShoesModels = async () => {
  try {
    const response = await axios.get(MODELS_API);
    shoesModels.value = response.data.data || response.data;
  } catch (error) {
    console.error('Помилка завантаження моделей:', error);
  }
};

// Відкриття модального вікна для редагування
const openModal = (item) => {
  errors.value = {};
  currentId.value = item.id;
  form.models_id = item.models_id;
  form.size = item.size;
  form.price = item.price;
  form.user_id = item.user_id || props.userId;
  showModal.value = true;
};

// Закриття модального вікна
const closeModal = () => {
  showModal.value = false;
};

// Оновлення запису
const saveForm = async () => {
  saving.value = true;
  errors.value = {};

  try {
    await axios.put(`${API_BASE}/${currentId.value}`, form);
    closeModal();
    fetchSales(pagination.current_page);
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.error('Помилка оновлення:', error);
    }
  } finally {
    saving.value = false;
  }
};

// Видалення запису
const deleteItem = async (id) => {
  if (!confirm('Ви дійсно бажаєте видалити цей запис про продаж?')) return;

  try {
    await axios.delete(`${API_BASE}/${id}`);
    fetchSales(pagination.current_page);
  } catch (error) {
    console.error('Помилка видалення:', error);
  }
};

onMounted(() => {
  fetchSales();
  fetchShoesModels();
});
</script>
