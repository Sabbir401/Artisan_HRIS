<template>
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.html">Artisan HRM</a>
        <!-- Sidebar Toggle-->
        <button
            class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0"
            id="sidebarToggle"
            href="#!"
        >
            <i class="fas fa-bars"></i>
        </button>
        <!-- Navbar Search-->
        <form
            class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0"
        >
            <!-- <div class="input-group">
                <input
                    class="form-control"
                    type="text"
                    placeholder="Search for..."
                    aria-label="Search for..."
                    aria-describedby="btnNavbarSearch"
                />
                <button
                    class="btn btn-primary"
                    id=""
                    type="button"
                >
                    <i class="fas fa-search"></i>
                </button>
            </div> -->
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a
                    class="nav-link dropdown-toggle"
                    id="navbarDropdown"
                    href="#"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                <i class="fa-solid fa-bell" style="font-size: large;"></i>
                </a>
                <ul
                    class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="navbarDropdown"
                >
                    <li class="dropdown-item">Settings</li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a
                    class="nav-link dropdown-toggle"
                    id="navbarDropdown"
                    href="#"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    {{ user.Full_Name }}
                    <img
                        :src="user.img_url"
                        alt="Image"
                        height="40px"
                        width="40px"
                        style="border-radius: 50%"
                    />
                </a>
                <ul
                    class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="navbarDropdown"
                >
                    <li class="dropdown-item">Settings</li>
                    <li class="dropdown-item">Activity Log</li>
                    <li><hr class="dropdown-divider" /></li>
                    <li class="dropdown-item" @click="logout">Logout</li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../api";
import { useRouter } from "vue-router";
import { useStore } from "vuex";

const store = useStore();
const router = useRouter();
const error = ref([]);
const user = ref("");

const getData = async () => {
    try {
        const response = await api.get("/current-user");
        user.value = response.data;
        if (user.value === "logout") {
            logout();
        }
    } catch (err) {
        error.value = err.message || "Error fetching data";
    }
};

function logout() {
    store.dispatch("removeToken", 0);
    router.push({ name: "Login" });
}

onMounted(() => getData());
</script>
