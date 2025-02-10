import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import { createVuetify } from 'vuetify';
import vueFilePond from 'vue-filepond';
import 'vuetify/styles';
import 'primeicons/primeicons.css';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import App from './components/CarBrandComponent.vue';
import router from './router/index.js';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import Aura from '@primevue/themes/aura';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import '@mdi/font/css/materialdesignicons.css'

const vuetify = createVuetify({
    components,
    directives,
});

const pinia = createPinia();
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview);

const app = createApp(App)
    .use(router)
    .use(vuetify)
    .use(pinia)
    .use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: 'true',
                cssLayer: {
                    name: 'primevue',
                }
            }
        }
    })
    .component('FilePond', FilePond);

app.mount('#app');
