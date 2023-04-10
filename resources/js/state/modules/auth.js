import Cookies from "js-cookie";
import adminApi from '../../api/adminAxios';
import router from "../../router/index";

// state
export const state = {
    token: Cookies.get("token") || null,
    // permission: JSON.parse(localStorage.getItem("permission")) || [],
    partner: JSON.parse(localStorage.getItem("partner")) || {},
    companies: JSON.parse(localStorage.getItem("companies")) || [],
    company_id: JSON.parse(localStorage.getItem("company_id")) || null,
    work_flow_trees: JSON.parse(localStorage.getItem("work_flow_trees")) || [],
    allWorkFlow: JSON.parse(localStorage.getItem("allWorkFlow")) || [],
    user: JSON.parse(localStorage.getItem("user")) || {},
    type: JSON.parse(localStorage.getItem("type")) || "",
    notification: true
}

// getters
export const getters = {
    token: state => state.token,
    permission: state => state.permission,
    loading: state => state.loading,
    partner: state => state.partner,
    companies: state => state.companies,
    errors: state => state.errors,
    company_id: state => state.company_id,
    work_flow_trees: state => state.work_flow_trees,
    user: state => state.user,
    type: state => state.type,
}

// mutations
export const mutations = {
    editToken(state,token){
        state.token = token;
        Cookies.set('token',token,{ expires: 7 });
    },
    // editPermission(state,permission){
    //
    //     let name = [];
    //     permission.forEach(el => {
    //         name.push(el.name);
    //         if(el.role && !name.includes(el.role)){
    //             name.push(el.role);
    //         }
    //     });
    //
    //     state.permission = name;
    //     localStorage.setItem('permission',JSON.stringify(name));
    // },
    editPartner(state,partner){
        state.partner = partner;
        localStorage.setItem('partner',JSON.stringify(partner));
    },
    editCompanies(state,companies){
        state.companies = companies;
        localStorage.setItem('companies',JSON.stringify(companies));
    },
    editCompanyId(state,company_id){
        state.company_id = company_id;
        localStorage.setItem('company_id',JSON.stringify(company_id));
    },
    editWorkFlowTrees(state,work_flow_trees){
        state.work_flow_trees = work_flow_trees;
        localStorage.setItem('work_flow_trees',JSON.stringify(work_flow_trees));
    },
    allWorkFlow(state,allWorkFlow){
        state.allWorkFlow = allWorkFlow;
        localStorage.setItem('allWorkFlow',JSON.stringify(allWorkFlow));
    },
    logoutToken(state){
        // state.roles = null;
        state.token = null;
        state.partner = {};
        state.user = {};
        state.companies = [];
        state.work_flow_trees = [];
        state.allWorkFlow = [];
        state.company_id = null;
        state.type = '';
            // state.permission = null;
        // localStorage.removeItem('permission');
        localStorage.removeItem('companies');
        localStorage.removeItem('partner');
        localStorage.removeItem('company_id');
        localStorage.removeItem('work_flow_trees');
        localStorage.removeItem('allWorkFlow');
        localStorage.removeItem('user');
        localStorage.removeItem('type');
        Cookies.remove('token')
    },
    editErrors(state,errors){
        state.errors = errors;
    },
    editType(state,type){
        state.type = type;
        localStorage.setItem('type',JSON.stringify(type));
    },
    editUser(state,user){
        state.user = user;
        localStorage.setItem('user',JSON.stringify(user));
    },
    editNotification(state,notification){
        state.notification = notification;
        localStorage.setItem('notification',JSON.stringify(notification));
    },
};

// actions
export const actions = {};
