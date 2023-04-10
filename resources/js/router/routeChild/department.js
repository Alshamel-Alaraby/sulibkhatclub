import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/department',
        name: 'department',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/department/index'),
    }
];
