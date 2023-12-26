<script>
import adminApi from "../../api/adminAxios";
import router from "../../router";
import Notification from "./notification";
import allRoutes from "../../helper/allRoute.js";
import routeModules from "../../helper/Rule.js";
import { selectedParents } from "../../helper/global.js";
import Swal from "sweetalert2";
import axios from 'axios';
import loader from "./loader.vue";
/**
 * Topbar component
 */
export default {
    data() {
        return {
            isLoader: false,
            languages: [
                {
                    flag: "/assets/images/us.jpg",
                    language: "en",
                    title: "English",
                },
                {
                    flag: "/assets/images/arabic.png",
                    language: "ar",
                    title: "Arabic",
                },
            ],
            selectedParents: selectedParents,
            current_language: this.$i18n.locale,
            text: null,
            flag: null,
            value: null,
            currentParentId: null,
            relates: {
                archiving: [
                    "arch-departments",
                    "archiving screen",
                    "arch-doc-status",
                    "document-fields",
                    "gen-arch-doc-types",
                ],
            },
            module: {},
            allRouteModule: [],
        };
    },
    components: {
        loader,
        Notification,
    },
    mounted() {
        this.value = this.languages.find((x) => x.language === this.$i18n.locale);
        this.text = this.value.title;
        this.flag = this.value.flag;
        this.allRouteModule = allRoutes.value;
    },
    methods: {
        async getMenu(prog_id, prog_module_id, dashboard_id) {
            this.isLoader = true
            selectedParents.value = [prog_id, prog_module_id,dashboard_id];
            localStorage.setItem("selectedParents", JSON.stringify(selectedParents.value));

            await axios
                .get(`${process.env.MIX_APP_URL_OUTSIDE}api/partners/get_children_inside_module_for_partner_sidebar_menu/${prog_module_id}`).then(async(res) => {
                    let folders_and_pages = []
                    await res.data.data.forEach((element) => {
                        folders_and_pages.push(element)
                    })
                    if(this.$store.state.auth.type == 'admin')
                    await this.getWorkflows(folders_and_pages)
                    let folders_and_pages_after_appended_show_property = await this.appendShowProperty(folders_and_pages)

                    localStorage.setItem('routeModules', JSON.stringify(folders_and_pages_after_appended_show_property))
                    routeModules.value = folders_and_pages_after_appended_show_property
                }).finally(() => {
                    this.isLoader = false
                })

        },
        appendShowProperty(folders_and_pages,parent = null,data = []) {

            folders_and_pages.forEach((element) => {

                if(element.type == 'page' && this.$store.state.auth.work_flow_trees.includes(element.page.middleware ?? element.page.name_e)){//
                    if(parent)
                        parent.isUserMenu = 1
                    element.isUserMenu = 1
                }

                if (element.childrens) {
                    this.appendShowProperty(element.childrens,element);
                }

                if(element && element.isUserMenu && parent){
                    parent.isUserMenu = 1
                }
            });

            return folders_and_pages;
        },

        async getWorkflows(folders_and_pages) {
            let data = [];
            data = this.get_all_pages_names(folders_and_pages)
            this.$store.commit("auth/editWorkFlowTrees", [
                "dictionary",
                "home",
                "Company",
                ...data[0],
            ]);
            this.$store.commit("auth/editCustomModules", data[1]);
        },
        get_all_pages_names(folders_and_pages, names = [], custom_modules = ['General']) {
            folders_and_pages.forEach((element) => {
                if (element.type == 'page') {
                    names.push(element.page.middleware ?? element.page.name_e);
                    if (element.page.module_screen && !custom_modules.includes(element.page.module_screen.name_e)) {
                        custom_modules.push(element.page.module_screen.name_e);
                    }
                }else
                    this.get_all_pages_names(element.childrens, names, custom_modules);
            });

            return [names, custom_modules];
        },
        /**
         * Toggle menu
         */
        toggleMenu() {
            this.$parent.toggleMenu();
        },
        /**
         * Full screen
         */
        initFullScreen() {
            document.body.classList.toggle("fullscreen-enable");
            if (
                !document.fullscreenElement &&
                /* alternative standard method */
                !document.mozFullScreenElement &&
                !document.webkitFullscreenElement
            ) {
                // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(
                        Element.ALLOW_KEYBOARD_INPUT
                    );
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        },
        /**
         * Toggle rightbar
         */
        toggleRightSidebar() {
            this.$parent.toggleRightSidebar();
        },
        /**
         * Set languages
         */
        setLanguage(locale, country, flag) {
            this.$i18n.locale = locale;
            this.current_language = locale;
            this.text = country;
            this.flag = flag;
            localStorage.setItem("lang", locale);
            if (locale == "ar") {
                document.body.style.textAlign = "right";
                document.body.classList.add("rtl");
                document.querySelector("html").style.direction = "rtl";
                document.querySelector("html").setAttribute("lang", locale || "ar");
                let style_dashboard = document.getElementById("style_dashboard");
                style_dashboard.setAttribute(
                    "href",
                    window.location.origin + `/css/custom.css`
                );
            } else {
                document.body.style.textAlign = "left";
                document.body.classList.remove("rtl");
                document.querySelector("html").style.direction = "ltr";
                document.querySelector("html").setAttribute("lang", locale || "ar");
                let style_dashboard = document.getElementById("style_dashboard");
                style_dashboard.setAttribute("href", "");
            }
        },
        /**
         * Horizontal-toggle menu
         */
        horizonalmenu() {
            let element = document.getElementById("topnav-menu-content");
            element.classList.toggle("show");
        },
        /**
         *  Logout Dashboard
         */
        async logout() {
            if (this.$store.state.auth.type != "admin") {
                await adminApi
                    .post(`/auth/logout`)
                    .then((res) => {
                        this.$store.commit("auth/logoutToken");
                        return this.$router.push({ name: "login" });
                    })
                    .catch((err) => {
                        this.$store.commit("auth/logoutToken");
                        return this.$router.push({ name: "login" });
                    })
                    .finally(() => { });
            } else {
                this.$store.commit("auth/logoutToken");
                return this.$router.push({ name: "login" });
            }
        },
    },
};
</script>

<template>
    <div>
        <loader size="large" v-if="isLoader" />
        <div class="navbar-custom">
            <div class="container-fluid">
                <ul class="list-unstyled topnav-menu float-right mb-0">
                    <li class="dropdown d-lg-inline-block">
                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" @click="initFullScreen"
                            data-toggle="fullscreen" href="#">
                            <i class="fe-maximize noti-icon"></i>
                        </a>
                    </li>

                    <b-nav-item-dropdown variant="white" class="d-lg-inline-block" right toggle-class="header-item">
                        <template v-slot:button-content>
                            <img class :src="flag" alt="Header Language" height="16" />
                            {{ text }}
                        </template>
                        <b-dropdown-item class="notify-item" v-for="(entry, i) in languages" :key="`Lang${i}`"
                            :value="entry" @click="setLanguage(entry.language, entry.title, entry.flag)"
                            :link-class="{ active: entry.language === current_language }">
                            <img :src="`${entry.flag}`" alt="user-image" class="mr-1" height="12" />
                            <span class="align-middle">{{ entry.title }}</span>
                        </b-dropdown-item>
                    </b-nav-item-dropdown>

                    <Notification />

                    <b-nav-item-dropdown right class="notification-list topbar-dropdown" menu-class="profile-dropdown"
                        toggle-class="p-0">
                        <template slot="button-content" class="nav-link dropdown-toggle">
                            <div class="nav-user mr-0">
                                <img src="../../assets/images/users/avatar-1.jpg" alt="user-image" class="rounded-circle" />
                                <span class="pro-user-name ml-1" v-if="$store.state.auth.type == 'admin'">
                                    {{
                                        $i18n.locale
                                        ? $store.getters["auth/partner"].name
                                        : $store.getters["auth/partner"].name_e
                                    }}
                                    <i class="mdi mdi-chevron-down"></i>
                                </span>
                                <span class="pro-user-name ml-1" v-else>
                                    {{
                                        $i18n.locale
                                        ? $store.state.auth.user.name
                                        : $store.state.auth.user.name_e
                                    }}
                                    <i class="mdi mdi-chevron-down"></i>
                                </span>
                            </div>
                        </template>

                        <b-dropdown-header>
                            <h6 class="text-overflow m-0 py-2">
                                {{ $t("navbar.dropdown.name.list.greet") }}
                            </h6>
                        </b-dropdown-header>

                        <b-dropdown-item href="#">
                            <i class="remixicon-account-circle-line"></i>
                            <span>{{ $t("navbar.dropdown.name.list.account") }}</span>
                        </b-dropdown-item>

                        <b-dropdown-item href="#">
                            <i class="remixicon-settings-3-line"></i>
                            <span>{{ $t("navbar.dropdown.name.list.settings") }}</span>
                        </b-dropdown-item>

                        <b-dropdown-item href="#">
                            <i class="remixicon-lock-line"></i>
                            <span>{{ $t("navbar.dropdown.name.list.lockscreen") }}</span>
                        </b-dropdown-item>

                        <b-dropdown-divider></b-dropdown-divider>
                        <a class="dropdown-item" href="javascript:void(0)" @click.prevent="logout">
                            <i class="fe-log-out mr-1"></i>
                            <span>{{ $t("navbar.dropdown.name.list.logout") }}</span>
                        </a>
                    </b-nav-item-dropdown>
                </ul>

                <!-- LOGO -->
                <div class="logo-box">
                    <router-link to="/" class="logo logo-dark text-center">
                        <span class="logo-sm">
                            <img src="../../assets/images/logo-sm-dark.png" alt height="24" />
                            <!-- <span class="logo-lg-text-light">Minton</span> -->
                        </span>
                        <span class="logo-lg">
                            <img src="../../assets/images/logo-dark.png" alt height="20" />
                            <!-- <span class="logo-lg-text-light">M</span> -->
                        </span>
                    </router-link>

                    <router-link to="/" class="logo logo-light text-center">
                        <span class="logo-sm">
                            <img src="../../assets/images/logo-sm.png" alt height="24" />
                        </span>
                        <span class="logo-lg">
                            <img src="../../assets/images/logo-light.png" alt height="20" />
                        </span>
                    </router-link>
                </div>

                <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                    <li>
                        <button class="button-menu-mobile waves-effect waves-light" @click="toggleMenu">
                            <i class="fe-menu"></i>
                        </button>
                    </li>

                    <li>
                        <!-- Mobile menu toggle (Horizontal Layout)-->
                        <a class="navbar-toggle nav-link" data-toggle="collapse" @click="horizonalmenu()"
                            data-target="#topnav-menu-content">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                        <!-- End mobile menu toggle-->
                    </li>

                    <template>
                        <li class="nav-route-related d-none d-md-block" v-if="$store.state.auth.parentModule.length > 0">
                            <div>
                                <ul class="list-unstyled">
                                    <li :key="index" :class="['d-inline']" style="margin: 0 0.5rem"
                                        v-for="(prog, index) in $store.state.auth.parentModule">


                                        <b-dropdown
                                            :variant="selectedParents.value.length && selectedParents.value[0] == prog.id ? 'secondary' : 'primary'"
                                            :html="(prog.icon ? `<i class='${prog.icon}' style='background:none!important;color:#000'></i> ` : '')+($i18n.locale == 'ar' ? prog.name : prog.name_e)" ref="dropdown">
                                            <template v-for="(prog_module, index) in prog.modules" :index="prog_module.id">

                                                <b-dropdown-item v-if="prog_module.isUserTopBar || $store.state.auth.type == 'admin'"
                                                    :class="selectedParents.value.length && selectedParents.value[1] == prog_module.project_program_module.id ? 'selected-program' : ''"
                                                    @click.prevent="getMenu(prog.id, prog_module.project_program_module.id,prog_module.project_program_module.module_dashboard_id)">
                                                     <i :class="prog_module.project_program_module.icon" v-show="prog_module.project_program_module.icon" style="background:none!important;color:#000"></i> {{ $i18n.locale == "ar" ? prog_module.project_program_module.name :
                                                        prog_module.project_program_module.name_e }}
                                                </b-dropdown-item>
                                            </template>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </template>
                </ul>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- Topbar Start -->

    <!-- end Topbar -->
</template>

<style>
.selected-program .dropdown-item {
    background: #f1f5f7 !important;
}
</style>

