<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.html");
    exit();
}
?>


<!DOCTYPE html>
<html>

  <head>
    <title>GreenFlow</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>

  <body>

    <div class="sidebar">
      <h2>GreenFlow</h2>
      <a href="index.php">Dashboard</a>
      <a href="farmers.php">Farmers</a>
      <a href="vegetables.php">Vegetables</a>
      <a href="entry.php">Manage Data</a>
      <a href="php/export_pdf.php" class="btn">Download PDF Report</a>
      <a href="php/logout.php">Logout</a>
    </div>

    <div class="main">
      <h1>Dashboard</h1>
      <div id="summary" class="summary-cards"></div>


      <!-- Vegitable Stock Chart -->
      <div class="chart-grid">
      <!-- Left chart -->
      <div class="chart-card">
        <h2>Vegetable Stock Chart</h2>
        <label for="chartType">Chart Type:</label>
        <select id="chartType">
          <option value="bar">Bar</option>
          <option value="pie">Pie</option>
        </select>
        <canvas id="stockChart" height="300"></canvas>
      </div>

      <!-- Right chart -->
      <div class="chart-card">
        <h2>Stock Quantity Trend</h2>
        <canvas id="trendChart" height="300"></canvas>
      </div>
    </div>



    </div>

    <!-- JavaScript -->
    <script>
      let vegChart; // store chart globally

      function drawVegChart(labels, data, type = 'bar') {
        const ctx = document.getElementById('stockChart').getContext('2d');

        // If chart already exists, destroy it
        if (vegChart) vegChart.destroy();

        vegChart = new Chart(ctx, {
          type: type,
          data: {
            labels: labels,
            datasets: [{
              label: 'Total Quantity',
              data: data,
              backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)',
                'rgba(200, 200, 200, 0.6)',
                'rgba(100, 200, 100, 0.6)',
                'rgba(200, 100, 100, 0.6)',
                'rgba(100, 100, 200, 0.6)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: type === 'pie' },
              title: {
                display: true,
                text: 'Vegetable Stock Summary'
              }
            },
            scales: type !== 'pie' ? {
              y: {
                beginAtZero: true,
                title: { display: true, text: 'Quantity' }
              },
              x: {
                title: { display: true, text: 'Vegetables' }
              }
            } : {}
          }
        });
      }

      // Initial chart + switch event
      window.onload = function () {
        fetch("php/get_dashboard.php")
          .then(res => res.json())
          .then(data => {
            const labels = data.vegetables.map(item => item.name);
            const values = data.vegetables.map(item => parseFloat(item.total_qty));

            // Draw initial bar chart
            drawVegChart(labels, values, 'bar');

            // Chart type switch
            document.getElementById("chartType").addEventListener("change", function () {
              const selectedType = this.value;
              drawVegChart(labels, values, selectedType);
            });

            // Generate summary cards
            const summary = document.getElementById("summary");

            let html = `
              <div class="card">
                <h3>Total Farmers</h3>
                <p style="color: #04a809ff">${data.farmer_summary}</p>
              </div>
              <div class="card">
                <h3>Total Vegetable Types</h3>
                <p style="color: #04a809ff">${data.vegetable_count}</p>
              </div>
              <div class="card">
                <h3>Total Vegetable Quantity</h3>
                <p style="color: #04a809ff">${data.total_quantity} kg</p>
              </div>
              <div class="card full">
                <h3>Vegetable Stock Summary</h3>
                <div class="stock-summary-columns">
            `;

            const colCount = 3;
            const itemsPerCol = Math.ceil(data.vegetables.length / colCount);

            for (let i = 0; i < colCount; i++) {
              html += `<ul>`;
              for (let j = i * itemsPerCol; j < (i + 1) * itemsPerCol && j < data.vegetables.length; j++) {
                const item = data.vegetables[j];
                html += `
                  <li>
                    <span class="veg-name">${item.name}</span>
                    <span class="veg-qty" style="color: #04a809ff">${item.total_qty} kg</span>
                  </li>`;
              }
              html += `</ul>`;
            }

            html += `</div></div>`;
            summary.innerHTML = html;
          });
      }


      // Fetch and draw line chart (quantity trends by date)
        fetch("php/get_trends.php")
          .then(res => res.json())
          .then(data => {
            const trendDates = data.map(item => item.date);
            const trendQuantities = data.map(item => parseFloat(item.quantity));

            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
              type: 'line',
              data: {
                labels: trendDates,
                datasets: [{
                  label: 'Total Quantity per Day',
                  data: trendQuantities,
                  fill: false,
                  borderColor: 'rgba(255, 99, 132, 1)',
                  tension: 0.1
                }]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: { display: true },
                  title: {
                    display: true,
                    text: 'Stock Trends Over Time'
                  }
                },
                scales: {
                  x: { title: { display: true, text: 'Date' }},
                  y: { title: { display: true, text: 'Quantity' }, beginAtZero: true }
                }
              }
            });
          });

    </script>


  </body>
</html>
