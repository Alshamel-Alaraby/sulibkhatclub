import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/customer',
        name: 'general customer',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/customer/index'),
    },
];
