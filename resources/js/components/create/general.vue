<template>
    <div>
        <bank :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'bank_create_form_general'" @created="$emit('created')" />
        <bankAccount :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'bank-account-create-general'" @created="$emit('created')" />
        <branch :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'branch-create-general'" @created="$emit('created')" />
        <employee :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="$emit('created')" />
        <customerGeneral :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="$emit('created')" />
        <country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'country-create-general'" @created="$emit('created')" />
        <city :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'city-create-general'" @created="$emit('created')" />
        <propertyTree :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="$emit('created')" />
        <avenue :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'avenues-create-general'" @created="$emit('created')" />
    </div>
</template>

<script>
import employee from "../../components/create/employee.vue";
import avenue from "../../components/create/avenue";
import bank from "../../components/create/bank";
import bankAccount from "../../components/create/bankAccount";
import branch from "../../components/create/branch";
import customerGeneral from "../../components/create/customerGeneral";
import country from "../../components/country";
import city from "../../components/city";
import propertyTree from "./property-tree"
import Layout from "../../views/layouts/main";
import PageHeader from "../Page-header";
import Switches from "vue-switches";
import ErrorMessage from "../widgets/errorMessage";
import loader from "../loader";
import Multiselect from "vue-multiselect";

export default {
    name: "general",
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        employee,
        customerGeneral,
        propertyTree,
        country,
        city,
        bank,
        bankAccount,
        branch,
        avenue
    },

    props:[
        "currentNode",
        "companyKeys",
        "defaultsKeys"
    ],
    methods:{

    }
}
</script>

<style scoped>

</style>
