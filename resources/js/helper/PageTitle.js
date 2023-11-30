import Vue from "vue";

const page_title = Vue.observable({
    value: localStorage.getItem("page_title") ? JSON.parse(localStorage.getItem("page_title")):[],
});


export default page_title;
