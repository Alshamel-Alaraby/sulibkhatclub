<script>
import Layout from "../../../layouts/main.vue";
import PageHeader from "../../../../components/general/Page-header.vue";
import loader from "../../../../components/general/loader.vue";
import translation from "../../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../../helper/permission";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import Modal from "./modal.vue";
import action from "./action.vue";
import page_title from "../../../../helper/PageTitle"
import adminApi from '../../../../api/adminAxios';
import Swal from "sweetalert2";
import successError from "../../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Rooms Categories",
        meta: [{ name: "description", content: "Rooms Categories" }],
    },
    watch: {
        pageTitle: {
            handler(newV, old) {
                this.page_title = page_title.value;
            },
        },
    },
    computed: {
        pageTitle: function () {
            return page_title.value;
        },
    },
    mixins: [translation, customTable, crudHelper, successError],
    components: {
        Layout, PageHeader, loader, searchPage, action,
        actionSetting, tableCustom, Modal
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "RoomsCategories", "all RoomsCategory");
        });
    },
    data() {
        return {
            url: '/h_m_s/rooms_categories',
            page_title: {},
            debounce: {},
            selected_parent: {},
            parent_rooms_categories: [],
            old_parent_rooms_categories: [],
            filtered_parent: [],
            search: '',
            searchField: ['name', 'name_e', 'children.name', 'children.name_e'],
        };
    },
    watch: {
        search(after, befour) {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(async () => {
                this.old_parent_rooms_categories = this.parent_rooms_categories
                await this.get_parent_rooms_categories();

            }, 900);
        },
    },
    async mounted() {
        await this.get_parent_rooms_categories();
        this.page_title = page_title.value
        this.getCustomTableFields('h_m_s_rooms_categories')

    },
    methods: {
        delete_category(id) {
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
                        .delete(`h_m_s/rooms_categories/${id}`)
                        .then((res) => {
                            this.get_parent_rooms_categories();
                            this.successFun('Yourrowhasbeendeleted', 'Deleted');
                        })

                        .catch((err) => {
                            console.log(err)
                            if (err.response.status == 400) {
                                this.errorFun('Error', 'CantDeleteRelation');
                            } else {
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }
            });
        },
        addInsideParent(name, parent_id) {
            this.$set(this.selected_parent, 'name', name)
            this.$set(this.selected_parent, 'parent_id', parent_id)
            this.$bvModal.show(`create`);
            this.type = '';
            this.idEdit = ''
        },
        collapse_parent_rooms_category(index) {
            this.$set(this.parent_rooms_categories[index], 'collapsed', !this.parent_rooms_categories[index]['collapsed'] ? 1 : 0)
        },
        get_parent_rooms_categories() {
            adminApi.get(`h_m_s/rooms_categories?search=${this.search}&${this.filterSearch(this.searchField)}company_id=${this.company_id}&type=parent`).then((res) => {
                this.parent_rooms_categories = res.data.data
                this.put_parents_and_children_in_one_array_for_fitler_edit_item(res.data.data)
            }).catch((e) => { console.log(e) })
                .finally(() => {
                    if (this.old_parent_rooms_categories.length > 0) {
                        this.parent_rooms_categories = this.truncate_rooms_categories_after_search(this.parent_rooms_categories, this.old_parent_rooms_categories)
                    } else {
                        this.old_parent_rooms_categories = this.parent_rooms_categories
                    }

                });
        },
        put_parents_and_children_in_one_array_for_fitler_edit_item(array_of_parents) {
            array_of_parents.forEach((ele) => {
                this.filtered_parent.push({
                    id: ele.id,
                    name: ele.name,
                    name_e: ele.name_e,
                    parent_id: ele.parent_id,
                    is_default: ele.is_default,
                    does_it_have_schedule: ele.does_it_have_schedule,
                })
                if (ele.children.length > 0) {
                    ele.children.forEach((item) => {
                        this.filtered_parent.push({
                            id: item.id,
                            name: item.name,
                            name_e: item.name_e,
                            parent_id: item.parent_id,
                            is_default: item.is_default,
                            does_it_have_schedule: item.does_it_have_schedule,
                        })
                    })
                }
            })
        },
        truncate_rooms_categories_after_search(new_rooms_categories, old_rooms_categories) {// if old rooms collapsed update the new rooms to make rooms still collapsed
            let array = []
            new_rooms_categories.forEach((new_element) => {
                let check = 0
                old_rooms_categories.forEach((old_elment) => {
                    if (old_elment.id == new_element.id) {
                        old_elment.children = new_element.children
                        old_elment.name = new_element.name
                        old_elment.name_e = new_element.name_e
                        check = 1

                        array.push(old_elment)
                    }
                })
                if (check == 0)
                    array.push(new_element)
            })
            return array
        },
        filterSearch(fields) {
            let filter = '';
            for (let i = 0; i < fields.length; ++i) {
                filter += `columns[${i}]=${fields[i]}&`;
            }
            return filter;
        },

    },
};
</script>

<template>
    <Layout>
        <PageHeader />

        <div class="row">
            <div class="col-12">
                <div class="card" style="background-color: #dff0fe;">
                    <div class="card-body">

                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ page_title && page_title.url == $route.fullPath ? ($i18n.locale ==
                                'ar' ? page_title.title : page_title.title_e) : $t('general.Rooms Categories') }}</h4>

                        </div>


                        <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                            <div class="d-inline-block">
                                <b-button
                                    @click.prevent="$bvModal.show(`create`); selected_parent = {}; type = ''; idEdit = ''"
                                    v-if="isPermission('create RoomsCategory')"
                                    variant="primary" class="font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                            </div>

                            <div class="d-inline-block position-relative" style="width: 70%">
                                <span :class="[
                                    'search-custom position-absolute',
                                    $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                                ]">
                                    <i class="fe-search"></i>
                                </span>
                                <input class="form-control" style="display: block; width: 93%; padding-top: 3px" type="text"
                                    v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />

                            </div>
                        </div>
                        <!-- end setting -->


                        <!-- start .table-responsive-->
                        <div class="mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <div class="row">

                                <div class="col-7 pt-3" :class="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                                    <ul id="myUL">
                                        <li v-for="(node, index) in parent_rooms_categories" :key="node.id"
                                            style="margin: 0 25px;">
                                            <span>
                                                <i :class="node.collapsed
                                                    ? 'fas fa-minus' : 'fas fa-plus'
                                                    " @click="collapse_parent_rooms_category(index)"
                                                    :style="[node.is_active == 'active' ? '' : 'background-color:#a4a5a5!important']"></i>
                                                <span style="font-size: 25px;color:#000;margin:0 10px"
                                                    @dblclick.prevent="addInsideParent($i18n.locale == 'ar' ? node.name : node.name_e, node.id); node.is_active = 'active'"
                                                    :class="{
                                                        active: node.id == selected_parent.parent_id,
                                                    }">
                                                    {{ $i18n.locale == "ar" ? node.name : node.name_e }}


                                                </span>




                                                <action :isEdit="true" :isDelete="true"
                                                    :permissionUpdate="isPermission('update RoomsCategory')"
                                                    :permissionDelete="isPermission('delete RoomsCategory')"
                                                    @deleteItem="id => delete_category(id)" @editRow="id => dbClickRow(id)"
                                                    :data_id="node.id" />

                                            </span>



                                            <ul class="nested list-unstyled"
                                                v-show="node.collapsed && Object.keys(node.children ?? []).length">
                                                <li v-for="(item, index) in node.children" :key="item.id"
                                                    style="margin: 0 25px;">
                                                    <span style="font-size:18px;font-weight:bold" class="my-2">
                                                        * {{ $i18n.locale == "ar" ? item.name : item.name_e }}

                                                        <action :isEdit="true" :isDelete="true"
                                                            :permissionUpdate="isPermission('update RoomsCategory')"
                                                            :permissionDelete="isPermission('delete RoomsCategory')"
                                                            @deleteItem="id => delete_category(id)"
                                                            @editRow="id => dbClickRow(id)" :data_id="item.id" />

                                                    </span>

                                                </li>
                                            </ul>


                                        </li>
                                    </ul>
                                </div>

                            </div>

                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>


        <!--  create   -->
        <Modal :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url"
            :selected_parent="selected_parent" :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
            :type="type" :idObjEdit="idEdit ? { idEdit, dataObj: this.filtered_parent.find(el => el.id == idEdit) } : null"
            @getDataTable="get_parent_rooms_categories()" :isPermission="isPermission" />


    </Layout>
</template>



<style scoped lang="scss">
#myUL {
    list-style-type: none;
}

#myUL {
    margin: 0;
    padding: 0;

    span {
        i {
            padding: 2px;
            background-color: #6dc6f5;
            color: #fff;
            border-radius: 50%;
            font-size: 18px;
            line-height: 17px;
            font-weight: 600;
        }

        span:hover,
        i:hover {
            cursor: pointer;
        }
    }
}

.nested {
    display: block;
}

.active {
    color: #0a15df;
    font-size: 24px;
    font-weight: bold;
}

.rtl {
    #myUL {
        .without-children {
            padding-right: 10px;
        }

        .nested {
            padding-right: 40px;
        }
    }
}

.ltr {
    #myUL {
        .without-children {
            padding-left: 10px;
            font-size: 19px;
            font-weight: bold;
        }
    }
}

.tooltip-inner {
    max-width: 750px !important;
    background-color: #eed900;
    color: black;
}
</style>
