<script setup>
import { ref, onMounted, computed } from "vue";
import Swal from "sweetalert2";
import api from "../../api";

const empp = ref([]);
const emp_img = ref([]);

const department = ref([]);
const error = ref([]);
const leaveType = ref();
const leave = ref([]);
const selectedType = ref("");
const selectedStatus = ref("");
const selectedDept = ref("");
const leaveSummery = ref([]);
const allLeave = ref([]);

const form = ref({
    department: "",
    Employee_Id: "",
    Status: "",
});

const getData = async () => {
    try {
        const [responsedept, responsetype, response] = await axios.all([
            api.get("/department"),
            api.get("/leave-type"),
            api.get("/all-leave"),
        ]);
        department.value = responsedept.data;
        leaveType.value = responsetype.data;
        allLeave.value = response.data;
    } catch (err) {
        error.value = err.message || "Error fetching data";
    } finally {
    }
};


function showLeave(id){

    console.log(id);
}

const leaveApproved = async (id) => {
    form.value.Status = "Approved";
    try {
        const response = await api.put(`/leave/${id}`, form.value);
        empp.value = response.data;
        Swal.fire({
            position: "middle",
            icon: "success",
            title: "Leave has been Approved",
            showConfirmButton: false,
            timer: 1500,
        });
        getEmployeeImg(form.value.Employee_Id);
    } catch (error) {
        console.error("Error updating store:", error);
    }
};

const leaveReject = async (id) => {
    form.value.Status = "Rejected";
    try {
        const response = await api.put(`/leave/${id}`, form.value);
        empp.value = response.data;
        Swal.fire({
            position: "middle",
            icon: "error",
            title: "Leave has been Rejected",
            showConfirmButton: false,
            timer: 1500,
        });
        getEmployeeImg(form.value.Employee_Id);
    } catch (error) {
        console.error("Error updating store:", error);
    }
};

const filteredData = computed(() => {
    return allLeave.value.filter((item) => {
        return (
            (selectedType.value === "" ||
                item.leave_type === selectedType.value) &&
            (selectedStatus.value === "" ||
                item.Status === selectedStatus.value) &&
            (selectedDept.value === "" ||
                item.department === selectedDept.value)
        );
    });
});

const getEmployee = async (id) => {
    try {
        const response = await api.get(`/emp/${id}`);
        empp.value = response.data;
    } catch (error) {
        console.error("Error updating store:", error);
    }
};

const getLeave = async (id) => {
    try {
        const response = await api.get(`/leave-summery/${id}`);
        leaveSummery.value = response.data;
    } catch (error) {
        console.error("Error updating store:", error);
    }
};

const getEmployeeImg = async (id) => {
    try {
        const [responseimg, responseleave] = await axios.all([
            api.get(`/empimg/${id}`),
            api.get(`/leave/${id}`),
        ]);
        emp_img.value = responseimg.data;
        leave.value = responseleave.data;
        getLeave(id);
    } catch (error) {
        console.error("Error updating store:", error);
    }
};

const totalLeaveDays = computed(() => {
    const totals = {};
    leave.value.forEach((l) => {
        const typeName = l.leave_type.Name;
        if (!totals[typeName]) {
            totals[typeName] = {
                totalDays: 0,
                maxDays: l.leave_type.Max_Days,
            };
        }
        if (l.Status === "Approved") {
            totals[typeName].totalDays += l.daysBetween;
        }
    });
    return totals;
});

const openAttachment = (url) => {
    window.open(url, "_blank");
};

onMounted(() => getData());
</script>

<template>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h1 class="mb-5">Applicant status</h1>
                    </div>
                    <div class="d-flex">
                        <div class="col-lg-4 px-2">
                            <form class="forms-sample" @submit.prevent="submit">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    ><i class="fa-regular fa-user"></i></span
                                                >
                                            </div>
                                            <input
                                                type="text"
                                                class="form-control"
                                                readonly
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"
                                                >Employee Name</label
                                            >
                                            <select
                                                class="form-control"
                                                name="status"
                                                id=""
                                                v-model="form.Employee_Id"
                                                @change="
                                                    getEmployeeImg(
                                                        form.Employee_Id
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
                            </form>
                        </div>

                        <div class="col-lg-4 px-2">
                            <table class="leave-status">
                                <thead class="table_head">
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Entitled</th>
                                        <th>Enjoyed</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tr
                                    v-for="(info, typeName) in totalLeaveDays"
                                    :key="typeName"
                                >
                                    <th>{{ typeName }}</th>
                                    <td>{{ info.maxDays }}</td>
                                    <td>{{ info.totalDays }}</td>
                                    <td>{{ info.maxDays - info.totalDays }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-lg-4 px-2">
                            <div class="d-flex justify-content-center">
                                <img
                                    :src="emp_img ? emp_img.img_url : ''"
                                    height="100%"
                                    width="100%"
                                    style="max-height: 110px; max-width: 110px"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <div class="text-center">
                        <h1 class="mb-5">Leave Summary</h1>
                    </div>

                    <div class="row d-flex justify-content-end">
                        <div class="col-lg-3">
                            <label for="exampleInputEmail1">Department</label>
                            <select v-model="selectedDept" class="form-control">
                                <option value="">All Department</option>
                                <option
                                    v-for="item in department"
                                    :key="item.id"
                                    :value="item.Name"
                                >
                                    {{ item.Name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="exampleInputEmail1">Leave Type</label>
                            <select v-model="selectedType" class="form-control">
                                <option value="">All Type</option>
                                <option
                                    v-for="item in leaveType"
                                    :key="item.id"
                                    :value="item.Name"
                                >
                                    {{ item.Name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="exampleInputEmail1">Leave Status</label>
                            <select
                                v-model="selectedStatus"
                                class="form-control"
                            >
                                <option value="">All Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Employee Name</th>
                                    <th>From Data</th>
                                    <th>To Date</th>
                                    <th>Total Days</th>
                                    <th>Leave Type</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                    <th>Operation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="ver-align"
                                    v-for="(l, index) in filteredData"
                                    :key="l.id"
                                    @click="showLeave(l.id)"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ l.Full_Name }}</td>
                                    <td>{{ l.From_Date }}</td>
                                    <td>{{ l.To_Date }}</td>
                                    <td>
                                        {{ l.daysBetween }}
                                    </td>
                                    <td>{{ l.leave_type }}</td>
                                    <td>{{ l.Status }}</td>
                                    <td v-if="l.Attachment_Url !== '/storage/'">
                                        <button
                                            @click="
                                                openAttachment(l.Attachment_Url)
                                            "
                                            class="custom-btn btn-15 mx-2"
                                        >
                                            <i
                                                class="fa-solid fa-file-arrow-down"
                                            ></i>
                                        </button>
                                    </td>
                                    <td v-else></td>
                                    <td>
                                        <button
                                            class="custom-btn btn-13 mx-1 px-1"
                                            @click="leaveApproved(l.id)"
                                        >
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        <button
                                            class="custom-btn btn-12 mx-1"
                                            @click="leaveReject(l.id)"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
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

<style>
.leave-status th,
.leave-status td {
    padding: 10px 3px;
    text-align: center;
    border: 1px solid #bcc7c7;
}

.table_head {
    color: #fff;
    font-weight: 700;
    background: #2bc3b9;
    background: -moz-linear-gradient(-45deg, #2bc3b9 0%, #036931 100%);
    background: -webkit-linear-gradient(-45deg, #2bc3b9 0%, #036931 100%);
    background: linear-gradient(135deg, #2bc3b9 0%, #036931 100%);
    white-space: nowrap;
}

.leave-status tr:nth-child(even) {
    background-color: #f2f2f2;
}

.leave-status {
    width: 100%;
}

.input-group-text{
    padding: 8px;
}

tr:hover {
    cursor: pointer;
}

tr:hover td {
    background-color: #d3d9d9 !important; 
}
</style>
