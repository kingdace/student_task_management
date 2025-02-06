import { ref } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { taskStore } from "../store/kabanStore.js";
import { successMsg } from "../../../../helper/toast-notification";
import { showErrorResponse, closeModal } from "../../../../helper/util";

export function useCreateTask() {
    const loading = ref(false);

    async function createTask() {
        try {
            loading.value = true;
            const response = await makeHttpReq(
                "tasks",
                "POST",
                taskStore.taskInput
            );
            loading.value = false;
            successMsg(response.message);
            closeModal("taskModal");
            taskStore.resetTaskInput();
            return response.task;
        } catch (error) {
            loading.value = false;
            showErrorResponse(error);
            return null;
        }
    }

    async function updateTask(taskId, taskData) {
        try {
            loading.value = true;
            const data = await makeHttpReq(`tasks/${taskId}`, "PUT", taskData);
            loading.value = false;
            successMsg(data.message);
            return true;
        } catch (error) {
            loading.value = false;
            showErrorResponse(error);
            return false;
        }
    }

    return { createTask, updateTask, loading };
}
