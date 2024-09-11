<script setup>
import { ref, onMounted } from "vue";
import api from "../api";

const permission = ref([]);
const error = ref([]);
const nav_tree = ref([]);

const getData = async () => {
    try {
        let { data } = await api.get("/navigation");
        if (data) {
            nav_tree.value = data["tree"];
        }
    } catch (err) {
        error.value = err.message || "Error fetching data";
    }
};

onMounted(() => getData());
</script>

<template>
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <Router-Link class="nav-link" :to="{ name: 'Home' }"
                        ><div class="sb-nav-link-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        Dashboard</Router-Link
                    >
                    <a
                        class="nav-link collapsed"
                        href="#"
                        data-bs-toggle="collapse"
                        data-bs-target="#Layout"
                        aria-expanded="false"
                        aria-controls="Layout"
                    >
                        <div class="sb-nav-link-icon">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        Security
                        <div class="sb-sidenav-collapse-arrow">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </a>
                    <div
                        class="collapse"
                        id="Layout"
                        aria-labelledby="headingTwo"
                        data-bs-parent="#sidenavAccordion"
                    >
                        <nav class="sb-sidenav-menu-nested nav">
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'Register' }"
                                >User Create</Router-Link
                            >
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'CreateRole' }"
                                >Role</Router-Link
                            >
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'CreatePermission' }"
                                >User Permission</Router-Link
                            >
                        </nav>
                    </div>
                    <div class="sb-sidenav-menu-heading">Interface</div>
                    <a
                        class="nav-link collapsed"
                        href="#"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseLayouts"
                        aria-expanded="false"
                        aria-controls="collapseLayouts"
                    >
                        <div class="sb-nav-link-icon">
                            <i class="fa-solid fa-wrench"></i>
                        </div>
                        Setup
                        <div class="sb-sidenav-collapse-arrow">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </a>
                    <div
                        class="collapse"
                        id="collapseLayouts"
                        aria-labelledby="headingOne"
                        data-bs-parent="#sidenavAccordion"
                    >
                        <nav class="sb-sidenav-menu-nested nav">
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'EmpList' }"
                                >Employee</Router-Link
                            >
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'EmployeeList' }"
                                >Employee List</Router-Link
                            >
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'Attendence' }"
                                >Attendence</Router-Link
                            >
                            <Router-Link class="nav-link" :to="{ name: 'Test' }"
                                >Test</Router-Link
                            >
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'Location' }"
                                >TSO Location</Router-Link
                            >
                        </nav>
                    </div>

                    <a
                        class="nav-link collapsed"
                        href="#"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapsePages"
                        aria-expanded="false"
                        aria-controls="collapsePages"
                    >
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        Report
                        <div class="sb-sidenav-collapse-arrow">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </a>
                    <div
                        class="collapse"
                        id="collapsePages"
                        aria-labelledby="headingTwo"
                        data-bs-parent="#sidenavAccordion"
                    >
                        <nav class="sb-sidenav-menu-nested nav">
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'AttendenceReport' }"
                                >Attendence</Router-Link
                            >
                            <!-- <a class="nav-link" href="register.html"
                                >Register</a
                            >
                            <a class="nav-link" href="password.html"
                                >Forgot Password</a
                            > -->
                        </nav>
                    </div>

                    <a
                        class="nav-link collapsed"
                        href="#"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseLayout"
                        aria-expanded="false"
                        aria-controls="collapseLayout"
                    >
                        <div class="sb-nav-link-icon">
                            <i class="fa-solid fa-wrench"></i>
                        </div>
                        Leave
                        <div class="sb-sidenav-collapse-arrow">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </a>

                    <div
                        class="collapse"
                        id="collapseLayout"
                        aria-labelledby="headingTwo"
                        data-bs-parent="#sidenavAccordion"
                    >
                        <nav class="sb-sidenav-menu-nested nav">
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'Holiday' }"
                                >Holiday</Router-Link
                            >
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'LeaveApply' }"
                                >Leave Application</Router-Link
                            >
                            <Router-Link
                                class="nav-link"
                                :to="{ name: 'LeaveStatus' }"
                                >Application Status</Router-Link
                            >
                        </nav>
                    </div>

                    <!-- <a
                        class="nav-link collapsed"
                        href="#"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapsePages"
                        aria-expanded="false"
                        aria-controls="collapsePages"
                    >
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        Report
                        <div class="sb-sidenav-collapse-arrow">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </a>
                    <div
                        class="collapse"
                        id="collapsePages"
                        aria-labelledby="headingTwo"
                        data-bs-parent="#sidenavAccordion"
                    >
                        <nav
                            class="sb-sidenav-menu-nested nav accordion"
                            id="sidenavAccordionPages"
                        >
                            <a
                                class="nav-link collapsed"
                                href="#"
                                data-bs-toggle="collapse"
                                data-bs-target="#pagesCollapseAuth"
                                aria-expanded="false"
                                aria-controls="pagesCollapseAuth"
                            >
                                Authentication
                                <div class="sb-sidenav-collapse-arrow">
                                    <i class="fas fa-angle-down"></i>
                                </div>
                            </a>
                            <div
                                class="collapse"
                                id="pagesCollapseAuth"
                                aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordionPages"
                            >
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="login.html"
                                        >Login</a
                                    >
                                    <a class="nav-link" href="register.html"
                                        >Register</a
                                    >
                                    <a class="nav-link" href="password.html"
                                        >Forgot Password</a
                                    >
                                </nav>
                            </div>
                            <a
                                class="nav-link collapsed"
                                href="#"
                                data-bs-toggle="collapse"
                                data-bs-target="#pagesCollapseError"
                                aria-expanded="false"
                                aria-controls="pagesCollapseError"
                            >
                                Error
                                <div class="sb-sidenav-collapse-arrow">
                                    <i class="fas fa-angle-down"></i>
                                </div>
                            </a>
                            <div
                                class="collapse"
                                id="pagesCollapseError"
                                aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordionPages"
                            >
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="401.html"
                                        >401 Page</a
                                    >
                                    <a class="nav-link" href="404.html"
                                        >404 Page</a
                                    >
                                    <a class="nav-link" href="500.html"
                                        >500 Page</a
                                    >
                                </nav>
                            </div>
                        </nav>
                    </div>
                    <div class="sb-sidenav-menu-heading">Addons</div>
                    <a class="nav-link" href="charts.html">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        Charts
                    </a>
                    <a class="nav-link" href="tables.html">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-table"></i>
                        </div>
                        Tables
                    </a> -->
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Developed by:</div>
                Artisan IT
            </div>
        </nav>
    </div>
</template>
