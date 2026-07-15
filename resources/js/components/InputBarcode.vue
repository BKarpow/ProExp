<template>
    <div class="barcode-input-container">
        <div class="input-group">

            <input
                type="text"
                v-model="barcode"
                @input="validateBarcode"
                :class="inputClasses"
                placeholder="Введіть штрих-код EAN-13 або скануйте"
                maxlength="13"
                :disabled="loading"
                ref="barcodeInput"
            >
            <button
                v-if="isMobileDevice && !isScanning"
                class="btn btn-outline-primary"
                type="button"
                @click="startScanner"
                :disabled="loading"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upc-scan" viewBox="0 0 16 16">
  <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z"/>
</svg>
            </button>
            <button
                v-else-if="isMobileDevice && isScanning"
                class="btn btn-outline-danger"
                type="button"
                @click="stopScanner"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sign-stop-fill" viewBox="0 0 16 16">
  <path d="M10.371 8.277v-.553c0-.827-.422-1.234-.987-1.234-.572 0-.99.407-.99 1.234v.553c0 .83.418 1.237.99 1.237.565 0 .987-.408.987-1.237m2.586-.24c.463 0 .735-.272.735-.744s-.272-.741-.735-.741h-.774v1.485z"/>
  <path d="M4.893 0a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146A.5.5 0 0 0 11.107 0zM3.16 10.08c-.931 0-1.447-.493-1.494-1.132h.653c.065.346.396.583.891.583.524 0 .83-.246.83-.62 0-.303-.203-.467-.637-.572l-.656-.164c-.61-.147-.978-.51-.978-1.078 0-.706.597-1.184 1.444-1.184.853 0 1.386.475 1.436 1.087h-.645c-.064-.32-.352-.542-.797-.542-.472 0-.77.246-.77.6 0 .261.196.437.553.522l.654.161c.673.164 1.06.487 1.06 1.11 0 .736-.574 1.228-1.544 1.228Zm3.427-3.51V10h-.665V6.57H4.753V6h3.006v.568H6.587Zm4.458 1.16v.544c0 1.131-.636 1.805-1.661 1.805-1.026 0-1.664-.674-1.664-1.805V7.73c0-1.136.638-1.807 1.664-1.807s1.66.674 1.66 1.807ZM11.52 6h1.535c.82 0 1.316.55 1.316 1.292 0 .747-.501 1.289-1.321 1.289h-.865V10h-.665V6.001Z"/>
</svg>
            </button>
            <span class="input-group-text">
                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Завантаження...</span>
                </div>
                <span v-else-if="barcode && validationStatus === 'valid'" class="text-success">
                    <i class="fas fa-check-circle"></i>
                </span>
                <span v-else-if="barcode && validationStatus === 'invalid'" class="text-danger">
                    <i class="fas fa-times-circle"></i>
                </span>
                <span v-else class="text-muted">
                    <i class="fas fa-barcode"></i>
                </span>
            </span>
        </div>

        <!-- Сканер -->
        <div v-if="isScanning" class="scanner-container mt-3">
            <div class="scanner-overlay">
                <video ref="videoElement" class="scanner-video w-100"></video>
                <div class="scanner-frame"></div>
                <div class="scanner-instructions text-center text-white mt-2">
                    Наведіть камеру на штрих-код
                </div>
            </div>
            <div class="text-center mt-2">
                <button class="btn btn-secondary btn-sm" @click="stopScanner">
                    <i class="fas fa-times me-1"></i> Скасувати сканування
                </button>
            </div>
        </div>

        <div v-if="showHelp" class="form-text text-muted mt-2">
            Штрих-код EAN-13 має містити рівно 13 цифр
        </div>

        <div v-if="errorMessage" class="alert alert-danger mt-2 mb-0" role="alert">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script>
import { BrowserMultiFormatReader, NotFoundException } from '@zxing/library';


export default {
    name: 'BarcodeInput',

    props: {
        value: {
            type: String,
            default: ''
        },
        showHelp: {
            type: Boolean,
            default: true
        },
        autoValidate: {
            type: Boolean,
            default: true
        },
        autoStartScanner: {
            type: Boolean,
            default: true
        }
    },

    data() {
        return {
            barcode: this.value,
            validationStatus: null,
            loading: false,
            errorMessage: '',
            isScanning: false,
            codeReader: null,
            isMobileDevice: false
        }
    },

    async mounted() {
        this.detectMobileDevice();
        this.codeReader = new BrowserMultiFormatReader();

        // Автоматичний запуск сканера на мобільних пристроях
        if (this.isMobileDevice && this.autoStartScanner && !this.barcode) {
            // Невелика затримка для ініціалізації компонента
            setTimeout(() => {
                this.startScanner();
            }, 1000);
        }
    },

    beforeUnmount() {
        this.stopScanner();
    },

    watch: {
        value(newVal) {
            this.barcode = newVal;
            if (this.autoValidate) {
                this.validateBarcode();
            }
        }
    },

    computed: {
        inputClasses() {
            return {
                'form-control': true,
                'is-valid': this.validationStatus === 'valid',
                'is-invalid': this.validationStatus === 'invalid',
                'loading': this.loading
            }
        }
    },

    methods: {
        detectMobileDevice() {
            this.isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },

        async startScanner() {
            try {
                this.isScanning = true;
                this.errorMessage = '';

                // Запит дозволу на використання камери
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                });

                const videoElement = this.$refs.videoElement;
                videoElement.srcObject = stream;
                videoElement.setAttribute('playsinline', true);

                await videoElement.play();

                // Запуск розпізнавання штрих-кодів
                this.codeReader.decodeFromVideoDevice(
                    null,
                    videoElement,
                    (result, error) => {
                        if (result) {
                            this.handleScannedCode(result.getText());
                        }

                        if (error && !(error instanceof NotFoundException)) {
                            console.error('Помилка сканування:', error);
                            this.errorMessage = 'Помилка сканування: ' + error.message;
                        }
                    }
                );

            } catch (error) {
                console.error('Помилка доступу до камери:', error);
                this.errorMessage = 'Не вдалося отримати доступ до камери. Перевірте дозволи.';
                this.stopScanner();
            }
        },

        stopScanner() {
            this.isScanning = false;

            if (this.codeReader) {
                this.codeReader.reset();
            }

            const videoElement = this.$refs.videoElement;
            if (videoElement && videoElement.srcObject) {
                const tracks = videoElement.srcObject.getTracks();
                tracks.forEach(track => track.stop());
                videoElement.srcObject = null;
            }
        },

        handleScannedCode(scannedCode) {
            // Очищаємо код від зайвих символів
            const cleanCode = scannedCode.replace(/[^\d]/g, '');

            // Перевіряємо чи це EAN-13 код
            if (cleanCode.length === 13) {
                this.barcode = cleanCode;
                this.validateBarcode();
                this.stopScanner();

                // Фокусуємося на полі вводу після сканування
                this.$nextTick(() => {
                    this.$refs.barcodeInput.focus();
                });
            } else {
                this.errorMessage = 'Сканований код не є валідним EAN-13 штрих-кодом';
            }
        },

        validateBarcode() {
            this.$emit('input', this.barcode);

            if (!this.barcode) {
                this.validationStatus = null;
                this.errorMessage = '';
                return;
            }

            if (this.barcode.length !== 13) {
                this.validationStatus = 'invalid';
                this.errorMessage = 'Штрих-код має містити рівно 13 цифр';
                return;
            }

            if (!/^\d+$/.test(this.barcode)) {
                this.validationStatus = 'invalid';
                this.errorMessage = 'Штрих-код має містити тільки цифри';
                return;
            }

            if (this.validateEAN13(this.barcode)) {
                this.validationStatus = 'valid';
                this.errorMessage = '';
                this.$emit('valid', this.barcode);
                beep();
            } else {
                this.validationStatus = 'invalid';
                this.errorMessage = 'Невірний штрих-код EAN-13';
                this.$emit('invalid', this.barcode);
                beep(450, 700, 0.2);
                beep(450, 700, 0.2);
            }
        },

        testBeep(){
            beep();
        },

        validateEAN13(barcode) {
            if (barcode.length !== 13) return false;

            const digits = barcode.split('').map(Number);
            const checkDigit = digits[12];

            let sum = 0;
            for (let i = 0; i < 12; i++) {
                sum += digits[i] * (i % 2 === 0 ? 1 : 3);
            }

            const calculatedCheckDigit = (10 - (sum % 10)) % 10;

            return checkDigit === calculatedCheckDigit;
        },

        clearValidation() {
            this.validationStatus = null;
            this.errorMessage = '';
        },

        reset() {
            this.barcode = '';
            this.clearValidation();
            this.stopScanner();
        },

        // Метод для ручного запуску сканера
        manualScan() {
            this.startScanner();
        }
    }
}
</script>

<style scoped>
.barcode-input-container {
    max-width: 400px;
}

.input-group-text {
    min-width: 50px;
    justify-content: center;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.fas {
    font-size: 1.1rem;
}

.scanner-container {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: #000;
}

.scanner-video {
    height: 300px;
    object-fit: cover;
}

.scanner-overlay {
    position: relative;
}

.scanner-frame {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 150px;
    border: 4px solid #00ff00;
    border-radius: 8px;
    box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.5);
}

.scanner-instructions {
    position: absolute;
    bottom: 10px;
    left: 0;
    right: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}

@media (max-width: 576px) {
    .scanner-video {
        height: 250px;
    }

    .scanner-frame {
        width: 180px;
        height: 80px;
    }
}
</style>
