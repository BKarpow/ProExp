/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import 'cropperjs/dist/cropper.min.js';
import VCalendar from 'v-calendar';
import 'v-calendar/dist/style.css';
import vSelect from "vue-select";
import { ZiggyVue } from '../../vendor/tightenco/ziggy';



import { registerSW } from 'virtual:pwa-register';

// Автоматичне оновлення Service Worker при зміні коду
const updateSW = registerSW({
  onNeedRefresh() {
      if (confirm('Доступна нова версія. Оновити?')) {
          updateSW(true);
      }
  },
  onOfflineReady() {
      console.log('Застосунок готовий до роботи офлайн!');
  },
});


/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

app.use(VCalendar, {});
app.use(ZiggyVue, {});

import ExampleComponent from './components/ExampleComponent.vue';
import ImageUploader from './components/ImageUploader.vue';
import InputBarcode from './components/InputBarcode.vue';
import CreateNewProduct from './components/CreateNewProduct.vue';
import ImageModal from './components/ImageModal.vue';
import InputDate from './components/InputDate.vue';
import CreateNewDateProduct from './components/CreateNewDateProduct.vue';
import SelectShopAndGroup from './components/SelectShopAndGroup.vue';
import PhoneInput from './components/PhoneInput.vue';
import DeleteButton from './components/DeleteButton.vue';
import DigitalLoupe from './components/DigitalLoupe.vue';
import ShowMagnify from './components/ShowMagnify.vue';
import FloatingActionButton from './components/FloatingActionButton.vue';
import InstallPWA from './components/InstallPWA.vue';
import SearchPaginate from './components/SearchPaginate.vue';
import CreateInventory from './components/CreateInventory.vue';
import InputPassword from './components/InputPassword.vue';
import ToDoList from './components/ToDoList.vue';
import ExpiryDateScanner from './components/ExpiryDateScanner.vue';
import DetectExp from './components/DetectExp.vue';
import DetectPrices from './components/DetectPrices.vue';
import CategoriesManager from './components/CategoriesManager.vue';
import ModelsManager from './components/ModelsManager.vue';
app.component('delete-btn', DeleteButton);
app.component('input-date', InputDate);
app.component('example-component', ExampleComponent);
app.component('input-barcode', InputBarcode);
app.component('image-upload', ImageUploader);
app.component('create-product', CreateNewProduct);
app.component('create-date', CreateNewDateProduct);
app.component('image-modal', ImageModal);
app.component('select-shop', SelectShopAndGroup );
app.component('phone-input', PhoneInput );
app.component('zoom', DigitalLoupe );
app.component('magnify', ShowMagnify );
app.component('fab', FloatingActionButton );
app.component('pwa', InstallPWA  );
app.component('search-date', SearchPaginate );
app.component("v-select", vSelect);
app.component("create-inventory", CreateInventory);
app.component("p-input", InputPassword);
app.component("todo", ToDoList );
app.component("date-scaner", ExpiryDateScanner);
app.component("detect-exp", DetectExp);
app.component("detect-prices", DetectPrices);
app.component("s-group", CategoriesManager);
app.component("s-models", ModelsManager);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW registered!', reg))
            .catch(err => console.error('SW registration failed!', err));
    });
}
