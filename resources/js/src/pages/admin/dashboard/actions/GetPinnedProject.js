import { ref } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { showErrorResponse } from "../../../../helper/util";

export function useGetPinnedProject() {
    const project = ref({ id: null, name: "" });

    async function getPinnedProject() {
        try {
            const { data } = await makeHttpReq("pinned/projects", "GET");
            project.value = data;
        } catch (error) {
            showErrorResponse(error);
        }
    }

    return { getPinnedProject, project };
}
