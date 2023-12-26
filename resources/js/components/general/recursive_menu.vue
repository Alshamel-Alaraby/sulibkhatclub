
<template>
    <ul class="list-unstyled" id="side-menu" style="">
        <li v-if="$store.state.auth.type == 'admin' && companies.length > 1">
            <router-link to="/dashboard/company" class="side-nav-link-ref">
                <i class="fas fa-city"></i>
                <span>{{ $t("menuitems.company.text") }}</span>
            </router-link>
        </li>


        <template v-for="(item, i) in menuItems">
            <li v-if="checkUserOrAdminPermission(item.isUserMenu ?? false)" :key="i + ' ' + item.id" >

                <template v-if="item.type == 'folder'">
                    <a href="javascript:void(0);" @click="clickOnMenuToCollapseChildrens(i)"
                        :class="['has-arrow', 'has-dropdown']">
                        <!-- <i :class="`${item.icon}`" v-if="item.icon"></i> -->
                        <span class="menu-arrow" style="position: static;" :style="item.collapsed ? ($i18n.locale == 'ar' ? 'transform: rotate(-90deg)!important' : 'transform: rotate(90deg)!important') : ''"></span>
                        <i class="fa fa-folder" style="color:#d5aa20;font-size:20px"></i>
                        <span>{{
                            $i18n.locale == "ar"
                            ? item.name
                            : item.name_e
                        }}</span>


                    </a>
                    <div class="collapse" :class="{ show: item.collapsed == 1 }" :id="`sidebarTasks${i}`">
                        <ul class="sub-menu nav-second-level" aria-expanded="false">
                            <recursive_menu :menuItems="item.childrens" :companies="[]" />
                        </ul>
                    </div>
                </template>
                <template v-else>
                    <router-link @click="isRouteClicked = true" :data-title="item.page.title" :data-title_e="item.page.title_e" :data-url="`/dashboard/${item.page.middleware_url}`"
                     :to="`/dashboard/${item.page.middleware_url == '/' ? '' : item.page.middleware_url}`"
                        v-if="checkPermission(item.page.middleware ?? item.page.name_e)" class="side-nav-link-ref page_item">

                        <i class="fa fa-file" style="color:#000"></i> {{
                            $i18n.locale == "ar"
                            ? (item.page.name ?? item.page.name_e)
                            : item.page.name_e
                        }}
                    </router-link>
                </template>

            </li>
        </template>

    </ul>
</template>


<script>
import page_title from "../../helper/PageTitle"
export default {
    inject: ["isRouteClicked", "menuLoadedCounter"],
    name: 'recursive_menu',
    props: ['menuItems','companies'],
    mounted(){
        $(document).on('click','.page_item',function(){
            let data = {"title":$(this).data('title'),"title_e":$(this).data('title_e'),"url":$(this).data('url')}
            localStorage.setItem('page_title',JSON.stringify(data))
            page_title.value = data
        })
    },
    methods: {
        checkUserOrAdminPermission(isUserMenu) {
            if (this.$store.state.auth.type != "admin") {
                return isUserMenu;
            }
            return true;
        },
        checkPermission(name) {
            return this.$store.state.auth.work_flow_trees.includes(name);
        },
        clickOnMenuToCollapseChildrens(index) {
            this.$set(this.menuItems[index], 'collapsed', !this.menuItems[index]['collapsed'] ? 1 : 0)

            if(this.menuItems[index]['collapsed'])
                this.menuItems.forEach((element,i) => {
                    if(index != i ){
                        this.$set(this.menuItems[i], 'collapsed', 0)
                    }
                });
        },
    }
}

</script>
