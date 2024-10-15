<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import api from "@/api";

const router = useRouter();
const store = useStore();
const form = reactive({
    username: "",
    password: "",
});
const err = ref("");

const login = async () => {
    try {
        const res = await api.post("/login", form);
        if (res.data.success) {
            store.dispatch("setToken", res.data.data.token);
            store.dispatch("setPermissions", res.data.data.permissions);
            router.push({ name: "Dashboard" });
        } else {
            err.value = res.data.message || "An unknown error occurred";
        }
    } catch (error) {
        err.value =
            error.response?.data?.message || "An unexpected error occurred";
    }
};

onMounted(() => {
    if (store.getters.getToken) {
        router.push({ name: "Dashboard" });
    }
});
</script>

<template>
    <div class="form-wrapper">
        <div class="form-container">
            <img
                src="/public/storage/uploads/1718087744_artisan.png"
                height="200px"
                width="200px"
            />
            <form class="form" @submit.prevent="login">
                <span class="text-danger">{{ err }}</span>
                <input
                    type="text"
                    class="input"
                    placeholder="Username"
                    v-model="form.username"
                />
                <input
                    type="password"
                    class="input"
                    placeholder="Password"
                    v-model="form.password"
                />
                <p class="page-link">
                    <span class="page-link-label">Forgot Password?</span>
                </p>
                <button class="form-btn">Log in</button>
            </form>
        </div>
    </div>
</template>

<style scoped>
.form-wrapper {
    background-image: url("/public/storage/uploads/bg-img-1.jpg");
    background-size: cover;
    background-repeat: no-repeat;
}

.form-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.form-container {
    width: 350px;
    height: 500px;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    border-radius: 10px;
    box-sizing: border-box;
    padding: 20px 30px;
}

.form {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 18px;
    margin-bottom: 15px;
}

.input {
    border-radius: 20px;
    border: 1px solid #c0c0c0;
    outline: 0 !important;
    box-sizing: border-box;
    padding: 12px 15px;
}

.page-link {
    text-decoration: underline;
    margin: 0;
    text-align: end;
    color: #747474;
    text-decoration-color: #747474;
}

.page-link-label {
    cursor: pointer;
    font-family: "Lucida Sans", "Lucida Sans Regular", "Lucida Grande",
        "Lucida Sans Unicode", Geneva, Verdana, sans-serif;
    font-size: 9px;
    font-weight: 700;
}

.page-link-label:hover {
    color: #000;
}

.form-btn {
    padding: 10px 15px;
    font-family: "Lucida Sans", "Lucida Sans Regular", "Lucida Grande",
        "Lucida Sans Unicode", Geneva, Verdana, sans-serif;
    border-radius: 20px;
    border: 0 !important;
    outline: 0 !important;
    background: teal;
    color: white;
    cursor: pointer;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
}
</style>
