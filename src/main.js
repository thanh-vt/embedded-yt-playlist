import { createApp } from 'vue'
import App from './App.vue'


if (!rootId) {
    var rootId = '#app';
}

createApp(App).mount(rootId)
