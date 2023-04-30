<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer, decimal, minValue } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/translation-mixin";
import Multiselect from "vue-multiselect";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Owner from "../../../components/create/realEstate/owner";
import Building from "../../../components/create/building";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Wallet",
        meta: [{ name: "Wallets", content: 'Wallets' }],
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {

            if (vm.$store.state.auth.work_flow_trees.includes("real estate-e")) {
                Swal.fire({
                    icon: "error",
                    title: `${vm.$t("general.Error")}`,
                    text: `${vm.$t("general.ModuleExpired")}`,
                });
                return vm.$router.push({ name: "home" });
            }

            if (vm.$store.state.auth.work_flow_trees.includes('wallets') || vm.$store.state.auth.work_flow_trees.includes('real estate') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Owner,
        Building,
        Multiselect
    },
    data() {
        return {
            tab: '',
            per_page: 50,
            search: '',
            debounce: {},
            walletsPagination: {},
            wallets: [],
            parents: [],
            enabled3: true,
            isLoader: false,
            create: {
                name: '',
                name_e: '',
            },
            edit: {
                name: '',
                name_e: '',
            },
            createOwner: {
                wallet_owners: [
                    {
                        owner_id: null,
                        percentage: 0,
                    }
                ]
            },
            editOwner: {
                wallet_owners: []
            },
            createBuilding: {
                wallet_id: null,
                building_id: null,
                bu_ty: "active",
            },
            editBuilding: {
                wallet_id: null,
                building_id: null,
                bu_ty: "active",
            },
            wallet_id: null,
            errors: {},
            owners: [],
            buildings: [],
            dropDownSenders: [],
            is_disabled: false,
            is_disabledOwner: false,
            is_disabledBuilding: false,
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            setting: {
                name: true,
                name_e: true,
            },
            filterSetting: ['name', 'name_e'],
            Tooltip: '',
            mouseEnter: null,
            printLoading: true,
            is_persentage: true,
            printObj: {
                id: "printWallet",
            }
        }
    },
    validations: {
        create: {
            name: { required, minLength: minLength(3), maxLength: maxLength(100) },
            name_e: { required, minLength: minLength(3), maxLength: maxLength(100) },
        },
        edit: {
            name: { required, minLength: minLength(3), maxLength: maxLength(100) },
            name_e: { required, minLength: minLength(3), maxLength: maxLength(100) },
        },
        createOwner: {
            wallet_id: { required },
            wallet_owners: {
                required,
                $each: {
                    owner_id: { required },
                    percentage: { required, decimal, minValue: minValue(0.01) },
                }
            }
        },
        editOwner: {
            wallet_id: { required },
            wallet_owners: {
                required,
                $each: {
                    owner_id: { required },
                    percentage: { required, decimal, minValue: minValue(0.01) },
                }
            }
        },
        createBuilding: {
            building_id: { required },
            wallet_id: { required },
            bu_ty: { required },
        },
        editBuilding: {
            building_id: { required },
            wallet_id: { required },
            bu_ty: { required },
        },
    },
    watch: {
        /**
         * watch per_page
         */
        per_page(after, befour) {
            this.getData();
        },
        /**
         * watch search
         */
        search(after, befour) {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getData();
            }, 400);
        },
        /**
         * watch check All table
         */
        isCheckAll(after, befour) {
            if (after) {
                this.wallets.forEach(el => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
        }
    },
    mounted() {
        this.getData();
    },
    methods: {
        addNewField() {
            this.createOwner.wallet_owners.push({
                owner_id: null,
                percentage: 0,
            });
        },
        removeNewField(index) {
            if (this.createOwner.wallet_owners.length > 1) {
                this.createOwner.wallet_owners.splice(index, 1);
                let totel = this.createOwner.wallet_owners.reduce((accumulator, curValue) => parseFloat(accumulator) + parseFloat(curValue.percentage), 0);
                this.is_persentage = totel == 100 ? true : false;
            }
        },
        addNewFieldEdit() {
            this.editOwner.wallet_owners.push({
                owner_id: null,
                percentage: 0,
            });
        },
        removeNewFieldEdit(index) {
            if (this.editOwner.wallet_owners.length > 1) {
                this.editOwner.wallet_owners.splice(index, 1);
                let totel = this.editOwner.wallet_owners.reduce((accumulator, curValue) => parseFloat(accumulator) + parseFloat(curValue.percentage), 0);
                this.is_persentage = totel == 100 ? true : false;
            }
        },
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
            this.edit.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
            this.edit.name_e = englishValue(txt);
        },
        async getOwner() {
            this.isLoader = true;

            await adminApi
                .get(`/real-estate/owners`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف مالك  ", name_e: "Add Owner" });
                    this.owners = l;
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
        async getBuildings() {
            this.isLoader = true;
            await adminApi
                .get(`/real-estate/buildings`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة مبنى", name_e: "Add building" });
                    this.buildings = l;
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
        showOwnerModal(index) {
            if (this.createOwner.wallet_owners[index].owner_id == 0) {
                console.log(this.createOwner.wallet_owners[index].owner_id);
                this.$bvModal.show("owner-create");
                this.createOwner.wallet_owners[index].owner_id = null;
            }
        },
        showOwnerEditModal(index) {
            if (this.editOwner.wallet_owners[index].owner_id == 0) {
                this.$bvModal.show("owner-create");
                console.log(this.editOwner.wallet_owners[index].owner_id);
                this.editOwner.wallet_owners[index].owner_id = null;
            }
        },
        showBuildingModal() {
            if (this.createBuilding.building_id == 0) {
                this.$bvModal.show("building-create");
                this.createBuilding.building_id = null;
            }
        },
        showBuildingModalEdit() {
            if (this.editBuilding.building_id == 0) {
                this.$bvModal.show("building-create");
                this.editBuilding.building_id = null;
            }
        },
        changeNumber(add) {
            if (add == 'add') {
                let totel = this.createOwner.wallet_owners.reduce((accumulator, curValue) => parseFloat(accumulator) + parseFloat(curValue.percentage), 0);
                this.is_persentage = totel == 100 ? true : false;
            } else {
                let totel = this.editOwner.wallet_owners.reduce((accumulator, curValue) => parseFloat(accumulator) + parseFloat(curValue.percentage), 0);
                this.is_persentage = totel == 100 ? true : false;
            }
        },
        /**
         *  start get Data module && pagination
         */
        getData(page = 1) {
            this.isLoader = true;
            let filter = '';
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }
            adminApi.get(`/real-estate/wallets?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                .then((res) => {
                    let l = res.data;
                    this.wallets = l.data;
                    this.walletsPagination = l.pagination;
                    this.current_page = l.pagination.current_page;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: 'error',
                        title: `${this.$t('general.Error')}`,
                        text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getDataCurrentPage(page = 1) {
            if (this.current_page <= this.walletsPagination.last_page && this.current_page != this.walletsPagination.current_page && this.current_page) {
                this.isLoader = true;
                let filter = '';
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi.get(`/real-estate/wallets?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                    .then((res) => {
                        let l = res.data;
                        this.wallets = l.data;
                        this.walletsPagination = l.pagination;
                        this.current_page = l.pagination.current_page;
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: 'error',
                            title: `${this.$t('general.Error')}`,
                            text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                        });
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        /**
         *  end get Data module && pagination
         */
        /**
         *  start delete module
         */
        deleteModule(id, index) {
            if (Array.isArray(id)) {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;
                        adminApi
                            .post(`/real-estate/wallets/bulk-delete`, { ids: id })
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })
                            .catch((err) => {
                                if (err.response.status == 400) {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.CantDeleteRelation")}`,
                                    });
                                    this.getData();
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
                });
            } else {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;

                        adminApi
                            .delete(`/real-estate/wallets/${id}`)
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })

                            .catch((err) => {
                                if (err.response.status == 400) {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.CantDeleteRelation")}`,
                                    });
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
                });
            }
        },
        /**
         *  end delete module
         */
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = { name: '', name_e: '' };
            this.createOwner = { wallet_owners: [{ owner_id: null, percentage: 0 }] };
            this.createBuilding = { wallet_id: null, building_id: null, bu_ty: "active" };
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.is_persentage = true;
            this.errors = {};
            this.wallet_id = null;
            this.is_disabled = false;
            this.is_disabledBuilding = false;
            this.is_disabledOwner = false;
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            this.wallet = null;
            await this.getOwner();
            await this.getBuildings();
            this.create = { name: '', name_e: '' };
            this.createOwner = { wallet_owners: [{ owner_id: null, percentage: 0 }] };
            this.createBuilding = { wallet_id: null, building_id: null, bu_ty: "active" };
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },
        /**
         *  create module
         */
        resetForm() {
            this.create = { name: '', name_e: '' };
            this.createOwner = { wallet_owners: [{ owner_id: null, percentage: 0 }] };
            this.createBuilding = { wallet_id: null, building_id: null, bu_ty: "active" };
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.is_disabled = false;
            this.is_disabledBuilding = false;
            this.is_disabledOwner = false;
            this.errors = {};
            this.wallet = null;
            this.is_persentage = true;
        },
        AddSubmit() {

            if (!this.create.name) { this.create.name = this.create.name_e }
            if (!this.create.name_e) { this.create.name_e = this.create.name }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi.post(`/real-estate/wallets`, this.create)
                    .then((res) => {
                        this.is_disabled = true;
                        this.wallet_id = res.data.data.id;
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                        this.isLoader = false;
                    });
            }

        },
        AddSubmitOwber() {
            this.$v.createOwner.$touch();

            let totel = this.createOwner.wallet_owners.reduce((accumulator, curValue) => parseFloat(accumulator) + parseFloat(curValue.percentage), 0);
            this.is_persentage = totel == 100;

            if (this.$v.createOwner.$invalid && !this.is_persentage) {
                return;
            } else {
                this.isLoader = true;
                this.createOwner.wallet_owners.map(e => e.wallet_id = this.wallet_id);
                this.errors = {};
                adminApi
                    .post(`/real-estate/wallet-owner`, { 'wallet-owner': this.createOwner.wallet_owners })
                    .then((res) => {
                        this.is_disabledOwner = true;
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
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
        AddSubmitBuilding() {
            this.createBuilding.wallet_id = this.wallet_id;
            if (this.$v.createBuilding.$invalid) {
                this.$v.createBuilding.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .post(`/real-estate/building-wallet`, {
                        ...this.createBuilding,
                        bu_ty: this.createBuilding.bu_ty == "active" ? 1 : 2,
                    })
                    .then((res) => {
                        this.getData();
                        this.is_disabledBuilding = true;
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
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
        /**
         *  edit module
         */
        editSubmit(id) {
            if (!this.edit.name) { this.edit.name = this.edit.name_e }
            if (!this.edit.name_e) { this.edit.name_e = this.edit.name }
            this.$v.edit.$touch();

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let { name, name_e, is_active } = this.edit;
                adminApi.put(`/real-estate/wallets/${id}`, { name, name_e, is_active })
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Editsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        editSubmitOwner(id) {
            this.$v.editOwner.$touch();

            let totel = this.editOwner.wallet_owners.reduce((accumulator, curValue) => parseFloat(accumulator) + parseFloat(curValue.percentage), 0);
            this.is_persentage = totel == 100;
            if (this.$v.editOwner.$invalid && !this.is_persentage) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.editOwner.wallet_owners.map(e => e.wallet_id = this.wallet_id);
                adminApi
                    .put(`/real-estate/wallet-owner/${this.wallet_id}`, { 'wallet-owner': this.editOwner.wallet_owners })
                    .then((res) => {
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
        editSubmitBuilding(id) {
            this.editBuilding.wallet_id = this.wallet_id;
            this.$v.editBuilding.$touch();
            if (this.$v.editBuilding.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/real-estate/building-wallet/${id}`, {
                        ...this.editBuilding,
                        bu_ty: this.editBuilding.bu_ty == "active" ? 1 : 2,
                    })
                    .then((res) => {
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
        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            await this.getOwner();
            await this.getBuildings();
            let module = this.wallets.find(e => id == e.id);
            console.log(module)
            this.wallet_id = module.id;
            this.is_persentage = true;
            this.edit.name = module.name;
            this.edit.name_e = module.name_e;
            if (module.create_owners &&module.create_owners.length) {
                module.create_owners.forEach((e) => {
                    adminApi
                        .get(`/real-estate/owners/ownerWalletPercentage/${this.wallet_id}/${e.id}`)
                        .then((res) => {
                            this.editOwner.wallet_owners.push({
                                owner_id: e.id,
                                percentage: res.data.data.percentage
                            });
                        })
                        .catch((err) => {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        })
                        .finally(() => {
                        });
                });
            }
            else {
                this.editOwner.wallet_owners.push({
                    owner_id: null,
                    percentage: 0
                });
            }
            this.editBuilding.building_id = module.buildings.length ? module.buildings[0].id : null;
            adminApi
                .get(`/real-estate/wallets/bu-ty/${this.wallet_id}/${this.editBuilding.building_id}`)
                .then((res) => {
                    this.editBuilding.bu_ty = res.data.data.bu_ty == 1 ? "inactive" : "active";
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
                .finally(() => {
                });

            this.editBuilding.wallet_id = this.wallet_id;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                name: '',
                name_e: '',
            };
            this.editOwner = { wallet_owners: [] };
            this.editBuilding = {
                wallet_id: null,
                building_id: null,
                bu_ty: "active",
            };
            this.is_disabledBuilding = false;
            this.is_disabledOwner = false;
            this.is_persentage = true;
        },

        /**
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        /**
         *  start  ckeckRow
         */
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        /**
         *  end  ckeckRow
         */
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/real-estate/wallets/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
                                }; Description: ${e.description} ;Created At: ${this.formatDate(
                                    e.created_at
                                )} \n`;
                        });
                        $(`#tooltip-${id}`).tooltip();
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                    });
            }
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
                } else {
                    XLSX.writeFile(wb, fn || (('Wallet' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        }
    },
};
</script>

<template>
    <Layout>
        <Owner :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getOwner" />
        <Building :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBuildings" />
        <PageHeader />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title"> {{ $t('general.walletsTable') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                        class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="name" class="mb-1">
                                            {{ getCompanyKey('wallet_name_ar') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1">
                                            {{ getCompanyKey('wallet_name_en') }}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%;">
                                    <span
                                        :class="['search-custom position-absolute', $i18n.locale == 'ar' ? 'search-custom-ar' : '']">
                                        <i class="fe-search"></i>
                                    </span>
                                    <input class="form-control" style="display:block;width:93%;padding-top: 3px;"
                                        type="text" v-model.trim="search" :placeholder="`${$t('general.Search')}...`">
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button v-b-modal.create variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t('general.Create') }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printWallet'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1"
                                        @click.prevent="deleteModule(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1"
                                        @click.prevent="deleteModule(checkAll[0])">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center  justify-content-end">
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
                                        <b-button class="mx-1 custom-btn-background">
                                            {{ $t('general.filter') }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button class="mx-1 custom-btn-background">
                                            {{ $t('general.group') }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown variant="primary"
                                            :html="`${$t('general.setting')} <i class='fe-settings'></i>`" ref="dropdown"
                                            class="dropdown-custom-ali">
                                            <b-form-checkbox v-model="setting.name" class="mb-1">{{
                                                getCompanyKey('wallet_name_ar')
                                            }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.name_e" class="mb-1">
                                                {{ getCompanyKey('wallet_name_en') }}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">Apply</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size:13px;">
                                            {{ walletsPagination.from }}-{{ walletsPagination.to }} /
                                            {{ walletsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)"
                                                :style="{ 'pointer-events': walletsPagination.current_page == 1 ? 'none' : '' }"
                                                @click.prevent="getData(walletsPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                class="pagination-current-page" />
                                            <a href="javascript:void(0)"
                                                :style="{ 'pointer-events': walletsPagination.last_page == walletsPagination.current_page ? 'none' : '' }"
                                                @click.prevent="getData(walletsPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal id="create" :title="getCompanyKey('wallet_create_form')" title-class="font-18"
                            body-class="p-4 " size="lg" :hide-footer="true" @show="resetModal" @hidden="resetModalHidden">
                            <form>
                                <div class="mb-3 d-flex justify-content-end">

                                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                                        type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                                        {{ $t('general.AddNewRecord') }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                                            @click.prevent="AddSubmit">
                                            {{ $t('general.Add') }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <b-tabs nav-class="nav-tabs nav-bordered">
                                    <b-tab :title="$t('general.DataEntry')" active>
                                        <div class="row justify-content-center">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="field-1" class="control-label">
                                                        {{ getCompanyKey('wallet_name_ar') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div dir="rtl">
                                                        <input @keyup="arabicValue(create.name)" type="text"
                                                            class="form-control" v-model="$v.create.name.$model" :class="{
                                                                'is-invalid': $v.create.name.$error || errors.name,
                                                                'is-valid': !$v.create.name.$invalid && !errors.name
                                                            }" id="field-1" />
                                                    </div>
                                                    <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                                                        {{ $t('general.Itmustbeatleast') }}
                                                        {{ $v.create.name.$params.minLength.min }} {{ $t('general.letters')
                                                        }}
                                                    </div>
                                                    <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                                                        {{ $t('general.Itmustbeatmost') }}
                                                        {{ $v.create.name.$params.maxLength.max }} {{ $t('general.letters')
                                                        }}
                                                    </div>
                                                    <template v-if="errors.name">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.name"
                                                            :key="index">
                                                            {{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="field-2" class="control-label">
                                                        {{ getCompanyKey('wallet_name_en') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div>
                                                        <input @keyup="englishValue(create.name_e)" type="text"
                                                            class="form-control" v-model="$v.create.name_e.$model" :class="{
                                                                'is-invalid': $v.create.name_e.$error || errors.name_e,
                                                                'is-valid': !$v.create.name_e.$invalid && !errors.name_e
                                                            }" id="field-2" />
                                                    </div>
                                                    <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                                                        {{ $t('general.Itmustbeatleast') }}
                                                        {{ $v.create.name_e.$params.minLength.min }} {{
                                                            $t('general.letters') }}
                                                    </div>
                                                    <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                                                        {{ $t('general.Itmustbeatmost') }}
                                                        {{ $v.create.name_e.$params.maxLength.max }} {{
                                                            $t('general.letters') }}
                                                    </div>
                                                    <template v-if="errors.name_e">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.name_e"
                                                            :key="index">{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </b-tab>
                                    <b-tab :disabled="!wallet_id" :title="$t('general.owner')">
                                        <div class="d-flex justify-content-end">
                                            <template v-if="!is_disabledOwner">
                                                <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                                                    @click.prevent="AddSubmitOwber">
                                                    {{ $t("general.Add") }}
                                                </b-button>

                                                <b-button variant="success" class="mx-1" disabled v-else>
                                                    <b-spinner small></b-spinner>
                                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                </b-button>
                                            </template>
                                            <!-- Emulate built in modal footer ok and cancel button actions -->
                                            <b-button variant="success" type="button" disabled class="mx-1"
                                                v-if="is_disabledOwner">
                                                {{ $t("general.Add") }}
                                            </b-button>
                                        </div>
                                        <template v-for="(item, index) in createOwner.wallet_owners">
                                            <div class="row" :key="index">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>{{ getCompanyKey("owner") }}</label>
                                                        <multiselect @input="showOwnerModal(index)"
                                                            v-model="$v.createOwner.wallet_owners.$each[index].owner_id.$model"
                                                            :options="owners.map((type) => type.id)" :custom-label="
                                                                (opt) =>
                                                                    $i18n.locale == 'ar'
                                                                        ? owners.find((x) => x.id == opt).name
                                                                        : owners.find((x) => x.id == opt).name_e
                                                            ">
                                                        </multiselect>
                                                        <div v-if="$v.createOwner.wallet_owners.$each[index].owner_id.$error"
                                                            class="text-danger">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.owner_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.owner_id"
                                                                :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("wallet_owner_percentage") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" step="0.01" class="form-control"
                                                            data-create="3" @input="changeNumber('add')"
                                                            v-model="$v.createOwner.wallet_owners.$each[index].percentage.$model"
                                                            :class="{
                                                                'is-invalid': $v.createOwner.wallet_owners.$each[index].percentage.$error || errors.percentage || !is_persentage,
                                                                'is-valid':
                                                                    !$v.createOwner.wallet_owners.$each[index].percentage.$invalid && !errors.percentage && is_persentage,
                                                            }" />
                                                        <template v-if="errors.percentage">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.percentage"
                                                                :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 p-0 pt-3">
                                                    <button v-if="(createOwner.wallet_owners.length - 1) == index"
                                                        type="button" @click.prevent="addNewField"
                                                        class="custom-btn-dowonload">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    <button v-if="createOwner.wallet_owners.length > 1" type="button"
                                                        @click.prevent="removeNewField(index)" class="custom-btn-dowonload">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </b-tab>
                                    <b-tab :disabled="!wallet_id" :title="$t('general.Building')">
                                        <div class="mb-3 d-flex justify-content-end">
                                            <template v-if="!is_disabledBuilding">
                                                <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader"
                                                    @click.prevent="AddSubmitBuilding">
                                                    {{ $t("general.Add") }}
                                                </b-button>

                                                <b-button variant="success" class="mx-1" disabled v-else>
                                                    <b-spinner small></b-spinner>
                                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                </b-button>
                                            </template>
                                            <b-button variant="success" type="button" disabled class="mx-1"
                                                v-if="is_disabledBuilding">
                                                {{ $t("general.Add") }}
                                            </b-button>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-md-10 position-relative">
                                                <div class="form-group">
                                                    <label class="my-1 mr-2">{{ getCompanyKey("building") }}</label>
                                                    <multiselect @input="showBuildingModal"
                                                        v-model="createBuilding.building_id"
                                                        :options="buildings.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? buildings.find((x) => x.id == opt).name
                                                                    : buildings.find((x) => x.id == opt).name_e
                                                        " :class="{
    'is-invalid':
        $v.createBuilding.building_id.$error || errors.building_id,
}">
                                                    </multiselect>
                                                    <div v-if="!$v.createBuilding.building_id.required"
                                                        class="invalid-feedback">
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>

                                                    <template v-if="errors.building_id">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.building_id"
                                                            :key="index">{{ errorMessage }}</ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label class="mr-2">
                                                        {{ getCompanyKey("building_wallet_bu_ty") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <b-form-group :class="{
                                                        'is-invalid': $v.createBuilding.bu_ty.$error || errors.bu_ty,
                                                        'is-valid': !$v.createBuilding.bu_ty.$invalid && !errors.bu_ty,
                                                    }">
                                                        <b-form-radio class="d-inline-block"
                                                            v-model="$v.createBuilding.bu_ty.$model" name="some-radios"
                                                            value="active">{{ $t("general.Building") }}</b-form-radio>
                                                        <b-form-radio class="d-inline-block m-1"
                                                            v-model="$v.createBuilding.bu_ty.$model" name="some-radios"
                                                            value="inactive">{{ $t("general.Unit") }}</b-form-radio>
                                                    </b-form-group>
                                                    <template v-if="errors.bu_ty">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.bu_ty"
                                                            :key="index">{{ errorMessage }}</ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </b-tab>
                                </b-tabs>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">

                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                id="printWallet">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 0;" v-if="enabled3" class="do-not-print">
                                            <div class="form-check custom-control">
                                                <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                    style="width: 17px;height: 17px;">
                                            </div>
                                        </th>
                                        <th v-if="setting.name">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('wallet_name_ar') }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="wallets.sort(sortString('name'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="wallets.sort(sortString('-name'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.name_e">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('wallet_name_en') }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="wallets.sort(sortString('name_e'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="wallets.sort(sortString('-name_e'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="enabled3" class="do-not-print">
                                            {{ $t('general.Action') }}
                                        </th>
                                        <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                    </tr>
                                </thead>
                                <tbody v-if="wallets.length > 0">
                                    <tr @click.capture="checkRow(data.id)"
                                        @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                        v-for="(data, index) in wallets" :key="data.id" class="body-tr-custom">
                                        <td v-if="enabled3" class="do-not-print">
                                            <div class="form-check custom-control" style="min-height: 1.9em;">
                                                <input style="width: 17px;height: 17px;" class="form-check-input"
                                                    type="checkbox" :value="data.id" v-model="checkAll">
                                            </div>
                                        </td>
                                        <td v-if="setting.name">
                                            <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                                        </td>
                                        <td v-if="setting.name_e">
                                            <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                                        </td>
                                        <td v-if="setting.is_active">
                                            <span :class="[
                                                data.is_active == 'active' ?
                                                    'text-success' :
                                                    'text-danger',
                                                'badge'
                                            ]">
                                                {{ data.is_active == 'active' ? `${$t('general.Active')}` :
                                                    `${$t('general.Inactive')}` }}
                                            </span>
                                        </td>
                                        <td v-if="enabled3" class="do-not-print">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    {{ $t('general.commands') }}
                                                    <i class="fas fa-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-custom">
                                                    <a class="dropdown-item" href="#"
                                                        @click="$bvModal.show(`modal-edit-${data.id}`)">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black">
                                                            <span>{{ $t('general.edit') }}</span>
                                                            <i class="mdi mdi-square-edit-outline text-info"></i>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item text-black" href="#"
                                                        @click.prevent="deleteModule(data.id)">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black">
                                                            <span>{{ $t('general.delete') }}</span>
                                                            <i class="fas fa-times text-danger"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <!--  edit   -->
                                            <b-modal :id="`modal-edit-${data.id}`"
                                                :title="getCompanyKey('wallet_edit_form')" title-class="font-18"
                                                body-class="p-4" :ref="`edit-${data.id}`" :hide-footer="true" size="lg"
                                                @show="resetModalEdit(data.id)" @hidden="resetModalHiddenEdit(data.id)">
                                                <form>
                                                    <div class="mb-3 d-flex justify-content-end">
                                                        <!-- Emulate built in modal footer ok and cancel button actions -->
                                                        <b-button variant="success" type="submit" class="mx-1"
                                                            v-if="!isLoader" @click.prevent="editSubmit(data.id)">
                                                            {{ $t('general.Edit') }}
                                                        </b-button>

                                                        <b-button variant="success" class="mx-1" disabled v-else>
                                                            <b-spinner small></b-spinner>
                                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                                        </b-button>

                                                        <b-button variant="danger" type="button"
                                                            @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                                                            {{ $t('general.Cancel') }}
                                                        </b-button>
                                                    </div>
                                                    <b-tabs nav-class="nav-tabs nav-bordered">
                                                        <b-tab :title="$t('general.DataEntry')" active>
                                                            <div class="row justify-content-center">
                                                                <div class="col-md-10">
                                                                    <div class="form-group">
                                                                        <label for="field-u-1" class="control-label">
                                                                            {{ getCompanyKey('wallet_name_ar') }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <div dir="rtl">
                                                                            <input @keyup="arabicValue(edit.name)"
                                                                                type="text" class="form-control"
                                                                                v-model="$v.edit.name.$model" :class="{
                                                                                    'is-invalid': $v.edit.name.$error || errors.name,
                                                                                    'is-valid': !$v.edit.name.$invalid && !errors.name
                                                                                }" id="field-u-1" />
                                                                        </div>
                                                                        <div v-if="!$v.edit.name.minLength"
                                                                            class="invalid-feedback">
                                                                            {{ $t('general.Itmustbeatleast') }}
                                                                            {{ $v.edit.name.$params.minLength.min }}
                                                                            {{ $t('general.letters') }}
                                                                        </div>
                                                                        <div v-if="!$v.edit.name.maxLength"
                                                                            class="invalid-feedback">
                                                                            {{ $t('general.Itmustbeatmost') }}
                                                                            {{ $v.edit.name.$params.maxLength.max }}
                                                                            {{ $t('general.letters') }}
                                                                        </div>
                                                                        <template v-if="errors.name">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.name"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <div class="form-group">
                                                                        <label for="field-u-2" class="control-label">
                                                                            {{ getCompanyKey('wallet_name_en') }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <div dir="ltr">
                                                                            <input @keyup="englishValue(edit.name_e)"
                                                                                type="text" class="form-control"
                                                                                v-model="$v.edit.name_e.$model" :class="{
                                                                                    'is-invalid': $v.edit.name_e.$error || errors.name_e,
                                                                                    'is-valid': !$v.edit.name_e.$invalid && !errors.name_e
                                                                                }" id="field-u-2" />
                                                                        </div>
                                                                        <div v-if="!$v.edit.name_e.minLength"
                                                                            class="invalid-feedback">
                                                                            {{ $t('general.Itmustbeatleast') }}
                                                                            {{ $v.edit.name_e.$params.minLength.min }}
                                                                            {{ $t('general.letters') }}
                                                                        </div>
                                                                        <div v-if="!$v.edit.name_e.maxLength"
                                                                            class="invalid-feedback">
                                                                            {{ $t('general.Itmustbeatmost') }}
                                                                            {{ $v.edit.name_e.$params.maxLength.max }}
                                                                            {{ $t('general.letters') }}
                                                                        </div>
                                                                        <div v-if="!$v.edit.name_e.alphaEnglish"
                                                                            class="invalid-feedback">{{
                                                                                $t('general.alphaEnglish')
                                                                            }}
                                                                        </div>
                                                                        <template v-if="errors.name_e">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.name_e"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                        <b-tab :title="$t('general.owner')">
                                                            <div class="d-flex justify-content-end">
                                                                <template v-if="!is_disabledOwner">
                                                                    <b-button variant="success" type="button" class="mx-1"
                                                                        v-if="!isLoader" @click.prevent="editSubmitOwner">
                                                                        {{ $t('general.Edit') }}
                                                                    </b-button>

                                                                    <b-button variant="success" class="mx-1" disabled
                                                                        v-else>
                                                                        <b-spinner small></b-spinner>
                                                                        <span class="sr-only">{{ $t("login.Loading")
                                                                        }}...</span>
                                                                    </b-button>
                                                                </template>
                                                                <!-- Emulate built in modal footer ok and cancel button actions -->
                                                                <b-button variant="success" type="button" disabled
                                                                    class="mx-1" v-if="is_disabledOwner">
                                                                    {{ $t("general.Add") }}
                                                                </b-button>
                                                            </div>
                                                            <template v-for="(item, index) in editOwner.wallet_owners">
                                                                <div class="row" :key="index">
                                                                    <div class="col-md-5">
                                                                        <div class="form-group">
                                                                            <label>{{ getCompanyKey("owner") }}</label>
                                                                            <multiselect @input="showOwnerEditModal(index)"
                                                                                v-model="$v.editOwner.wallet_owners.$each[index].owner_id.$model"
                                                                                :options="owners.map((type) => type.id)"
                                                                                :custom-label="
                                                                                    (opt) =>
                                                                                        $i18n.locale == 'ar'
                                                                                            ? owners.find((x) => x.id == opt).name
                                                                                            : owners.find((x) => x.id == opt).name_e
                                                                                ">
                                                                            </multiselect>
                                                                            <div v-if="$v.editOwner.wallet_owners.$each[index].owner_id.$error"
                                                                                class="text-danger">
                                                                                {{ $t("general.fieldIsRequired") }}
                                                                            </div>
                                                                            <template v-if="errors.owner_id">
                                                                                <ErrorMessage
                                                                                    v-for="(errorMessage, index) in errors.owner_id"
                                                                                    :key="index">{{ errorMessage }}
                                                                                </ErrorMessage>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-5">
                                                                        <div class="form-group">
                                                                            <label class="control-label">
                                                                                {{ getCompanyKey("wallet_owner_percentage")
                                                                                }}
                                                                                <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="number" step="0.01"
                                                                                class="form-control"
                                                                                @input="changeNumber('edit')"
                                                                                v-model="$v.editOwner.wallet_owners.$each[index].percentage.$model"
                                                                                :class="{
                                                                                    'is-invalid': $v.editOwner.wallet_owners.$each[index].percentage.$error || errors.percentage || !is_persentage,
                                                                                    'is-valid':
                                                                                        !$v.editOwner.wallet_owners.$each[index].percentage.$invalid && !errors.percentage && is_persentage,
                                                                                }" />
                                                                            <template v-if="errors.percentage">
                                                                                <ErrorMessage
                                                                                    v-for="(errorMessage, index) in errors.percentage"
                                                                                    :key="index">{{ errorMessage }}
                                                                                </ErrorMessage>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 p-0 pt-3">
                                                                        <button
                                                                            v-if="(editOwner.wallet_owners.length - 1) == index"
                                                                            type="button" @click.prevent="addNewFieldEdit"
                                                                            class="custom-btn-dowonload">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                        <button v-if="editOwner.wallet_owners.length > 1"
                                                                            type="button"
                                                                            @click.prevent="removeNewFieldEdit(index)"
                                                                            class="custom-btn-dowonload">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </b-tab>
                                                        <b-tab :title="$t('general.Building')">
                                                            <div class="mb-3 d-flex justify-content-end">
                                                                <template v-if="!is_disabledBuilding">
                                                                    <b-button variant="success" type="submit" class="mx-1"
                                                                        v-if="!isLoader"
                                                                        @click.prevent="editSubmitBuilding">
                                                                        {{ $t('general.Edit') }}
                                                                    </b-button>

                                                                    <b-button variant="success" class="mx-1" disabled
                                                                        v-else>
                                                                        <b-spinner small></b-spinner>
                                                                        <span class="sr-only">{{ $t("login.Loading")
                                                                        }}...</span>
                                                                    </b-button>
                                                                </template>
                                                                <b-button variant="success" type="button" disabled
                                                                    class="mx-1" v-if="is_disabledBuilding">
                                                                    {{ $t("general.Add") }}
                                                                </b-button>
                                                            </div>
                                                            <div class="row justify-content-center">
                                                                <div class="col-md-10 position-relative">
                                                                    <div class="form-group">
                                                                        <label class="my-1 mr-2">{{
                                                                            getCompanyKey("building") }}</label>
                                                                        <multiselect @input="showBuildingModalEdit"
                                                                            v-model="editBuilding.building_id"
                                                                            :options="buildings.map((type) => type.id)"
                                                                            :custom-label="
                                                                                (opt) =>
                                                                                    $i18n.locale == 'ar'
                                                                                        ? buildings.find((x) => x.id == opt).name
                                                                                        : buildings.find((x) => x.id == opt).name_e
                                                                            " :class="{
    'is-invalid':
        $v.editBuilding.building_id.$error || errors.building_id,
}">
                                                                        </multiselect>
                                                                        <div v-if="!$v.editBuilding.building_id.required"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>

                                                                        <template v-if="errors.building_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.building_id"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <div class="form-group">
                                                                        <label class="mr-2">
                                                                            {{ getCompanyKey("building_wallet_bu_ty") }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <b-form-group :class="{
                                                                            'is-invalid': $v.editBuilding.bu_ty.$error || errors.bu_ty,
                                                                            'is-valid': !$v.editBuilding.bu_ty.$invalid && !errors.bu_ty,
                                                                        }">
                                                                            <b-form-radio class="d-inline-block"
                                                                                v-model="$v.editBuilding.bu_ty.$model"
                                                                                name="some-radios" value="active">{{
                                                                                    $t("general.Building") }}</b-form-radio>
                                                                            <b-form-radio class="d-inline-block m-1"
                                                                                v-model="$v.editBuilding.bu_ty.$model"
                                                                                name="some-radios" value="inactive">{{
                                                                                    $t("general.Unit") }}</b-form-radio>
                                                                        </b-form-group>
                                                                        <template v-if="errors.bu_ty">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.bu_ty"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                    </b-tabs>
                                                </form>
                                            </b-modal>
                                            <!--  /edit   -->
                                        </td>
                                        <td v-if="enabled3" class="do-not-print">
                                            <button @mousemove="log(data.id)" @mouseover="log(data.id)" type="button"
                                                class="btn" :id="`tooltip-${data.id}`"
                                                :data-placement="$i18n.locale == 'en' ? 'left' : 'right'" :title="Tooltip">
                                                <i class="fe-info" style="font-size: 22px"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="6">{{ $t('general.notDataFound') }}</th>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <!-- end .table-responsive-->

                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped></style>

