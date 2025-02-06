import { ref } from "vue";
import { makeHttpReq } from "../../../helper/makeHttpReq";
import { showError, successMsg } from "../../../helper/toast-notification";
import { showErrorResponse } from "../../../helper/util";

export function useLogoutUser() {
    const loading = ref(false);

    async function logout(userId) {
        try {
            loading.value = true;
            const data = await makeHttpReq("logout", "POST", {
                userId: userId,
            });
            loading.value = false;
        } catch (error) {
            console.log(error);
            showErrorResponse(error);
            if (error.message == "Not authenticated") {
                window.location.href = "/app/login";
            }
            loading.value = false;
        }
    }

    return { logout, loading };
}
