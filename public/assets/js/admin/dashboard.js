class AdminDashboard
{
    initialize()
    {
        this.presentTopTutors();
        this.presentImpairmentRatio();
        this.presentGroupedImpairments();
    }

    hide(elementSelector)
    {
        document.querySelector(elementSelector).classList.add('d-none');
    }

    presentTopTutors()
    {
        if (!document.getElementById('chartData-top-tutors'))
        {
            this.hide('.top-tutors-col');
            return;
        }

        let chartData = document.querySelector('#chartData-top-tutors').value.trim();

        if (!chartData)
        {
            this.hide('.top-tutors-col');
            return;
        }

        chartData = JSON.parse(chartData);

        let chartProps = {
            'labels': [],
            'data': []
        };

        let contest = {};

        chartData.forEach((data, index) => {

            contest[data.totalLearners] = {
                'tutorName': data.tutorName,
                'tutorPhoto': data.tutorPhoto,
                'tutorDetails': data.tutorDetails
            };

            chartProps.labels.push(data.tutorName);
            chartProps.data.push(data.totalLearners);

            $('.photos').append(`<div class="photo-item d-block text-center">
                                          <img src="${data.tutorPhoto}" alt="photo">
                                          <p class="mb-1">
                                              <strong class="text-12 text-truncate">${data.tutorFname}</strong>
                                          </p>
                                      </div>`);
        });

        // Display all top 5 tutors
        let ctx = document.getElementById('topTutorsChart').getContext('2d');
        let chartOptions = {
            type: 'bar',
            data: {
                labels: chartProps.labels,
                datasets: [{
                    label: "Tutor's Learners",
                    data: chartProps.data,
                    backgroundColor: [
                        '#6f42c1',
                        '#9F81D5',
                        '#6f42c1',
                        '#9F81D5',
                        '#6f42c1'
                    ],
                    barThickness: 16,
                    borderColor: [
                        '#6610f2',
                        '#9F6DD5',
                        '#6610f2',
                        '#9F6DD5',
                        '#6610f2',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    y: {
                        ticks: {
                            font: {
                                weight: 'bold', // Make x-axis labels bold,
                            },
                            color: '#2E244D'
                        }
                    },
                    x: {
                        beginAtZero: true
                    }
                }
            }
        };
        let topTutorsChart = new Chart(ctx, chartOptions);
    }

    presentImpairmentRatio()
    {
        let dataSourceImpaired = document.querySelector('#chartdata-impared-ratio').value;

        if (dataSourceImpaired.trim() !== '')
        {
            dataSourceImpaired = JSON.parse(dataSourceImpaired);

            if (Object.keys(dataSourceImpaired).length > 0)
            {
                let ctx = document.getElementById('impairedRatioChart').getContext('2d');
                this.toDoughnutChart(ctx, dataSourceImpaired, 'With Impairments');
            }
        }

        let dataSourceNonImpaired = document.querySelector('#chartdata-nonimpared-ratio').value;

        if (dataSourceNonImpaired.trim() !== '')
        {
            dataSourceNonImpaired = JSON.parse(dataSourceNonImpaired);

            if (Object.keys(dataSourceNonImpaired).length > 0)
            {
                let ctx = document.getElementById('nonImpairedRatioChart').getContext('2d');
                this.toDoughnutChart(ctx, dataSourceNonImpaired, 'Without Impairments', ['#DC3545', '#FFA30E']);
            }
        }

    }

    presentGroupedImpairments()
    {
        let dataSource = document.querySelector('#chartdata-grouped-impairments').value;

        if (dataSource.trim() === '')
        {
            this.hide('.groupedImpairmentsCol');
            return;
        }

        let chartData = JSON.parse(dataSource);

        if (!chartData)
        {
            this.hide('.groupedImpairmentsCol');
            return;
        }

        console.log(chartData);

        let learnersDataset = [
            parseInt(chartData[0].total_deaf_and_mute_learners),
            parseInt(chartData[0].total_deaf_learners),
            parseInt(chartData[0].total_mute_learners)
        ];

        let tutorsDataset = [
            parseInt(chartData[0].total_deaf_and_mute_tutors),
            parseInt(chartData[0].total_deaf_tutors),
            parseInt(chartData[0].total_mute_tutors)
        ];

        chartData = {
            labels: ['Deaf & Mute', 'Deaf', 'Mute'],
            datasets: [
                {
                    label: 'Learners',
                    data: learnersDataset,
                    borderColor: '#FDAA57',
                    backgroundColor: '#FF7701',
                    barThickness: 20
                },
                {
                    label: 'Tutors',
                    data: tutorsDataset,
                    borderColor: '#023FA8',
                    backgroundColor: '#0D6EFD',
                    barThickness: 20
                }
            ]
        };

        const config = {
            type: 'bar',
            data: chartData,
            options:
            {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Comparison of Impairments'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            },
        };

        let ctx = document.getElementById('groupedImpairmentsChart').getContext('2d');
        let chart = new Chart(ctx, config);
    }


    toDoughnutChart(ctx, dataSource, title, colors)
    {
        colors = colors || ['#007E7E', '#FA6127'];

        let data = {
            type: 'doughnut',
            labels: Object.keys(dataSource),
            datasets: [
                {
                    data: Object.values(dataSource),
                    backgroundColor: colors
                }
            ]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                cutout: '70%',
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: title
                    }
                }
            }
        });
    }
}

$(document).ready(function ()
{
    let driver = new AdminDashboard();
    driver.initialize();
});
