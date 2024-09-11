<script setup>
import { ref, onMounted } from "vue";
import api from "../api";

const error = ref([]);
const nav_tree = ref([]);

const filterActiveNodes = (nodes) => {
    return nodes
        .filter((node) => node.active === 1)
        .map((node) => ({
            ...node,
            children: node.children ? filterActiveNodes(node.children) : [],
        }));
};

const getData = async () => {
    try {
        let { data } = await api.get("/navigation");
        if (data && data.tree) {
            nav_tree.value = filterActiveNodes(data.tree);
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
                <div class="nav mt-5">
                    <Router-Link class="nav-link" :to="{ name: 'Home' }"
                        ><div class="sb-nav-link-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        Dashboard</Router-Link
                    >
                    <div v-for="nav in nav_tree" :key="nav.id">
                        <a
                            class="nav-link collapsed"
                            href="#"
                            data-bs-toggle="collapse"
                            :data-bs-target="'#' + nav.name"
                            aria-expanded="false"
                            :aria-controls="nav.name"
                        >
                            <div class="sb-nav-link-icon">
                                <i :class="nav.icon"></i>
                            </div>
                            {{ nav.name }}
                            <div
                                class="sb-sidenav-collapse-arrow"
                                v-if="nav.children"
                            >
                                <i class="fas fa-angle-down"></i>
                            </div>
                        </a>
                        <div
                            class="collapse"
                            :id="nav.name"
                            aria-labelledby="headingTwo"
                            data-bs-parent="#sidenavAccordion"
                            v-if="nav.children"
                        >
                            <nav
                                class="sb-sidenav-menu-nested nav"
                                v-for="child in nav.children"
                                :key="child.id"
                            >
                                <Router-Link
                                    class="nav-link"
                                    :to="{ name: child.page }"
                                    >{{ child.name }}</Router-Link
                                >
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Developed by:</div>
                Artisan IT
            </div>
        </nav>
    </div>
</template>
