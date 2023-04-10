import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/document',
        name: 'document',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/document/index'),
    },
];
