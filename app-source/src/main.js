import Vue from 'vue'
import App from './App.vue'

Vue.config.productionTip = false

const mountEl = document.querySelector("#app");

new Vue({
    propsData: { ...mountEl.dataset },
    props: ['src', 'proxy'],
    render: h => h(App),
}).$mount('#app')
