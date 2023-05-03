<template>
    <b-modal
        size="lg"
        :id="id"
        :title="getCompanyKey('boardRent_quotation_create_form')"
        title-class="font-18"
        body-class="p-4 "
        @hidden="resetModalHidden"
        :hide-footer="true"
    >
        <div class="row">

        </div>
    </b-modal>
</template>

<script>
import transMixinComp from "../../../helper/translation-comp-mixin";

export default {
    name: "quotation-panel",
    mixins: [transMixinComp],
    props: ["companyKeys", "defaultsKeys","id"],
    data() {
        return {
            panel: null
        }
    },
    methods: {
        resetModalHidden() {
            this.panel = null;
            this.$bvModal.hide(this.id);
        },
    }
}
</script>

<style scoped>

</style>
