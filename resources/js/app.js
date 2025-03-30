import './bootstrap';
import { createApp } from 'vue';

// Importuj Vue komponenty
import ExampleComponent from './components/ExampleComponent.vue';

const app = createApp({});

// Globálne registruj komponenty (voliteľné)
app.component('example-component', ExampleComponent);

// Mountni aplikáciu do `#app`, ak existuje
app.mount('#app');

