import { ref } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { showErrorResponse } from "../../../../helper/util";

export function useGetChartData() {
    const chartData = ref({ tasks: [0, 0, 0], progress: 0 });

    async function getChartData(projectId) {
        if (!projectId) return;
        try {
            const data = await makeHttpReq(
                `chart-data/projects?projectId=${projectId}`,
                "GET"
            );
            chartData.value = data;
            updateData();
        } catch (error) {
            showErrorResponse(error);
        }
    }

    function updateData() {
        window.Echo.channel("projectProgress").listen(
            "TrackProjectProgress",
            (e) => {
                chartData.value.progress = 0;
                setTimeout(
                    () => (chartData.value.progress = e.projectProgress),
                    2000
                );
            }
        );

        window.Echo.channel("tasks").listen(
            "TrackCompletedAndPendingTask",
            (e) => {
                chartData.value.tasks = [0, 0, 0];
                setTimeout(() => (chartData.value.tasks = e.tasks), 2000);
            }
        );
    }

    return { chartData, getChartData };
}
