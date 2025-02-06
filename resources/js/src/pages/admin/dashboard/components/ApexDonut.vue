<template>
    <apexchart
        height="200"
        width="100%"
        type="pie"
        :options="options"
        :series="series"
    ></apexchart>
</template>

<script>
import ApexCharts from "apexcharts";

export default {
    name: "ApexDonut",
    props: {
        task: { type: Array, required: true },
    },
    data() {
        return {
            options: {
                chart: {
                    id: "apex-donut",
                    background: "transparent",
                },
                labels: ["On Progress", "Completed", "Not Started"],
                colors: ["#ffa500", "#4caf50", "#0096FF"],
                legend: {
                    position: "bottom",
                    fontSize: "13px",
                    offsetY: 0,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: "75%",
                        },
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toFixed(1) + "%";
                    },
                },
                responsive: [
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200,
                            },
                            legend: {
                                position: "bottom",
                            },
                        },
                    },
                ],
            },
            series: this.task || [0, 0, 0],
        };
    },
    watch: {
        task(newVal) {
            this.series = newVal || [0, 0, 0];
        },
    },
};
</script>
