<script setup>
defineProps({
    ProjectData: Object,
});
</script>
<template>
    <div class="progress-wrapper">
        <div class="progress-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow text-primary"></i>
                <span class="progress-label">Project Progress</span>
            </div>
            <span class="progress-percentage">
                <i class="bi bi-percent"></i>
                {{ ProjectData?.data?.task_progress?.progress }}%
            </span>
        </div>
        <div class="progress" role="progressbar">
            <div
                class="progress-bar"
                :style="{
                    width:
                        parseInt(ProjectData?.data?.task_progress?.progress) +
                        '%',
                }"
            ></div>
        </div>
    </div>
</template>

<style scoped>
.progress-wrapper {
    padding: 1.5rem 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 1.5rem;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.progress-label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.1rem;
}

.progress-percentage {
    font-weight: 700;
    color: #2c503e;
    font-size: 1.1rem;
    background: #f8fafc;
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

.progress {
    height: 0.85rem;
    background-color: #f1f5f9;
    border-radius: 1rem;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(45deg, #3b82f6, #01f52e);
    transition: width 0.6s ease;
}
</style>
