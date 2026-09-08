<template>
  <div :class="['app-wrapper p-2 p-md-3', isDarkMode ? 'bg-dark text-light' : 'bg-light text-dark']">
    <div class="container-fluid max-width-lg">

      <!-- Шапка: Назва + Пошук + Темний режим + Додати -->
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h4 class="fw-bold m-0 d-flex align-items-center gap-2">
          👟 Склад взуття
        </h4>


        <div class="d-flex align-items-center gap-2">
          <!-- Перемикач темної/світлої теми -->
          <button
            type="button"
            class="btn btn-sm shadow-sm"
            :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-dark'"
            @click="toggleTheme"
            title="Змінити тему"
          >
            {{ isDarkMode ? '☀️ Світла' : '🌙 Темна' }}
          </button>

          <button class="btn btn-primary btn-sm fw-bold shadow-sm" @click="openModal()">
            ➕ Додати
          </button>
        </div>
      </div>
      <NavShoesMenu/>

      <!-- Пошуковий блок (по таблиці) -->
      <div class="mb-3">
        <div class="input-group input-group-sm shadow-sm">
          <span class="input-group-text border-0" :class="isDarkMode ? 'bg-secondary text-light' : 'bg-white text-muted'">🔍</span>
          <input
            v-model="searchQuery"
            type="text"
            class="form-control border-0"
            :class="isDarkMode ? 'bg-secondary text-light placeholder-light' : 'bg-white text-dark'"
            placeholder="Пошук по моделі..."
          />
          <button
            v-if="searchQuery"
            class="btn border-0"
            :class="isDarkMode ? 'bg-secondary text-light' : 'bg-white text-muted'"
            type="button"
            @click="searchQuery = ''"
          >
            ✕
          </button>
        </div>
      </div>

      <!-- Динамічні Вкладки для Груп -->
      <ul class="nav nav-pills mb-3 group-tabs gap-1 flex-wrap overflow-auto pb-1">
        <li class="nav-item">
          <button
            class="nav-item-btn rounded-pill px-3 py-1 border-0 fw-bold small"
            :class="selectedGroupId === null ? 'btn-primary text-white' : (isDarkMode ? 'bg-light text-dark' : 'bg-white text-dark')"
            @click="selectGroup(null)"
          >
            Всі
          </button>
        </li>
        <li v-for="group in groups" :key="group.id" class="nav-item">
          <button
            class="nav-item-btn rounded-pill px-3 py-1 border-0 fw-bold small"
            :class="selectedGroupId === group.id ? 'btn-primary text-white' : (isDarkMode ? 'bg-light text-dark' : 'bg-white text-dark')"
            @click="selectGroup(group.id)"
          >
            {{ group.name }}
          </button>
        </li>
      </ul>

      <!-- Спінер завантаження -->
      <div v-if="loading" class="text-center py-4">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <!-- Компактна Таблиця -->
      <div v-else class="card border-0 shadow-sm rounded-3 overflow-hidden" :class="isDarkMode ? 'bg-dark' : 'bg-white'">
        <div class="table-responsive">
          <table
            class="table align-middle text-nowrap mb-0 custom-stylish-table"
            :class="isDarkMode ? 'table-dark table-hover' : 'table-hover'"
          >
            <tbody>
              <tr v-if="filteredItems.length === 0">
                <td colspan="3" class="text-center py-4 text-muted">
                  {{ searchQuery ? 'Нічого не знайдено' : 'Немає записів у цій категорії' }}
                </td>
              </tr>

              <tr v-else v-for="item in filteredItems" :key="item.id">
                <!-- Колонка 1: Модель та Ціна -->
                <td class="px-3 py-2 cell-model">
                  <div class="fw-bold text-truncate" style="max-width: 240px;" :title="item.model?.name">
                    {{ item.model?.name || '—' }}
                  </div>
                  <small v-if="item.price" class="fw-bold text-success d-block" style="font-size: 0.8rem;">
                    {{ item.price }} ₴
                  </small>
                </td>

                <!-- Колонка 2: Розміри -->
                <td class="px-2 py-2 cell-sizes">
                  <div class="d-flex flex-wrap gap-1 ">
                    <template v-if="item.sizes && item.sizes.length">
                      <span
                        v-for="(size, idx) in item.sizes"
                        :key="idx"
                        class="badge size-badge shadow-sm"
                        :class="isDarkMode ? 'bg-secondary text-light border border-dark' : 'bg-light text-dark border'"
                        @click="openSellModal(item, size, idx, item.price)"
                        title="Натисніть щоб продати цей розмір"
                      >
                        {{ size }}
                      </span>
                    </template>
                    <span v-else class="small text-muted fst-italic">Немає</span>
                  </div>
                </td>

                <!-- Колонка 3: Випадаюче меню дій -->
                <td class="px-2 py-2 text-end cell-actions" style="width: 1%;">
                  <div class="dropdown">
                    <button
                      class="btn btn-sm border-0 px-2 py-0 text-secondary fw-bold rounded-circle action-dots-btn"
                      type="button"
                      data-bs-toggle="dropdown"
                      aria-expanded="false"
                      title="Дії"
                    >
                      •••
                    </button>
                    <ul
                      class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3"
                      :class="isDarkMode ? 'dropdown-menu-dark bg-secondary' : ''"
                    >
                      <li>
                        <button class="dropdown-item small d-flex align-items-center gap-2" @click="openModal(item)">
                          ✏️ Редагувати
                        </button>
                      </li>
                      <li>
                        <button class="dropdown-item small text-danger d-flex align-items-center gap-2" @click="deleteItem(item.id)">
                          🗑️ Видалити
                        </button>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Пагінація -->
      <div v-if="pagination.last_page > 1" class="d-flex justify-content-between align-items-center mt-3">
        <button
          class="btn btn-sm rounded-pill px-3 shadow-sm"
          :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-primary'"
          :disabled="pagination.current_page === 1"
          @click="fetchItems(pagination.current_page - 1)"
        >
          ← Попередня
        </button>

        <span class="small fw-bold">
          {{ pagination.current_page }} / {{ pagination.last_page }}
        </span>

        <button
          class="btn btn-sm rounded-pill px-3 shadow-sm"
          :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-primary'"
          :disabled="pagination.current_page === pagination.last_page"
          @click="fetchItems(pagination.current_page + 1)"
        >
          Наступна →
        </button>
      </div>

      <!-- Модальне вікно підтвердження продажу -->
      <div v-if="showSellModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(2px);">
        <div class="modal-dialog modal-dialog-centered modal-sm">
          <div class="modal-content rounded-4 border-0 shadow-lg" :class="isDarkMode ? 'bg-dark text-light border-secondary' : ''">
            <div class="modal-body text-center p-3">
              <div class="fs-2 mb-1">🛍️</div>
              <h6 class="fw-bold mb-1">Продати розмір {{ selectedSize }}?</h6>
              <p class="small text-muted mb-3">{{ selectedSellItem?.model?.name }}</p>
              <div class="mb-2">
                <input type="text" class="form-control" v-model="salPrice" />
              </div>
              <div class="d-grid gap-2">
                <button
                  type="button"
                  class="btn btn-sm btn-success rounded-pill fw-bold shadow-sm"
                  :disabled="selling"
                  @click="confirmSell"
                >
                  <span v-if="selling" class="spinner-border spinner-border-sm me-1"></span>
                  Продано
                </button>
                <button
                  type="button"
                  class="btn btn-sm btn-light rounded-pill text-muted"
                  @click="showSellModal = false"
                >
                  Скасувати
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Модальне вікно (Створення / Редагування) -->
      <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(2px);" @click="isModelDropdownOpen = false">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content rounded-4 border-0 shadow-lg" :class="isDarkMode ? 'bg-dark text-light' : ''" @click.stop>
            <div class="modal-header border-bottom-0 pb-0">
              <h5 class="modal-title fw-bold">
                {{ isEditing ? 'Редагувати' : 'Новий товар' }}
              </h5>
              <button
                type="button"
                class="btn-close"
                :class="isDarkMode ? 'btn-close-white' : ''"
                @click="closeModal"
              ></button>
            </div>
            <form @submit.prevent="saveItem">
              <div class="modal-body">

                <!-- Група -->
                <div class="mb-3">
                  <label class="form-label small fw-bold">Група взуття *</label>
                  <select
                    v-model="form.group_id"
                    class="form-select rounded-3"
                    :class="[isDarkMode ? 'bg-dark text-light border-secondary' : '', { 'is-invalid': errors.group_id }]"
                    required
                  >
                    <option value="" disabled>Оберіть групу...</option>
                    <option v-for="group in groups" :key="group.id" :value="group.id">
                      {{ group.name }}
                    </option>
                  </select>
                  <div v-if="errors.group_id" class="invalid-feedback">{{ errors.group_id[0] }}</div>
                </div>

                <!-- Модель (Об'єднана із інтегрованим динамічним пошуком) -->
                <div class="mb-3 position-relative">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-bold m-0">Модель взуття *</label>
                    <button
                      type="button"
                      class="btn btn-link btn-sm p-0 text-decoration-none small fw-bold"
                      @click="toggleCreateModel"
                    >
                      {{ showCreateModelInput ? '✕ Скасувати' : '➕ Створити нову модель' }}
                    </button>
                  </div>

                  <!-- Поле для швидкого створення моделі -->
                  <div v-if="showCreateModelInput" class="p-2 mb-2 rounded-3 border" :class="isDarkMode ? 'bg-secondary bg-opacity-25 border-secondary' : 'bg-light'">
                    <div class="input-group input-group-sm">
                      <input
                        v-model="newModelName"
                        type="text"
                        class="form-control rounded-start-2"
                        :class="[isDarkMode ? 'bg-dark text-light border-secondary' : '', { 'is-invalid': modelError }]"
                        placeholder="Введіть назву моделі..."
                        @keyup.enter.prevent="createNewModel"
                      />
                      <button
                        type="button"
                        class="btn btn-success fw-bold px-3"
                        :disabled="creatingModel || !newModelName.trim()"
                        @click="createNewModel"
                      >
                        <span v-if="creatingModel" class="spinner-border spinner-border-sm me-1"></span>
                        Зберегти
                      </button>
                    </div>
                    <div v-if="modelError" class="text-danger small mt-1">{{ modelError }}</div>
                  </div>

                  <!-- Об'єднаний селектор-пошук -->
                  <div class="position-relative">
                    <button
                      type="button"
                      class="form-select text-start d-flex justify-content-between align-items-center rounded-3"
                      :class="[isDarkMode ? 'bg-dark text-light border-secondary' : '', { 'is-invalid': errors.models_id }]"
                      @click.stop="toggleModelDropdown"
                    >
                      <span class="text-truncate">
                        {{ selectedModelName || 'Оберіть модель зі списку...' }}
                      </span>
                    </button>

                    <!-- Випадаючий список із динамічним пошуком всередині -->
                    <div
                      v-if="isModelDropdownOpen"
                      class="dropdown-menu show w-100 p-2 shadow-lg border mt-1 position-absolute top-100 start-0 z-3"
                      :class="isDarkMode ? 'dropdown-menu-dark bg-secondary' : 'bg-white'"
                      @click.stop
                    >
                      <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text border-0" :class="isDarkMode ? 'bg-dark text-light' : 'bg-light text-muted'">🔍</span>
                        <input
                          v-model="modelSearchQuery"
                          type="text"
                          class="form-control border-0"
                          :class="isDarkMode ? 'bg-dark text-light placeholder-light' : 'bg-light text-dark'"
                          placeholder="Динамічний пошук..."
                          ref="modelSearchInput"
                        />
                        <button
                          v-if="modelSearchQuery"
                          class="btn border-0"
                          :class="isDarkMode ? 'bg-dark text-light' : 'bg-light text-muted'"
                          type="button"
                          @click="modelSearchQuery = ''"
                        >
                          ✕
                        </button>
                      </div>

                      <ul class="list-unstyled mb-0 overflow-auto style-scrollbar" style="max-height: 180px;">
                        <li v-if="filteredModels.length === 0" class="text-center py-2 text-muted small">
                          Нічого не знайдено
                        </li>
                        <li
                          v-for="model in filteredModels"
                          :key="model.id"
                          class="dropdown-item rounded px-2 py-1 small cursor-pointer d-flex justify-content-between align-items-center"
                          :class="{ 'active': form.models_id === model.id }"
                          @click="selectModel(model)"
                        >
                          <span>{{ model.name }}</span>
                          <span v-if="form.models_id === model.id" class="small fw-bold">✓</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div v-if="errors.models_id" class="invalid-feedback d-block">{{ errors.models_id[0] }}</div>
                </div>

                <!-- Вибір розмірів кнопками -->
                <div class="mb-3">
                  <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                    <span>Натисніть для додавання розміру (35-46):</span>
                    <span class="badge bg-primary">Усього: {{ selectedSizesList.length }} шт.</span>
                  </label>

                  <div class="d-flex flex-wrap gap-1 mb-2">
                    <button
                      v-for="s in availableSizes"
                      :key="s"
                      type="button"
                      class="btn btn-sm btn-outline-primary rounded-3 size-picker-btn fw-bold"
                      @click="addSize(s)"
                    >
                      {{ s }}
                    </button>
                  </div>

                  <div
                    class="p-2 rounded-3 border min-height-sizes d-flex flex-wrap gap-1 align-items-center"
                    :class="isDarkMode ? 'bg-secondary bg-opacity-25 border-secondary' : 'bg-light'"
                  >
                    <span v-if="selectedSizesList.length === 0" class="small text-muted fst-italic">
                      Натисніть кнопки вище, щоб додати розміри...
                    </span>
                    <span
                      v-for="(size, idx) in selectedSizesList"
                      :key="idx"
                      class="badge bg-primary rounded-pill size-selected-badge"
                      @click="removeSize(idx)"
                      title="Натисніть щоб видалити цей розмір"
                    >
                      {{ size }} <span class="ms-1 opacity-75">✕</span>
                    </span>
                  </div>
                </div>

                <!-- Ціна -->
                <div class="mb-3">
                  <label class="form-label small fw-bold">Ціна (грн)</label>
                  <input
                    v-model.number="form.price"
                    type="number"
                    min="0"
                    class="form-control rounded-3"
                    :class="isDarkMode ? 'bg-dark text-light border-secondary' : ''"
                  />
                </div>

                <!-- Активність -->
                <div class="form-check form-switch mb-2">
                  <input
                    v-model="form.active"
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    id="activeSwitch"
                  />
                  <label class="form-check-label small fw-bold" for="activeSwitch">
                    {{ form.active ? '🟢 Товар активний' : '🔴 Товар прихований' }}
                  </label>
                </div>

              </div>

              <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3" @click="closeModal">Скасувати</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                  Зберегти
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, nextTick, onMounted } from 'vue';
import axios from 'axios';
import NavShoesMenu from './NavShoesMenu.vue';

const API_URL = '/rapi/warehouse-shoes';
const API_SALES_URL = '/rapi/sales-shoes';
const API_MODELS_URL = '/rapi/shoes/models';

const props = defineProps({
  userId: {
    type: Number,
    required: true,
    default: null
  }
});

const availableSizes = [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46];

const isDarkMode = ref(localStorage.getItem('warehouse_theme') === 'dark');

const toggleTheme = () => {
  isDarkMode.value = !isDarkMode.value;
  localStorage.setItem('warehouse_theme', isDarkMode.value ? 'dark' : 'light');
};

const savedGroup = localStorage.getItem('warehouse_selected_group');
const selectedGroupId = ref(savedGroup !== null ? (savedGroup === 'null' ? null : Number(savedGroup)) : null);

const selectGroup = (groupId) => {
  selectedGroupId.value = groupId;
  localStorage.setItem('warehouse_selected_group', groupId === null ? 'null' : groupId);
};

const items = ref([]);
const groups = ref([]);
const models = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const salPrice = ref(0);

// Стан випадаючого селектора з пошуком
const isModelDropdownOpen = ref(false);
const modelSearchQuery = ref('');
const modelSearchInput = ref(null);

// Фокусування на поле введення після відкриття
watch(isModelDropdownOpen, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      modelSearchInput.value?.focus();
    });
  }
});

const toggleModelDropdown = () => {
  isModelDropdownOpen.value = !isModelDropdownOpen.value;
};

// Створення нової моделі
const showCreateModelInput = ref(false);
const newModelName = ref('');
const creatingModel = ref(false);
const modelError = ref('');

const selectedSizesList = ref([]);

const showSellModal = ref(false);
const selling = ref(false);
const selectedSellItem = ref(null);
const selectedSize = ref(null);
const selectedSizeIndex = ref(null);

const searchQuery = ref('');

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

// Динамічне фільтрування моделей
const filteredModels = computed(() => {
  if (!modelSearchQuery.value.trim()) {
    return models.value;
  }
  const query = modelSearchQuery.value.toLowerCase().trim();
  return models.value.filter(m => m.name && m.name.toLowerCase().includes(query));
});

// Назва вибраної моделі
const selectedModelName = computed(() => {
  const found = models.value.find(m => m.id === form.models_id);
  return found ? found.name : '';
});

const selectModel = (model) => {
  form.models_id = model.id;
  isModelDropdownOpen.value = false;
  modelSearchQuery.value = '';
};

const addSize = (size) => {
  selectedSizesList.value.push(String(size));
};

const removeSize = (index) => {
  selectedSizesList.value.splice(index, 1);
};

const toggleCreateModel = () => {
  showCreateModelInput.value = !showCreateModelInput.value;
  newModelName.value = '';
  modelError.value = '';
};

const createNewModel = async () => {
  if (!newModelName.value.trim()) return;

  creatingModel.value = true;
  modelError.value = '';

  try {
    const res = await axios.post(API_MODELS_URL, {
      name: newModelName.value.trim()
    });

    const createdModel = res.data.data || res.data;

    models.value.push(createdModel);
    form.models_id = createdModel.id;

    newModelName.value = '';
    showCreateModelInput.value = false;
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      modelError.value = err.response.data.message;
    } else {
      modelError.value = 'Помилка при створенні моделі.';
    }
  } finally {
    creatingModel.value = false;
  }
};

const filteredItems = computed(() => {
  let result = items.value;

  if (selectedGroupId.value !== null) {
    result = result.filter(item => item.group_id === selectedGroupId.value);
  }

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim();
    result = result.filter(item => {
      const modelName = item.model?.name?.toLowerCase() || '';
      return modelName.includes(query);
    });
  }

  return result;
});

const openSellModal = (item, size, index, price) => {
  salPrice.value = price;
  selectedSellItem.value = item;
  selectedSize.value = size;
  selectedSizeIndex.value = index;
  showSellModal.value = true;
};

const saveSale = async (updatedSizes) => {
  const payload = {
    models_id: selectedSellItem.value.models_id,
    size: updatedSizes,
    price: selectedSellItem.value.price || salPrice.value || 0,
    user_id: props.userId,
  };
  try {
    await axios.post(`${API_SALES_URL}/`, payload);
  } catch (err) {
    console.error('Помилка при запису продажу:', err);
  }
};

const confirmSell = async () => {
  if (!selectedSellItem.value) return;
  selling.value = true;

  const updatedSizes = [...selectedSellItem.value.sizes];
  updatedSizes.splice(selectedSizeIndex.value, 1);
  const selSize = selectedSellItem.value.sizes[selectedSizeIndex.value];

  const payload = {
    group_id: selectedSellItem.value.group_id,
    models_id: selectedSellItem.value.models_id,
    sizes: updatedSizes,
    residual: updatedSizes.length,
    price: selectedSellItem.value.price,
    user_id: props.userId,
    active: selectedSellItem.value.active
  };

  saveSale(selSize);
  try {
    await axios.put(`${API_URL}/${selectedSellItem.value.id}`, payload);
    showSellModal.value = false;
    fetchItems(pagination.current_page);
  } catch (err) {
    console.error('Помилка при продажу:', err);
  } finally {
    selling.value = false;
  }
};

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
    console.error('Помилка складових:', err);
  } finally {
    loading.value = false;
  }
};

const openModal = (item = null) => {
  errors.value = {};
  showCreateModelInput.value = false;
  newModelName.value = '';
  modelError.value = '';
  modelSearchQuery.value = '';
  isModelDropdownOpen.value = false;

  if (item) {
    isEditing.value = true;
    currentId.value = item.id;
    form.group_id = item.group_id;
    form.models_id = item.models_id;
    form.price = item.price ?? 0;
    form.active = Boolean(item.active);
    selectedSizesList.value = item.sizes ? [...item.sizes] : [];
  } else {
    isEditing.value = false;
    currentId.value = null;
    form.group_id = '';
    form.models_id = '';
    form.price = 0;
    form.active = true;
    selectedSizesList.value = [];
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveItem = async () => {
  saving.value = true;
  errors.value = {};

  form.sizes = selectedSizesList.value;
  form.residual = selectedSizesList.value.length;

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
  if (!confirm('Видалити цей запис?')) return;
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
.app-wrapper {
  min-height: 100vh;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.group-tabs::-webkit-scrollbar {
  display: none;
}
.group-tabs {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.nav-item-btn {
  transition: all 0.2s ease;
  white-space: nowrap;
}

.custom-stylish-table {
  font-size: 0.875rem;
}

.cell-model {
  width: 32%;
  min-width: 120px;
}

.cell-sizes {
  white-space: normal !important;
}

.size-badge {
  cursor: pointer;
  font-size: 0.8rem;
  padding: 4px 8px;
  border-radius: 6px;
  transition: all 0.15s ease-in-out;
}

.size-badge:hover {
  transform: scale(1.08);
  background-color: #0d6efd !important;
  color: #fff !important;
}

.size-picker-btn {
  width: 40px;
  height: 34px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
}

.min-height-sizes {
  min-height: 48px;
}

.size-selected-badge {
  cursor: pointer;
  padding: 5px 10px;
  font-size: 0.82rem;
  transition: opacity 0.15s ease;
}

.size-selected-badge:hover {
  opacity: 0.8;
}

.action-dots-btn {
  font-size: 1.1rem;
  line-height: 1;
}

.placeholder-light::placeholder {
  color: #a0a0a0;
}

.cursor-pointer {
  cursor: pointer;
}

.style-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.style-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(128, 128, 128, 0.4);
  border-radius: 4px;
}
</style>
