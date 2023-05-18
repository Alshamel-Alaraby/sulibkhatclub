import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/document/index',
        name: 'document',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/document/index'),
    },
    {
        path: '/dashboard/document/document-status',
        name: 'document status',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/document/document-status'),
    },
    {
        path: '/dashboard/document/document-reason',
        name: 'document reason',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/document/document-reason'),
    },
];
