<script setup>
import { ref, onMounted, watch, computed } from "vue";
import api from "@/api";
import Swal from "sweetalert2";

const error = ref([]);
const department = ref([]);
const deviceName = ref([]);
const empp = ref([]);
const emp_img = ref([]);
const assetAll = ref([]);
const selectedDept = ref("");
const selectedEmp = ref("");
const empAsset = ref("");

const form = ref({
    department: "",
    employee_Id: "",
    device_id: "",
    date: "",
    serialNumber: "",
    quantity: "",
});

// Fetch department and navigation data
const getData = async () => {
    try {
        const [responseDept, responseDevice, responseAll] = await axios.all([
            api.get("/department"),
            api.get("/get-asset"),
            api.get("/employee-asset"),
        ]);
        department.value = responseDept.data;
        deviceName.value = responseDevice.data;
        assetAll.value = responseAll.data;
    } catch (err) {
        if (err.response && err.response.status === 401) {
            router.push({ name: "Login" });
        } else {
            error.value = err.message || "Error fetching data";
        }
    }
};

watch(
    () => empAsset,
    (item) => {
        if (item) {
            form.value.department = item.Dept_Name || "";
            form.value.employee_Id = item.Full_Name || "";
            form.value.device_id = item.Device_Name || "";
            form.value.date = item.Date || "";
            form.value.quantity = item.Quantity || "";
            form.value.serialNumber = item.Serial_Number || "";
        }
    },
    { immediate: true }
);

const filteredData = computed(() => {
    return assetAll.value.filter((item) => {
        return (
            (selectedDept.value === "" ||
                item.Dept_Name === selectedDept.value) &&
            (selectedEmp.value === "" || item.Full_Name === selectedEmp.value)
        );
    });
});

function editEmpAsset (id) {
    const response = api.get(`/editEmpAsset/${id}`);
    // empAsset.value =  response.data;
    console.log(response.data);
}

// const editEmpAsset = async (id) => {
//   try {
//     const response = await api.get(`/editEmpAsset/${id}`);
//     if (response.ok) {
//       empAsset.value = await response.json();
//       console.log(response.data);
//     } else {
//       console.error("Error fetching employee asset data:", response.status);
//     }
//   } catch (error) {
//     console.error("Error:", error);
//   }
// };

// Fetch employees based on department selection
const getEmployee = async (id) => {
    try {
        const response = await api.get(`/emp/${id}`);
        empp.value = response.data;
    } catch (error) {
        console.error("Error fetching employees:", error);
    }
};

const getEmployeeImg = async (id) => {
    try {
        const [responseimg] = await axios.all([api.get(`/empimg/${id}`)]);
        emp_img.value = responseimg.data;
    } catch (error) {
        console.error("Error updating store:", error);
    }
};

// Reset form data
const resetForm = () => {
    Object.keys(form.value).forEach((key) => {
        if (typeof form.value[key] === "string") {
            form.value[key] = "";
        }
    });
};

// Handle form submission
const submit = async () => {
    try {
        const response = await api.post(`/employee-asset`, form.value);
        if (response.data.success) {
            resetForm();
            Swal.fire({
                position: "middle",
                icon: "success",
                title: "Employee permission has been saved",
                showConfirmButton: false,
                timer: 1500,
            });
        }
    } catch (error) {
        console.error("Error submitting attendance:", error);
    }
};

onMounted(() => getData());
</script>

<template>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <h1 class="mb-5">Employee Asset Infromation</h1>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 mb-3">
                                    <form @submit.prevent="submit">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Department</label>
                                                    <select
                                                        class="form-control"
                                                        v-model="
                                                            form.department
                                                        "
                                                        @change="
                                                            getEmployee(
                                                                form.department
                                                            )
                                                        "
                                                    >
                                                        <option
                                                            selected
                                                            disabled
                                                        >
                                                            select
                                                        </option>
                                                        <option
                                                            v-for="dept in department"
                                                            :key="dept.id"
                                                            :value="dept.id"
                                                        >
                                                            {{ dept.Name }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Employee Name</label>
                                                    <select
                                                        class="form-control"
                                                        name="status"
                                                        id=""
                                                        v-model="
                                                            form.employee_Id
                                                        "
                                                        @change="
                                                            getEmployeeImg(
                                                                form.employee_Id
                                                            )
                                                        "
                                                    >
                                                        <option
                                                            v-for="e in empp"
                                                            :key="e.id"
                                                            :value="e.id"
                                                        >
                                                            {{ e.Full_Name }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Device Name</label>
                                                    <select
                                                        class="form-control"
                                                        v-model="form.device_id"
                                                    >
                                                        <option
                                                            selected
                                                            disabled
                                                        >
                                                            select
                                                        </option>
                                                        <option
                                                            v-for="device in deviceName"
                                                            :key="device.id"
                                                            :value="device.id"
                                                        >
                                                            {{
                                                                device.Device_Name
                                                            }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input
                                                        type="date"
                                                        class="form-control"
                                                        v-model="form.date"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="form.quantity"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Serial Number</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="
                                                            form.serialNumber
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button
                                                type="reset"
                                                class="custom-btn btn-12 m-3 p-2"
                                            >
                                                <i class="fa-solid fa-x"></i>
                                                | Reset
                                            </button>
                                            <button
                                                type="submit"
                                                class="custom-btn btn-13 m-3 p-2"
                                            >
                                                <i
                                                    class="fa-solid fa-check"
                                                ></i>
                                                | Save
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <img
                                :src="emp_img ? emp_img.img_url : ''"
                                class="card-img"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <div class="text-center">
                        <h1 class="mb-5">Asset Summary</h1>
                    </div>

                    <div class="row d-flex justify-content-end">
                        <div class="col-lg-3">
                            <label for="exampleInputEmail1">Department</label>
                            <select v-model="selectedDept" class="form-control">
                                <option value="">All Department</option>
                                <option
                                    v-for="dept in assetAll"
                                    :key="dept.id"
                                    :value="dept.Dept_Name"
                                >
                                    {{ dept.Dept_Name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="exampleInputEmail1">Employee</label>
                            <select
                                v-model="selectedEmp"
                                class="form-control"
                            >
                                <option value="">All Employee</option>
                                <option
                                    v-for="emp in assetAll"
                                    :key="emp.id"
                                    :value="emp.Full_Name"
                                >
                                    {{ emp.Full_Name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Department</th>
                                    <th>Employee Name</th>
                                    <th>Device Name</th>
                                    <th>Date</th>
                                    <th>Quantity</th>
                                    <th>Serial Number</th>
                                    <th>Operation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="ver-align"
                                    v-for="(item, index) in filteredData"
                                    :key="item.id"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.Dept_Name }}</td>
                                    <td>{{ item.Full_Name }}</td>
                                    <td>
                                        {{ item.Device_Name }}
                                    </td>
                                    <td>{{ item.Date }}</td>
                                    <td class="text-center">{{ item.Quantity }}</td>
                                    <td>{{ item.Serial_Number }}</td>
                                    
                                    <td class="text-center">
                                        <button
                                            class="custom-btn btn-13 mx-1 px-1"
                                            @click="editEmpAsset(item.id)"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card {
    height: 320px;
    max-height: 320px;
    overflow: hidden; /* Ensures that any overflow content is hidden */
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-body {
    width: 100%;
    height: 100%;
}

.card-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain; /* Ensures the entire image is visible */
}
</style>
