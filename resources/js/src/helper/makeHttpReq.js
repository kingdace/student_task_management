import { APP } from "../App/APP";
import { getUserData } from "./getUserData";

export function makeHttpReq(endpoint, verb, input, isFormData = false) {
    return new Promise(async (resolve, reject) => {
        try {
            const userData = getUserData();
            const headers = {};

            if (!isFormData) {
                headers["content-type"] = "application/json";
            }

            if (userData?.token) {
                headers.Authorization = "Bearer " + userData.token;
            }

            const options = {
                method: verb,
                headers,
            };

            if (verb === "POST" || verb === "PUT") {
                options.body = isFormData ? input : JSON.stringify(input);
            }

            const res = await fetch(`${APP.apiBaseURL}/${endpoint}`, options);
            const data = await res.json();

            if (!res.ok) {
                reject(data.message || "Request failed");
                return;
            }
            resolve(data);
        } catch (error) {
            console.error("Request error:", error);
            reject(error.message || "Network error");
        }
    });
}
