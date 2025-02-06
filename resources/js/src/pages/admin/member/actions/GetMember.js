import { ref } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { showErrorResponse } from "../../../../helper/util";

export function useGetMembers() {
    const loading = ref(false);
    const memberData = ref({}); // Initialize as an empty object

    async function getMembers(page = 1, query = "") {
        try {
            loading.value = true;
            const data = await makeHttpReq(
                `members?query=${query}&page=${page}&per_page=100`,
                "GET"
            );
            loading.value = false;
            memberData.value = data;
        } catch (error) {
            loading.value = false;
            showErrorResponse(error);
        }
    }

    return { getMembers, memberData, loading };
}
