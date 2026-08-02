<template>
  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Моделі взуття</h2>
      <button class="btn btn-primary" @click="openModal()">
        <i class="bi bi-plus-lg"></i> Додати модель
      </button>
    </div>

    <div class="table-responsive shadow-sm rounded">
      <table class="table table-hover table-striped align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Модель</th>
            <th scope="col">Опис</th>
            <th scope="col" class="text-end">Дії</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="5" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Завантаження...</span>
              </div>
            </td>
          </tr>
          <tr v-else-if="categories.length === 0">
            <td colspan="5" class="text-center py-4 text-muted">
              Записів не знайдено
            </td>
          </tr>
          <tr v-else v-for="category in categories" :key="category.id">
            <td>{{ category.id }}</td>
            <td class="fw-bold">{{ category.name }}</td>
            
            <td>{{ category.desc || '—' }}</td>
            <td class="text-end">
              <button 
                class="btn btn-sm btn-outline-warning me-2" 
                @click="openModal(category)"
                title="Редагувати"
              >
                ✏️
              </button>
              <button 
                class="btn btn-sm btn-outline-danger" 
                @click="deleteCategory(category.id)"
                title="Видалити"
              >
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <nav v-if="pagination.last_page > 1" class="mt-3">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
          <button class="page-link" @click="fetchCategories(pagination.current_page - 1)">Попередня</button>
        </li>
        <li 
          v-for="page in pagination.last_page" 
          :key="page" 
          class="page-item" 
          :class="{ active: page === pagination.current_page }"
        >
          <button class="page-link" @click="fetchCategories(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
          <button class="page-link" @click="fetchCategories(pagination.current_page + 1)">Наступна</button>
        </li>
      </ul>
    </nav>

    <div v-if="showModal" class="modal fade show d-block tab-index='-1'" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ isEditing ? 'Редагувати модель' : 'Створити нову' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="saveCategory">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Модель <span class="text-danger">*</span></label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  class="form-control" 
                  :class="{ 'is-invalid': errors.name }"
                  required
                >
                <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
              </div>

              

              <div class="mb-3">
                <label class="form-label">Опис</label>
                <textarea 
                  v-model="form.description" 
                  class="form-control" 
                  rows="3"
                  :class="{ 'is-invalid': errors.description }"
                ></textarea>
                <div v-if="errors.description" class="invalid-feedback">{{ errors.description[0] }}</div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Скасувати</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
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
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

// Базовий URL вашого API (припустимо, що це /api/categories)
const API_URL = '/rapi/shoes/models';

// Стан компонента
const categories = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
});

const form = reactive({
  name: '', // значення за замовчуванням
  desc: ''
});

const errors = ref({});

// 1. Отримання списку категорій з пагінацією
const fetchCategories = async (page = 1) => {
  loading.value = true;
  try {
    const response = await axios.get(`${API_URL}?page=${page}`);
    categories.value = response.data.data;
    pagination.current_page = response.data.current_page;
    pagination.last_page = response.data.last_page;
  } catch (error) {
    console.error('Помилка завантаження даних:', error);
  } finally {
    loading.value = false;
  }
};

// 2. Відкриття модалки (для створення або редагування)
const openModal = (category = null) => {
  errors.value = {};
  if (category) {
    isEditing.value = true;
    currentId.value = category.id;
    form.name = category.name;
    form.desc = category.description;
  } else {
    isEditing.value = false;
    currentId.value = null;
    form.name = '';
    // form.days_notifications = 3;
    form.desc = '';
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

// 3. Створення або Оновлення (Store / Update)
const saveCategory = async () => {
  saving.value = true;
  errors.value = {};
  try {
    if (isEditing.value) {
      await axios.put(`${API_URL}/${currentId.value}`, form);
    } else {
      await axios.post(API_URL, form);
    }
    closeModal();
    fetchCategories(pagination.current_page);
  } catch (error) {
    if (error.response && error.response.status === 422) {
      // Помилки валідації Laravel
      errors.value = error.response.data.errors;
    } else {
      console.error('Помилка збереження:', error);
    }
  } finally {
    saving.value = false;
  }
};

// 4. Видалення (Destroy)
const deleteCategory = async (id) => {
  if (!confirm('Ви дійсно бажаєте видалити цю групу товарів?')) return;
  
  try {
    await axios.delete(`${API_URL}/${id}`);
    fetchCategories(pagination.current_page);
  } catch (error) {
    console.error('Помилка видалення:', error);
  }
};

// Завантажуємо дані при монтуванні компонента
onMounted(() => {
  fetchCategories();
});
</script>