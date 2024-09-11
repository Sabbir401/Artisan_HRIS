<script setup>
import { ref, onMounted, defineAsyncComponent } from "vue";
import api from "@/api";
import Swal from "sweetalert2";
import { Bootstrap4Pagination } from "laravel-vue-pagination";

const assetComponent = defineAsyncComponent(() =>
    import("../assets/components/assetsComponent.vue")
);

const isLoading = ref(true);
const error = ref(null);
const selectedStore = ref(null);
const heading = ref(null);

const assets = ref([]);
const assetId = ref();
const assetModel = ref(false);

const assetOpen = (title) => {
    heading.value = title;
    assetModel.value = true;
};
const holidayClose = () => {
    assetModel.value = false;
    getData();
};

const getData = async (page = 1) => {
    try {
        const response = await api.get(`/asset?page=${page}`);
        assets.value = response.data;
    } catch (err) {
        error.value = err.message || "Error fetching data";
    } finally {
        isLoading.value = false;
    }
};

const editAsset = async (id) => {
    try {
        const response = await api.get(`/asset/${id}`);
        selectedStore.value = response.data;
        assetId.value = id;
        assetOpen("Update");
    } catch (err) {
        console.error("Error fetching store data for editing:", err);
    }
};

const deleteAsset = async (id) => {
    try {
        const response = await api.delete(`/delete-asset/${id}`);
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
            v-if="assetModel"
            :is="assetComponent"
            :isOpen="assetModel"
            :editStore="selectedStore"
            :updateinfo="heading"
            :assetId="assetId"
            @modal-close="holidayClose"
            @submit="submitHandler"
            name="first-modal"
        />
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-11">
                            <h2 class="card-title m-2">Asset List</h2>
                        </div>
                        <div class="col-lg-1">
                            <button
                                @click="assetOpen('Save')"
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
                                    <th>Device Name</th>
                                    <th>Device Type</th>
                                    <th>Specification</th>
                                    <th>Quantity</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(asset, index) in assets.data"
                                    :key="asset.id"
                                >
                                    <td>{{ assets.from + index }}</td>
                                    <td>
                                        {{ asset ? asset.Device_Name : "" }}
                                    </td>
                                    <td>
                                        {{
                                            asset ? asset.device_type.Name : ""
                                        }}
                                    </td>
                                    <td>
                                        {{ asset ? asset.Specification : "" }}
                                    </td>
                                    <td>{{ "" }}</td>
                                    <td>
                                        <button
                                            class="custom-btn btn-15"
                                            @click="editAsset(asset.id)"
                                        >
                                            <i
                                                class="fa-regular fa-pen-to-square"
                                            ></i>
                                        </button>
                                        <button
                                            class="custom-btn btn-12 mx-2"
                                            @click="deleteAsset(asset.id)"
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
                            :data="assets"
                            :limit="2"
                            @pagination-change-page="getData"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
