import { createRouter, createWebHistory } from "vue-router";

import store from "./store/store";


const routes = [
    {
        path: "/login",
        name: "Login",
        component: () => import('./pages/login.vue'),
    },
    {
        path: "/home",
        name: "Home",
        component: () => import('./pages/home.vue'),
        meta: {
            requireAuth: true,
        },
    },
    {
        path: "/setup/employee/employeeList",
        name: "EmployeeList",
        component: () => import('./pages/setup/employee/employeeList.vue'),
        meta: {
            requireAuth: true,
            pageId: 7,
        },
    },
    {
        path: "/setup/employee/empexl",
        name: "EmpExl",
        component: () => import('./pages/setup/employee/empExl.vue'),
        meta: {
            requireAuth: true,
            pageId: 8,
        },
    },
    {
        path: "/setup/employee/empdetails/:id",
        name: "EmpDetails",
        component: () => import('./pages/setup/employee/employeeDetails.vue'),
        meta: {
            requireAuth: true,
        },
    },
    {
        path: "/setup/employee/emp/:id?",
    name: "Emp",
    component: () => import('./pages/setup/employee/employee.vue'),
    meta: {
        requireAuth: true,
    },
    children: [
        {
            path: "/setup/employee/employee/:id?",
            name: "Employee",
            component: () => import('./pages/setup/employee/employeeInfo.vue'),
            meta: {
                requireAuth: true,
            },
        },
        {
            path: "/setup/employee/personal/:id?",
            name: "Personal",
            component: () => import('./pages/setup/employee/personalInfo.vue'),
            meta: {
                requireAuth: true,
            },
        },
        {
            path: "/setup/employee/official/:id?",
            name: "Official",
            component: () => import('./pages/setup/employee/officialInfo.vue'),
            meta: {
                requireAuth: true,
            },
        },
        {
            path: "/setup/employee/professional/:id?",
            name: "Professional",
            component: () => import('./pages/setup/employee/professionalInfo.vue'),
            meta: {
                requireAuth: true,
            },
        },
    ],
    },
    {
        path: "/setup/attendence/tso-location",
        name: "TsoLocation",
        component: () => import('./pages/setup/attendence/tsoLocation.vue'),
        meta: {
            requireAuth: true,
            pageId: 10,
        },
    },
    {
        path: "/setup/attendence",
        name: "Attendence",
        component: () => import('./pages/setup/attendence/attendence.vue'),
        meta: {
            requireAuth: true,
            pageId: 9,
        },
    },
    {
        path: "/atten",
        name: "atten",
        component: () => import('./pages/setup/attendence/atten.vue'),
        meta: {
            requireAuth: true,
        },
    },
    {
        path: "/setup/assetInformation",
        name: "AssetInformation",
        component: () => import('./pages/setup/assets/assetInformation.vue'),
        meta: {
            requireAuth: true,
            pageId: 18,
        },
    },
    {
        path: "/setup/emp-assets-info",
        name: "EmpAssetsInfo",
        component: () => import('./pages/setup/assets/empAssetInfo.vue'),
        meta: {
            requireAuth: true,
            pageId: 20,
        },
    },
    {
        path: "/report/attendenceReport",
        name: "AttendenceReport",
        component: () => import('./pages/report/AttendenceReport.vue'),
        meta: {
            requireAuth: true,
            pageId: 12,
        },
    },
    {
        path: "/security/create-role",
        name: "CreateRole",
        component: () => import('./pages/security/createRole.vue'),
        meta: {
            requireAuth: true,
            pageId: 4,
        },
    },
    {
        path: "/security/user-registration",
        name: "UserRegistration",
        component: () => import('./pages/security/userRegistration.vue'),
        meta: {
            requireAuth: true,
            pageId: 3,
        },
    },
    {
        path: "/security/user-page-permission",
        name: "UserPagePermission",
        component: () => import('./pages/security/userPagePermission.vue'),
        meta: {
            requireAuth: true,
            pageId: 5,
        },
    },
    {
        path: "/leave",
        name: "Leave",
        component: () => import('./pages/leave/leave.vue'),
        meta: {
            requireAuth: true,
        },
    },
    {
        path: "/leave/leave-apply",
        name: "LeaveApply",
        component: () => import('./pages/leave/leaveApplication.vue'),
        meta: {
            requireAuth: true,
            pageId: 15,
        },
    },
    {
        path: "/leave/leave-status",
        name: "LeaveStatus",
        component: () => import('./pages/leave/leaveStatus.vue'),
        meta: {
            requireAuth: true,
            pageId: 16,
        },
    },
    {
        path: "/leave/holiday",
        name: "Holiday",
        component: () => import('./pages/leave/holiday.vue'),
        meta: {
            requireAuth: true,
            pageId: 14,
        },
    },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// router.beforeEach((to, from,) => {
//     if (to.meta.requireAuth && store.getters.getToken == 0) {
//         return { name: "Login" };
//     }
//     if (to.meta.requireAuth == false && localStorage.getItem("token")) {
//         return { name: "Home" };
//     }
// });


router.beforeEach((to, from, next) => {
    const userPermissions = store.getters.getPermissions;
    if (to.meta.pageId) {
        if (!userPermissions.includes(to.meta.pageId)) {
            return next('/');
        }
    }
    next();
});



export default router;
