b<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import permissionGuard from "../../../../helper/permission";

import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import bootstrapPlugin from "@fullcalendar/bootstrap";
import listPlugin from "@fullcalendar/list";
import Swal from "sweetalert2";
import {maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import Multiselect from "vue-multiselect";
import translation from "../../../../helper/mixin/translation-mixin";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import {formatDateOnly} from "../../../../helper/startDate";
import DatePicker from "vue2-datepicker";

/**
 * Calendar component
 */
export default {
    page: {
        title: "Tasks Calendar",
        meta: [{name: "Tasks Calendar", content: 'Tasks Calendar'}],
    },
    components: {
        Layout,
        FullCalendar,
        PageHeader,
        Multiselect,
        ErrorMessage,
        loader,
        DatePicker
    },
    mixins: [translation],
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Tasks Report", "all Calender");
        });
    },
    beforeMount() {
        let l = new Date();
        this.search.months_number = l.getMonth() + 1;
        this.search.year = l.getFullYear();
        this.getData();
    },
    mounted() {
        this.$store.dispatch('locationIp/getIp');
        this.getCustomTableFields();
        this.getLocation();
        if (this.isVisible("department_id"))  this.getDepartment();
        if (this.isVisible("status_id"))  this.getStatus();
        if (this.isVisible("priority_id"))  this.getPriority();
    },
    data() {
        return {
            title: "Calendar",
            items: [
                {
                    text: "Minton",
                },
                {
                    text: "Apps",
                },
                {
                    text: "Calendar",
                    active: true,
                },
            ],
            create: {
                employee_id: null,
                department_id: null,
                customer_id:null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                actual_execution_date:this.formatDate(new Date()),
                actual_start_time:'',
                actual_end_time:'',
                actual_execution_duration:'0 Day 0 Minutes',
                actual_execution_end_date:this.formatDate(new Date()),
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
                type: 'equipment',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: '',
                is_closed: 0,
                task_requirement: ''
            },

            calendarEvents: [],
            calendarOptions: {
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
                },
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin,
                    bootstrapPlugin,
                    listPlugin,
                ],
                initialView: "dayGridMonth",
                themeSystem: "bootstrap",
                initialEvents: [],
                events: [],
                editable: true,
                droppable: true,
                eventResizableFromStart: true,
                dateClick: false,
                // dateClick: this.isPermission('create Task') ? this.dateClicked : false,
                eventClick: false,
                // eventClick: this.isPermission('update Task') ? this.editEvent : false,
                eventsSet: this.handleEvents,
                weekends: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                customButtons: {
                    prev: { // this overrides the prev button
                        text: "<",
                        click: () => {
                            if(this.search.months_number != 1){
                                this.search.months_number -= 1;
                            }else {
                                this.search.months_number = 12;
                                this.search.year -= 1
                            }
                            this.getData();
                            let calendarApi = this.$refs.fullCalendar.getApi();
                            calendarApi.prev();
                        }
                    },
                    next: { // this overrides the next button
                        text: ">",
                        click: () => {
                            if(this.search.months_number != 12){
                                this.search.months_number += 1;
                            }else {
                                this.search.months_number = 1;
                                this.search.year += 1
                            }
                            this.getData();
                            let calendarApi = this.$refs.fullCalendar.getApi();
                            calendarApi.next();
                        }
                    }
                }
            },
            showModal: false,
            submitted: false,
            submit: false,
            newEventData: {},
            deleteId: {},
            search: {
                customer_ids: [],
                status_ids: [],
                department_ids: [],
                employee_ids: [],
                months_number: 0,
                year: 0
            },
            is_disabled: false,
            customers: [],
            isLoader: false,
            isVaildPhone: false,
            errors: {},
            employees: [],
            fields: [],
            employeeDepartments: [],
            all_locations: [],
            all_departments: [],
            departments: [],
            departmentTasks: [],
            statuses: [],
            equipments: [],
            locations: [],
            equipment_childs: [],
            priorities: [],
            equipment_id: null,
            images: [],
            types: ['customer','general','equipment'],
            media: {},
            task_id: null,
            department_id: null,
            titleFile: "",
            saveImageName: [],
            showPhoto: "../../../../../images/img-1.png",
            image: '',
            idDelete: null,
        };
    },
    validations: {
        create: {
            employee_id: {required},
            department_id: {required},
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
            admin_note: {},
            media: {},
            type: {required},
            customer_id: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "customer";
                })},
            contact_person: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "customer";
                })},
            contact_phone: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "customer";
                })},
            equipment_id: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "equipment";
                })},
            location_id: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "equipment";
                })},
            task_requirement: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "general";
                })},
                actual_execution_date: {required: requiredIf(function (model) {
                    return this.isRequired("actual_execution_date");
                })},
            actual_start_time:{required: requiredIf(function (model) {
                    return this.isRequired("actual_start_time");
                })},
            actual_end_time:{required: requiredIf(function (model) {
                    return this.isRequired("actual_end_time");
                })},
            actual_execution_duration: {required: requiredIf(function (model) {
                    return this.isRequired("actual_execution_duration");
                })},
            actual_execution_end_date: {required: requiredIf(function (model) {
                    return this.isRequired("actual_execution_end_date");
                })},
            is_closed: {required},
            priority_id: {required}
        },

        titleFile: {required, minLength: minLength(2), maxLength: maxLength(100),}
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_tasks`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isCustom = false;
                });
        },
        isRequired(fieldName) {
            if (this.fields.length > 0) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_required == 1 ? true : false;
            }
            return true;
        },
        isVisible(fieldName) {
            if (this.fields.length > 0) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_visible == 1 ? true : false;
            }
            return true;
        },
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        getData(){
            this.isLoader = true;

            adminApi.post(`/tasks/all`, this.search)
                .then((res) => {
                    let l = res.data.data;
                    this.calendarOptions.initialEvents = l;
                    this.calendarOptions.events = l;
                    this.search = {
                        customer_ids: [],
                        status_ids: [],
                        department_ids: [],
                        employee_ids: [],
                        months_number: this.search.months_number,
                        year: this.search.year
                    };
                    this.$bvModal.hide(`search`);
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
        /**
         * Modal form submit
         */
        // eslint-disable-next-line no-unused-vars
        AddSubmit() {
            this.create.company_id = JSON.parse(localStorage.getItem("company_id"));
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi.post(`/tasks`, {
                    ...this.create,
                            execution_date: this.create.execution_date.slice(0,10)  + (this.create.start_time ? ` ${this.create.start_time}` : ' 00:00:00'),
                            execution_end_date: this.create.execution_end_date.slice(0,10)  + (this.create.end_time ? ` ${this.create.end_time}` : ' 00:00:00'),
                            actual_execution_date: this.create.actual_execution_date.slice(0,10)  + (this.create.start_time ? ` ${this.create.start_time}` : ' 00:00:00'),
                            actual_execution_end_date: this.create.actual_execution_end_date.slice(0,10)  + (this.create.end_time ? ` ${this.create.end_time}` : ' 00:00:00'),
                            company_id: this.$store.getters["auth/company_id"]
                        }).then((res) => {
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
        // eslint-disable-next-line no-unused-vars
        /**
         * Edit event modal submit
         */
        // eslint-disable-next-line no-unused-vars
        editSubmit() {
            this.create.company_id = JSON.parse(localStorage.getItem("company_id"));
            this.$v.create.$touch();
            this.images.forEach((e) => {
                this.create.old_media.push(e.id);
            });

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi.put(`/tasks/${this.task_id}`, {
                    ...this.create,
                    execution_date: this.create.execution_date.slice(0,10) +  (this.create.start_time ? ` ${this.create.start_time}` : ' 00:00:00'),
                    execution_end_date: this.create.execution_end_date.slice(0,10)  +  (this.create.end_time ? ` ${this.create.end_time}`  : ' 00:00:00'),
                    actual_execution_date: this.create.actual_execution_date.slice(0,10)  + (this.create.start_time ? ` ${this.create.start_time}` : ' 00:00:00'),
                    actual_execution_end_date: this.create.actual_execution_end_date.slice(0,10)  + (this.create.end_time ? ` ${this.create.end_time}` : ' 00:00:00'),
                })
                    .then((res) => {
                        this.getData();
                        this.showModal = false;
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
        /**
         * Modal open for add event
         */
        dataCreate() {
            let time = new Date();
            this.create = {
                employee_id: null,
                department_id: null,
                customer_id: null,
                contact_person: '',
                contact_phone: '',
                task_title: '',
                department_task_id: null,
                owners: [],
                supervisors: [],
                notifications: [],
                status_id: 1,
                execution_date: this.formatDate(this.newEventData.date),
                start_time: `${time.getHours()}:${time.getMinutes()}:${time.getSeconds()}`,
                end_time: `${time.getHours() + 2}:${time.getMinutes()}:${time.getSeconds()}`,
                execution_duration: '0 Day 0 Minutes',
                actual_execution_date:this.formatDate(new Date()),
                actual_start_time:'',
                actual_end_time:'',
                actual_execution_duration:'0 Day 0 Minutes',
                actual_execution_end_date:this.formatDate(new Date()),
                notification_date: this.formatDate(this.newEventData.date),
                execution_end_date: this.formatDate(this.newEventData.date),
                note:'',
                type: 'equipment',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: '',
                is_closed: 0,
                task_requirement: ''
            };
            this.department_id = null;
            this.task_id = null;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.media = {};
            this.images = [];
        },
        resetModalHidden() {
            this.create = {
                employee_id: null,
                department_id: null,
                customer_id: null,
                contact_person: '',
                contact_phone: '',
                task_title: '',
                department_task_id: null,
                owners: [],
                supervisors: [],
                notifications: [],
                status_id: null,
                execution_date: this.formatDate(new Date()),
                start_time: '',
                end_time: '',
                execution_duration: '0 Day 0 Minutes',
                notification_date: this.formatDate(new Date()),
                execution_end_date: this.formatDate(new Date()),
                note: '',
                media: null,
                type: 'equipment',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: '',
                is_closed: 0,
                task_requirement: ''
            };
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.images = [];
            this.department_id = null;
            this.task_id = null;
            this.is_disabled = false;
            this.showModal = false;
        },
        async dateClicked(info) {
            this.newEventData = info;
            this.showModal = true;
            await this.getStatus();
            this.dataCreate();
            await this.getEmployees();
            await this.getEmployeeDepartments();
            await this.getPriority();
            this.calcDurationCreate();
            this.showPhoto = "../../../../../images/img-1.png";
        },
        resetForm() {
            this.dataCreate();
            this.is_disabled = false;
        },
        /**
         * Modal open for edit event
         */
        async editEvent(info) {
            this.showModal = true;
            this.task_id =  info.event.id;
            let calendar = this.calendarOptions.events.find((el) => el.id ==  this.task_id)
            await this.getEmployees();
            await this.getEmployeeDepartments();


            this.department_id = calendar.department_id;
            this.task_id = calendar.id;
            await this.getDepartmentTask();
            this.create.employee_id = calendar.employee_id;
            this.create.department_id = calendar.department_id;
            this.create.task_title = calendar.task_title;
            this.create.department_task_id = calendar.department_task_id;
            this.create.owners = calendar.owners;
            this.create.supervisors = calendar.supervisors;
            this.create.notifications = calendar.notifications;
            this.create.status_id = calendar.status_id;
            this.create.execution_date = calendar.execution_date;
            this.create.start_time = calendar.start_time;
            this.create.end_time = calendar.end_time;
            this.create.execution_duration = calendar.execution_duration;
            this.create.notification_date = calendar.notification_date;
            this.create.execution_end_date = calendar.execution_end_date;
            this.create.admin_note = calendar.admin_note;
            this.create.is_closed = calendar.is_closed ;

            this.create.actual_execution_date = calendar.actual_execution_date;
            this.create.actual_start_time = calendar.actual_start_time;
            this.create.actual_end_time = calendar.actual_end_time;
            this.create.actual_execution_duration = calendar.actual_execution_duration;
            this.create.actual_execution_end_date = calendar.actual_execution_end_date;
            this.create.note = calendar.note;
            if( this.create.type == 'customer'){
                await this.getCustomers();
                this.create.customer_id = calendar.customer_id;
                this.create.contact_person = calendar.contact_person;
                this.create.contact_phone = calendar.contact_phone;
            }else if (this.create.type == 'equipment') {
                this.create.location_id = calendar.location_id;
                await this.getEquipment(calendar.location_id);
                this.equipment_id = calendar.equipment.parent_id;
                await this.getEquipmentChild(this.equipment_id);
                this.create.equipment_id = calendar.equipment.id;
            }else{
                this.create.task_requirement = calendar.task_requirement;
            }

            await this.getStatus();
            this.create.status_id = calendar.status_id;
            await this.getPriority();
            this.create.priority_id = calendar.priority_id;

            this.images = calendar.media ?? [];
            if (this.images && this.images.length > 0) {
                this.showPhoto = this.images[this.images.length - 1].webp;
            } else {
                this.showPhoto = "../../../../../images/img-1.png";
            }
            this.errors = {};
        },

        confirm() {
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
                        .delete(`/tasks/${this.task_id}`)
                        .then((res) => {
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
        },

        /**
         * Show list of events
         */
        handleEvents(events) {
            this.currentEvents = events;
        },

        /**
         *  reset Modal (create)
         */
        resetModalHiddenSearch() {
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.$bvModal.hide(`search`);
            this.search = {
                customer_ids: [],
                status_ids: [],
                department_ids: [],
                employee_ids: [],
                months_number: this.search.months_number,
                year: this.search.year
            };
        },
        /**
         *  hidden Modal (create)
         */
        async resetModalSearch() {
            await this.getCustomers();
            await this.getEmployees();
            await this.getStatus();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },
        // start status
        async getStatus() {
            this.isLoader = true;
            await adminApi
                .get(`/statuses?module_type=bordRent`)
                .then((res) => {
                    let l = res.data.data;
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
            if (this.create.status_id == 0) {
                this.$bvModal.show("status-create");
                this.create.status_id = null;
            }
        },

        //start employee
        async getEmployeeDepartments() {
            await adminApi
                .get(`/employees?depertment=1`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضف موظف", name_e: "Add Employee"});
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
            } else {
                let customer = this.customerDepartment(this.create.employee_id);
                if (customer) {
                    this.create.owners = [];
                    this.create.department_id = customer.department_id;
                    this.department_id = customer.department_id;
                    this.create.owners.push(this.create.employee_id);
                    await this.getDepartmentTask();
                }
            }
        },
        async showEmployeeModalEdit() {
            if (this.create.employee_id == 0) {
                this.$bvModal.show("employee-create");
                this.create.employee_id = null;
            } else {
                let customer = this.customerDepartment(this.create.employee_id);
                if (customer) {
                    this.create.owners = [];
                    this.create.department_id = customer.department_id;
                    this.department_id = customer.department_id;
                    this.create.owners.push(this.create.employee_id);
                    await this.getDepartmentTask();
                }
            }
        },
        customerDepartment(id) {
            if (this.employees && id) {
                return this.employees.find(e => id == e.id);
            }
        },
        // start department
        getDepartment() {
            this.isLoader = true;
            let emp_id = this.$store.state.auth.type == 'admin' ? '' : this.$store.state.auth.user.employee_id
            adminApi
                .get(`/depertments?task_employee_id=${emp_id}`)
                .then((res) => {
                    let l = res.data.data;

                    this.departments = l;
                    this.all_departments = l;

                    let locations= this.get_default_locations(l)

                    this.locations = locations

                    if(locations.length == 1){
                        this.create.location_id = locations[0].id
                        this.getEquipment(this.create.location_id)
                    }
                    if(l.length == 1)
                        this.create.department_id = l[0].id

                    if(this.create.location_id && this.create.department_id)
                        this.setSupervisorsAndAttentions(l[0])

                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        get_default_locations(departments){
            let emp_id = this.$store.state.auth.type == 'admin' ? '' : this.$store.state.auth.user.employee_id
            let locations = []
            departments.forEach((department) => {
                department.locations.forEach((ele) => {
                    this.all_locations.forEach((location) => {
                        if(ele.location_id == location.id && (this.$store.state.auth.type == 'admin' || (ele.supervisors.includes(emp_id) || ele.attentions.includes(emp_id) || ele.engineers.includes(emp_id))))
                            locations.push(location)
                    })

                })
            })

            return locations
        },
        showDepartmentModal() {
            if(!this.create.location_id && !this.create.department_id){
                this.departments = this.all_departments
                this.locations= this.get_default_locations(this.all_departments)
            }
            let department = this.departments.find((el) => el.id ==  this.create.department_id)
            if(department){
                if(!this.create.location_id || !department.locations.find((el) => el.location_id ==  this.create.location_id)){
                    this.locations = []
                    this.create.location_id = null
                    this.create.supervisors = []
                    this.create.notifications = []
                }else{
                    this.setSupervisorsAndAttentions()
                }

                this.department_id = this.create.department_id;
                this.setLocation()
                this.getDepartmentTask();
            }else{
                this.create.supervisors = []
                this.create.notifications = []
            }
        },
        setSupervisorsAndAttentions(department){
            department.locations.forEach((ele) => {
                if(ele.location_id == this.create.location_id){
                    setTimeout(() => {
                        this.create.supervisors = ele.supervisors
                        this.create.notifications = ele.attentions
                    },1000)

                }
            })
        },
        setLocation(){
            let department = this.departments.find((el) => el.id ==  this.create.department_id)
            let emp_id = this.$store.state.auth.type == 'admin' ? '' : this.$store.state.auth.user.employee_id

            let locations= []
                department.locations.forEach((ele) => {
                    this.all_locations.forEach((location) => {
                        if(ele.location_id == location.id && (this.$store.state.auth.type == 'admin' || (ele.supervisors.includes(emp_id) || ele.attentions.includes(emp_id) || ele.engineers.includes(emp_id))))
                            locations.push(location)
                    })
                })
                this.locations = locations
                if(locations.length == 1){
                    this.create.location_id = locations[0].id
                    this.getEquipment(this.create.location_id)
                    this.setSupervisorsAndAttentions(department)
                }
        },
        setDepartment(){
            let departments =[]
            this.all_departments.forEach((department) =>{
                department.locations.forEach((ele) => {
                    if(ele.location_id == this.create.location_id)
                        departments.push(department)

                })
            })
            this.departments = departments
            if(departments.length == 1){
                this.create.department_id = departments[0].id
                this.setSupervisorsAndAttentions(departments[0])
            }

        },
        async showDepartmentModal() {
            if (this.create.department_id == 0) {
                this.$bvModal.show("create_department_task");
                this.create.department_id = null;
            }else {
                let department = this.departments.find((el) => el.id ==  this.create.department_id)
                this.department_id = this.create.department_id;
                this.create.supervisors = department.supervisors ? department.supervisors : [];
                await this.getDepartmentTask();
            }
        },
        async showDepartmentModalEdit() {
            if (this.create.department_id == 0) {
                this.$bvModal.show("create_department_task");
                this.create.department_id = null;
            }else {
                let department = this.departments.find((el) => el.id ==  this.create.department_id)
                this.department_id = this.create.department_id;
                this.create.supervisors = department.supervisors ? department.supervisors : [];
                await this.getDepartmentTask();
            }
        },

        // start customer
        async getCustomers(search='') {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer?limet=10&company_id=${this.company_id}${search? '&search=${search}&columns[0]=name&columns[1]=name_e' : ''}`)
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
        getCustomerData (id) {
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
        // start equipment
        async getEquipment(location){
            this.isLoader = true;
            await adminApi
                .get(`/equipments?location_id=${location}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف معده", name_e: "Add Equipment" });
                    this.equipments = l;
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
        async getEquipmentChild(child){
            this.isLoader = true;
            await adminApi
                .get(`/equipments?equipment_id=${child}`)
                .then((res) => {
                    let l = res.data.data;
                    this.equipment_childs = l;
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
        async showEquipmentModal() {
            if (this.equipment_id == 0) {
                this.$bvModal.show("equipment-create");
            }else {
                await this.getEquipmentChild(this.equipment_id)
            }
        },
        async showEquipmentModalEdit() {
            if (this.equipment_id == 0) {
                this.$bvModal.show("equipment-create");
            }else {
                await this.getEquipmentChild(this.equipment_id)
            }
        },

        // start location
        async getLocation(){
            this.isLoader = true;
            await adminApi
                .get(`/locations`)
                .then((res) => {
                    let l = res.data.data;
                    this.all_locations = l;
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
        async  showLocationModal() {
            if(!this.create.location_id && !this.create.department_id){
                this.departments = this.all_departments
                this.locations= this.get_default_locations(this.all_departments)
            }
            this.create.supervisors = []
            this.create.notifications = []
            if(this.create.location_id){
                this.setDepartment()
                this.getEquipment(this.create.location_id);
                if(this.create.department_id){
                    let department = this.departments.find((el) => el.id ==  this.create.department_id)
                    this.setSupervisorsAndAttentions(department)
                }
            }
        },

        // start department task
        async getDepartmentTask() {
            this.isLoader = true;
            await adminApi
                .get(`/department-tasks?department_id=${this.department_id}`)
                .then((res) => {
                    let l = res.data.data;
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
            if (this.create.department_task_id == 0) {
                this.$bvModal.show("create-task");
                this.create.department_task_id = null;
            }
        },
        // chech employee id create
        checkIncloudIdOwners(e) {
            let employee_id = e[e.length - 1];
            if (employee_id) {
                if (this.create.supervisors.includes(employee_id)) {
                    this.create.supervisors.splice(this.create.supervisors.indexOf(employee_id), 1)
                }
                if (this.create.notifications.includes(employee_id)) {
                    this.create.notifications.splice(this.create.notifications.indexOf(employee_id), 1)
                }
            }
        },
        checkIncloudIdSupervisors(e) {
            let employee_id = e[e.length - 1];
            if (employee_id) {
                if (this.create.owners.includes(employee_id)) {
                    this.create.owners.splice(this.create.owners.indexOf(employee_id), 1)
                }
                if (this.create.notifications.includes(employee_id)) {
                    this.create.notifications.splice(this.create.notifications.indexOf(employee_id), 1)
                }
            }
        },
        checkIncloudIdNotifications(e) {
            let employee_id = e[e.length - 1];
            if (employee_id) {
                if (this.create.owners.includes(employee_id)) {
                    this.create.owners.splice(this.create.owners.indexOf(employee_id), 1)
                }
                if (this.create.supervisors.includes(employee_id)) {
                    this.create.supervisors.splice(this.create.supervisors.indexOf(employee_id), 1)
                }
            }
        },
        // chech employee id edit
        checkIncloudsIdOwnersEdit(e) {
            let employee_id = e[e.length - 1];
            if (employee_id) {
                if (this.create.supervisors.includes(employee_id)) {
                    this.create.supervisors.splice(this.create.supervisors.indexOf(employee_id), 1)
                }
                if (this.create.notifications.includes(employee_id)) {
                    this.create.notifications.splice(this.create.notifications.indexOf(employee_id), 1)
                }
            }
        },
        checkIncloudsIdSupervisorsEdit(e) {
            let employee_id = e[e.length - 1];
            if (employee_id) {
                if (this.create.owners.includes(employee_id)) {
                    this.create.owners.splice(this.create.owners.indexOf(employee_id), 1)
                }
                if (this.create.notifications.includes(employee_id)) {
                    this.create.notifications.splice(this.create.notifications.indexOf(employee_id), 1)
                }
            }
        },
        checkIncloudsIdNotificationsEdit(e) {
            let employee_id = e[e.length - 1];
            if (employee_id) {
                if (this.create.owners.includes(employee_id)) {
                    this.create.owners.splice(this.create.owners.indexOf(employee_id), 1)
                }
                if (this.create.supervisors.includes(employee_id)) {
                    this.create.supervisors.splice(this.create.supervisors.indexOf(employee_id), 1)
                }
            }
        },
        calcDurationCreate() {
            let TotalDays = 0;
            let TotalTime = '0 Minutes';
            let execution_date = new Date(this.create.execution_date).getTime();
            let execution_end_date = new Date(this.create.execution_end_date).getTime();
            if (execution_date < execution_end_date) {
                let difference = execution_end_date - execution_date;
                TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
            }

            // --------- calc Time --------------
            var today = new Date().toJSON().slice(0, 10).replace(/-/g, '/');

            let startTime = new Date(today + " " + this.create.start_time);
            let endTime = new Date(today + " " + this.create.end_time);
            if (endTime > startTime) {
                var difference = endTime.getTime() - startTime.getTime();

                TotalTime = Math.round(difference / 60000) + " Minutes";
            }

            this.create.execution_duration = `${TotalDays} Days, ${TotalTime}`

        },
        calcDurationEdit() {
            let TotalDays = 0;
            let TotalTime = '0 Minutes';
            let execution_date = new Date(this.create.execution_date).getTime();
            let execution_end_date = new Date(this.create.execution_end_date).getTime();
            if (execution_date < execution_end_date) {
                let difference = execution_end_date - execution_date;
                TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
            }

            // --------- calc Time --------------
            var today = new Date().toJSON().slice(0, 10).replace(/-/g, '/');

            let startTime = new Date(today + " " + this.create.start_time);
            let endTime = new Date(today + " " + this.create.end_time);
            if (endTime > startTime) {
                var difference = endTime.getTime() - startTime.getTime();

                TotalTime = Math.round(difference / 60000) + " Minutes";
            }

            this.create.execution_duration = `${TotalDays} Days, ${TotalTime}`

        },
        uploadPhotoTitle() {
            this.titleFile = "";
            this.$bvModal.show(`uploadPhotoTitleCreate`);
            this.errors = {};
        },
        uploadPhotoTitleHidden() {
            this.titleFile = "";
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
                                .put(`/tasks/${this.task_id}`, {old_media, media: new_media})
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
                                        .put(`/tasks/${this.country_id}`, {old_media, media: new_media})
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
                .put(`/tasks/${this.task_id}`, {old_media})
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
        formatDate(value) {
            return formatDateOnly(value);
        },
        async  showTypeModal(){
                if(this.create.type == "equipment") {
                    this.create.equipment_id = null;
                    this.create.location_id = null;
                    this.create.supervisors = []
                    this.create.notifications = []
                    this.create.priority_id = null;
                    this.equipments = [];
                    this.equipment_childs = [];
                }else if(this.create.type == "customer") {
                    this.create.customer_id = null;
                    this.create.priority_id = null;
                    this.create.contact_person = '';
                    this.create.contact_phone = '';
                    if(this.customers.length == 0)  this.getCustomers();
                }else {
                    this.create.task_requirement = '';
                }
        },
        async getPriority() {
            this.isLoader = true;

            await adminApi
                .get(`/priorities`)
                .then((res) => {
                    let l = res.data.data;
                    this.priorities = l;
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
    },
}
</script>

<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <div class="calender-card-body position-relative">
            <!--       start loader       -->
            <loader size="large" v-if="isLoader" />
            <!--       end loader       -->
            <div class="row position-relative">
                <div :class="['col-12 position-absolute',$i18n.locale == 'en' ?'search':'search-ar']">
                    <b-button
                        v-b-modal.search
                        variant="primary"
                        class="btn-sm mx-1 font-weight-bold"
                    >
                        {{ $t('general.Search') }}
                        <i class="fe-search"></i>
                    </b-button>
                    <b-modal
                        id="search"
                        :title="$t('general.Search')"
                        title-class="font-18"
                        body-class="p-4"
                        size="lg"
                        :hide-footer="true"
                        @show="resetModalSearch"
                        @hidden="resetModalHiddenSearch"
                    >
                        <form>
                            <div class="d-flex justify-content-end">
                                <template v-if="!is_disabled">
                                    <b-button
                                        variant="success"
                                        type="button" class="mx-1"
                                        v-if="!isLoader"
                                        @click.prevent="getData"
                                    >
                                        {{ $t('general.Search') }}
                                    </b-button>

                                    <b-button variant="success" class="mx-1" disabled v-else>
                                        <b-spinner small></b-spinner>
                                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                    </b-button>
                                </template>
                                <!-- Emulate built in modal footer ok and cancel button actions -->

                                <b-button variant="danger" type="button" @click.prevent="resetModalHiddenSearch">
                                    {{ $t('general.Cancel') }}
                                </b-button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ getCompanyKey('employee') }}</label>
                                        <multiselect
                                            v-model="search.employee_ids"
                                            :options="employeeDepartments.map((type) => type.id)"
                                            :custom-label=" (opt) => $i18n.locale == 'ar' ? employeeDepartments.find((x) => x.id == opt).name : employeeDepartments.find((x) => x.id == opt).name_e "
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("boardRent_task_department") }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            v-model="search.department_ids"
                                            :options="departments.map((type) => type.id)"
                                            :custom-label="(opt) => $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group position-relative">
                                        <label
                                            class="control-label">{{ $t("general.customer") }}
                                        </label>
                                        <multiselect
                                            :multiple="true"
                                            :internalSearch="false"
                                            @search-change="searchCustomer"
                                            v-model="search.customer_ids"
                                            :options="customers.map((type) => type.id)"
                                            :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ getCompanyKey('task_status') }}<span class="text-danger">*</span></label>
                                        <multiselect
                                            :multiple="true"
                                            v-model="search.status_ids"
                                            :options="statuses.map((type) => type.id)"
                                            :custom-label="(opt) => $i18n.locale == 'ar'? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </b-modal>
                </div>

                <div class="col-12">
                    <div class="app-calendar">
                                <FullCalendar
                                    ref="fullCalendar"
                                    :options="calendarOptions"
                                ></FullCalendar>
                            </div>
                </div>

                <b-modal
                    v-model="showModal"
                    :title="getCompanyKey('boardRent_task_create_form')"
                    title-class="font-18"
                    dialog-class="modal-full-width"
                    body-class=""
                    :hide-footer="true"
                    @hidden="resetModalHidden"
                >
                    <form>
                        <div :class="['d-flex justify-content-end position-absolute',$i18n.locale == 'en' ? 'button-left' : 'button-right']">
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
                        <b-tabs nav-class="nav-tabs nav-bordered">
                            <b-tab :title="$t('general.DataEntry')" active>
                        <div class="row">
                            <div v-if="isVisible('type')" class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey('boardRent_task_type') }}
                                        <span v-if="isRequired('type')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showTypeModal('create')"
                                        v-model="create.type"
                                        :options="types.map((type) => type)"
                                        :class="{'is-invalid': $v.create.type.$error || errors.type,'is-valid': !$v.create.type.$invalid && !errors.type,}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.create.type.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.type">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.type"
                                                      :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('employee_id')" class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey('employee') }}
                                        <span v-if="isRequired('employee_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showEmployeeModal"
                                        v-model="create.employee_id"
                                        :options="employeeDepartments.map((type) => type.id)"
                                        :custom-label=" (opt) => employeeDepartments.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? employeeDepartments.find((x) => x.id == opt).name : employeeDepartments.find((x) => x.id == opt).name_e
                                            : null
                                        "
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
                            <div v-if="isVisible('department_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label class="control-label">
                                        {{ getCompanyKey("boardRent_task_department") }}
                                        <span v-if="isRequired('department_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showDepartmentModal" v-model="create.department_id"
                                        :options="departments.map((type) => type.id)"
                                        :custom-label=" (opt) => departments.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e
                                            : null
                                        "
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
                            <div  v-if="create.type == 'customer' && isVisible('customer_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("customer") }}
                                        <span v-if="isRequired('customer_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showCustomerModal"
                                        :internalSearch="false"
                                        @search-change="searchCustomer"
                                        v-model="$v.create.customer_id.$model"
                                        :options="customers.map((type) => type.id)"
                                        :custom-label=" (opt) => customers.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name : customers.find((x) => x.id == opt).name_e
                                            : null
                                        "
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
                            <div v-if="create.type == 'customer' && isVisible('contact_person')" class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('general_customer_contact_person') }}
                                        <span v-if="isRequired('contact_person')" class="text-danger">*</span>
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
                            <div v-if="create.type == 'customer' && isVisible('contact_phone')" class="col-md-4">
                                <div class="form-group">
                                    <label  class="control-label">
                                        {{ getCompanyKey('general_customer_contact_phones') }}
                                        <span v-if="isRequired('contact_phone')" class="text-danger">*</span>
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
                            <div v-if="create.type == 'equipment' && isVisible('location_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("boardRent_task_location") }}
                                        <span v-if="isRequired('location_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showLocationModal"
                                        v-model="$v.create.location_id.$model"
                                        :options="locations.map((type) => type.id)"
                                        :custom-label=" (opt) => locations.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? locations.find((x) => x.id == opt).name : locations.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.location_id.$error || errors.location_id,'is-valid': !$v.create.location_id.$invalid && !errors.location_id,}"
                                    >
                                    </multiselect>

                                    <template v-if="errors.location_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.location_id"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                 <!--           <div v-if="create.type == 'equipment' && isVisible('equipment_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("equipment") }}
                                        <span  v-if="isRequired('equipment_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showEquipmentModal"
                                        v-model="equipment_id"
                                        :options="equipments.map((type) => type.id)"
                                        :custom-label=" (opt) => equipments.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? equipments.find((x) => x.id == opt).name : equipments.find((x) => x.id == opt).name_e
                                            : null
                                        "                                      >
                                    </multiselect>
                                </div>
                            </div>-->
                            <div v-if="create.type == 'equipment' && isVisible('equipment_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("boardRent_task_equipment") }}
                                        <span v-if="isRequired('equipment_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="$v.create.equipment_id.$model"
                                        :options="equipments.map((type) => type.id)"
                                        :custom-label=" (opt) => equipments.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? equipments.find((x) => x.id == opt).name : equipments.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.equipment_id.$error || errors.equipment_id,'is-valid': !$v.create.equipment_id.$invalid && !errors.equipment_id,}"
                                    >
                                    </multiselect>

                                    <template v-if="errors.equipment_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.equipment_id"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div v-if="create.type == 'general' && isVisible('task_requirement')" class="col-md-6">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("boardRent_task_task_requirement") }}
                                        <span v-if="isRequired('task_requirement')"  class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="$v.create.task_requirement.$model"
                                        :class="{
                                            'is-invalid':$v.create.task_requirement.$error || errors.task_requirement,
                                            'is-valid':!$v.create.task_requirement.$invalid && !errors.task_requirement
                                        }"
                                    />
                                    <template v-if="errors.task_requirement">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.task_requirement"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                            </div>
                            <div v-if="isVisible('task_title')" class="col-md-4">
                                <div class="form-group">
                                    <label  class="control-label">
                                        {{ getCompanyKey('task_title') }}
                                        <span v-if="isRequired('task_title')" class="text-danger">*</span>
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
                            <div v-if="isVisible('department_task_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label class="control-label">
                                        {{ getCompanyKey("task_type") }}
                                        <span v-if="isRequired('department_task_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showDepartmentTaskModal" v-model="create.department_task_id"
                                        :options="departmentTasks.map((type) => type.id)"
                                        :custom-label=" (opt) => departmentTasks.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? departmentTasks.find((x) => x.id == opt).name : departmentTasks.find((x) => x.id == opt).name_e
                                            : null
                                        "
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
                            <div v-if="isVisible('status_id')" class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey('task_status') }}
                                        <span v-if="isRequired('status_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="create.status_id"
                                        :options="statuses.map((type) => type.id)"
                                        :custom-label=" (opt) => statuses.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                            : null
                                        "
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
                            <div  class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey('task_owners') }}
                                        <span  class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        :disabled="!(create.type == 'general')"
                                        :multiple="true"
                                        @input="checkIncloudIdOwners"
                                        v-model="create.owners"
                                        :options="employees.map((type) => type.id)"
                                        :custom-label=" (opt) => employees.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e
                                            : null
                                        "
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
                            <div  class="col-md-4">
                                <div class="form-group">
                                    <label>{{ getCompanyKey('task_supervisors') }}<span class="text-danger">*</span></label>
                                    <multiselect
                                        :multiple="true"
                                        @input="checkIncloudIdSupervisors"
                                        v-model="create.supervisors"
                                        :options="employees.map((type) => type.id)"
                                        :custom-label=" (opt) => employees.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e
                                            : null
                                        "
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
                            <div  class="col-md-4">
                                <div class="form-group">
                                    <label>{{ getCompanyKey('task_notifications') }}<span class="text-danger">*</span></label>
                                    <multiselect
                                        :multiple="true"
                                        @input="checkIncloudIdNotifications"
                                        v-model="create.notifications"
                                        :options="employees.map((type) => type.id)"
                                        :custom-label=" (opt) => employees.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e
                                            : null
                                        "
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
                            <div v-if="isVisible('execution_date')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("execution_date") }}
                                        <span v-if="isRequired('execution_date')" class="text-danger">*</span>
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
                            <div v-if="isVisible('start_time')" class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("task_start_time") }}
                                        <span v-if="isRequired('start_time')" class="text-danger">*</span>
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
                            <div v-if="isVisible('execution_end_date')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("execution_end_date") }}
                                        <span v-if="isRequired('execution_end_date')" class="text-danger">*</span>
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
                            <div v-if="isVisible('end_time')" class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("task_end_time") }}
                                        <span v-if="isRequired('end_time')" class="text-danger">*</span>
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
                            <div v-if="isVisible('execution_duration')" class="col-md-2">
                                <div class="form-group">
                                    <label  class="control-label">
                                        {{ getCompanyKey('execution_duration') }}
                                        <span v-if="isRequired('execution_duration')" class="text-danger">*</span>
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

                            <div v-if="isVisible('actual_execution_date')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("actual_execution_date") }}
                                        <span v-if="isRequired('actual_execution_date')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDurationCreate(true)"
                                        type="date"
                                        v-model="$v.create.actual_execution_date.$model"
                                        format="YYYY-MM-DD"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                            'is-invalid': $v.create.actual_execution_date.$error || errors.actual_execution_date,
                                            'is-valid': !$v.create.actual_execution_date.$invalid && !errors.actual_execution_date,
                                        }">
                                    </date-picker>
                                    <template v-if="errors.actual_execution_date">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.actual_execution_date" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('actual_start_time')" class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("actual_task_start_time") }}
                                        <span v-if="isRequired('actual_start_time')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDurationCreate(true)"
                                        type="time"
                                        v-model="$v.create.actual_start_time.$model"
                                        format="HH:mm:ss"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                                                'is-invalid': $v.create.actual_start_time.$error || errors.actual_start_time,
                                                                'is-valid': !$v.create.actual_start_time.$invalid && !errors.actual_start_time,
                                                            }">
                                    </date-picker>
                                    <template v-if="errors.actual_start_time">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.actual_start_time" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('actual_execution_end_date')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("actual_execution_end_date") }}
                                        <span v-if="isRequired('actual_execution_end_date')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDurationCreate(true)"
                                        type="date"
                                        v-model="$v.create.actual_execution_end_date.$model"
                                        format="YYYY-MM-DD"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                                                'is-invalid': $v.create.actual_execution_end_date.$error || errors.actual_execution_end_date,
                                                                'is-valid': !$v.create.actual_execution_end_date.$invalid && !errors.actual_execution_end_date,
                                                            }">
                                    </date-picker>
                                    <template v-if="errors.actual_execution_end_date">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.actual_execution_end_date" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('actual_end_time')" class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("actual_task_end_time") }}
                                        <span v-if="isRequired('actual_end_time')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDurationCreate(true)"
                                        type="time"
                                        v-model="$v.create.actual_end_time.$model"
                                        format="HH:mm:ss"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                                                'is-invalid': $v.create.actual_end_time.$error || errors.actual_end_time,
                                                                'is-valid': !$v.create.actual_end_time.$invalid && !errors.actual_end_time,
                                                            }">
                                    </date-picker>
                                    <template v-if="errors.actual_end_time">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.actual_end_time" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('actual_execution_duration')" class="col-md-2">
                                <div class="form-group">
                                    <label  class="control-label">
                                        {{ getCompanyKey('actual_execution_duration') }}
                                        <span v-if="isRequired('actual_execution_duration')" class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        :disabled="true"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.create.actual_execution_duration.$model"
                                        :class="{
                                                                'is-invalid':$v.create.actual_execution_duration.$error || errors.actual_execution_duration,
                                                                'is-valid':!$v.create.actual_execution_duration.$invalid && !errors.actual_execution_duration
                                                            }"
                                    />
                                    <template v-if="errors.actual_execution_duration">
                                        <ErrorMessage v-for="(errorMessage,index) in errors.actual_execution_duration" :key="index">{{ errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>

                            <div v-if="isVisible('notification_date')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("notification_date") }}
                                        <span v-if="isRequired('notification_date')" class="text-danger">*</span>
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
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("task_priority") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="$v.create.priority_id.$model"
                                        :options="priorities.map((type) => type.id)"
                                        :custom-label="(opt) => $i18n.locale == 'ar' ? priorities.find((x) => x.id == opt).name : priorities.find((x) => x.id == opt).name_e"
                                        :class="{'is-invalid': $v.create.priority_id.$error || errors.priority_id,'is-valid': !$v.create.priority_id.$invalid && !errors.priority_id,}"
                                    >
                                    </multiselect>
                                    <template v-if="errors.priority_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.priority_id" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('is_closed')" class="col-md-2" >
                                <div class="form-group">
                                    <label class="my-1 mr-2">
                                        {{ getCompanyKey("boardRent_task_is_closed") }}
                                        <span v-if="isRequired('is_closed')" class="text-danger">*</span>
                                    </label>
                                    <b-form-group>
                                        <b-form-radio
                                            class="d-inline-block"
                                            v-model="$v.create.is_closed.$model"
                                            name="some-radios-create"
                                            value="1"
                                        >{{ $t("general.Yes") }}</b-form-radio
                                        >
                                        <b-form-radio
                                            class="d-inline-block m-1"
                                            v-model="$v.create.is_closed.$model"
                                            name="some-radios-create"
                                            value="0"
                                        >{{ $t("general.No") }}</b-form-radio
                                        >
                                    </b-form-group>
                                    <template v-if="errors.is_closed">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.is_closed"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('note')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("boardRent_panel_note") }}
                                        <span v-if="isRequired('note')" class="text-danger">*</span>
                                    </label>
                                    <textarea v-model="$v.create.note.$model" class="form-control" :maxlength="1000" rows="4"></textarea>
                                    <template v-if="errors.note">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.note"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('admin_note')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("boardRent_panel_admin_note") }}
                                        <span v-if="isRequired('admin_note')" class="text-danger">*</span>
                                    </label>
                                    <textarea  v-model="$v.create.admin_note.$model" class="form-control" :class="{'is-invalid': $v.create.admin_note.$error || errors.admin_note}" :maxlength="1000" rows="4"></textarea>
                                    <template v-if="errors.admin_note">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.admin_note"
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
                                                                     @error="src = img" />
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
                                    <img :src="showPhoto" class="img-thumbnail" @error="src = img" />
                                </div>
                            </div>
                        </div>
                    </b-tab>
                            </b-tabs>
                    </form>
                </b-modal>

            </div>
        </div>
    </Layout>
</template>

<style>
.calender-card-body{
    padding: 0 5px;
}
.calender-card-body .search{
    top: 10px;
    left: 150px;
    z-index: 99;
}

.calender-card-body .search-ar{
    top: 10px;
    left: unset;
    right: 77.6%;
    z-index: 99;
}

.tabs .tab-content {
    padding: 10px 60px 30px;
    min-height: 300px;
    background-color: #f5f5f5;
    position: relative;
}

.nav-bordered {
    border: unset !important;
}

.nav {
    background-color: #dff0fe;
}

.nav-tabs .nav-link {
    border: 1px solid #b7b7b7 !important;
    background-color: #d7e5f2;
    border-bottom: 0 !important;
    margin-bottom: 1px;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
    color: #000;
    background-color: hsl(0deg 0% 96%);
    border-bottom: 0 !important;
}

.button-left {
    right: 20px;
    left: unset;
}
.button-right {
    right: unset;
    left: 20px;
}
</style>
