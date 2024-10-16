<script setup>
import { ref, onMounted } from "vue";
import api from "@/api";
import { RouterLink, RouterView } from "vue-router";

const error = ref("");
const department = ref([]);
const dashCount = ref([]);

const initializeChart = async () => {
    const [responsedept, dashCountres] = await axios.all([
        api.get("/dept-chart"),
        api.get("/dashboard-count"),
    ]);
    department.value = responsedept.data;
    dashCount.value = dashCountres.data;

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

    function getRandomColor() {
        const letters = "0123456789ABCDEF";
        let color = "#";
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    var pie = document.getElementById("myPieChart");

    var backgroundColors = department.value.map(() => getRandomColor());

    // Create the pie chart
    var myPieChart = new Chart(pie, {
        type: "pie",
        data: {
            labels: ['Active', 'Inactive'],
            datasets: [
                {
                    data: [dashCount.value.employee_act, dashCount.value.employee_inact,],
                    backgroundColor: backgroundColors, 
                },
            ],
        },
    });
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
                        <div class="card-body d-flex justify-content-between">
                            <div>Total Active Employee</div>
                            <div>{{ dashCount.employee_act }}</div>
                        </div>
                        <div
                            class="card-footer d-flex align-items-center justify-content-between"
                        >
                            <router-link
                                class="small text-white stretched-link"
                                :to="{ name: 'EmployeeList' }"
                                >View Details</router-link
                            >
                            <div class="small text-white">
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body d-flex justify-content-between">
                            <div>Total Department</div>
                            <div>{{ dashCount.department }}</div>
                        </div>
                        <div
                            class="card-footer d-flex align-items-center justify-content-between"
                        >
                            <router-link
                                class="small text-white stretched-link"
                                :to="{ name: 'EmployeeList' }"
                                >View Details</router-link
                            >
                            <div class="small text-white">
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body d-flex justify-content-between">
                            <div>Total Inactive Employee</div>
                            <div>{{ dashCount.employee_inact }}</div>
                        </div>
                        <div
                            class="card-footer d-flex align-items-center justify-content-between"
                        >
                            <router-link
                                class="small text-white stretched-link"
                                :to="{ name: 'EmployeeList' }"
                                >View Details</router-link
                            >
                            <div class="small text-white">
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body d-flex justify-content-between">
                            <div>Total Leave</div>
                            <div>{{ dashCount.leave }}</div>
                        </div>
                        <div
                            class="card-footer d-flex align-items-center justify-content-between"
                        >
                            <router-link
                                class="small text-white stretched-link"
                                :to="{ name: 'LeaveStatus' }"
                                >View Details</router-link
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
                            <i class="fas fa-chart-pie me-1"></i>
                            Department Wise Employee
                        </div>
                        <div class="card-body">
                            <canvas
                                id="myPieChart"
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
