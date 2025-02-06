<script setup>
defineProps({
    ProjectData: Object,
});
</script>
<template>
    <div class="project-header">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-folder2-open text-primary fs-4"></i>
            <h2 class="project-title mb-0">{{ ProjectData?.data?.title }}</h2>
        </div>
        <div class="project-dates">
            <span class="date-item">
                <i class="bi bi-calendar2-check text-success"></i>
                Start: <strong>{{ ProjectData?.data?.startDate }}</strong>
            </span>
            <span class="date-separator">•</span>
            <span class="date-item">
                <i class="bi bi-calendar2-x text-danger"></i>
                End: <strong>{{ ProjectData?.data?.endDate }}</strong>
            </span>
        </div>
    </div>
</template>

<style scoped>
.project-header {
    padding: 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 1.5rem;
}

.project-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
}

.project-dates {
    color: #64748b;
    font-size: 1rem;
}

.date-item {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
}

.date-item i {
    font-size: 1.1rem;
}

.date-item strong {
    color: #334155;
    font-weight: 600;
}

.date-separator {
    margin: 0 1.5rem;
    color: #cbd5e1;
}
</style>
