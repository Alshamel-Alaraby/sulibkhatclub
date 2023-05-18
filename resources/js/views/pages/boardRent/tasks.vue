<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import {dynamicSortNumber, dynamicSortString} from "../../../helper/tableSort";
import {formatDateOnly} from "../../../helper/startDate";
import Multiselect from "vue-multiselect";
// require styles
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import translation from "../../../helper/translation-mixin";
import employee from "../../../components/create/employee";
import Department from "../../../components/create/department";
import customerGeneral from "../../../components/create/customerGeneral";
import task from "../../../components/create/task";
import Status from "../../../components/create/status";
import DatePicker from "vue2-datepicker";
import Templates from "../email/templates";

/**
 * Advanced Table component
 */

export default {
    page: {
        title: "Tasks",
        meta: [{ name: "description", content: 'Tasks' }],
    },
    mixins: [translation],
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            if (vm.$store.state.auth.work_flow_trees.includes('board rent') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    components: {
        Templates,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        employee,
        Department,
        customerGeneral,
        task,
        Status,
        DatePicker
    },
    data() {
        return {
            fields: [],
            per_page: 50,
            search: '',
            debounce: {},
            tasksPagination: {},
            tasks: [],
            employees: [],
            employeeDepartments: [],
            departments: [],
            customers: [],
            departmentTasks: [],
            statuses: [],
            isLoader: false,
            isVaildPhone: false,
            create: {
                employee_id: null,
                department_id: null,
                customer_id:null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id:null,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
                media: [],
            },
            edit: {
                employee_id: null,
                department_id: null,
                customer_id:null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id:null,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
                old_media: [],
            },
            task_id: null,
            department_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            images: [],
            media: {},
            titleFile:"",
            saveImageName: [],
            showPhoto: "../../../../../images/img-1.png",
            current_page: 1,
            enabled3: true,
            image: '',
            setting: {
                employee_id: true,
                department_id: true,
                customer_id: true,
                contact_person: true,
                contact_phone: true,
                task_title:true,
                department_task_id:true,
                owners:true,
                supervisors:true,
                notifications:true,
                status_id:true,
                execution_date:true,
                start_time:true,
                end_time:true,
                execution_duration:true,
                notification_date:true,
                execution_end_date:true,
                note:true,
            },
            is_disabled: false,
            filterSetting: [
                this.$i18n.locale == 'ar'? 'employee.name': 'employee.name_e',
                this.$i18n.locale == 'ar'? 'department.name': 'department.name_e',
                this.$i18n.locale == 'ar'? 'customer.name': 'customer.name_e',
                'contact_person',
                'contact_phone',
                'task_title',
                this.$i18n.locale == 'ar'? 'departmentTask.name': 'departmentTask.name_e',
                this.$i18n.locale == 'ar'? 'owners.name': 'owners.name_e',
                this.$i18n.locale == 'ar'? 'supervisors.name': 'supervisors.name_e',
                this.$i18n.locale == 'ar'? 'notifications.name': 'notifications.name_e',
                this.$i18n.locale == 'ar'? 'status.name': 'status.name_e',
                'execution_date',
                'start_time',
                'end_time',
                'execution_duration',
                'notification_date',
                'execution_end_date',
                'note',
            ],
            Tooltip: '',
            idDelete: null,
            mouseEnter: null,
            printLoading: true,
            printObj: {
                id: "printCustomer",
            }
        }
    },
    validations: {
        create: {
            employee_id: {required},
            department_id: {required},
            customer_id: {required},
            contact_person: {required},
            contact_phone: {required},
            task_title: {required},
            department_task_id: {required},
            owners: {required},
            supervisors: {required},
            notifications: {required},
            status_id: {required},
            execution_date: {required},
            start_time:{},
            end_time:{},
            execution_duration: {required},
            notification_date: {required},
            execution_end_date: {required},
            note: {},
            media: {},
        },
        edit: {
            employee_id: {required},
            department_id: {required},
            customer_id: {required},
            contact_person: {required},
            contact_phone: {required},
            task_title: {required},
            department_task_id: {required},
            owners: {required},
            supervisors: {required},
            notifications: {required},
            status_id: {required},
            execution_date: {required},
            start_time:{},
            end_time:{},
            execution_duration: {required},
            notification_date: {required},
            execution_end_date: {required},
            note: {},
            media: {},
        },
        titleFile:{required,minLength: minLength(2),maxLength: maxLength(100),}
    },
    watch: {
        /**
         * watch per_page
         */
        per_page(after,befour){
            this.getData();
        },
        /**
         * watch search
         */
        search(after,befour){
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getData();
            }, 400);
        },
        /**
         * watch check All table
         */
        isCheckAll(after,befour){
            if(after){
                this.builds.forEach(el => {
                    if(!this.checkAll.includes(el.id)){
                        this.checkAll.push(el.id);
                    }
                });
            }else{
                this.checkAll = [];
            }
        }
    },
    mounted() {
        this.getData();
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
        /**
         *  start get Data tasks && pagination
         */
        getData(page = 1){
            this.isLoader = true;
            let filter = '';
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }

            adminApi.get(`/tasks?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                .then((res) => {
                    let l = res.data;
                    this.tasks = l.data;
                    this.tasksPagination = l.pagination;
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
        getDataCurrentPage(page = 1){
            if(this.current_page <= this.tasksPagination.last_page && this.current_page != this.tasksPagination.current_page && this.current_page){
                this.isLoader = true;
                let filter = '';
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi.get(`/tasks?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                    .then((res) => {
                        let l = res.data;
                        this.tasks = l.data;
                        this.tasksPagination = l.pagination;
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
         *  end get Data countrie && pagination
         */
        /**
         *  start delete countrie
         */
        deleteCountry(id, index) {
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
                            .post(`/tasks/bulk-delete`, { ids: id })
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
                            .delete(`/tasks/${id}`)
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
         *  end delete countrie
         */
        /**
         *  reset Modal (create)
         */
        dataCreate() {
            this.create = {
                employee_id: null,
                department_id: null,
                customer_id: null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id:null,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
            };
            this.department_id = null;
            this.task_id = null;
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.media = {};
            this.images = [];
        },
        resetModalHidden(){
            this.create =  {
                employee_id: null,
                department_id: null,
                customer_id: null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id:null,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
                media: null,
            };
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.images = [];
            this.department_id = null;
            this.task_id = null;
            this.is_disabled = false;
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal(){
            await this.getEmployees();
            await this.getEmployeeDepartments();
            await this.getEmployees();
            await this.getDepartment();
            await this.getCustomers();
            await this.getStatus();
            this.dataCreate();
            this.showPhoto = "../../../../../images/img-1.png";
        },
        /**
         *  create
         */
        resetForm(){
            this.dataCreate();
            this.is_disabled = false;
        },
        AddSubmit(){
            this.create.company_id = JSON.parse(localStorage.getItem("company_id"));
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi.post(`/tasks`,this.create)
                    .then((res) => {
                        this.is_disabled = true;
                        this.task_id = res.data.data.id;
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },500);
                    })
                    .catch((err) => {
                        if(err.response.data){
                            this.errors = err.response.data.errors;
                        }else {
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
        /**
         *  edit countrie
         */
        editSubmit(id){

            this.edit.company_id = JSON.parse(localStorage.getItem("company_id"));
            this.$v.edit.$touch();
            this.images.forEach((e) => {
                this.edit.old_media.push(e.id);
            });

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi.put(`/tasks/${id}`,this.edit)
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
                        },500);
                    })
                    .catch((err) => {
                        if(err.response.data){
                            this.errors = err.response.data.errors;
                        }else {
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
        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id){
            await this.getEmployees();
            await this.getEmployeeDepartments();
            await this.getEmployees();
            await this.getDepartment();
            await this.getCustomers();
            await this.getStatus();

            let build = this.tasks.find(e => id == e.id );
            let owners = [];
            build.owners.forEach((el)=>{
                owners.push(el.id);
            });
            let supervisors = [];
            build.supervisors.forEach((el)=>{
                supervisors.push(el.id);
            });
            let notifications = [];
            build.notifications.forEach((el)=>{
                notifications.push(el.id);
            });
            this.department_id = build.department_id;
            this.task_id = build.id;
            await this.getDepartmentTask();
            this.edit.employee_id = build.employee_id;
            this.edit.department_id = build.department_id;
            this.edit.customer_id = build.customer_id;
            this.edit.contact_person = build.contact_person;
            this.edit.contact_phone = build.contact_phone;
            this.edit.task_title = build.task_title;
            this.edit.department_task_id = build.department_task_id;
            this.edit.owners = owners;
            this.edit.supervisors = supervisors;
            this.edit.notifications = notifications;
            this.edit.status_id = build.status_id;
            this.edit.execution_date = build.execution_date;
            this.edit.start_time = build.start_time;
            this.edit.end_time = build.end_time;
            this.edit.execution_duration = build.execution_duration;
            this.edit.notification_date = build.notification_date;
            this.edit.execution_end_date = build.execution_end_date;
            this.edit.note = build.note;


            this.images = build.media ?? [];
            if (this.images && this.images.length > 0) {
                this.showPhoto = this.images[this.images.length - 1].webp;
            } else {
                this.showPhoto = "../../../../../images/img-1.png";
            }
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id){
            this.errors = {};
            this.edit = {
                employee_id: null,
                department_id: null,
                customer_id: null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id:null,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
                old_media: [],
            };
            this.department_id = null;
            this.task_id = null;
            this.employees = [];
            this.images = [];
        },
        /**
         *  start  dynamicSortString
         */
        sortString(value){ return dynamicSortString(value);},
        sortNumber(value){return dynamicSortNumber(value);},
        /**
         *  start  ckeckRow
         */
        checkRow(id){
            if(!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            }else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index,1);
            }
        },
        /**
         *  end  ckeckRow
         */
        /**
         *  start Image ceate
         */
        uploadPhotoTitle () {
            this.titleFile="";
            this.$bvModal.show(`uploadPhotoTitleCreate`);
            this.errors = {};
        },
        uploadPhotoTitleHidden(){
            this.titleFile="";
            this.$bvModal.hide(`uploadPhotoTitleCreate`);
            this.errors = {};
        },
        changePhoto() {
            document.getElementById("uploadImageCreate").click();
        },
        changePhotoEdit() {
            document.getElementById("uploadImageEdit").click();
        },
        onImageChanged(e) {
            const file = e.target.files[0];
            this.addImage(file);
        },
        addImage(file) {
            this.media = file; //upload
            if (file) {
                this.idDelete = null;
                let is_media = this.images.find(
                    (e) => e.name == file.name.slice(0, file.name.indexOf("."))
                );
                this.idDelete = is_media ? is_media.id : null;
                if (!this.idDelete) {
                    this.isLoader = true;
                    let formDate = new FormData();
                    formDate.append("media[][media]", this.media);
                    formDate.append("media[][title]", this.titleFile);
                    adminApi
                        .post(`/media-name`, formDate)
                        .then((res) => {
                            let old_media = [];
                            this.images.forEach((e) => old_media.push(e.id));
                            let new_media = [];
                            res.data.data.forEach((e) => new_media.push(e.id));

                            adminApi
                                .put(`/tasks/${this.task_id}`, { old_media, media: new_media })
                                .then((res) => {
                                    this.images = res.data.data.media ?? [];
                                    if (this.images && this.images.length > 0) {
                                        this.showPhoto = this.images[this.images.length - 1].webp;
                                    } else {
                                        this.showPhoto = "../../../../../images/img-1.png";
                                    }
                                    this.getData();
                                    this.uploadPhotoTitleHidden();
                                })
                                .catch((err) => {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                                    });
                                });
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
                } else {
                    Swal.fire({
                        title: `${this.$t("general.Thisfilehasalreadybeenuploaded")}`,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: `${this.$t("general.Replace")}`,
                        cancelButtonText: `${this.$t("general.Nocancel")}`,
                        confirmButtonClass: "btn btn-success mt-2",
                        cancelButtonClass: "btn btn-danger ml-2 mt-2",
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.value) {
                            this.isLoader = true;
                            let formDate = new FormData();
                            formDate.append("media[0]", this.media);
                            adminApi
                                .post(`/media`, formDate)
                                .then((res) => {
                                    let old_media = [];
                                    this.images.forEach((e) => old_media.push(e.id));
                                    old_media.splice(old_media.indexOf(this.idDelete), 1);
                                    let new_media = [];
                                    res.data.data.forEach((e) => new_media.push(e.id));

                                    adminApi
                                        .put(`/tasks/${this.country_id}`, { old_media, media: new_media })
                                        .then((res) => {
                                            this.images = res.data.data.media ?? [];
                                            if (this.images && this.images.length > 0) {
                                                this.showPhoto = this.images[this.images.length - 1].webp;
                                            } else {
                                                this.showPhoto = "../../../../../images/img-1.png";
                                            }
                                            this.getData();
                                        })
                                        .catch((err) => {
                                            Swal.fire({
                                                icon: "error",
                                                title: `${this.$t("general.Error")}`,
                                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                                            });
                                        });
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
                    });
                }
            }
        },
        deleteImageCreate(id, index) {
            let old_media = [];
            this.images.forEach((e) => {
                if (e.id != id) {
                    old_media.push(e.id);
                }
            });
            adminApi
                .put(`/tasks/${this.task_id}`, { old_media })
                .then((res) => {
                    this.tasks[index] = res.data.data;
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = "../../../../../images/img-1.png";
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        /**
         *  end Image ceate
         *
         */
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if(this.mouseEnter != id){
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/tasks/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                                e.event
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
        // start status
        async getStatus(){
            this.isLoader = true;
            await adminApi
                .get(`/statuses?module_type=bordRent`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف حاله", name_e: "Add Status" });
                    this.statuses = l;
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
        showStatusModal() {
            if (this.create.status_id == 0) {
                this.$bvModal.show("status-create");
                this.create.status_id = null;
            }
        },
        showStatusModalEdit() {
            if (this.edit.status_id == 0) {
                this.$bvModal.show("status-create");
                this.edit.status_id = null;
            }
        },

        //start employee
        async getEmployeeDepartments() {
            await adminApi
                .get(`/employees?depertment=1`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف موظف", name_e: "Add Employee" });
                    this.employeeDepartments = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getEmployees() {
            await adminApi
                .get(`/employees`)
                .then((res) => {
                    let l = res.data.data;
                    this.employees = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async showEmployeeModal() {
            if (this.create.employee_id == 0) {
                this.$bvModal.show("employee-create");
                this.create.employee_id = null;
            }else {
                let customer = this.customerDepartment(this.create.employee_id);
                if (customer)
                {
                    this.create.owners = [];
                    this.create.department_id = customer.department_id;
                    this.department_id = customer.department_id;
                    this.create.owners.push(this.create.employee_id);
                    await this.getDepartmentTask();
                }
            }
        },
       async showEmployeeModalEdit() {
            if(this.edit.employee_id == 0) {
                this.$bvModal.show("employee-create");
                this.edit.employee_id = null;
            }else {
                let customer = this.customerDepartment(this.edit.employee_id);
                if (customer)
                {
                    this.edit.owners = [];
                    this.edit.department_id = customer.department_id;
                    this.department_id = customer.department_id;
                    this.edit.owners.push(this.edit.employee_id);
                    await this.getDepartmentTask();
                }
            }
        },

        customerDepartment(id)
        {
            if (this.employees && id)
            {
                return this.employees.find(e => id == e.id);
            }
        },

        // start department
        async getDepartment() {
            this.isLoader = true;
            await adminApi
                .get(`/depertments?employees=1`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف قسم", name_e: "Add Department" });
                    this.departments = l;
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
       async showDepartmentModal() {
            if (this.create.department_id == 0) {
                this.$bvModal.show("create_department_task");
                this.create.department_id = null;
            }else {
                this.department_id = this.create.department_id;
                await this.getDepartmentTask();
            }
        },
        async showDepartmentModalEdit() {
            if (this.edit.department_id == 0) {
                this.$bvModal.show("create_department_task");
                this.edit.department_id = null;
            }else {
                this.department_id = this.edit.department_id;
                await this.getDepartmentTask();
            }
        },

        // start customer
        async getCustomers(search='') {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer?limet=10&company_id=${this.company_id}&search=${search}&columns[0]=name&columns[1]=name_e`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضافة زبون", name_e: "Add customer"});
                    this.customers = l;
                    this.isLoader = false;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        showCustomerModal() {
            if (this.create.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.create.customer_id = null;
            }else {
                let customer = this.getCustomerData(this.create.customer_id)
                if (customer)
                {
                    this.create.contact_person = customer.contact_person;
                    this.create.contact_phone = customer.contact_phone;
                }
            }
        },
        showCustomerModalEdit() {
            if (this.edit.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.edit.customer_id = null;
            }else {
                let customer = this.getCustomerData(this.edit.customer_id)
                if (customer)
                {
                    this.edit.contact_person = customer.contact_person;
                    this.edit.contact_phone = customer.contact_phone;
                }
            }
        },
        getCustomerData (id)
        {
            if (this.customers && id)
            {
                return this.customers.find(e => id == e.id);
            }
        },
        async searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getCustomers(search);
            }, 500);

        },

        // start department task
        async getDepartmentTask() {
            this.isLoader = true;
            await adminApi
                .get(`/department-tasks?department_id=${this.department_id}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضافة مهمة", name_e: "Add Task"});
                    this.departmentTasks = l;
                    this.isLoader = false;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },

        showDepartmentTaskModal() {
            if (this.create.department_task_id == 0) {
                this.$bvModal.show("create-task");
                this.create.department_task_id = null;
            }
        },
        showDepartmentTaskModalEdit() {
            if (this.edit.department_task_id == 0) {
                this.$bvModal.show("create-task");
                this.edit.department_task_id = null;
            }
        },
        // chech employee id create
        checkIncloudIdOwners(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.create.supervisors.includes(employee_id))
                {
                    this.create.supervisors.splice(this.create.supervisors.indexOf(employee_id),1)
                }
                if (this.create.notifications.includes(employee_id))
                {
                    this.create.notifications.splice(this.create.notifications.indexOf(employee_id),1)
                }
            }
        },
        checkIncloudIdSupervisors(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.create.owners.includes(employee_id))
                {
                    this.create.owners.splice(this.create.owners.indexOf(employee_id),1)
                }
                if (this.create.notifications.includes(employee_id))
                {
                    this.create.notifications.splice(this.create.notifications.indexOf(employee_id),1)
                }
            }
        },
        checkIncloudIdNotifications(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.create.owners.includes(employee_id))
                {
                    this.create.owners.splice(this.create.owners.indexOf(employee_id),1)
                }
                if (this.create.supervisors.includes(employee_id))
                {
                    this.create.supervisors.splice(this.create.supervisors.indexOf(employee_id),1)
                }
            }
        },
        // chech employee id edit
        checkIncloudsIdOwnersEdit(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.edit.supervisors.includes(employee_id))
                {
                    this.edit.supervisors.splice(this.edit.supervisors.indexOf(employee_id),1)
                }
                if (this.edit.notifications.includes(employee_id))
                {
                    this.edit.notifications.splice(this.edit.notifications.indexOf(employee_id),1)
                }
            }
        },
        checkIncloudsIdSupervisorsEdit(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.edit.owners.includes(employee_id))
                {
                    this.edit.owners.splice(this.edit.owners.indexOf(employee_id),1)
                }
                if (this.edit.notifications.includes(employee_id))
                {
                    this.edit.notifications.splice(this.edit.notifications.indexOf(employee_id),1)
                }
            }
        },
        checkIncloudsIdNotificationsEdit(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.edit.owners.includes(employee_id))
                {
                    this.edit.owners.splice(this.edit.owners.indexOf(employee_id),1)
                }
                if (this.edit.supervisors.includes(employee_id))
                {
                    this.edit.supervisors.splice(this.edit.supervisors.indexOf(employee_id),1)
                }
            }
        },

        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('tasks' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },

        calcDurationCreate()
        {
            let TotalDays = 0 ;
            let TotalTime = '0 Minutes';
            let execution_date = new Date(this.create.execution_date).getTime();
            let execution_end_date = new Date(this.create.execution_end_date).getTime();
            if (execution_date < execution_end_date)
            {
                let difference = execution_end_date - execution_date;
                TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
            }

            // --------- calc Time --------------
            var today = new Date().toJSON().slice(0, 10).replace(/-/g, '/');

            let startTime = new Date(today + " " +this.create.start_time);
            let endTime = new Date(today + " " +this.create.end_time);
            if (endTime > startTime){
                var difference = endTime.getTime() - startTime.getTime();

                TotalTime = Math.round(difference / 60000) + " Minutes";
            }

            this.create.execution_duration = `${TotalDays} Days, ${TotalTime}`

        },

        calcDurationEdit()
        {
            let TotalDays = 0 ;
            let TotalTime = '0 Minutes';
            let execution_date = new Date(this.edit.execution_date).getTime();
            let execution_end_date = new Date(this.edit.execution_end_date).getTime();
            if (execution_date < execution_end_date)
            {
                let difference = execution_end_date - execution_date;
                TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
            }

            // --------- calc Time --------------
            var today = new Date().toJSON().slice(0, 10).replace(/-/g, '/');

            let startTime = new Date(today + " " +this.edit.start_time);
            let endTime = new Date(today + " " +this.edit.end_time);
            if (endTime > startTime){
                var difference = endTime.getTime() - startTime.getTime();

                TotalTime = Math.round(difference / 60000) + " Minutes";
            }

            this.edit.execution_duration = `${TotalDays} Days, ${TotalTime}`

        },
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <employee :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getEmployees" />
        <Department :id="'create_department_task'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getDepartment" />
        <customerGeneral :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCustomers"/>
        <task :department_id="department_id" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getDepartmentTask()"/>
        <Status :module_type="'bordRent'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getStatus" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.tasksTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown" class="btn-block setting-search dropdown-menu-custom-company">
                                        <b-form-checkbox v-model="filterSetting" :value="$i18n.locale == 'ar'?'employee.name':'employee.name_e'" class="mb-1">{{ getCompanyKey('employee') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" :value="$i18n.locale == 'ar'?'department.name' : 'department.name_e'" class="mb-1">{{ getCompanyKey("boardRent_task_department") }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e'" class="mb-1">{{ getCompanyKey("customer") }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="contact_person" class="mb-1">{{ getCompanyKey('general_customer_contact_person') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="contact_phone" class="mb-1">{{ getCompanyKey('general_customer_contact_phones') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="task_title" class="mb-1">{{ getCompanyKey('task_title') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'departmentTask.name' : 'departmentTask.name_e'" class="mb-1">{{ getCompanyKey('task_type') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'owners.name' : 'owners.name_e'" class="mb-1">{{ getCompanyKey('task_owners') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'supervisors.name' : 'supervisors.name_e'" class="mb-1">{{ getCompanyKey('task_supervisors') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'notifications.name' : 'notifications.name_e'" class="mb-1">{{ getCompanyKey('task_notifications') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'status.name' : 'status.name_e'" class="mb-1">{{ getCompanyKey('task_status') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="execution_date" class="mb-1">{{ getCompanyKey('execution_date') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="start_time" class="mb-1">{{ getCompanyKey('task_start_time') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="end_time" class="mb-1">{{ getCompanyKey('task_end_time') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="execution_duration" class="mb-1">{{ getCompanyKey('execution_duration') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="notification_date" class="mb-1">{{ getCompanyKey('notification_date') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="execution_end_date" class="mb-1">{{ getCompanyKey('execution_end_date') }}</b-form-checkbox>
                                        <b-form-checkbox  v-model="filterSetting" value="note" class="mb-1">{{ getCompanyKey('boardRent_panel_note') }}</b-form-checkbox>

                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%;">
                                    <span
                                        :class="['search-custom position-absolute',$i18n.locale == 'ar'?'search-custom-ar':'']"
                                    >
                                        <i class="fe-search"></i>
                                    </span>
                                    <input
                                        class="form-control"
                                        style="display:block;width:93%;padding-top: 3px;"
                                        type="text"
                                        v-model.trim="search"
                                        :placeholder="`${$t('general.Search')}...`"
                                    >
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t('general.Create') }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printCustomer'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button
                                        class="custom-btn-dowonload"
                                        @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1"
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length > 1"
                                        @click.prevent="deleteCountry(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length == 1"
                                        @click.prevent="deleteCountry(checkAll[0])"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center  justify-content-end"
                            >
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
                                        <b-button
                                            class="mx-1 custom-btn-background"
                                        >
                                            {{ $t('general.filter') }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button
                                            class="mx-1 custom-btn-background"
                                        >
                                            {{ $t('general.group') }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown
                                            variant="primary"
                                            :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                            ref="dropdown" class="dropdown-custom-ali dropdown-menu-custom-company"
                                        >
                                            <b-form-checkbox  v-model="setting.employee_id" class="mb-1">{{ getCompanyKey('employee') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.department_id" class="mb-1">{{ getCompanyKey("boardRent_task_department") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{ getCompanyKey("customer")}}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.contact_person" class="mb-1">{{ getCompanyKey('general_customer_contact_person') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.contact_phone" class="mb-1">{{getCompanyKey('general_customer_contact_phones') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.task_title" class="mb-1">{{getCompanyKey('task_title') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.department_task_id" class="mb-1">{{getCompanyKey('task_type') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.owners" class="mb-1">{{getCompanyKey('task_owners') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.supervisors" class="mb-1">{{getCompanyKey('task_supervisors') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.notifications" class="mb-1">{{getCompanyKey('task_notifications') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.status_id" class="mb-1">{{getCompanyKey('task_status') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.execution_date" class="mb-1">{{getCompanyKey('execution_date') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.start_time" class="mb-1">{{getCompanyKey('task_start_time') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.end_time" class="mb-1">{{getCompanyKey('task_end_time') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.execution_duration" class="mb-1">{{getCompanyKey('execution_duration') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.notification_date" class="mb-1">{{getCompanyKey('notification_date') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.execution_end_date" class="mb-1">{{getCompanyKey('execution_end_date') }}</b-form-checkbox>
                                            <b-form-checkbox  v-model="setting.note" class="mb-1">{{getCompanyKey('boardRent_panel_note') }}</b-form-checkbox>
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
                                            {{ tasksPagination.from }}-{{ tasksPagination.to }} / {{ tasksPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{'pointer-events':tasksPagination.current_page == 1 ? 'none': ''}"
                                                @click.prevent="getData(tasksPagination.current_page - 1)"
                                            >
                                                <span>&lt;</span>
                                            </a>
                                            <input
                                                type="text"
                                                @keyup.enter="getDataCurrentPage()"
                                                v-model="current_page"
                                                class="pagination-current-page"
                                            />
                                            <a
                                                href="javascript:void(0)"
                                                :style="{'pointer-events':tasksPagination.last_page == tasksPagination.current_page ? 'none': ''}"
                                                @click.prevent="getData(tasksPagination.current_page + 1)"
                                            >
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal
                            id="create"
                            :title="getCompanyKey('boardRent_task_create_form')"
                            title-class="font-18"
                            dialog-class="modal-full-width"
                            body-class="p-4 "
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3 d-flex justify-content-end">

                                            <b-button
                                                variant="success"
                                                :disabled="!is_disabled"
                                                @click.prevent="resetForm"
                                                type="button" :class="['font-weight-bold px-2',is_disabled?'mx-2': '']"
                                            >
                                                {{ $t('general.AddNewRecord') }}
                                            </b-button>
                                            <template v-if="!is_disabled">
                                                <b-button
                                                    variant="success"
                                                    type="button" class="mx-1"
                                                    v-if="!isLoader"
                                                    @click.prevent="AddSubmit"
                                                >
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
                                    </div>
                                    <b-tabs nav-class="nav-tabs nav-bordered">
                                        <b-tab :title="$t('general.DataEntry')" active>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ getCompanyKey('employee') }}<span class="text-danger">*</span></label>
                                                        <multiselect
                                                            @input="showEmployeeModal"
                                                            v-model="create.employee_id"
                                                            :options="employeeDepartments.map((type) => type.id)"
                                                            :custom-label=" (opt) => $i18n.locale == 'ar' ? employeeDepartments.find((x) => x.id == opt).name : employeeDepartments.find((x) => x.id == opt).name_e "
                                                            :class="{'is-invalid': $v.create.employee_id.$error || errors.employee_id,'is-valid': !$v.create.employee_id.$invalid && !errors.employee_id,}"
                                                        >
                                                        </multiselect>
                                                        <div v-if="!$v.create.employee_id.required" class="invalid-feedback">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>

                                                        <template v-if="errors.employee_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.employee_id"
                                                                          :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("boardRent_task_department") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showDepartmentModal" v-model="create.department_id"
                                                            :options="departments.map((type) => type.id)"
                                                            :custom-label="(opt) => $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e"
                                                            :class="{'is-invalid': $v.create.department_id.$error || errors.department_id,'is-valid': !$v.create.department_id.$invalid && !errors.department_id,}"
                                                        >
                                                        </multiselect>
                                                        <div v-if="$v.create.department_id.$error || errors.department_id" class="text-danger">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.department_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.department_id" :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group position-relative">
                                                        <label
                                                            class="control-label">{{ getCompanyKey("customer") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showCustomerModal"
                                                            :internalSearch="false"
                                                            @search-change="searchCustomer"
                                                            v-model="$v.create.customer_id.$model"
                                                            :options="customers.map((type) => type.id)"
                                                            :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                                            :class="{'is-invalid': $v.create.customer_id.$error || errors.customer_id,'is-valid': !$v.create.customer_id.$invalid && !errors.customer_id,}"
                                                        >
                                                        </multiselect>

                                                        <template v-if="errors.customer_id">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.customer_id"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_contact_person') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            data-create="9"
                                                            v-model="$v.create.contact_person.$model"
                                                            :class="{
                                                                'is-invalid':$v.create.contact_person.$error || errors.contact_person,
                                                                'is-valid':!$v.create.contact_person.$invalid && !errors.contact_person
                                                            }"
                                                        />
                                                        <template v-if="errors.contact_person">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.contact_person" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label  class="control-label">
                                                            {{ getCompanyKey('general_customer_contact_phones') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            data-create="9"
                                                            v-model="$v.create.contact_phone.$model"
                                                            :class="{
                                                                'is-invalid':$v.create.contact_phone.$error || errors.contact_phone,
                                                                'is-valid':!$v.create.contact_phone.$invalid && !errors.contact_phone
                                                            }"
                                                        />
                                                        <template v-if="errors.contact_phone">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.contact_phone" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label  class="control-label">
                                                            {{ getCompanyKey('task_title') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            data-create="9"
                                                            v-model="$v.create.task_title.$model"
                                                            :class="{
                                                                'is-invalid':$v.create.task_title.$error || errors.task_title,
                                                                'is-valid':!$v.create.task_title.$invalid && !errors.task_title
                                                            }"
                                                        />
                                                        <template v-if="errors.task_title">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.task_title" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("task_type") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showDepartmentTaskModal" v-model="create.department_task_id"
                                                            :options="departmentTasks.map((type) => type.id)"
                                                            :custom-label="(opt) => $i18n.locale == 'ar' ? departmentTasks.find((x) => x.id == opt).name : departmentTasks.find((x) => x.id == opt).name_e"
                                                            :class="{'is-invalid': $v.create.department_task_id.$error || errors.department_task_id,'is-valid': !$v.create.department_task_id.$invalid && !errors.department_task_id,}"
                                                        >
                                                        </multiselect>
                                                        <div v-if="$v.create.department_task_id.$error || errors.department_task_id" class="text-danger">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.department_task_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.department_task_id" :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ getCompanyKey('task_status') }}<span class="text-danger">*</span></label>
                                                        <multiselect
                                                            @input="showStatusModal"
                                                            v-model="create.status_id"
                                                            :options="statuses.map((type) => type.id)"
                                                            :custom-label="(opt) => $i18n.locale == 'ar'? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e"
                                                            :class="{'is-invalid': $v.create.status_id.$error || errors.status_id,'is-valid': !$v.create.status_id.$invalid && !errors.status_id,}"
                                                        >
                                                        </multiselect>
                                                        <div v-if="!$v.create.status_id.required" class="invalid-feedback">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>

                                                        <template v-if="errors.status_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.status_id"
                                                                          :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ getCompanyKey('task_owners') }}<span class="text-danger">*</span></label>
                                                        <multiselect
                                                            :disabled="true"
                                                            :multiple="true"
                                                            @input="checkIncloudIdOwners"
                                                            v-model="create.owners"
                                                            :options="employees.map((type) => type.id)"
                                                            :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                                            :class="{'is-invalid': $v.create.owners.$error || errors.owners,'is-valid': !$v.create.owners.$invalid && !errors.owners,}"
                                                        >
                                                        </multiselect>
                                                        <div v-if="!$v.create.owners.required" class="invalid-feedback">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>

                                                        <template v-if="errors.owners">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.owners"
                                                                          :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ getCompanyKey('task_supervisors') }}<span class="text-danger">*</span></label>
                                                        <multiselect
                                                            :multiple="true"
                                                            @input="checkIncloudIdSupervisors"
                                                            v-model="create.supervisors"
                                                            :options="employees.map((type) => type.id)"
                                                            :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                                            :class="{'is-invalid': $v.create.supervisors.$error || errors.supervisors,'is-valid': !$v.create.supervisors.$invalid && !errors.supervisors,}"
                                                        >
                                                        </multiselect>
                                                        <div v-if="!$v.create.supervisors.required" class="invalid-feedback">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>

                                                        <template v-if="errors.supervisors">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.supervisors"
                                                                          :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ getCompanyKey('task_notifications') }}<span class="text-danger">*</span></label>
                                                        <multiselect
                                                            :multiple="true"
                                                            @input="checkIncloudIdNotifications"
                                                            v-model="create.notifications"
                                                            :options="employees.map((type) => type.id)"
                                                            :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                                            :class="{'is-invalid': $v.create.notifications.$error || errors.notifications,'is-valid': !$v.create.notifications.$invalid && !errors.notifications,}"
                                                        >
                                                        </multiselect>
                                                        <div v-if="!$v.create.notifications.required" class="invalid-feedback">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>

                                                        <template v-if="errors.notifications">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.notifications"
                                                                          :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("execution_date") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <date-picker
                                                            @input="calcDurationCreate"
                                                            type="date"
                                                            v-model="$v.create.execution_date.$model"
                                                            format="YYYY-MM-DD"
                                                            valueType="format"
                                                            :confirm="false"
                                                            :class="{
                                                                'is-invalid': $v.create.execution_date.$error || errors.execution_date,
                                                                'is-valid': !$v.create.execution_date.$invalid && !errors.execution_date,
                                                            }">
                                                        </date-picker>
                                                        <template v-if="errors.execution_date">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.execution_date" :key="index">
                                                                {{
                                                                    errorMessage
                                                                }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("task_start_time") }}
                                                        </label>
                                                        <date-picker
                                                            @input="calcDurationCreate"
                                                            type="time"
                                                            v-model="$v.create.start_time.$model"
                                                            format="HH:mm:ss"
                                                            valueType="format"
                                                            :confirm="false"
                                                            :class="{
                                                                'is-invalid': $v.create.start_time.$error || errors.start_time,
                                                                'is-valid': !$v.create.start_time.$invalid && !errors.start_time,
                                                            }">
                                                        </date-picker>
                                                        <template v-if="errors.start_time">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.start_time" :key="index">
                                                                {{
                                                                    errorMessage
                                                                }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("execution_end_date") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <date-picker
                                                            @input="calcDurationCreate"
                                                            type="date"
                                                            v-model="$v.create.execution_end_date.$model"
                                                            format="YYYY-MM-DD"
                                                            valueType="format"
                                                            :confirm="false"
                                                            :class="{
                                                                'is-invalid': $v.create.execution_end_date.$error || errors.execution_end_date,
                                                                'is-valid': !$v.create.execution_end_date.$invalid && !errors.execution_end_date,
                                                            }">
                                                        </date-picker>
                                                        <template v-if="errors.execution_end_date">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.execution_end_date" :key="index">
                                                                {{
                                                                    errorMessage
                                                                }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("task_end_time") }}
                                                        </label>
                                                        <date-picker
                                                            @input="calcDurationCreate"
                                                            type="time"
                                                            v-model="$v.create.end_time.$model"
                                                            format="HH:mm:ss"
                                                            valueType="format"
                                                            :confirm="false"
                                                            :class="{
                                                                'is-invalid': $v.create.end_time.$error || errors.end_time,
                                                                'is-valid': !$v.create.end_time.$invalid && !errors.end_time,
                                                            }">
                                                        </date-picker>
                                                        <template v-if="errors.end_time">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.end_time" :key="index">
                                                                {{
                                                                    errorMessage
                                                                }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label  class="control-label">
                                                            {{ getCompanyKey('execution_duration') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            :disabled="true"
                                                            class="form-control"
                                                            data-create="9"
                                                            v-model="$v.create.execution_duration.$model"
                                                            :class="{
                                                                'is-invalid':$v.create.execution_duration.$error || errors.execution_duration,
                                                                'is-valid':!$v.create.execution_duration.$invalid && !errors.execution_duration
                                                            }"
                                                        />
                                                        <template v-if="errors.execution_duration">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.execution_duration" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("notification_date") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <date-picker
                                                            type="date"
                                                            v-model="$v.create.notification_date.$model"
                                                            format="YYYY-MM-DD"
                                                            valueType="format"
                                                            :confirm="false"
                                                            :class="{
                                                                'is-invalid': $v.create.notification_date.$error || errors.notification_date,
                                                                'is-valid': !$v.create.notification_date.$invalid && !errors.notification_date,
                                                            }">
                                                        </date-picker>
                                                        <template v-if="errors.notification_date">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.notification_date" :key="index">
                                                                {{
                                                                    errorMessage
                                                                }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("boardRent_panel_note") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea v-model="$v.create.note.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
                                                        <template v-if="errors.note">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.note"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab>
                                        <b-tab :disabled="!task_id" :title="$t('general.attachment')">
                                            <div class="row">
                                                <b-modal
                                                    id="uploadPhotoTitleCreate"
                                                    :title="$t('general.ImageUploads')"
                                                    title-class="font-18"
                                                    body-class="p-4 "
                                                    :hide-footer="true"
                                                    @show="uploadPhotoTitle"
                                                    @hidden="uploadPhotoTitleHidden"
                                                >
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ $t("general.titleFile") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div dir="rtl">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                data-create="1"
                                                                v-model="$v.titleFile.$model"
                                                                :class="{
                                                                        'is-invalid':$v.titleFile.$error || errors.title,
                                                                        'is-valid':!$v.titleFile.$invalid && !errors.title
                                                                    }"
                                                            />
                                                        </div>
                                                        <div v-if="!$v.titleFile.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.titleFile.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                                        <div v-if="!$v.titleFile.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.titleFile.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                                        <template v-if="errors.title">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.title" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>

                                                    <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file" id="uploadImageCreate"
                                                           @change.prevent="onImageChanged" class="input-file-upload position-absolute" :class="[
                                                        'd-none',
                                                        {
                                                          'is-invalid': $v.create.media.$error || errors.media,
                                                          'is-valid': !$v.create.media.$invalid && !errors.media,
                                                        },
                                                      ]" />

                                                    <b-button :disabled="!titleFile && $v.titleFile.$error" @click="changePhoto" variant="success" type="button"
                                                              class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                        {{ $t("general.Add") }}
                                                    </b-button>
                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                    </b-button>

                                                </b-modal>
                                                <div class="col-md-8 my-1">
                                                    <!-- file upload -->
                                                    <div class="row align-content-between" style="width: 100%; height: 100%">
                                                        <div class="col-12">
                                                            <div class="d-flex flex-wrap">
                                                                <div :class="[
                                                          'dropzone-previews col-4 position-relative mb-2',
                                                        ]" v-for="(photo, index) in images" :key="photo.id">
                                                                    <div :class="[
                                                            'card mb-0 shadow-none border',
                                                            images.length - 1 == index ? 'bg-primary' : '',
                                                          ]">
                                                                        <div class="p-2">
                                                                            <div class="row align-items-center">
                                                                                <div class="col-auto" @click="showPhoto = photo.webp">
                                                                                    <img data-dz-thumbnail :src="photo.webp" class="avatar-sm rounded bg-light"
                                                                                         @error="src = '../../../../../images/img-1.png'" />
                                                                                </div>
                                                                                <div class="col pl-0">
                                                                                    <a href="javascript:void(0);" :class="[
                                                                    'font-weight-bold',
                                                                    images.length - 1 == index
                                                                      ? 'text-white'
                                                                      : 'text-muted',
                                                                  ]" data-dz-name>
                                                                                        {{ photo.title }}
                                                                                    </a>
                                                                                </div>
                                                                                <!-- Button -->
                                                                                <a href="javascript:void(0);" :class="[
                                                                  'btn-danger dropzone-close',
                                                                  $i18n.locale == 'ar'
                                                                    ? 'dropzone-close-rtl'
                                                                    : '',
                                                                ]" data-dz-remove @click.prevent="
                                                                    deleteImageCreate(photo.id, index)
                                                                    ">
                                                                                    <i class="fe-x"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="footer-image col-12">
                                                            <b-button @click="uploadPhotoTitle" variant="success" type="button"
                                                                      class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                                {{ $t("general.Add") }}
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="show-dropzone">
                                                        <img :src="showPhoto" class="img-thumbnail" @error="src = '../../../../../images/img-1.png'" />
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab>
                                    </b-tabs>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">

                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printCustomer">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 0;" v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control">
                                            <input
                                                class="form-check-input"
                                                type="checkbox" v-model="isCheckAll"
                                                style="width: 17px;height: 17px;"
                                            >
                                        </div>
                                    </th>
                                    <th v-if="setting.employee_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('employee') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>

                                    <th v-if="setting.department_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_task_department") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>

                                    <th v-if="setting.customer_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("customer") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort( sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>

                                    <th v-if="setting.contact_person">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_contact_person') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('contact_person'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-contact_person'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.contact_phone">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_contact_phones') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('contact_phone'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-contact_phone'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.task_title">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('task_title') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('task_title'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-task_title'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.department_task_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('task_type') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.owners">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('task_owners') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.supervisors">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('task_supervisors') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.notifications">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('task_notifications') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.status_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('task_status') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.execution_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('execution_date') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('execution_date'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-execution_date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.start_time">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('task_start_time') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('start_time'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-start_time'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.end_time">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('task_end_time') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('end_time'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-end_time'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.execution_duration">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('execution_duration') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('execution_duration'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-execution_duration'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.notification_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('notification_date') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('notification_date'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-notification_date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.execution_end_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('execution_end_date') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('execution_end_date'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-execution_end_date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.note">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('boardRent_panel_note') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="tasks.sort(sortString('note'))"></i>
                                                <i class="fas fa-arrow-down" @click="tasks.sort(sortString('-note'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t('general.Action') }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="tasks.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data,index) in tasks"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em;">
                                            <input
                                                style="width: 17px;height: 17px;"
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="checkAll"
                                                :value="data.id"
                                            >
                                        </div>
                                    </td>
                                    <td v-if="setting.employee_id">
                                        <h5 v-if="data.employee" class="m-0 font-weight-normal">
                                            {{ data.employee? $i18n.locale == "ar" ? data.employee.name : data.employee.name_e : ' - '}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.department_id">
                                        <h5 v-if="data.department" class="m-0 font-weight-normal">
                                            {{$i18n.locale == "ar" ? data.department.name : data.department.name_e }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.customer_id">
                                        <h5 v-if="data.customer" class="m-0 font-weight-normal">
                                            {{ $i18n.locale == "ar" ? data.customer.name : data.customer.name_e}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.contact_person"><h5 class="m-0 font-weight-normal">{{ data.contact_person }}</h5></td>
                                    <td v-if="setting.contact_phone"><h5 class="m-0 font-weight-normal">{{ data.contact_phone }}</h5></td>
                                    <td v-if="setting.task_title"><h5 class="m-0 font-weight-normal">{{ data.task_title }}</h5></td>
                                    <td v-if="setting.department_task_id">
                                        <h5 v-if="data.department_task" class="m-0 font-weight-normal">
                                            {{ $i18n.locale == "ar" ? data.department_task.name : data.department_task.name_e}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.owners">
                                        <h5 v-if="data.owners.length > 0" class="m-0 font-weight-normal">
                                            <span v-for="(owner,index) in data.owners"
                                                      :key="owner.id">
                                            {{ $i18n.locale == "ar" ? owner.name : owner.name_e}}
                                                <span> - </span>
                                            </span>
                                        </h5>
                                    </td>
                                    <td v-if="setting.supervisors">
                                        <h5 v-if="data.supervisors.length > 0" class="m-0 font-weight-normal">
                                            <span v-for="(supervisor,index) in data.supervisors"
                                            :key="supervisor.id">
                                            {{ $i18n.locale == "ar" ? supervisor.name : supervisor.name_e}}
                                                 <span> - </span>
                                            </span>
                                        </h5>
                                    </td>
                                    <td v-if="setting.notifications">
                                        <h5 v-if="data.notifications.length > 0" class="m-0 font-weight-normal">
                                            <span v-for="(notification,index) in data.notifications"
                                                      :key="notification.id">
                                            {{ $i18n.locale == "ar" ? notification.name : notification.name_e}}
                                                 <span> - </span>
                                            </span>
                                        </h5>
                                    </td>
                                    <td v-if="setting.status_id">
                                        <h5 v-if="data.status" class="m-0 font-weight-normal">
                                            {{ $i18n.locale == "ar" ? data.status.name : data.status.name_e}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.execution_date"><h5 class="m-0 font-weight-normal">{{ data.execution_date }}</h5></td>
                                    <td v-if="setting.start_time"><h5 class="m-0 font-weight-normal">{{ data.start_time }}</h5></td>
                                    <td v-if="setting.end_time"><h5 class="m-0 font-weight-normal">{{ data.end_time }}</h5></td>
                                    <td v-if="setting.execution_duration"><h5 class="m-0 font-weight-normal">{{ data.execution_duration }}</h5></td>
                                    <td v-if="setting.notification_date"><h5 class="m-0 font-weight-normal">{{ data.notification_date }}</h5></td>
                                    <td v-if="setting.execution_end_date"><h5 class="m-0 font-weight-normal">{{ data.execution_end_date }}</h5></td>
                                    <td v-if="setting.note"><h5 class="m-0 font-weight-normal">{{ data.note }}</h5></td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                data-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                {{ $t('general.commands') }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    @click="$bvModal.show(`modal-edit-${data.id}`)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t('general.edit') }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="deleteCountry(data.id)"
                                                >
                                                    <div class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t('general.delete') }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal
                                            :id="`modal-edit-${data.id}`"
                                            :title="getCompanyKey('boardRent_task_edit_form')"
                                            title-class="font-18"
                                            body-class="p-4"
                                            dialog-class="modal-full-width"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(data.id)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                        >
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button variant="success" type="submit" class="mx-1"
                                                              v-if="!isLoader"
                                                              @click.prevent="editSubmit(data.id)"
                                                    >
                                                        {{ $t('general.Edit') }}
                                                    </b-button>

                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                                    </b-button>

                                                    <b-button
                                                        variant="danger"
                                                        type="button"
                                                        @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                                                    >
                                                        {{ $t('general.Cancel') }}
                                                    </b-button>
                                                </div>
                                                <b-tabs nav-class="nav-tabs nav-bordered">
                                                    <b-tab :title="$t('general.DataEntry')" active>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>{{ getCompanyKey('employee') }}<span class="text-danger">*</span></label>
                                                                    <multiselect
                                                                        @input="showEmployeeModalEdit"
                                                                        v-model="edit.employee_id"
                                                                        :options="employeeDepartments.map((type) => type.id)"
                                                                        :custom-label="(opt) =>$i18n.locale == 'ar'? employeeDepartments.find((x) => x.id == opt).name : employeeDepartments.find((x) => x.id == opt).name_e"
                                                                        :class="{'is-invalid': $v.edit.employee_id.$error || errors.employee_id,'is-valid': !$v.edit.employee_id.$invalid && !errors.employee_id,}"
                                                                    >
                                                                    </multiselect>
                                                                    <div v-if="!$v.edit.employee_id.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>

                                                                    <template v-if="errors.employee_id">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.employee_id" :key="index">
                                                                            {{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group position-relative">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("boardRent_task_department") }}
                                                                        <span  class="text-danger">*</span>
                                                                    </label>
                                                                    <multiselect
                                                                        @input="showDepartmentModalEdit" v-model="edit.department_id"
                                                                        :options="departments.map((type) => type.id)"
                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e"
                                                                        :class="{'is-invalid': $v.edit.department_id.$error || errors.department_id,'is-valid': !$v.edit.department_id.$invalid && !errors.department_id,}"
                                                                    >
                                                                    </multiselect>
                                                                    <div v-if="$v.edit.department_id.$error || errors.department_id" class="text-danger">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors.department_id">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.department_id" :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <hr  style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group position-relative">
                                                                    <label
                                                                        class="control-label">{{ getCompanyKey("customer") }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <multiselect
                                                                        @input="showCustomerModal"
                                                                        :internalSearch="false"
                                                                        @search-change="searchCustomer"
                                                                        v-model="$v.edit.customer_id.$model"
                                                                        :options="customers.map((type) => type.id)"
                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                                                        :class="{'is-invalid': $v.edit.customer_id.$error || errors.customer_id,'is-valid': !$v.edit.customer_id.$invalid && !errors.customer_id,}"
                                                                    >
                                                                    </multiselect>

                                                                    <template v-if="errors.customer_id">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.customer_id"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey('general_customer_contact_person') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        data-edit="9"
                                                                        v-model="$v.edit.contact_person.$model"
                                                                        :class="{
                                                                            'is-invalid':$v.edit.contact_person.$error || errors.contact_person,
                                                                            'is-valid':!$v.edit.contact_person.$invalid && !errors.contact_person
                                                                        }"
                                                                    />
                                                                    <template v-if="errors.contact_person">
                                                                        <ErrorMessage v-for="(errorMessage,index) in errors.contact_person" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label  class="control-label">
                                                                        {{ getCompanyKey('general_customer_contact_phones') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        data-create="9"
                                                                        v-model="$v.edit.contact_phone.$model"
                                                                        :class="{
                                                                            'is-invalid':$v.edit.contact_phone.$error || errors.contact_phone,
                                                                            'is-valid':!$v.edit.contact_phone.$invalid && !errors.contact_phone
                                                                        }"
                                                                    />
                                                                    <template v-if="errors.contact_phone">
                                                                        <ErrorMessage v-for="(errorMessage,index) in errors.contact_phone" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <hr  style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label  class="control-label">
                                                                        {{ getCompanyKey('task_title') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        data-create="9"
                                                                        v-model="$v.edit.task_title.$model"
                                                                        :class="{
                                                                            'is-invalid':$v.edit.task_title.$error || errors.task_title,
                                                                            'is-valid':!$v.edit.task_title.$invalid && !errors.task_title
                                                                        }"
                                                                    />
                                                                    <template v-if="errors.task_title">
                                                                        <ErrorMessage v-for="(errorMessage,index) in errors.task_title" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group position-relative">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("task_type") }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <multiselect
                                                                        @input="showDepartmentTaskModalEdit" v-model="edit.department_task_id"
                                                                        :options="departmentTasks.map((type) => type.id)"
                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? departmentTasks.find((x) => x.id == opt).name : departmentTasks.find((x) => x.id == opt).name_e"
                                                                        :class="{'is-invalid': $v.edit.department_task_id.$error || errors.department_task_id,'is-valid': !$v.edit.department_task_id.$invalid && !errors.department_task_id,}"
                                                                    >
                                                                    </multiselect>
                                                                    <div v-if="$v.edit.department_task_id.$error || errors.department_task_id" class="text-danger">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors.department_task_id">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.department_task_id" :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>{{ getCompanyKey('task_status') }}<span class="text-danger">*</span></label>
                                                                    <multiselect
                                                                        @input="showStatusModalEdit"
                                                                        v-model="edit.status_id"
                                                                        :options="statuses.map((type) => type.id)"
                                                                        :custom-label="(opt) => $i18n.locale == 'ar'? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e"
                                                                        :class="{'is-invalid': $v.edit.status_id.$error || errors.status_id,'is-valid': !$v.edit.status_id.$invalid && !errors.status_id,}"
                                                                    >
                                                                    </multiselect>
                                                                    <div v-if="!$v.edit.status_id.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>

                                                                    <template v-if="errors.status_id">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.status_id"
                                                                                      :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>{{ getCompanyKey('task_owners') }}<span class="text-danger">*</span></label>
                                                                    <multiselect
                                                                        :disabled="true"
                                                                        @input="checkIncloudsIdOwnersEdit"
                                                                        :multiple="true"
                                                                        v-model="edit.owners"
                                                                        :options="employees.map((type) => type.id)"
                                                                        :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                                                        :class="{'is-invalid': $v.edit.owners.$error || errors.owners,'is-valid': !$v.edit.owners.$invalid && !errors.owners,}"
                                                                    >
                                                                    </multiselect>
                                                                    <div v-if="!$v.edit.owners.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>

                                                                    <template v-if="errors.owners">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.owners"
                                                                                      :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>{{ getCompanyKey('task_supervisors') }}<span class="text-danger">*</span></label>
                                                                    <multiselect
                                                                        @input="checkIncloudsIdSupervisorsEdit"
                                                                        :multiple="true"
                                                                        v-model="edit.supervisors"
                                                                        :options="employees.map((type) => type.id)"
                                                                        :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                                                        :class="{'is-invalid': $v.edit.supervisors.$error || errors.supervisors,'is-valid': !$v.edit.supervisors.$invalid && !errors.supervisors,}"
                                                                    >
                                                                    </multiselect>
                                                                    <div v-if="!$v.edit.supervisors.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>

                                                                    <template v-if="errors.supervisors">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.supervisors"
                                                                                      :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>{{ getCompanyKey('task_notifications') }}<span class="text-danger">*</span></label>
                                                                    <multiselect
                                                                        :multiple="true"
                                                                        @input="checkIncloudsIdNotificationsEdit"
                                                                        v-model="edit.notifications"
                                                                        :options="employees.map((type) => type.id)"
                                                                        :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                                                        :class="{'is-invalid': $v.edit.notifications.$error || errors.notifications,'is-valid': !$v.edit.notifications.$invalid && !errors.notifications,}"
                                                                    >
                                                                    </multiselect>
                                                                    <div v-if="!$v.edit.notifications.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>

                                                                    <template v-if="errors.notifications">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.notifications"
                                                                                      :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("execution_date") }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <date-picker
                                                                        type="date"
                                                                        @input="calcDurationEdit"
                                                                        v-model="$v.edit.execution_date.$model"
                                                                        format="YYYY-MM-DD"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                        :class="{
                                                                            'is-invalid': $v.edit.execution_date.$error || errors.execution_date,
                                                                            'is-valid': !$v.edit.execution_date.$invalid && !errors.execution_date,
                                                                        }">
                                                                    </date-picker>
                                                                    <template v-if="errors.execution_date">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.execution_date" :key="index">
                                                                            {{
                                                                                errorMessage
                                                                            }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("task_start_time") }}
                                                                    </label>
                                                                    <date-picker
                                                                        @input="calcDurationEdit"
                                                                        type="time"
                                                                        v-model="$v.edit.start_time.$model"
                                                                        format="HH:mm:ss"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                        :class="{
                                                                            'is-invalid': $v.edit.start_time.$error || errors.start_time,
                                                                            'is-valid': !$v.edit.start_time.$invalid && !errors.start_time,
                                                                        }">
                                                                    </date-picker>
                                                                    <template v-if="errors.start_time">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.start_time" :key="index">
                                                                            {{ errorMessage}}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("execution_end_date") }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <date-picker
                                                                        @input="calcDurationEdit"
                                                                        type="date"
                                                                        v-model="$v.edit.execution_end_date.$model"
                                                                        format="YYYY-MM-DD"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                        :class="{
                                                                            'is-invalid': $v.edit.execution_end_date.$error || errors.execution_end_date,
                                                                            'is-valid': !$v.edit.execution_end_date.$invalid && !errors.execution_end_date,
                                                                        }">
                                                                    </date-picker>
                                                                    <template v-if="errors.execution_end_date">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.execution_end_date" :key="index">
                                                                            {{
                                                                                errorMessage
                                                                            }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("task_end_time") }}
                                                                    </label>
                                                                    <date-picker
                                                                        @input="calcDurationEdit"
                                                                        type="time"
                                                                        v-model="$v.edit.end_time.$model"
                                                                        format="HH:mm:ss"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                        :class="{
                                                                            'is-invalid': $v.edit.end_time.$error || errors.end_time,
                                                                            'is-valid': !$v.edit.end_time.$invalid && !errors.end_time,
                                                                        }">
                                                                    </date-picker>
                                                                    <template v-if="errors.end_time">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.end_time" :key="index">
                                                                            {{errorMessage}}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label  class="control-label">
                                                                        {{ getCompanyKey('execution_duration') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input
                                                                        :disabled="true"
                                                                        type="text"
                                                                        class="form-control"
                                                                        data-create="9"
                                                                        v-model="$v.edit.execution_duration.$model"
                                                                        :class="{
                                                                            'is-invalid':$v.edit.execution_duration.$error || errors.execution_duration,
                                                                            'is-valid':!$v.edit.execution_duration.$invalid && !errors.execution_duration
                                                                        }"
                                                                    />
                                                                    <template v-if="errors.execution_duration">
                                                                        <ErrorMessage v-for="(errorMessage,index) in errors.execution_duration" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("notification_date") }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <date-picker
                                                                        type="date"
                                                                        v-model="$v.edit.notification_date.$model"
                                                                        format="YYYY-MM-DD"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                        :class="{
                                                                            'is-invalid': $v.edit.notification_date.$error || errors.notification_date,
                                                                            'is-valid': !$v.edit.notification_date.$invalid && !errors.notification_date,
                                                                        }">
                                                                    </date-picker>
                                                                    <template v-if="errors.notification_date">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.notification_date" :key="index">
                                                                            {{
                                                                                errorMessage
                                                                            }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("boardRent_panel_note") }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <textarea v-model="$v.edit.note.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
                                                                    <template v-if="errors.note">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.note"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </b-tab>
                                                    <b-tab :title="$t('general.attachment')">
                                                        <div class="row">
                                                            <b-modal
                                                                id="uploadPhotoTitleCreate"
                                                                :title="$t('general.ImageUploads')"
                                                                title-class="font-18"
                                                                body-class="p-4 "
                                                                :hide-footer="true"
                                                                @show="uploadPhotoTitle"
                                                                @hidden="uploadPhotoTitleHidden"
                                                            >
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ $t("general.titleFile") }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div dir="rtl">
                                                                        <input
                                                                            type="text"
                                                                            class="form-control"
                                                                            data-create="1"
                                                                            v-model="$v.titleFile.$model"
                                                                            :class="{
                                                                    'is-invalid':$v.titleFile.$error || errors.title,
                                                                    'is-valid':!$v.titleFile.$invalid && !errors.title
                                                                }"
                                                                        />
                                                                    </div>
                                                                    <div v-if="!$v.titleFile.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.titleFile.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                                                    <div v-if="!$v.titleFile.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.titleFile.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                                                    <template v-if="errors.title">
                                                                        <ErrorMessage v-for="(errorMessage,index) in errors.title" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                    </template>
                                                                </div>

                                                                <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file"
                                                                       id="uploadImageEdit" @change.prevent="onImageChanged"
                                                                       class="input-file-upload position-absolute" :class="[
                                                                    'd-none',
                                                                    {
                                                                      'is-invalid': $v.edit.media.$error || errors.media,
                                                                      'is-valid':
                                                                        !$v.edit.media.$invalid && !errors.media,
                                                                    },
                                                                  ]" />

                                                                <b-button :disabled="!titleFile && $v.titleFile.$error" @click="changePhotoEdit" variant="success" type="button"
                                                                          class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                                    {{ $t("general.Add") }}
                                                                </b-button>
                                                                <b-button variant="success" class="mx-1" disabled v-else>
                                                                    <b-spinner small></b-spinner>
                                                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                                </b-button>

                                                            </b-modal>

                                                            <div class="col-md-8 my-1">
                                                                <!-- file upload -->
                                                                <div class="row align-content-between" style="width: 100%; height: 100%">
                                                                    <div class="col-12">
                                                                        <div class="d-flex flex-wrap">
                                                                            <div class="dropzone-previews col-4 position-relative mb-2"
                                                                                 v-for="(photo, index) in images">
                                                                                <div :class="[
                                                                                    'card mb-0 shadow-none border',
                                                                                    images.length - 1 == index
                                                                                      ? 'bg-primary'
                                                                                      : '',
                                                                                  ]">
                                                                                    <div class="p-2">
                                                                                        <div class="row align-items-center">
                                                                                            <div class="col-auto" @click="showPhoto = photo.webp">
                                                                                                <img data-dz-thumbnail :src="photo.webp"
                                                                                                     class="avatar-sm rounded bg-light"
                                                                                                     @error="src = '../../../../../images/img-1.png'" />
                                                                                            </div>
                                                                                            <div class="col pl-0">
                                                                                                <a href="javascript:void(0);" :class="[
                                                                                                    'font-weight-bold',
                                                                                                    images.length - 1 == index
                                                                                                      ? 'text-white'
                                                                                                      : 'text-muted',
                                                                                                  ]" data-dz-name>
                                                                                                    {{ photo.title }}
                                                                                                </a>
                                                                                            </div>
                                                                                            <!-- Button -->
                                                                                            <a href="javascript:void(0);" :class="[
                                                                                              'btn-danger text-muted dropzone-close',
                                                                                              $i18n.locale == 'ar'
                                                                                                ? 'dropzone-close-rtl'
                                                                                                : '',
                                                                                            ]" data-dz-remove @click.prevent="
                                                                                                      deleteImageCreate(photo.id, index)
                                                                                                    ">
                                                                                                <i class="fe-x"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="footer-image col-12">
                                                                        <b-button @click="uploadPhotoTitle" variant="success" type="button"
                                                                                  class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                                            {{ $t("general.Add") }}
                                                                        </b-button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="show-dropzone">
                                                                    <img :src="showPhoto" class="img-thumbnail" @error="src = '../../../../../images/img-1.png'" />
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
                                        <button
                                            @mousemove="log(data.id)"
                                            @mouseover="log(data.id)"
                                            type="button"
                                            class="btn"
                                            :id="`tooltip-${data.id}`"
                                            :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                                            :title="Tooltip"
                                        >
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="21">{{ $t('general.notDataFound') }}</th>
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
<style scoped>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
}
.modal-body .card .tabs .tab-content {
    padding: 20px 60px 40px !important;
}
</style>
