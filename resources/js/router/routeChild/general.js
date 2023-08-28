import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";
import companyId from "../../middleware/companyId";
import guest from "../../middleware/guest";

export default [
    {
        path: '/dashboard/avenue',
        name: 'avenue',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/avenue/index'),
    },
    {
        path: '/dashboard/country',
        name: 'country',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/country/index'),
    },
    {
        path: '/dashboard/company',
        name: 'company',
        meta: {
            middleware: [companyId,checkAuth]
        },
        component: () => import('../../views/pages/general/company/index'),
    },
    {
        path: '/dashboard/colors',
        name: 'colors',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/colors/index'),
    },
    {
        path: '/dashboard/city',
        name: 'city',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/city/index'),
    },
    {
        path: '/dashboard/category',
        name: 'Category',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/category/index'),
    },
    {
        path: '/dashboard/brand',
        name: 'Brand',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/brand/index'),
    },
    {
        path: '/dashboard/branch',
        name: 'branch',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/branch/index')
    },
    {
        path: '/dashboard/banks',
        name: 'banks',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/banks/index'),
    },
    {
        path: '/dashboard/bankAccount',
        name: 'bankAccount',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/bankAccounts/index'),
    },
    {
        path: '/dashboard/customer-resource',
        name: 'customer resource',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/customerResource/index'),
    },
    {
        path: '/dashboard/department',
        name: 'department',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/department/index'),
    },
    {
        path: '/dashboard/customer-group',
        name: 'Customer Group',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/customerGroups/index'),
    },
    {
        path: '/dashboard/currency',
        name: 'currency',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/currency/index'),
    },
    {
        path: '/dashboard/customer',
        name: 'general customer',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/customer/index'),
    },
    {
        path: '/dashboard/financialYear',
        name: 'financialYear',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/financialYear/index'),
    },
    {
        path: '/dashboard/general-customer-category',
        name: 'customer-category',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/customer_category/index'),
    },
    {
        path: '/dashboard/employee',
        name: 'employee',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/employee/index'),
    },
    {
        path: '/dashboard/equipment',
        name: 'equipment',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/equipment/index'),
    },
    {
        path: '/dashboard/externalSalesmen',
        name: 'externalSalesmen',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/externalSalesmen/index'),
    },
    {
        path: '/dashboard/document/index',
        name: 'document',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/document/index'),
    },
    {
        path: '/dashboard/document/document-status',
        name: 'document status',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/document/document-status'),
    },
    {
        path: '/dashboard/document/document-reason',
        name: 'document reason',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/document/document-reason'),
    },
    {
        path: '/dashboard/dictionary',
        name: 'dictionary',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/dictionary/index'),
    },
    {
        path: '/dashboard/periodic-maintenance',
        name: 'periodic maintenance',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/periodic-maintenance/index'),
    },
    {
        path: '/dashboard/paymentTypes',
        name: 'paymentTypes',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/paymentTypes/index'),
    },
    {
        path: '/dashboard/login',
        name: 'login',
        component: () => import('../../views/pages/general/auth/login'),
        meta: {
            middleware: [guest]
        },
    },
    {
        path: '/dashboard/location',
        name: 'location',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/location/index'),
    },
    {
        path: '/dashboard/internalSalesman',
        name: 'internalSalesman',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/internalSalesMen/index'),
    },
    {
        path: '/dashboard/group',
        name: 'Group',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/group/index'),
    },
    {
        path: '/dashboard/governorate',
        name: 'governorate',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/governorate/index'),
    },
    {
        path: '/dashboard/sector',
        name: 'sector',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/sector/index'),
    },
    {
        path: '/dashboard/screen-properties',
        name: 'screen-properties',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/screenproperties/index'),
    },
    {
        path: '/dashboard/salesmenTypes',
        name: 'salesmenTypes',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/salesmenTypes/index'),
    },
    {
        path: '/dashboard/salesmen',
        name: 'salesmen',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/salesmen/index'),
    },
    {
        path: '/dashboard/salesman-plan',
        name: 'salesman-plan',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/salesman-plan/index'),
    },
    {
        path: '/dashboard/salesman-plan-details',
        name: 'salesman-plan',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/salesman-plan-details/index'),
    },
    {
        path: '/dashboard/roles',
        name: 'roles',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/roles/index'),
    },
    {
        path: '/dashboard/tree-properties',
        name: 'tree-properties',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/property-tree/index'),
    },
    {
        path: '/dashboard/priority',
        name: 'priority',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/priority/index'),
    },
    {
        path: '/dashboard/ticket-manager/tasks',
        name: 'ticketManager tasks',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/ticketManager/tasks'),
    },
    {
        path: '/dashboard/ticket-manager/tasks-calender',
        name: 'ticketManager calender',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/ticketManager/calender'),
    },
    {
        path: '/dashboard/units',
        name: 'Units',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/units/index'),
    },
    {
        path: '/dashboard/users',
        name: 'users',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/users/index'),
    },
    {
        path: '/dashboard/Variant-Attributes',
        name: 'Variant Attributes',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/variantAttributes/index'),
    },
    {
        path: '/dashboard/statuses',
        name: 'statuses',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/statuses/index'),
    },
    {
        path: '/dashboard/taxes',
        name: 'Taxes',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/taxes/index'),
    },
    {
        path: '/dashboard/supplier',
        name: 'supplier',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/supplier/index'),
    },
    {
        path: '/dashboard/street',
        name: 'street',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/street/index'),
    },
    {
        path: '/dashboard/store',
        name: 'store',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/store/index'),
    },
    {
        path: '/dashboard/notifications',
        name: 'notifications',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/notification')
    },
    {
        path: '/dashboard/custom-table',
        name: 'custom table',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/custom-table/index')
    },
    {
        path: '/dashboard/database-backup',
        name: 'Database backup',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/DatabaseBackup/index')
    },
    {
        path: '/dashboard/',
        name: 'home',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/dashboard/sales/index')
    },
    //**********************************************
    {
        path: '/dashboard/dashboard/crm',
        name: 'crm-dashboard',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/dashboard/crm/index')
    },
    {
        path: '/dashboard/dashboard/analytics',
        name: 'analytics-dashboard',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/dashboard/analytics/index')
    },
    {
        path:'*',
        name:'page404',
        component: () => import('../../views/pages/general/error/404.vue')
    },
    {
        path: '/dashboard/serial',
        name: 'Serial',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/serial/index'),
    },
    {
        path: '/dashboard/paymentMethod',
        name: 'Payment Method',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/paymentMethod/index'),
    },

];
