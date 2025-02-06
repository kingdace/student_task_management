import { ref } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { showErrorResponse } from "../../../../helper/util";

export function useGetTotalProject() {
    const countProject = ref({ count: 0 });

    async function getTotalProject() {
        try {
            const data = await makeHttpReq("count/projects", "GET");
            countProject.value = data;
            updateData();
        } catch (error) {
            showErrorResponse(error);
        }
    }

    function updateData() {
        window.Echo.channel("countProject").listen("NewProjectCreated", (e) => {
            countProject.value = { count: e.countProject };
        });
    }

    return { countProject, getTotalProject };
}
