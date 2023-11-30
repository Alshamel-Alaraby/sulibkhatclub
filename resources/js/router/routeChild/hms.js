import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/h_m_s/specialties',
        name: 'specialties',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/specialties/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/diagnosis_tests',
        name: 'diagnosis_tests',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/diagnosis_tests/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/services_types',
        name: 'services_types',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/services_types/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/drugs',
        name: 'drugs',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/drugs/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/rooms_categories',
        name: 'rooms_categories',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/rooms_categories/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/rooms',
        name: 'rooms',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/rooms/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/doctors',
        name: 'doctors',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/doctors/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/patients',
        name: 'patients',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/patients/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/insurance_companies',
        name: 'insurance_companies',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/insurance_companies/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/prescriptions',
        name: 'prescriptions',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/prescriptions/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/appointments',
        name: 'appointments',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/appointments/index.vue'),
    },
    {
        path: '/dashboard/h_m_s/appointments/waiting_rooms',
        name: 'waiting_rooms',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/appointments/waiting_rooms.vue'),
    },
    {
        path: '/dashboard/h_m_s/appointments/upcoming_appointments',
        name: 'upcoming_appointments',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/h_m_s/appointments/upcoming_appointments.vue'),
    },
];
