import { ref } from "vue";
import { makeHttpReq } from "../../../../../helper/makeHttpReq";
import { showErrorResponse } from "../../../../../helper/util";
import { successMsg } from "../../../../../helper/toast-notification";

export const TaskStatus = {
    NOT_STARTED: 0,
    PENDING: 1,
    COMPLETED: 2,
};

/**
 * @typedef {Object} Member
 * @property {number} id
 * @property {string} name
 * @property {string} email
 * @property {string} created_at
 * @property {string} updated_at
 */

/**
 * @typedef {Object} TaskMember
 * @property {number} id
 * @property {number} projectId
 * @property {number} taskId
 * @property {number} memberId
 * @property {string} created_at
 * @property {string} updated_at
 * @property {Member} members
 */

/**
 * @typedef {Object} Task
 * @property {number} id
 * @property {number} projectId
 * @property {string} name
 * @property {number} status
 * @property {string} created_at
 * @property {string} updated_at
 * @property {TaskMember[]} task_members
 */

/**
 * @typedef {Object} TaskProgress
 * @property {number} id
 * @property {number} projectId
 * @property {string} progress
 * @property {number} pinned_on_dashbaord
 * @property {string} created_at
 * @property {string} updated_at
 */

/**
 * @typedef {Object} ProjectData
 * @property {number} id
 * @property {string} name
 * @property {number} status
 * @property {string} startDate
 * @property {string} endDate
 * @property {string} slug
 * @property {string} created_at
 * @property {string} updated_at
 * @property {Task[]} tasks
 * @property {TaskProgress} task_progress
 */

/**
 * @typedef {Object} SingleProjectResponse
 * @property {ProjectData} data
 */

export function useGetProjectDetail() {
    /** @type {import('vue').Ref<SingleProjectResponse>} */
    const ProjectData = ref({});
    const loading = ref(false);

    /**
     * Fetches project details by slug
     * @param {string} slug - The project slug
     */
    async function getProjectDetail(slug) {
        try {
            loading.value = true;
            const data = await makeHttpReq(`projects/${slug}`, "GET");
            loading.value = false;
            ProjectData.value = data;
        } catch (error) {
            loading.value = false;
            showErrorResponse(error);
        }
    }

    return { getProjectDetail, ProjectData, loading };
}
