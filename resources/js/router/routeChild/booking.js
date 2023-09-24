import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/booking/rooms',
        name: 'Booking Rooms',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/rooms'),
    },
    {
        path: '/dashboard/booking/services',
        name: 'Booking Services',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/services')
    },
    {
        path: '/dashboard/booking/companies',
        name: 'Booking Company',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/company')
    },
    {
        path: '/dashboard/booking/guest',
        name: 'Booking Guest',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/guest'),

    },
    {
        path: '/dashboard/booking/setting',
        name: 'Booking Setting',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/setting'),

    },
    {
        path: '/dashboard/booking/Invoice-Temporarily',
        name: 'Invoice Temporarily',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/InvoiceTemporarily'),

    },
    {
        path: '/dashboard/booking/Invoice',
        name: 'Booking Invoice',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/Invoice'),
    },
    {
        path: '/dashboard/booking/attendants',
        name: 'Booking Invoice',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/attendant'),
    },
    {
        path: '/dashboard/booking/checkout',
        name: 'CheckOut',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/checkout'),

    },
    {
        path: '/dashboard/booking/dailyInvoice',
        name: 'Daily Invoice',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/dailyInvoice'),

    },
    {
        path: '/dashboard/booking/items',
        name: 'Booking Items',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/items'),

    },
    {
        path: '/dashboard/booking/report/checkIn',
        name: 'check in report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/report/check-in'),

    },
    {
        path: '/dashboard/booking/report/accommodationInvoice',
        name: 'accommodation invoice report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/report/accommodation-invoice'),

    },
    {
        path: '/dashboard/booking/report/items',
        name: 'items report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/booking/report/items'),

    },
];
