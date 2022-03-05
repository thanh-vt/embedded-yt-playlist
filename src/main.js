import Vue from 'vue'
import App from './App.vue'
// import Carousel3d from 'vue-carousel-3d';
//
// Vue.use(Carousel3d);

if (!rootId) {
    var rootId = '#app';
}

new Vue(App).$mount(rootId);

