import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/hr-payroll-head',
        name: 'hr',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/hr/hr_payroll_head'),
    },
    {
        path: '/dashboard/hr-job-title',
        name: 'hr',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/hr/hr_job_title'),
    },
    {
        path: '/dashboard/hr-request-type',
        name: 'hr',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/hr/hr_request_type'),
    },
    {
        path: '/dashboard/hr-online-request',
        name: 'hr',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/hr/hr_online_request'),
    },
];
