<template>
    <div
        class="modal fade"
        id="taskDetailsModal"
        tabindex="-1"
        aria-labelledby="taskDetailsModalLabel"
        :aria-modal="true"
        role="dialog"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #007bff">
                    <h5
                        class="modal-title text-white"
                        id="taskDetailsModalLabel"
                    >
                        Task Details
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        @click="$emit('closeModal')"
                    ></button>
                </div>
                <div class="modal-body">
                    <TaskDetails :task="task" @refresh="$emit('refresh')" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import TaskDetails from "./TaskDetails.vue";

defineProps({
    task: {
        type: Object,
        required: true,
    },
});

defineEmits(["closeModal", "refresh"]);
</script>

<style scoped>
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    border-bottom: none;
    padding: 1rem 1.5rem;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.modal-body {
    padding: 1.5rem;
    background-color: #f8f9fa;
}

.modal {
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: block;
}
</style>
