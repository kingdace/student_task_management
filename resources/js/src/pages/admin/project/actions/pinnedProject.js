import { ref } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { showErrorResponse } from "../../../../helper/util";
import { successMsg } from "../../../../helper/toast-notification";

export function usePinnedProject() {
    const loading = ref(false);
    async function pinnedProject(projectId) {
        try {
            loading.value = true;
            const data = await makeHttpReq("projects/pinned", "POST", {
                projectId: projectId,
            });
            loading.value = false;
            successMsg(data.message);
        } catch (error) {
            loading.value = false;
            showErrorResponse(error);
        }
    }

    return { pinnedProject };
}
