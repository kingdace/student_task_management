import { ref } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { showErrorResponse } from "../../../../helper/util";

export function useGetProject() {
    const loading = ref(false);
    const ProjectData = ref({});
    async function getProjects(page = 1, query = "") {
        try {
            loading.value = true;
            const data = await makeHttpReq(
                `projects?query=${query}&page=${page}`,
                "GET"
            );
            loading.value = false;
            ProjectData.value = data;
        } catch (error) {
            loading.value = false;
            showErrorResponse(error);
        }
    }

    return { getProjects, ProjectData, loading };
}
