<template>
    <div>
        <div class="row d-flex justify-content-between">
            <div class="col-lg-2 mt-5">
                <button class="custom-btn btn-13 p-2" @click="fetchAttendence">
                    Fetch Attendence
                </button>
            </div>
            <div class="col-lg-2 mt-5">
                <button class="custom-btn btn-12 p-2" @click="fetchMachineUser">
                    Fetch Machine User
                </button>
            </div>
            <div class="col-lg-2 mt-5">
                <button
                    class="custom-btn btn-12 p-2"
                    @click="fetchMachineAttendence"
                >
                    Fetch Machine Attendence
                </button>
            </div>
            <div v-if="loading" class="overlay-loading">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import Swal from "sweetalert2";
import api from "@/api";
const loading = ref(false);


const fetchAttendence = async () => {
    loading.value = true;
    try {
        const response = await api.get("/fetch-attendence");
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
};

const fetchMachineUser = async () => {
    loading.value = true;
    try {
        const response = await api.get("/fetch-zkt-user");
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
};

const fetchMachineAttendence = async () => {
    loading.value = true;
    try {
        const response = await api.get("/fetch-zkt-attendence");
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
};

</script>
