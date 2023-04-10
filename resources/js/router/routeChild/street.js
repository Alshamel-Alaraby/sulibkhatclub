import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/street',
        name: 'street',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/street/index'),
    },
];
