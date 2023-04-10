import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/category',
        name: 'category',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/category/index'),
    },
];
