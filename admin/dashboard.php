<?php 
include '../conx.php'; //to go back ../ to go in admin/conx.php
include 'queries.php';
include 'header.php';
?>

<style>
    .content {
        padding: 2rem 3rem;
    }

    h1 {
        margin-bottom: 2.5rem;
        color: #2c3e50;
        font-size: 2rem;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
        padding: 0.5rem;
    }

    .stat {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        animation: scaleIn 0.5s ease forwards;
        opacity: 0;
    }

    .stat:nth-child(1) { animation-delay: 0.1s; }
    .stat:nth-child(2) { animation-delay: 0.2s; }
    .stat:nth-child(3) { animation-delay: 0.3s; }
    .stat:nth-child(4) { animation-delay: 0.4s; }

    .stat h2 {
        color: #666;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .stat p {
        color: #1a2a6c;
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0;
    }

    .charts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 4rem;  /* Increased from 3rem */
        padding: 1.5rem;
    }

    .chart {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        animation: slideUp 0.5s ease forwards;
        opacity: 0;
        width: 100% !important;
        display: block !important;
        margin: 3rem auto !important;  /* Increased from 2rem */
        min-height: 300px;  /* Increased from 280px */
        max-width: 450px;
    }

    .chart + .chart {
        margin-top: 4rem !important;  /* Increased from 3rem */
    }

    .chart:nth-child(1) { animation-delay: 0.5s; }
    .chart:nth-child(2) { animation-delay: 0.6s; }
    .chart:nth-child(3) { animation-delay: 0.7s; }

    .chart h2 {
        margin-bottom: 1.5rem;
    }

    @keyframes scaleIn {
        from { 
            transform: scale(0.9);
            opacity: 0;
        }
        to { 
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .content {
            padding: 1.5rem;
        }
        
        .stats {
            gap: 1rem;
        }
        
        .charts {
            grid-template-columns: 1fr;
        }

        .chart {
            min-height: 250px;
        }
    }
</style>

    <div class="content">
        <h1>Dashboard</h1>
        <div class="stats">
			<div class="stat">
				<h2>Total Users</h2>
				<p><?=$nbusers?></p>
			</div>
			<div class="stat">
				<h2>Total Items</h2>
				<p><?=$nbItems?></p>
			</div>
			<div class="stat">
				<h2>Total Categories</h2>
				<p><?=$nbCategories?></p>
			</div>
			<div class="stat">
				<h2>Total Plans</h2>
				<p><?=$nbPlans?><p>
			</div>
		</div>

		<div class="charts">
			<div class="chart">
				<h2>Users Chart</h2>
				<canvas id="usersChart"></canvas>
			</div>
			<div class="chart">
				<h2>Items Chart</h2>
				<canvas id="itemsChart"></canvas>
			</div>
			<div class="chart">
				<h2>Categories Chart</h2>
				<canvas id="categoriesChart"></canvas>
			</div>
		</div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const usersChartCtx = document.getElementById('usersChart').getContext('2d');
    const itemsChartCtx = document.getElementById('itemsChart').getContext('2d');
    const categoriesChartCtx = document.getElementById('categoriesChart').getContext('2d');

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.5,
    };

    const usersChart = new Chart(usersChartCtx, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: '# of Users',
                data: [12, 19, 3, 5, 2, 3, 7],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: chartOptions
    });

    const itemsChart = new Chart(itemsChartCtx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: '# of Items',
                data: [15, 29, 5, 10, 4, 6, 8],
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: chartOptions
    });

    const categoriesChart = new Chart(categoriesChartCtx, {
        type: 'pie',
        data: {
            labels: ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
            datasets: [{
                label: '# of Categories',
                data: [10, 20, 30, 40],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            ...chartOptions,
            aspectRatio: 1.2
        }
    });
</script>
</body>
</html>
