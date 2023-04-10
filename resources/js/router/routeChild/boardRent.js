import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/boardRent/sell-method',
        name: 'boardRent ',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/sell-method'),
    },
    {
        path: '/dashboard/boardRent/panel',
        name: 'boardRent panel',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/panel'),
    },
    {
        path: '/dashboard/boardRent/package',
        name: 'boardRent package',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/package'),
    },
    {
        path: '/dashboard/boardRent/order',
        name: 'boardRent order',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/order'),
    },
    {
        path: '/dashboard/boardRent/quotation',
        name: 'boardRent quotation',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/quotation'),
    },
    {
        path: '/dashboard/boardRent/invoice',
        name: 'boardRent invoice',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/invoice'),
    },
    {
        path: '/dashboard/boardRent/tasks',
        name: 'boardRent tasks',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/tasks'),
    },
    {
        path: '/dashboard/boardRent/sector',
        name: 'boardRent tasks',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/sector'),
    },
    {
        path: '/dashboard/boardRent/unit-status',
        name: 'boardRent unitStatus',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/unitStatus'),
    },
    {
        path: '/dashboard/boardRent/look-up',
        name: 'boardRent look up',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/lookUps'),
    },
];
