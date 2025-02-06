<script setup>
import { onMounted } from "vue";
import ApexDonut from "./components/ApexDonut.vue";
import ApexRadialBar from "./components/ApexRadialBar.vue";
import { useGetPinnedProject } from "./actions/GetPinnedProject";
import { useGetTotalProject } from "./actions/countProject";
import { useGetChartData } from "./actions/getChartData";

const { project, getPinnedProject } = useGetPinnedProject();
const { countProject, getTotalProject } = useGetTotalProject();
const { chartData, getChartData } = useGetChartData();

onMounted(async () => {
    await getPinnedProject();
    if (project.value.id) {
        getChartData(project.value.id);
    }
    getTotalProject();
});
</script>

<template>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h4 class="dashboard-title">DASHBOARD</h4>
            <div class="project-title">
                <i>Pinned Project:</i> <b>{{ project?.title }}</b>
            </div>
        </header>

        <div class="dashboard-grid">
            <div class="dashboard-card total-projects">
                <div class="card-header">
                    <b>Total Projects</b>
                </div>
                <div class="card-body">
                    <h2>{{ countProject?.count }}</h2>
                    <span class="label">Active Projects</span>
                </div>
            </div>

            <div class="dashboard-card task-progress">
                <div class="card-header">
                    <b>Task Progress</b>
                </div>
                <div class="card-body">
                    <ApexDonut :task="chartData.tasks || [0, 0, 0]" />
                </div>
            </div>

            <div class="dashboard-card completed-tasks">
                <div class="card-header">
                    <b>Completed Tasks</b>
                </div>
                <div class="card-body">
                    <ApexRadialBar :percent="chartData.progress || 0" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.dashboard-container {
    max-width: 1200px;
    margin: 1.5rem auto;
    padding: 0 1.5rem;
}

.dashboard-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.dashboard-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.project-title {
    font-size: 1.1rem;
    color: #0b3e71;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    align-items: stretch;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease;
    height: 280px;
    display: flex;
    flex-direction: column;
}

.dashboard-card:hover {
    transform: translateY(-3px);
}

.card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #eee;
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
}

.card-body {
    flex: 1;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.total-projects,
.task-progress,
.completed-tasks {
    min-height: unset;
}

.total-projects .card-body {
    flex-direction: column;
    gap: 0.5rem;
}

.total-projects .card-body h2 {
    font-size: 4rem;
    color: #2c3e50;
    margin: -30px;
    line-height: 1;
}

.total-projects .card-body .label {
    font-size: 0.9rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 25px;
}

.task-progress .card-body,
.completed-tasks .card-body {
    padding: 0.5rem;
}

.completed-tasks .card-body {
    padding-top: 0;
}

.completed-tasks .card-body :deep(svg) {
    margin-top: -10px;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 0 1rem;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .dashboard-card {
        height: 280px;
    }
}
</style>
