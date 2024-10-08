<script setup>
import { ref, defineProps, getCurrentInstance, onMounted, watch } from "vue";
import Swal from "sweetalert2";
import api from "@/api";

const { isOpen, editStore, updateinfo, assetId } = defineProps([
    "isOpen",
    "editStore",
    "updateinfo",
    "assetId",
]);

const flag = ref(false); 
const device_types = ref('');

const form = ref({
    device_name: "",
    device_type_id: "",
    device_type: "",
    specification: "",
    description: "",// New field for device type
});

watch(
    () => editStore,
    (item) => {
        if (item) {
            form.value.device_name = item.Device_Name || "";
            form.value.device_type_id = item.Device_Type_Id || "";
            form.value.specification = item.Specification || "";
            form.value.description = item.Description || "";
        }
    },
    { immediate: true }
);

const getData = async () => {
    try {
        const response = await api.get("/device-type");
        device_types.value = response.data;
  
    } catch (err) {
        console.error = err.message || "Error fetching data";
    }
};

const instance = getCurrentInstance();

const resetForm = () => {
    Object.keys(form.value).forEach((key) => {
        if (typeof form.value[key] === "string") {
            form.value[key] = "";
        } else {
            form.value[key] = null; // or any other default value you prefer
        }
    });
    flag.value = false; // Reset the flag when the form is reset
};

const closeModal = () => {
    instance.emit("modal-close");
};

const create = async () => {
    try {
        const response = await api.post("/asset", form.value);
        if (response.data.success) {
            instance.emit("modal-close");
            Swal.fire({
                position: "middle",
                icon: "success",
                title: "Information has been Saved",
                showConfirmButton: false,
                timer: 1500,
            });
            resetForm();
        }
    } catch (error) {
        console.error("Error creating store:", error);
    }
};

const update = async () => {
    try {
        const response = await api.put(`/asset/${assetId}`, form.value);
        if (response.data.success) {
            instance.emit("modal-close");
            Swal.fire({
                position: "middle",
                icon: "success",
                title: "Information has been Updated",
                showConfirmButton: false,
                timer: 1500,
            });
        }
    } catch (error) {
        console.error("Error updating store:", error);
    }
};

const submit = () => {
    if (updateinfo === "Save") {
        create();
    } else if (updateinfo === "Update") {
        update();
    } else {
        console.error("Invalid updateinfo value:", updateinfo);
    }
};

const isType = (event) => {
    event.preventDefault(); 
    flag.value = true;
    form.value.device_type_id = '';
};
onMounted(() => getData());
</script>

<template>
    <div
        v-if="isOpen"
        class="modal-mask col-lg-6 col-md-6 grid-margin stretch-card"
        ref="target"
    >
        <div class="card">
            <div class="card-body">
                <form class="forms-sample" @submit.prevent="submit">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Device Name*</label>
                                <input type="text" class="form-control" v-model="form.device_name" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label for="exampleInputEmail1">Device Type*</label>
                            <div class="d-flex align-items-center">
                                <div v-if="!flag">
                                    <select class="form-control" v-model="form.device_type_id">
                                        <option value="" disabled>Select a device type</option>
                                        <option 
                                        v-for="item in device_types"
                                            :key="item.id"
                                            :value="item.id"
                                        >{{ item.Name }}</option>
                                    </select>
                                </div>
                                <div v-else>
                                    <input type="text" class="form-control" v-model="form.device_type" />
                                </div>
                                <button class="add-type" @click="isType">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Specification*</label>
                                <input type="text" class="form-control" v-model="form.specification" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Description</label>
                                <textarea class="form-control" rows="2" v-model="form.description"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="custom-btn btn-13 p-2">
                            <i class="fa-solid fa-check"></i> | {{ updateinfo }}
                        </button>
                        <button
                            type="reset"
                            class="custom-btn btn-12 p-2"
                            @click.stop="closeModal"
                        >
                            <i class="fa-solid fa-x"></i> | Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
#BrandModal,
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-container {
    width: 300px;
    padding: 20px 30px;
    background-color: #620d0d;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    background-color: blue;
}

.add-type {
    border: none;
    border-radius: 50%;
    margin-left: 5px;
    color: white;
    background-color: rgb(34, 207, 207);
    cursor: pointer;
}
</style>
