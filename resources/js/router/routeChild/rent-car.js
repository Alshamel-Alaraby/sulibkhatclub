import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/rent-car/body-types',
        name: 'rent car',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/rentCar/bodyTypes'),
    },
    {
        path: '/dashboard/rent-car/fuel-types',
        name: 'rent car',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/rentCar/fuelTypes'),
    },
    {
        path: '/dashboard/rent-car/seats-materials',
        name: 'rent car',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/rentCar/seatsMaterials'),
    },
    {
        path: '/dashboard/rent-car/specifications',
        name: 'rent car',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/rentCar/specifications'),
    },
    {
        path: '/dashboard/rent-car/sun-roofs',
        name: 'rent car',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/rentCar/sunroofs'),
    },
    {
        path: '/dashboard/rent-car/transmissions',
        name: 'rent car',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/rentCar/transmissions'),
    },
    {
        path: '/dashboard/rent-car/car',
        name: 'rent car',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/rentCar/car'),
    },
    {
        path: '/dashboard/rent-car/brand-model',
        name: 'rent car',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/rentCar/model-brand/index'),
    },
];
