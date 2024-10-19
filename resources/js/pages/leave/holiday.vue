<script setup>
import { ref, onMounted, defineAsyncComponent } from "vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import api from "../../api";
import Swal from "sweetalert2";

const holidayComponent = defineAsyncComponent(() =>
    import("../leave/component/holidayForm.vue")
);

const currentYear = ref(new Date().getFullYear());

const isLoading = ref(true);
const error = ref(null);
const selectedStore = ref(null);
const heading = ref(null);

const holidays = ref([]);
const holidayId = ref();
const holidayModel = ref(false);

const holidayOpen = (title) => {
    heading.value = title;
    holidayModel.value = true;
};
const holidayClose = () => {
    holidayModel.value = false;
};

const getData = async (page = 1) => {
    try {
        const response = await api.get(`/holiday?page=${page}`);
        holidays.value = response.data;
    } catch (err) {
        error.value = err.message || "Error fetching data";
    } finally {
        isLoading.value = false;
    }
};

const editholiday = async (id) => {
    try {
        const response = await api.get(`/holiday/${id}`);
        selectedStore.value = response.data;
        holidayId.value = id;
        holidayOpen("Update");
    } catch (err) {
        console.error("Error fetching store data for editing:", err);
    }
};

const deleteholiday = async (id) => {
    try {
        const response = await api.delete(`/delete-holiday/${id}`);
        console.log(response.data.success);
        if (response.data.success) {
            Swal.fire({
                position: "middle",
                icon: "success",
                title: "Information has been Deleted",
                showConfirmButton: false,
                timer: 1500,
            });
            await getData();
        }
    } catch (err) {
        console.error("Error fetching store data for editing:", err);
    }
};

const submitHandler = async () => {
    await getData();
};
onMounted(() => getData());
</script>

<template>
    <div class="container p-5">
        <component
            v-if="holidayModel"
            :is="holidayComponent"
            :isOpen="holidayModel"
            :editStore="selectedStore"
            :updateinfo="heading"
            :holiId="holidayId"
            @modal-close="holidayClose"
            @submit="submitHandler"
            name="first-modal"
        />
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-11">
                            <h2 class="card-title m-2">
                                Holiday List of {{ currentYear }}
                            </h2>
                        </div>
                        <div class="col-lg-1">
                            <button
                                @click="holidayOpen('Save')"
                                type="button"
                                class="btn-plus"
                            >
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Serial</th>
                                    <th>Name of the Holiday</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>No of Days</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(holiday, index) in holidays.data"
                                    :key="holiday.id"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ holiday.Name }}</td>
                                    <td>{{ holiday.From_Date }}</td>
                                    <td>{{ holiday.To_Date }}</td>
                                    <td>{{ holiday.Duration }}</td>
                                    <td>
                                        <button
                                            class="custom-btn btn-15"
                                            @click="editholiday(holiday.id)"
                                        >
                                            <i
                                                class="fa-regular fa-pen-to-square"
                                            ></i>
                                        </button>
                                        <button
                                            class="custom-btn btn-12 mx-2"
                                            @click="deleteholiday(holiday.id)"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        <Bootstrap4Pagination
                            :data="holidays"
                            :limit="2"
                            @pagination-change-page="getData"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
