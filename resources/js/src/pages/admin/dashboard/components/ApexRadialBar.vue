<template>
    <apexchart
        height="200"
        width="100%"
        type="radialBar"
        :options="options"
        :series="series"
    ></apexchart>
</template>

<script>
import ApexCharts from "apexcharts";

export default {
    name: "ApexRadialBar",
    props: {
        percent: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            options: {
                chart: {
                    height: 200,
                    type: "radialBar",
                    offsetY: -10,
                    background: "transparent",
                },
                colors: ["#4caf50"],
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        hollow: {
                            margin: 15,
                            size: "70%",
                        },
                        track: {
                            background: "#e7e7e7",
                            strokeWidth: "97%",
                            margin: 5,
                            dropShadow: {
                                enabled: true,
                                top: 2,
                                left: 0,
                                blur: 4,
                                opacity: 0.15,
                            },
                        },
                        dataLabels: {
                            name: {
                                show: false,
                            },
                            value: {
                                offsetY: 10,
                                fontSize: "22px",
                                formatter: function (val) {
                                    return val + "%";
                                },
                            },
                        },
                    },
                },
                responsive: [
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                height: 200,
                            },
                        },
                    },
                ],
            },
            series: [this.percent || 0],
        };
    },
    watch: {
        percent(newVal) {
            this.series = [newVal || 0]; // Update series when percent changes
        },
    },
};
</script>
