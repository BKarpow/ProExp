<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

// Оголошуємо події (emits)
const emit = defineEmits(['success', 'error', 'ps']);
const props = defineProps({
    pid: {
        type: String,
        default: null
    }
});

// Стан компонента
const videoRef = ref(null);
const canvasRef = ref(null);
const stream = ref(null);
const isLoading = ref(false);
const expiryDate = ref(null);
const errorMessage = ref(null);
const isCameraOpen = ref(false);

// Запуск камери смартфона
const startCamera = async () => {
  errorMessage.value = null;
  try {
    const constraints = {
      video: {
        facingMode: 'environment',
        width: { ideal: 1280 },
        height: { ideal: 720 }
      },
      audio: false
    };

    stream.value = await navigator.mediaDevices.getUserMedia(constraints);
    if (videoRef.value) {
      videoRef.value.srcObject = stream.value;
      isCameraOpen.value = true;
    }
  } catch (err) {
    const cameraError = "Не вдалося отримати доступ до камери. Перевірте дозволи.";
    errorMessage.value = cameraError;

    // Трігеримо подію помилки назовні
    emit('error', cameraError);
    console.error("Помилка камери:", err);
  }
};

// Зупинка камери
const stopCamera = () => {
  if (stream.value) {
    stream.value.getTracks().forEach(track => track.stop());
  }
  isCameraOpen.value = false;
};

// Зробити фото та відправити на сервер
const captureAndDetect = async () => {
  if (!videoRef.value || !canvasRef.value) return;

  isLoading.value = true;
  errorMessage.value = null;
  expiryDate.value = null;

  const video = videoRef.value;
  const canvas = canvasRef.value;
  const context = canvas.getContext('2d');

  // 🌟 Оптимальний розмір для ШІ (максимум 800 пікселів по більшій стороні)
  const maxDimension = 800;
  let width = video.videoWidth;
  let height = video.videoHeight;

  // Пропорційно зменшуємо ширину та висоту
  if (width > maxDimension || height > maxDimension) {
    if (width > height) {
      height = Math.round((height * maxDimension) / width);
      width = maxDimension;
    } else {
      width = Math.round((width * maxDimension) / height);
      height = maxDimension;
    }
  }

  // Встановлюємо зменшені розміри для canvas
  canvas.width = width;
  canvas.height = height;
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  canvas.toBlob(async (blob) => {
    if (!blob) {
      isLoading.value = false;
      const captureError = "Не вдалося зафіксувати кадр.";
      errorMessage.value = captureError;
      emit('error', captureError); // Подія помилки
      return;
    }

    const formData = new FormData();

    formData.append('image', blob, 'capture.jpg');


    try {
      const response = await axios.post(route('detect.prices'), formData);

      if (response.data.success) {
        const detectedDate = response.data.data.name + ', ' + response.data.data.name;
        emit('ps', response.data.pathScreen);

        if (detectedDate) {
          expiryDate.value = detectedDate;

          // 🔥 Трігеримо подію успіху та передаємо дату
          emit('success', detectedDate);
        } else {
          const noDateError = "Термін придатності не знайдено на фото.";
          errorMessage.value = noDateError;

          // Можна вважати це помилкою розпізнавання
          emit('error', noDateError);
        }
      } else {
        const apiError = response.data.message || "Помилка розпізнавання.";
        errorMessage.value = apiError;
        emit('error', apiError); // Подія помилки
      }
    } catch (err) {
      let serverError = "Помилка мережі при з'єднанні з сервером.";

      if (err.response && err.response.data) {
        serverError = err.response.data.error || err.response.data.message || "Сталася помилка на сервері.";
      }

      errorMessage.value = serverError;

      // 🔥 Трігеримо подію на будь-яку серверну/мережеву помилку
      emit('error', serverError);
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  }, 'image/jpeg', 0.85);
};

onMounted(() => {
  startCamera();
});

onBeforeUnmount(() => {
  stopCamera();
});
</script>

<template>
  <div class="scanner-container">
    <h2 class="title">Сканер цінників</h2>

    <div class="video-wrapper">
      <video
        ref="videoRef"
        autoplay
        playsinline
        muted
        class="video-stream"
        :class="{ 'loading-blur': isLoading }"
      ></video>



      <div v-if="isLoading" class="loader-overlay">
        <div class="spinner"></div>
        <p>ШІ розпізнає цінник...</p>
      </div>
    </div>

    <canvas ref="canvasRef" style="display: none;"></canvas>

    <div class="controls">
      <button
        @click="captureAndDetect"
        :disabled="isLoading || !isCameraOpen"
        class="btn-capture"
      >
        {{ isLoading ? 'Обробка...' : 'Прочитати цінник' }}
      </button>

      <div v-if="expiryDate" class="result-box success">
        <strong>Знайдено дані:</strong>
        <span class="date-text">{{ expiryDate }}</span>
      </div>

      <div v-if="expiryDate === null && !isLoading" class="result-box warning">
        Не вдалося знайти або розпізнати назву на фото. Спробуйте ще раз.
      </div>

      <div v-if="errorMessage" class="result-box error">
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.scanner-container {
  max-width: 500px;
  margin: 0 auto;
  padding: 15px;
  font-family: sans-serif;
  text-align: center;
}

.title {
  font-size: 1.2rem;
  color: #333;
  margin-bottom: 15px;
}

.video-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 4 / 3;
  background-color: #000;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.video-stream {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: filter 0.3s;
}

.loading-blur {
  filter: blur(5px);
}

/* Рамка фокусування */
.scanner-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

.target-box {
  width: 70%;
  height: 35%;
  border: 3px dashed #00ffcc;
  border-radius: 8px;
  box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding-bottom: 10px;
}

.tip-text {
  color: #00ffcc;
  font-size: 0.8rem;
  background: rgba(0,0,0,0.6);
  padding: 2px 8px;
  border-radius: 4px;
}

/* Спіннер завантаження */
.loader-overlay {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(255,255,255,0.7);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #333;
  font-weight: bold;
}

.spinner {
  width: 40px; height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Кнопки та результати */
.controls {
  margin-top: 20px;
}

.btn-capture {
  width: 100%;
  padding: 14px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  box-shadow: 0 4px 6px rgba(0,123,255,0.2);
}

.btn-capture:disabled {
  background-color: #ccc;
  box-shadow: none;
}

.result-box {
  margin-top: 15px;
  padding: 12px;
  border-radius: 6px;
  font-size: 0.95rem;
}

.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.date-text {
  font-size: 1.1rem;
  font-weight: bold;
  margin-left: 5px;
}
</style>
