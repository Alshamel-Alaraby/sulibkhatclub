import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/sub-contact-groups',
        name: 'sub-contact-groups',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/sub-contact-group/index'),
    },
];
