<script setup>
import { ref, onMounted } from "vue";
import api from "@/api";

const error = ref("");
const department = ref([]);
const dashCount = ref([]);

const getData = async () => {
    try {
        const [responsedept, dashCountres] = await axios.all([
            api.get("/department"),
            api.get("/dashboard-count"),
        ]);
        department.value = responsedept.data;
        dashCount.value = dashCountres.data;

        // Call initializeChart() only after data has been fetched
        await initializeChart();
    } catch (err) {
        error.value = err.message || "Error fetching data";
    }
};

const initializeChart = async () => {
    const [responsedept, responsetype] = await axios.all([
        api.get("/dept-chart"),
        api.get("/leave-type"),
    ]);
    department.value = responsedept.data;

    const ctx = document.getElementById("myBarChart").getContext("2d");

    if (ctx) {
        // Initialize your Chart.js instance
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: department.value.map((dept) => dept.Name),
                datasets: [
                    {
                        label: "Employee",
                        backgroundColor: "rgba(29,100,96,1)",
                        borderColor: "rgba(29,100,96,1)",
                        data: department.value.map(
                            (dept) => dept.employee_count
                        ),
                    },
                ],
            },
            options: {
                scales: {
                    xAxes: [
                        {
                            time: {
                                unit: "Department",
                            },
                            gridLines: {
                                display: false,
                            },
                        },
                    ],
                    yAxes: [
                        {
                            ticks: {
                                min: 0,
                                max: 50,
                                maxTicksLimit: 5,
                            },
                            gridLines: {
                                display: true,
                            },
                        },
                    ],
                },
                legend: {
                    display: false,
                },
            },
        });
    } else {
        console.error("Canvas element not found.");
    }
};

onMounted(() => initializeChart());
</script>

<template>
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Dashboard</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-dark text-white mb-4">
                        <div class="card-body">Total Employee</div>
                        <div
                            class="card-footer d-flex align-items-center justify-content-between"
                        >
                            <a class="small text-white stretched-link" href="#"
                                >View Details</a
                            >
                            <div class="small text-white">
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">Total Department</div>
                        <div
                            class="card-footer d-flex align-items-center justify-content-between"
                        >
                            <a class="small text-white stretched-link" href="#"
                                >View Details</a
                            >
                            <div class="small text-white">
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">Success Card</div>
                        <div
                            class="card-footer d-flex align-items-center justify-content-between"
                        >
                            <a class="small text-white stretched-link" href="#"
                                >View Details</a
                            >
                            <div class="small text-white">
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body">Total Leave</div>
                        <div
                            class="card-footer d-flex align-items-center justify-content-between"
                        >
                            <a class="small text-white stretched-link" href="#"
                                >View Details</a
                            >
                            <div class="small text-white">
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Department Wise Employee
                        </div>
                        <div class="card-body">
                            <canvas
                                id="myBarChart"
                                width="100%"
                                height="40"
                            ></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-area me-1"></i>
                            Area Chart Example
                        </div>
                        <div class="card-body">
                            <canvas
                                id="myAreaChart"
                                width="100%"
                                height="40"
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
.centered-content {
    display: flex;
    width: 100%;
    height: 100vh; /* Full viewport height */
    text-align: center;
    flex-direction: column; /* Ensure content stacks vertically */
}

h1 {
    margin: 0;
    font-size: 50px;
    font-weight: 700;
}

.highlight {
    font-size: 40px;
    font-weight: 700;
    color: #3a6b4c;
}
</style>
