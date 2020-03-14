
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


import VTooltip from 'v-tooltip'

import VeeValidate from 'vee-validate'

import VueMasonry from 'vue-masonry-css'

import VuejsDialog from "vuejs-dialog"

window.moment = require('moment');

window.moment.locale();

window.Vue = require('vue');


/* moment */
Vue.filter('humanReadableTime', function (value) {
    return moment(value , "YYYY-MM-DD HH:mm:ss").fromNow();
});

Vue.use(VTooltip);

Vue.use(VueMasonry);

Vue.use(VeeValidate);

Vue.use(VuejsDialog,{
    html: true,
    loader: false,
    okText: 'Proceed',
    cancelText: 'Cancel',
    animation: 'zoom' });

const options = {
    color: '#bffaf3',
    failedColor: '#874b4b',
    thickness: '5px',
    transition: {
        speed: '0.2s',
        opacity: '0.6s',
        termination: 300
    },
    autoRevert: true,
    location: 'left',
    inverse: false
}
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('login', require('./components/client/Login.vue'));
Vue.component('register', require('./components/client/Register.vue'));
Vue.component('phone-confirmation', require('./components/client/PhoneConfirmation.vue'));
Vue.component('navigation', require('./components/client/Navigation.vue'));
Vue.component('items', require('./components/client/Items.vue'));
Vue.component('my-items', require('./components/client/MyItems.vue'));
Vue.component('profile', require('./components/client/Profile.vue'));
Vue.component('another-user-profile', require('./components/client/AnotherUserProfile.vue'));

const app = new Vue({
    el: '#app'
});
