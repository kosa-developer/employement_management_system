
$(document).ready(function () {
    for (var a = 0; a < StatusArrayData.length; a++) {
        var time = [];
        var status_code = [];
        var patientStatusData = StatusArrayData[a].status_array;
        for (var i = 0; i < patientStatusData.length; i++) {
            status_code.push(patientStatusData[i].status);
            time.push(patientStatusData[i].status_time);
        }
        var config = {
            type: 'line',
            data: {
                labels: time,
                datasets: [{
                        label: "Patients status",
                        fill: false,
                        backgroundColor: window.chartColors.blue,
                        borderColor: window.chartColors.blue,
                        data: status_code,
                    }]
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                    text: 'PATIENTS RECEIVED'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Time'
                            }
                        }],
                    yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Status'
                            }
                        }]
                }
            }
        };
        var ctx = document.getElementById("chartjs_line" + StatusArrayData[a].admission_id).getContext("2d");
        window.myLine = new Chart(ctx, config);
    }
});

//Monthly report
$(document).ready(function () {
    //var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var MONTHS = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];
    var data_set = [];
    for (var i = 0; i < MonthlypatientsArrayData.length; i++) {
        data_set.push(MonthlypatientsArrayData[i].total);
    }
    var config = {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{
                    label: "Patients",
                    fill: false,
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.blue,
                    data: data_set,
                }]
        },
        options: {
            responsive: true,
            title: {
                display: false,
                text: 'PATIENTS RECEIVED'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    }],
                yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Patients'
                        }
                    }]
            }
        }
    };
    var ctx = document.getElementById("monthly_graph_report").getContext("2d");
    window.myLine = new Chart(ctx, config);
});
//Death Occurances
$(document).ready(function () {
    var MONTHS = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];
    var data_set = [];
    for (var i = 0; i < DeathOccuranceArrayData.length; i++) {
        data_set.push(DeathOccuranceArrayData[i].total);
    }
    var config = {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{
                    label: "Patients",
                    fill: false,
                    backgroundColor: window.chartColors.red,
                    borderColor: window.chartColors.red,
                    data: data_set,
                }]
        },
        options: {
            responsive: true,
            title: {
                display: false,
                text: 'PATIENTS'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    }],
                yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Deaths Occurances'
                        }
                    }]
            }
        }
    };
    var ctx = document.getElementById("death_report_graph").getContext("2d");
    window.myLine = new Chart(ctx, config);
});

$(document).ready(function () {
    var data_set = [],labels=[];
    for (var i = 0; i < DailypatientsArrayData.length; i++) {
        data_set.push(DailypatientsArrayData[i].total);
        labels.push(DailypatientsArrayData[i].label);
    }
    var config = {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: "Patients",
                    fill: false,
                    backgroundColor: window.chartColors.purple,
                    borderColor: window.chartColors.purple,
                    data: data_set,
                }]
        },
        options: {
            responsive: true,
            title: {
                display: false,
                text: 'PATIENTS RECEIVED'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Day'
                        }
                    }],
                yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Patients'
                        }
                    }]
            }
        }
    };
    var ctx = document.getElementById("daily_graph_report").getContext("2d");
    window.myLine = new Chart(ctx, config);
});


$(document).ready(function () {
    var randomScalingFactor = function () {
        return Math.round(Math.random() * 100);
    };

    var config = {
        type: 'pie',
        data: {
            datasets: [{
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                    ],
                    backgroundColor: [
                        window.chartColors.red,
                        window.chartColors.orange,
                        window.chartColors.yellow,
                        window.chartColors.green,
                        window.chartColors.blue,
                    ],
                    label: 'Dataset 1'
                }],
            labels: [
                "Red",
                "Orange",
                "Yellow",
                "Green",
                "Blue"
            ]
        },
        options: {
            responsive: true
        }
    };

    var ctx = document.getElementById("chartjs_pie").getContext("2d");
    window.myPie = new Chart(ctx, config);
});

$(document).ready(function () {
    var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var color = Chart.helpers.color;
    var barChartData = {
        labels: ["January", "February", "March", "April", "May", "June", "July"],
        datasets: [{
                label: 'New Patients',
                backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                borderColor: window.chartColors.red,
                borderWidth: 1,
                data: [
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor()
                ]
            }, {
                label: 'Old Patients',
                backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                borderColor: window.chartColors.blue,
                borderWidth: 1,
                data: [
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor()
                ]
            }]

    };

    var ctx = document.getElementById("chartjs_bar").getContext("2d");
    window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Bar Chart'
            }
        }
    });

});

$(document).ready(function () {
    var randomScalingFactor = function () {
        return Math.round(Math.random() * 100);
    };

    var chartColors = window.chartColors;
    var color = Chart.helpers.color;
    var config = {
        data: {
            datasets: [{
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                    ],
                    backgroundColor: [
                        color(chartColors.red).alpha(0.5).rgbString(),
                        color(chartColors.orange).alpha(0.5).rgbString(),
                        color(chartColors.yellow).alpha(0.5).rgbString(),
                        color(chartColors.green).alpha(0.5).rgbString(),
                        color(chartColors.blue).alpha(0.5).rgbString(),
                    ],
                    label: 'My dataset' // for legend
                }],
            labels: [
                "Red",
                "Orange",
                "Yellow",
                "Green",
                "Blue"
            ]
        },
        options: {
            responsive: true,
            legend: {
                position: 'right',
            },
            title: {
                display: true,
                text: 'Polar Area Chart'
            },
            scale: {
                ticks: {
                    beginAtZero: true
                },
                reverse: false
            },
            animation: {
                animateRotate: false,
                animateScale: true
            }
        }
    };

    var ctx = document.getElementById("chartjs_polar");
    window.myPolarArea = Chart.PolarArea(ctx, config);

});

$(document).ready(function () {
    var randomScalingFactor = function () {
        return Math.round(Math.random() * 100);
    };

    var config = {
        type: 'doughnut',
        data: {
            datasets: [{
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                    ],
                    backgroundColor: [
                        window.chartColors.red,
                        window.chartColors.orange,
                        window.chartColors.yellow,
                        window.chartColors.green,
                        window.chartColors.blue,
                    ],
                    label: 'Dataset 1'
                }],
            labels: [
                "Red",
                "Orange",
                "Yellow",
                "Green",
                "Blue"
            ]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Doughnut Chart'
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    };

    var ctx = document.getElementById("chartjs_doughnut").getContext("2d");
    window.myDoughnut = new Chart(ctx, config);

});

$(document).ready(function () {
    var randomScalingFactor = function () {
        return Math.round(Math.random() * 100);
    };

    var color = Chart.helpers.color;
    var config = {
        type: 'radar',
        data: {
            labels: [["Eating", "Dinner"], ["Drinking", "Water"], "Sleeping", ["Designing", "Graphics"], "Coding", "Cycling", "Running"],
            datasets: [{
                    label: "New Patients",
                    backgroundColor: color(window.chartColors.red).alpha(0.2).rgbString(),
                    borderColor: window.chartColors.red,
                    pointBackgroundColor: window.chartColors.red,
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor()
                    ]
                }, {
                    label: "Old Patients",
                    backgroundColor: color(window.chartColors.blue).alpha(0.2).rgbString(),
                    borderColor: window.chartColors.blue,
                    pointBackgroundColor: window.chartColors.blue,
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor()
                    ]
                }, ]
        },
        options: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Radar Chart'
            },
            scale: {
                ticks: {
                    beginAtZero: true
                }
            }
        }
    };

    window.myRadar = new Chart(document.getElementById("radar_chart"), config);


});

