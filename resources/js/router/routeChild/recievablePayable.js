import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/installment-status',
        name: 'installmentStatus',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/installmentStatus'),
    },
    {
        path: '/dashboard/installment-condition',
        name: 'installmentCondition',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/instalmentcondation'),
    },
    {
        path: '/dashboard/installment-payment-type',
        name: 'installmentPaymentType',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/InstallmentPaymentType'),
    },
    {
        path: '/dashboard/sub-contact-group',
        name: 'subContactGroup',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/sub-contact-group'),
    },
    {
        path: '/dashboard/MainContactGroups',
        name: 'MainContactGroups',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/MainContactGroups'),
    },
    {
        path: '/dashboard/InstallmentPaymentPlanDetail',
        name: 'InstallmentPaymentPlanDetail',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/InstallmentPaymentPlanDetail'),
    },
    {
        path: '/dashboard/installment-payment-plan',
        name: 'installment-payment-plan',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/InstallmentPaymentPlan'),
    },
    {
        path: '/dashboard/payment-plan-installments',
        name: 'payment plan installment',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/payment_plan_installments'),
    },
    {
        path: '/dashboard/installment-document-plan',
        name: 'installment document plan',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/documentPlans'),
    },
    {
        path: '/dashboard/screen-subcontact-group',
        name: 'screen subcontact group',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/screenSubcontactgroup'),
    },

    //start transaction

    {
        path: '/dashboard/receivablePayable/openingBalance',
        name: 'installment opening balance',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/transaction/openingBalance'),
    },
    {
        path: '/dashboard/receivablePayable/reservation',
        name: 'receivable-payable-reservation',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/transaction/reservation'),
    },
    {
        path: '/dashboard/receivablePayable/contract',
        name: 'receivable-payable-contract',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/transaction/contract'),
    },
    {
        path: '/dashboard/receivablePayable/voucher',
        name: 'receipt voucher',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/transaction/voucher'),
    },
    {
        path: '/dashboard/receivablePayable/creditNote',
        name: 'credit note',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/transaction/creditNote'),
    },
    {
        path: '/dashboard/receivablePayable/debitNote',
        name: 'debit note',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/recievablePayable/transaction/debitNote'),
    },
];
