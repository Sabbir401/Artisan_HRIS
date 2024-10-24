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
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <!-- Search form here -->
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown position-relative p-2">
                <a
                    class="nav-link position-relative"
                    id="navbarDropdown"
                    href="#"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="fa-solid fa-bell" style="font-size: large"></i>
                    <!-- Notification count badge -->
                    <span
                        v-if="totalNotifications > 0"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    >
                        {{ totalNotifications }}
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </a>
                <ul
                    class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="navbarDropdown"
                    style="background-color: #c7d1cb;"
                >
                    <div v-for="item in notification" :key="item.id">
                        <li
                            class="dropdown-item"
                            v-if="item.Status === 'Pending'"
                            @click="router.push('/leave/leave-status')"
                        >
                            {{ item.Full_Name }} Requested a Leave
                        </li>
                    </div>
                    <div v-for="leave in employeeLeave" :key="leave.id">
                        <li
                            class="dropdown-item"
                            v-if="leave.Notification === 1"
                            @click="destroyNotification(leave.id)"
                        >
                            {{ leave.Full_Name }}, your {{ leave.leave_type }} Leave has been {{ leave.Status }}
                        </li>
                    </div>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a
                    class="nav-link dropdown-toggle"
                    id="userDropdown"
                    href="#"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    {{ user.Full_Name }}
                    <img
                        :src="user.img_url"
                        alt="User Image"
                        height="40px"
                        width="40px"
                        style="border-radius: 50%"
                    />
                </a>
                <ul
                    class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="userDropdown"
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
import { ref, onMounted, computed } from "vue";
import api from "../api";
import { useRouter } from "vue-router";
import { useStore } from "vuex";

const store = useStore();
const router = useRouter();
const error = ref("");
const user = ref({});
const notification = ref([]);
const employeeLeave = ref([]);

// Compute the total number of notifications from both existing notifications and employee leaves
const totalNotifications = computed(() => {
    const notificationCount = notification.value.filter(item => item.Status === "Pending").length; // Count of existing notifications
    const leaveCount = employeeLeave.value.filter(leave => leave.Notification === 1).length; // Count from employeeLeave
    return notificationCount + leaveCount; // Sum of both counts
});

const getData = async () => {
    try {
        const [responseUser, responseNotification, responseleave] = await axios.all([
            api.get("/current-user"),
            api.get("/all-leave"),
            api.get("/employee-leave"),
        ]);
        user.value = responseUser.data;
        notification.value = responseNotification.data;
        employeeLeave.value = responseleave.data;
    } catch (err) {
        error.value = err.message || "Error fetching data";
    }
    if (user.value === "logout") {
        logout();
    }
};

const destroyNotification = async (id) => {
    try {
        await api.get(`/destroy-notification/${id}`);
        getData();
    } catch (error) {
        // Handle error
    }
};

const logout = async () => {
    store.dispatch("removeToken", 0);
    localStorage.removeItem("token");
    try {
        await api.get('/logout');
        window.location.reload();
    } catch (error) {
        console.error("Error logging out", error);
    }
    router.push({ name: "Login" });
};

onMounted(() => getData());
</script>
