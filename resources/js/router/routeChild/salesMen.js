import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/salesmen',
        name: 'salesmen',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/salesmen/index'),
    },
    {
        path: '/dashboard/salesman-plan',
        name: 'salesman-plan',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/salesman-plan/index'),
    },
    {
        path: '/dashboard/salesman-plan-details',
        name: 'salesman-plan',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/salesman-plan-details/index'),
    },
];
