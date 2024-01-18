<template>
    <div>
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit' ?getCompanyKey('role_create_form'):getCompanyKey('role_edit_form')"
            dialog-class="modal-full-width"
            title-class="font-18"
            body-class="p-4 "
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <div class="mb-3 d-flex justify-content-end">
                    <b-button
                        v-if="type != 'edit'"
                        variant="success"
                        :disabled="!is_disabled"
                        @click.prevent="resetForm"
                        type="button"
                        :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
                    >
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button
                            variant="success"
                            type="button"
                            class="mx-1"
                            v-if="!isLoader"
                            @click.prevent="AddSubmit"
                        >
                            {{ type != 'edit' ? $t("general.Add") : $t("general.edit") }}
                        </b-button>

                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <!-- Emulate built in modal footer ok and cancel button actions -->

                    <b-button
                        variant="danger"
                        type="button"
                        @click.prevent="resetModalHidden"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("role_name_ar") }}
                                <span class="text-danger">*</span>
                            </label>
                            <div>
                                <input
                                    type="text"
                                    @keyup="arabicValueName(create.name)"
                                    class="form-control"
                                    v-model="$v.create.name.$model"
                                    :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }"
                                    id="field-1"
                                />
                            </div>
                            <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.name.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.name.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.name">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.name"
                                    :key="index"
                                >
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey("role_name_en") }}
                                <span class="text-danger">*</span>
                            </label>
                            <div>
                                <input
                                    @keyup="englishValueName(create.name_e)"
                                    type="text"
                                    class="form-control"
                                    v-model="$v.create.name_e.$model"
                                    :class="{
                                                'is-invalid': $v.create.name_e.$error || errors.name_e,
                                                'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                                              }"
                                    id="field-2"
                                />
                            </div>
                            <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.name_e.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.name_e.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.name_e">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.name_e"
                                    :key="index"
                                >
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2" v-for="(module,index) in modules">
                        <div class="accordion" role="tablist">
                            <b-card no-body class="mb-1">
                                <b-card-header header-tag="header" class="p-1" role="tab">
                                    <b-button class="btn-accordion-custom" block
                                              v-b-toggle="`accordion-create-${index}`" variant="info">{{ module }}
                                    </b-button>
                                </b-card-header>
                                <b-collapse :id="`accordion-create-${index}`" accordion="my-accordion" role="tabpanel">
                                    <b-card-body class="bg-white">
                                        <div class="row justify-content-end mb-2">
                                            <b-button @click="selectAll(module,'create',index)" class="mx-1"
                                                      variant="outline-info">
                                                {{ $t('general.selectALL') }}
                                                <span style="margin: 0px 4px;"><input
                                                    v-model="checkAllPermission[index]" value="check" type="radio"
                                                    class=""></span>
                                            </b-button>
                                            <b-button @click="notSelectAll(module,'create',index)" class="mx-1"
                                                      variant="outline-info">
                                                {{ $t('general.notSelectALL') }}
                                                <span style="margin: 0px 4px;"><input
                                                    v-model="checkAllPermission[index]" value="notCheck" type="radio"
                                                    class=""></span>
                                            </b-button>
                                        </div>
                                        <div class="row">
                                            <template v-for="item in tables.filter(el => el.module == module)">
                                                <div class="col-2 mb-2">
                                                    <b-dropdown
                                                        menu-class="w-100"
                                                        variant="primary"
                                                        class="dropdown-permission dropdown-menu-custom-company"
                                                        :html="item.table"
                                                    >
                                                        <div class="mb-1">
                                                            <b-button v-b-tooltip.hover
                                                                      :title="$t('general.customTable')" size="sm"
                                                                      class="mx-1" variant="info">
                                                                <i class="fas fa-couch"></i>
                                                            </b-button>
                                                            <b-button v-b-tooltip.hover
                                                                      :title="$t('general.translation')" size="sm"
                                                                      class="mx-1" variant="info">
                                                                <i class="fas fa-globe"></i>
                                                            </b-button>
                                                            <b-button v-b-tooltip.hover
                                                                      :title="$t('general.additionalRule')" size="sm"
                                                                      class="mx-1" variant="info">
                                                                <i class="fas fa-plus"></i>
                                                            </b-button>
                                                            <!-- Add button to get the hotfields of the table -->
                                                            <b-button @click="getTableHotFields(item.table);"
                                                                      v-b-tooltip:hover :title="$t('general.hotFields')"
                                                                      size="sm" class="mx-1" variant="info">
                                                                <i class="fas fa-fire"></i>
                                                            </b-button>
                                                        </div>
                                                        <template
                                                            v-for="da in filterPermission(item.table)"
                                                        >
                                                            <b-form-checkbox
                                                                :value="da.id"
                                                                v-model="$v.create.permissions.$model"
                                                                class="mb-1"
                                                            >
                                                                {{ da.title }}
                                                            </b-form-checkbox>
                                                        </template>
                                                    </b-dropdown>
                                                </div>
                                            </template>
                                        </div>
                                    </b-card-body>
                                </b-collapse>
                            </b-card>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->

        <b-modal
            id="hot_keys"
            :title="getCompanyKey('hot_keys_form','Hot Keys')"
            dialog-class="modal-full-width"
            size="xl"
            title-class="font-18"
            body-class="p-4 "
            :hide-footer="true"
        >

        <!--            @show="resetModal"-->
<!--            @hidden="resetModalHidden"-->
            <form>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <b-card no-body class="mb-1">
<!--                            <b-card-header header-tag="header" class="p-1" role="tab">
                                <b-button class="btn-accordion-custom" block
                                          v-b-toggle="`accordion-create-${index}`" variant="info">{{ module }}
                                </b-button>
                            </b-card-header>-->
                            <b-card-body class="bg-white">
                                <div class="row">
                                    <template v-for="item in hotkeys">
                                        <div class="col-2 mb-2">
                                            <b-dropdown
                                                menu-class="w-100"
                                                variant="primary"
                                                class="dropdown-permission dropdown-menu-custom-company"
                                                :html="item.column_name"
                                            >
                                                <b-form-checkbox
                                                    :value=true
                                                    v-model="added_hotkeys[selected_table][item.column_name].can_see"
                                                    @input="changeCanSee(selected_table,item.column_name)"
                                                    class="mb-1"
                                                >
                                                    {{ $t('is_visible') }}
                                                </b-form-checkbox>
                                                <b-form-checkbox
                                                    v-if="added_hotkeys[selected_table][item.column_name].can_see"
                                                    :value=true
                                                    v-model="added_hotkeys[selected_table][item.column_name].can_edit"
                                                    class="mb-1"
                                                >
                                                    {{ $t('Edit') }}
                                                </b-form-checkbox>
                                                <b-form-checkbox
                                                    v-if="added_hotkeys[selected_table][item.column_name].can_see"
                                                    :value=true
                                                    v-model="added_hotkeys[selected_table][item.column_name].can_plus"
                                                    class="mb-1"
                                                >
                                                    {{ $t('Can be increased') }}
                                                </b-form-checkbox>
                                                <b-form-input
                                                    v-if="added_hotkeys[selected_table][item.column_name].can_see"
                                                    v-model="added_hotkeys[selected_table][item.column_name].percentage"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    :placeholder="$t('percentage')"
                                                    class="mb-1"
                                                    :disabled="!added_hotkeys[selected_table][item.column_name].can_plus"
                                                >
                                                </b-form-input>
                                            </b-dropdown>
                                        </div>
                                    </template>
                                </div>
                            </b-card-body>
                        </b-card>
                    </div>
                </div>
            </form>
        </b-modal>

    </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import {maxLength, minLength, required} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    mixins: [transMixinComp, successError],
    components: {
        ErrorMessage,
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true}, isVisiblePage: {default: null}, isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null}, isPermission: {}, url: {default: '/roles'}
    },
    data() {
        return {
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                permissions: [],
                added_hotkeys : {}
            },
            errors: {},
            isCheckAll: false,
            checkAllPermission: [],
            current_page: 1,
            is_disabled: false,
            permissions_range: [],
            permissions: [],
            hotkeys: [],
            added_hotkeys: {},
            tables: [],
            modules: [],
            company_id: null,
            selected_table: '',
        };
    },
    validations: {
        create: {
            name: {
                required,
                minLength: minLength(3),
                maxLength: maxLength(100),
            },
            name_e: {
                required,
                minLength: minLength(3),
                maxLength: maxLength(100),
            },
            permissions: {required}
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getPermission();
    },
    methods: {
        getPermission() {
            this.isLoader = true;
            let workflows = '';
            this.$store.state.auth.work_flow_trees.forEach((el, i) => workflows += `workflows[${i}]=${el}&`);
            adminApi
                .get(
                    `/roles/permissions?${workflows}`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.forEach(el => {
                        this.permissions_range.push(el.id)
                        if( !this.modules.includes(el.module) ){
                            this.modules.push(el.module);
                            this.checkAllPermission.push('');
                        }
                        if (!this.tables.find(e => e.table == el.crud_name)) {
                            this.tables.push({table: el.crud_name, module: el.module});
                        }
                    });
                    this.permissions = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        defaultData(edit = null) {
            this.create = {name: "", name_e: "", permissions: []};
            this.checkAllPermission.forEach((el, index) => this.checkAllPermission[index] = '');
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout(() => {
                if (this.type != 'edit') {
                    return;
                } else {
                    if (this.idObjEdit.dataObj) {
                        // reset added_hotkeys
                        this.added_hotkeys = {};
                        // reset hotkeys
                        this.hotkeys = [];

                        let module = this.idObjEdit.dataObj;
                        this.create.name = module.name;
                        this.create.name_e = module.name_e;
                        let items = [];
                        module.permissions.forEach((el) => {
                            items.push(el.id);
                        });
                        this.create.permissions = items;
                        module.hotkeys.forEach((el) =>{
                            if (!this.added_hotkeys.hasOwnProperty(el.table)) {
                                this.$set(this.added_hotkeys, el.table, {});
                            }

                            // Now, safely set or update the column_name key
                            if (!this.added_hotkeys[el.table].hasOwnProperty(el.column_name)) {
                                this.$set(this.added_hotkeys[el.table], el.column_name, {
                                    can_see: el.can_see == 1,
                                    can_edit: el.can_edit == 1 ,
                                    can_plus: el.can_plus == 1,
                                    percentage: el.percentage,
                                });
                            }
                        })
                    }
                }
            }, 50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {

            if (!this.create.name_e) {
                this.create.name_e = this.create.name;
            }
            if (!this.create.name) {
                this.create.name = this.create.name_e;
            }
            this.create.added_hotkeys = this.added_hotkeys;
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                if (this.type != 'edit') {
                    adminApi
                        .post(this.url, {...this.create, company_id: this.company_id})
                        .then((res) => {
                            this.is_disabled = true;
                            if (!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");

                            this.handleCreatedEvent();
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error', 'Thereisanerrorinthesystem');

                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                } else {
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create,
                            permissions_range:this.permissions_range
                        })
                        .then((res) => {
                            this.$bvModal.hide(this.id);
                            this.$emit("getDataTable");
                            this.handleCreatedEvent();
                            setTimeout(() => {
                                this.successFun('Editsuccessfully');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }
            }
        },
        handleCreatedEvent() {
            // reset added_hotkeys
            this.added_hotkeys = {};
            // reset hotkeys
            this.hotkeys = [];
        },
        editSubmit(id) {
            if (!this.edit.name) {
                this.edit.name = this.edit.name_e;
            }
            if (!this.edit.name_e) {
                this.edit.name_e = this.edit.name;
            }

            this.$v.edit.$touch();

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.edit;
                adminApi
                    .put(`/roles/${id}`, {...this.edit})
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Editsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        filterPermission(table) {
            let data = this.permissions.filter(el => el.crud_name == table);
            return data;
        },
        selectAll(module, data, index) {
            let permission = this.permissions.filter(el => el.module == module);
            this.checkAllPermission[index] = 'check';
            if (data == 'create') {
                permission.forEach(e => {
                    if (!this.create.permissions.includes(e.id)) {
                        this.create.permissions.push(e.id);
                    }
                });
            } else {
                permission.forEach(e => {
                    if (!this.edit.permissions.includes(e.id)) {
                        this.edit.permissions.push(e.id);
                    }
                });
            }
        },
        notSelectAll(module, data, index) {
            let permission = this.permissions.filter(el => el.module == module);
            this.checkAllPermission[index] = 'notCheck';
            if (data == 'create') {
                permission.forEach(el => {
                    if (this.create.permissions.includes(el.id)) {
                        let index = this.create.permissions.indexOf(el.id);
                        this.create.permissions.splice(index, 1);
                    }
                });
            } else {
                permission.forEach(el => {
                    if (this.edit.permissions.includes(el.id)) {
                        let index = this.edit.permissions.indexOf(el.id);
                        this.edit.permissions.splice(index, 1);
                    }
                });
            }
        },
        arabicValueName(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt) {
            this.create.name_e = englishValue(txt);
        },
        getTableHotFields(table) {
            this.isLoader = true;
            adminApi
                .post(
                    `/roles/get-hotkeys`,
                    // pass table name to the api
                    {
                        table: table,
                        company_id: this.company_id,
                    }
                )
                .then((res) => {
                    this.selected_table = table;
                    this.hotkeys = res.data.data;
                    if (this.hotkeys != null)
                    {
                        this.hotkeys.forEach((el) => {
                            if (!this.added_hotkeys.hasOwnProperty(table)) {
                                this.$set(this.added_hotkeys, table, {});
                            }

                            // Now, safely set or update the column_name key
                            if (!this.added_hotkeys[table].hasOwnProperty(el.column_name)) {
                                this.$set(this.added_hotkeys[table], el.column_name, {
                                    can_see: true,
                                    can_edit: true,
                                    can_plus: true,
                                    percentage: 0,
                                });
                            }
                        })
                    }

                    this.$bvModal.show('hot_keys');
                })
                .catch((err) => {
                    console.log(err);
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        changeCanSee(table, column_name) {
            if (!this.added_hotkeys[table][column_name].can_see) {
                this.added_hotkeys[table][column_name].can_edit = false;
                this.added_hotkeys[table][column_name].can_plus = false;
                this.added_hotkeys[table][column_name].percentage = 0;
            }
        },
    },
};
</script>

<style scoped>
.dropdown-permission {
    width: 100% !important;
}

.card-body {
    padding: 1.5rem !important;
}

.dropdown-menu-custom-company.dropdown .dropdown-menu {
    overflow-y: scroll;
    height: 300px;
}

.accordion > .card {
    overflow: unset !important;
}

.btn-accordion-custom {
    overflow-anchor: none;
    font-weight: 700;
    color: yellow;
    font-size: 22px;
}

form {
    position: relative;
}
</style>
