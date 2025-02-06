import { ref } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { successMsg } from "../../../../helper/toast-notification";
import { showErrorResponse } from "../../../../helper/util";
import { projectStore } from "../store/projectStore";

export function useCreateOrUpdateProject() {
    const loading = ref(false);
    async function createOrUpdate() {
        try {
            loading.value = true;
            const data = projectStore.edit
                ? await updateProject()
                : await createProject();

            loading.value = false;
            projectStore.projectInput = {
                id: null,
                title: "",
                startDate: "",
                endDate: "",
            };
            successMsg(data.message);
        } catch (error) {
            loading.value = false;
            showErrorResponse(error);
        }
    }

    return { createOrUpdate, loading };
}

async function createProject() {
    const data = await makeHttpReq(
        "projects",
        "POST",
        projectStore.projectInput
    );

    return data;
}

async function updateProject() {
    const data = await makeHttpReq(
        "projects",
        "PUT",
        projectStore.projectInput
    );
    projectStore.edit = false;
    return data;
}
