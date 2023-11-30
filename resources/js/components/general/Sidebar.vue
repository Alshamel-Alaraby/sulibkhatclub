<script>
import { mapState } from "vuex";
import allRoutes from "../../helper/allRoute.js";
import { selectedParents,_companies } from "../../helper/global.js";
import routeModules from "../../helper/Rule.js";

import recursive_menu from "./recursive_menu.vue";

export default {
  inject: ["isRouteClicked", "menuLoadedCounter"],
  components:{
    recursive_menu
  },
  data() {
    return {
      menuItems: [],
      companies: [],
      workFlowTree: [],
      allWorkFlowTree: [],
    };
  },
  props: {
    type: {
      type: String,
      required: true,
    },
    width: {
      type: String,
      required: true,
    },
    size: {
      type: String,
      required: true,
    },
    menu: {
      type: String,
      required: true,
    },
    topbar: {
      type: String,
      required: true,
    },
  },
  computed: {
    ...mapState(["layout"]),
    routeMod: function () {
      return routeModules.value;
    },
  },
  watch: {
    type: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "dark":
              document.body.setAttribute("data-sidebar-color", "dark");
              break;
            case "light":
              document.body.setAttribute("data-sidebar-color", "light");
              break;
            case "brand":
              document.body.setAttribute("data-sidebar-color", "brand");
              break;
            case "gradient":
              document.body.setAttribute("data-sidebar-color", "gradient");
              break;
            default:
              document.body.setAttribute("data-sidebar-color", "light");
              break;
          }
        }
      },
    },
    width: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "boxed":
              document.body.setAttribute("data-layout-width", "boxed");
              document.body.setAttribute("data-sidebar-size", "condensed");
              break;
            case "fluid":
              document.body.setAttribute("data-layout-width", "fluid");
              document.body.removeAttribute("data-sidebar-size");
              document.body.removeAttribute("data-layout-width");
              break;
            default:
              document.body.setAttribute("data-layout-mode", "fluid");
              break;
          }
        }
      },
    },
    size: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "default":
              document.body.setAttribute("data-sidebar-size", "default");
              break;
            case "condensed":
              document.body.setAttribute("data-sidebar-size", "condensed");
              break;
            case "compact":
              document.body.setAttribute("data-sidebar-size", "compact");
              break;
            default:
              document.body.setAttribute("data-sidebar-size", "default");
              break;
          }
        }
      },
    },
    routeMod: {
      handler(newV, old) {
        this.menuItems = routeModules.value;
      },
    },
    menu: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "fixed":
              document.body.setAttribute("data-layout-menu-position", "fixed");
              break;
            case "scrollable":
              document.body.setAttribute(
                "data-layout-menu-position",
                "scrollable"
              );
              break;
            default:
              document.body.setAttribute("data-layout-menu-position", "fixed");
              break;
          }
        }
      },
    },
    topbar: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "dark":
              document.body.setAttribute("data-topbar-color", "dark");
              break;
            case "light":
              document.body.setAttribute("data-topbar-color", "light");
              break;
            default:
              document.body.setAttribute("data-topbar-color", "dark");
              break;
          }
        }
      },
    },
  },
  mounted() {
    this.companies = _companies.value
    this.workFlowTree = this.$store.state.auth.work_flow_trees;
    this.allWorkFlowTree = this.$store.state.auth.allWorkFlow;
    if (selectedParents.value.length) {
      let is_web = 0;
      this.$store.state.auth.parentModule.forEach((program,index) => {
            if(program.id == selectedParents.value[0]){
                program.modules.forEach((module_node) => {
                    if(module_node.project_program_module.id == selectedParents.value[1]){
                        is_web = module_node.is_web;
                    }
                })
            }
      });
      this.$store.commit("auth/editIsWeb", is_web);
      this.menuItems = routeModules.value;
    } else {
      this.menuItems = [];
    }

    localStorage.setItem("menuLoaded", true);
    // this._activateMenuDropdown();
    // this.$router.afterEach((routeTo, routeFrom) => {
    //   this._activateMenuDropdown();
    // });
  },
  methods: {
    _activateMenuDropdown() {
      const resetParent = (el) => {
        el.classList.remove("active");
        var parent = el.parentElement;
        if (parent) {
          parent.classList.remove("menuitem-active");
          const parent2 = parent.parentElement;
          if (parent2) {
            const parent3 = parent2.parentElement;
            if (parent3) {
              parent3.classList.remove("show");
              const parent4 = parent3.parentElement;
              if (parent4) {
                parent4.classList.remove("menuitem-active");
              }
            }
          }
        }
      };
      var links = document.getElementsByClassName("side-nav-link-ref");
      var matchingMenuItem = null;
      const paths = [];
      for (let i = 0; i < links.length; i++) {
        // reset menu
        resetParent(links[i]);
      }
      for (var i = 0; i < links.length; i++) {
        paths.push(links[i]["pathname"]);
      }
      var itemIndex = paths.indexOf(window.location.pathname);
      if (itemIndex === -1) {
        const strIndex = window.location.pathname.lastIndexOf("/");
        const item = window.location.pathname.substr(0, strIndex).toString();
        matchingMenuItem = links[paths.indexOf(item)];
      } else {
        matchingMenuItem = links[itemIndex];
      }

      if (matchingMenuItem) {
        matchingMenuItem.classList.add("active");
        var parent = matchingMenuItem.parentElement;

        /**
         * TODO: This is hard coded way of expading/activating parent menu dropdown and working till level 3.
         * We should come up with non hard coded approach
         */
        if (parent) {
          parent.classList.add("menuitem-active");
          const parent2 = parent.parentElement;
          if (parent2) {
            const parent3 = parent2.parentElement;
            if (parent3) {
              parent3.classList.add("show");
              const parent4 = parent3.parentElement;
              if (parent4) {
                parent4.classList.add("menuitem-active");
              }
            }
          }
        }
      }
    },

  },
};
</script>

<template>
  <!-- ========== Left Sidebar Start ========== -->
  <div class="left-side-menu">
    <!-- LOGO -->
    <div class="logo-box">
      <router-link to="/" class="logo logo-dark text-center">
        <span class="logo-sm">
          <img src="/images/shamel-logo-006.png" alt height="24" />
          <!-- <span class="logo-lg-text-light">Minton</span> -->
        </span>
        <span class="logo-lg">
          <img src="/images/shamel-logo-006.png" alt height="20" />
          <!-- <span class="logo-lg-text-light">M</span> -->
        </span>
      </router-link>

      <router-link to="/" class="logo logo-light text-center">
        <span class="logo-sm">
          <img src="/images/shamel-logo-006.png" alt height="24" />
        </span>
        <span class="logo-lg">
          <img src="/images/shamel-logo-006.png" alt height="20" />
        </span>
      </router-link>
    </div>

    <simplebar class="h-100" data-simplebar>
      <!-- User box -->
      <div class="user-box text-center">
        <img
          src="../../assets/images/users/avatar-1.jpg"
          alt="user-img"
          title="Mat Helme"
          class="rounded-circle avatar-md"
        />
        <div class="dropdown">
          <a
            href="javascript: void(0);"
            class="text-reset dropdown-toggle h5 mt-2 mb-1 d-block"
            data-toggle="dropdown"
            >Nik Patel</a
          >
          <div class="dropdown-menu user-pro-dropdown">
            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <i class="fe-user mr-1"></i>
              <span>My Account</span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <i class="fe-settings mr-1"></i>
              <span>Settings</span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <i class="fe-lock mr-1"></i>
              <span>Lock Screen</span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <i class="fe-log-out mr-1"></i>
              <span>Logout</span>
            </a>
          </div>
        </div>
        <p class="text-reset" >Admin Head</p>
      </div>

      <!--- Sidemenu -->

      <div id="sidebar-menu">
        <!-- Left Menu Start -->

            <recursive_menu :menuItems="menuItems" :companies="companies" />

        </div>
        <div class="clearfix"></div>


    </simplebar>
    <!-- Sidebar -left -->
  </div>
  <!-- Left Sidebar End -->
</template>
<style scoped>
.logo-lg img {
  width: 70px;
  height: 45px;
}
.logo-sm img {
  width: 70px;
  height: 45px;
}
</style>
