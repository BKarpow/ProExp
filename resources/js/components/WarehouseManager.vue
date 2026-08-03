<template>
  <div class="container my-3 px-2 px-sm-3">
    <!-- Шапка з кнопкою створення -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="fw-bold m-0">Склад взуття</h3>
      <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" @click="openModal()">
        ➕ Додати
      </button>
    </div>

    <!-- Пошуковий блок -->
    <div class="mb-3">
      <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden">
        <span class="input-group-text bg-white border-0 ps-3">🔍</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          class="form-control border-0 bg-white" 
          placeholder="Пошук моделі чи групи..."
        />
        <button 
          v-if="searchQuery" 
          class="btn btn-white border-0 pe-3 text-secondary" 
          type="button" 
          @click="searchQuery = ''"
        >
          ✕
        </button>
      </div>
    </div>

    <!-- Спінер завантаження -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <div class="small text-muted mt-2">Завантаження залишків...</div>
    </div>

    <!-- Порожній стан -->
    <div v-else-if="filteredItems.length === 0" class="text-center py-5 bg-white rounded-3 shadow-sm">
      <div class="fs-1">👟</div>
      <p class="text-muted mb-0">
        {{ searchQuery ? 'Нічого не знайдено' : 'Склад порожній' }}
      </p>
    </div>

    <!-- Список Карток (Card View) -->
    <div v-else class="row g-3">
      <div 
        v-for="item in filteredItems" 
        :key="item.id" 
        class="col-12 col-sm-6 col-md-4"
      >
        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden position-relative hover-card">
          <div class="card-body p-3 d-flex flex-column justify-content-between">
            
            <!-- Верхня частина: Іконка, Модель та Група -->
            <div>
              <div class="d-flex align-items-start gap-2 mb-2">
                <div class="shoe-icon-wrapper bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                  👟
                </div>

                <div class="overflow-hidden flex-grow-1">
                  <h6 class="card-title fw-bold text-dark text-truncate mb-0" :title="item.model?.name">
                    {{ item.model?.name || 'Без назви' }}
                  </h6>
                  <small class="text-muted text-truncate d-block" :title="item.group?.name">
                    {{ item.group?.name || 'Без групи' }}
                  </small>
                </div>
              </div>

              <!-- Блок Розмірів (Клікабельні для продажу) -->
              <div class="my-2">
                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">
                  РОЗМІРИ (натисніть для продажу):
                </small>
                <div class="d-flex flex-wrap gap-1">
                  <button 
                    v-if="item.sizes && item.sizes.length"
                    v-for="(size, idx) in item.sizes" 
                    :key="idx" 
                    type="button"
                    class="btn btn-sm btn-light text-dark border border-secondary border-opacity-25 fw-semibold px-2 py-1 size-btn"
                    @click="confirmSellSize(item, size)"
                    title="Продати цей розмір"
                  >
                    {{ size }} 🏷️
                  </button>
                  <span v-else class="small text-muted fst-italic">Немає в наявності</span>
                </div>
              </div>
            </div>

            <!-- Нижня частина: Ціна, залишок та дії -->
            <div class="pt-2 border-top mt-2 d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-bold text-primary fs-6">
                  {{ item.price ? `${item.price} ₴` : 'Ціна не вказана' }}
                </div>
                <div class="small">
                  Залишок: 
                  <span class="fw-bold" :class="(item.sizes?.length || 0) > 0 ? 'text-success' : 'text-danger'">
                    {{ item.sizes?.length || 0 }} шт.
                  </span>
                </div>
              </div>

              <div class="d-flex gap-1">
                <button 
                  class="btn btn-outline-warning btn-sm border-0 rounded-circle p-2 d-flex align-items-center justify-content-center" 
                  style="width: 34px; height: 34px;"
                  @click="openModal(item)" 
                  title="Редагувати"
                >
                  ✏️
                </button>
                <button 
                  class="btn btn-outline-danger btn-sm border-0 rounded-circle p-2 d-flex align-items-center justify-content-center" 
                  style="width: 34px; height: 34px;"
                  @click="deleteItem(item.id)" 
                  title="Видалити"
                >
                  🗑️
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Мобільна Пагінація -->
    <div v-if="pagination.last_page > 1" class="d-flex justify-content-between align-items-center mt-4 px-1">
      <button 
        class="btn btn-outline-primary btn-sm rounded-pill px-3" 
        :disabled="pagination.current_page === 1"
        @click="fetchItems(pagination.current_page - 1)"
      >
        ← Попередня
      </button>

      <span class="small text-muted fw-bold">
        {{ pagination.current_page }} з {{ pagination.last_page }}
      </span>

      <button 
        class="btn btn-outline-primary btn-sm rounded-pill px-3" 
        :disabled="pagination.current_page === pagination.last_page"
        @click="fetchItems(pagination.current_page + 1)"
      >
        Наступна →
      </button>
    </div>

    <!-- Модальне вікно підтвердження продажу -->
    <div v-if="showSellModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(2px);">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-3">
          <div class="fs-1 mb-2">🛍️</div>
          <h5 class="fw-bold mb-1">Продати розмір?</h5>
          <p class="small text-muted mb-3">
            Модель: <strong>{{ itemToSell?.model?.name }}</strong><br>
            Розмір: <span class="badge bg-primary fs-6">{{ sizeToSell }}</span>
          </p>

          <div class="d-flex gap-2 justify-content-center">
            <button class="btn btn-light rounded-pill px-3 flex-grow-1" @click="showSellModal = false">
              Скасувати
            </button>
            <button class="btn btn-success rounded-pill px-3 flex-grow-1" :disabled="selling" @click="processSellSize">
              <span v-if="selling" class="spinner-border spinner-border-sm me-1"></span>
              Продано 💰
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальне вікно (Створення / Редагування) -->
    <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(2px);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">
              {{ isEditing ? 'Редагувати товар' : 'Новий товар' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Група взуття *</label>
                <select v-model="form.group_id" class="form-select rounded-3" :class="{ 'is-invalid': errors.group_id }" required>
                  <option value="" disabled>Оберіть групу...</option>
                  <option v-for="group in groups" :key="group.id" :value="group.id">
                    {{ group.name }}
                  </option>
                </select>
                <div v-if="errors.group_id" class="invalid-feedback">{{ errors.group_id[0] }}</div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Модель взуття *</label>
                <select v-model="form.models_id" class="form-select rounded-3" :class="{ 'is-invalid': errors.models_id }" required>
                  <option value="" disabled>Оберіть модель...</option>
                  <option v-for="model in models" :key="model.id" :value="model.id">
                    {{ model.name }}
                  </option>
                </select>
                <div v-if="errors.models_id" class="invalid-feedback">{{ errors.models_id[0] }}</div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Розміри в наявності</label>
                <input 
                  v-model="sizesInput" 
                  type="text" 
                  class="form-control rounded-3" 
                  placeholder="38, 39, 40, 41" 
                />
                <div class="form-text small">
                  Вкажіть через кому. Кількість шт. автоматично: 
                  <span class="badge bg-secondary">{{ computedResidual }}</span>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Ціна (грн)</label>
                <input v-model.number="form.price" type="number" min="0" class="form-control rounded-3" placeholder="0" />
              </div>

              <div class="form-check form-switch mb-2 pt-1">
                <input 
                  v-model="form.active" 
                  class="form-check-input" 
                  type="checkbox" 
                  role="switch" 
                  id="activeSwitch"
                />
                <label class="form-check-label small fw-bold" for="activeSwitch">
                  {{ form.active ? '🟢 Товар активний' : '🔴 Товар прихований (неактивний)' }}
                </label>
              </div>
            </div>

            <div class="modal-footer border-0 pt-0">
              <button type="button" class="btn btn-light rounded-pill px-4" @click="closeModal">Скасувати</button>
              <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="saving">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span> 
                Зберегти
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';

const API_URL = '/rapi/warehouse-shoes';

const items = ref([]);
const groups = ref([]);
const models = ref([]);
const loading = ref(false);
const saving = ref(false);
const selling = ref(false);

const showModal = ref(false);
const showSellModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

// Змінні для модалки продажу
const itemToSell = ref(null);
const sizeToSell = ref(null);

const searchQuery = ref('');
const sizesInput = ref('');

const pagination = reactive({
  current_page: 1,
  last_page: 1,
});

const form = reactive({
  group_id: '',
  models_id: '',
  sizes: [],
  residual: 0,
  price: 0,
  active: true,
});

const errors = ref({});

const filteredItems = computed(() => {
  if (!searchQuery.value.trim()) return items.value;
  const query = searchQuery.value.toLowerCase().trim();
  return items.value.filter(item => {
    const modelName = item.model?.name?.toLowerCase() || '';
    const groupName = item.group?.name?.toLowerCase() || '';
    return modelName.includes(query) || groupName.includes(query);
  });
});

const computedResidual = computed(() => {
  if (!sizesInput.value) return 0;
  return sizesInput.value
    .split(',')
    .map(s => s.trim())
    .filter(s => s.length > 0).length;
});

const fetchFormData = async () => {
  try {
    const res = await axios.get(`${API_URL}/form-data`);
    groups.value = res.data.groups;
    models.value = res.data.models;
  } catch (err) {
    console.error('Помилка списків:', err);
  }
};

const fetchItems = async (page = 1) => {
  loading.value = true;
  try {
    const res = await axios.get(`${API_URL}?page=${page}`);
    items.value = res.data.data;
    pagination.current_page = res.data.current_page;
    pagination.last_page = res.data.last_page;
  } catch (err) {
    console.error('Помилка завантаження складських залишків:', err);
  } finally {
    loading.value = false;
  }
};

// 1. Відкриття діалогу продажу розміру
const confirmSellSize = (item, size) => {
  itemToSell.value = item;
  sizeToSell.value = size;
  showSellModal.value = true;
};

// 2. Обробка продажу та оновлення на сервері
const processSellSize = async () => {
  if (!itemToSell.value || !sizeToSell.value) return;

  selling.value = true;

  // Видаляємо лише ПЕРШЕ входження даного розміру
  const currentSizes = [...(itemToSell.value.sizes || [])];
  const targetIndex = currentSizes.indexOf(sizeToSell.value);

  if (targetIndex !== -1) {
    currentSizes.splice(targetIndex, 1);
  }

  const updatedPayload = {
    group_id: itemToSell.value.group_id,
    models_id: itemToSell.value.models_id,
    price: itemToSell.value.price,
    active: itemToSell.value.active,
    sizes: currentSizes,
    residual: currentSizes.length // новий залишок
  };

  try {
    await axios.put(`${API_URL}/${itemToSell.value.id}`, updatedPayload);
    showSellModal.value = false;
    fetchItems(pagination.current_page); // Оновлюємо списки
  } catch (err) {
    console.error('Помилка виконання продажу:', err);
  } finally {
    selling.value = false;
  }
};

const openModal = (item = null) => {
  errors.value = {};
  if (item) {
    isEditing.value = true;
    currentId.value = item.id;
    form.group_id = item.group_id;
    form.models_id = item.models_id;
    form.price = item.price ?? 0;
    form.active = Boolean(item.active);
    sizesInput.value = item.sizes ? item.sizes.join(', ') : '';
  } else {
    isEditing.value = false;
    currentId.value = null;
    form.group_id = '';
    form.models_id = '';
    form.price = 0;
    form.active = true;
    sizesInput.value = '';
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveItem = async () => {
  saving.value = true;
  errors.value = {};

  const parsedSizes = sizesInput.value
    .split(',')
    .map(s => s.trim())
    .filter(s => s.length > 0);

  form.sizes = parsedSizes;
  form.residual = parsedSizes.length;

  try {
    if (isEditing.value) {
      await axios.put(`${API_URL}/${currentId.value}`, form);
    } else {
      await axios.post(API_URL, form);
    }
    closeModal();
    fetchItems(pagination.current_page);
  } catch (err) {
    if (err.response && err.response.status === 422) {
      errors.value = err.response.data.errors;
    } else {
      console.error('Помилка збереження:', err);
    }
  } finally {
    saving.value = false;
  }
};

const deleteItem = async (id) => {
  if (!confirm('Видалити цю модель зі складу?')) return;
  try {
    await axios.delete(`${API_URL}/${id}`);
    fetchItems(pagination.current_page);
  } catch (err) {
    console.error('Помилка видалення:', err);
  }
};

onMounted(() => {
  fetchFormData();
  fetchItems();
});
</script>

<style scoped>
.shoe-icon-wrapper {
  width: 42px;
  height: 42px;
  font-size: 1.3rem;
}

.hover-card {
  transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.size-btn {
  transition: all 0.15s ease;
  font-size: 0.8rem;
}

.size-btn:hover, .size-btn:active {
  background-color: #198754 !important;
  color: #fff !important;
  border-color: #198754 !important;
}
</style>