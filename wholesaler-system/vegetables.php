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
  <title>Vegetable Trends</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/style.css">
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

    <h2>Vegetable Supplier Summary</h2>
    <div class="veg-cards-container" id="vegCardsContainer"></div>

    <h2>All Vegetable Trends (Multi-Line)</h2>
    <canvas id="multiTrendChart" width="800" height="400"></canvas>

    <hr>

    <h2>Single Vegetable Trend</h2>
    <div class="single-veg-dropdown">
      <label for="vegSelectFilter"><strong>Select Vegetable:</strong></label>
      <select id="vegSelectFilter">
        <option value="">-- Choose One --</option>
      </select>
    </div>
    <canvas id="singleVegChart" width="700" height="300"></canvas>
  </div>

  <script>
    // 🟢 Multi-line Chart
    function getRandomColor() {
      const r = Math.floor(Math.random() * 200);
      const g = Math.floor(Math.random() * 200);
      const b = Math.floor(Math.random() * 200);
      return `rgba(${r},${g},${b}, 0.8)`;
    }

    // Fetch and render Vegetable Supplier Summary
    fetch("php/load_vegetable_farmers.php")
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById("vegCardsContainer");

        for (const [vegName, info] of Object.entries(data)) {
          const card = document.createElement("div");
          card.className = "veg-card";

          const title = `<h3>${vegName} - ${info.total}kg</h3>`;
          let farmerList = "<ul>";
          info.farmers.forEach(farmer => {
            farmerList += `<li><span>${farmer.name}</span><span style="color: #04a809ff">${farmer.qty}kg</span></li>`;
          });
          farmerList += "</ul>";

          card.innerHTML = title + farmerList;
          container.appendChild(card);
        }
      });


    fetch("php/get_trends_by_vegetable.php")
      .then(res => res.json())
      .then(data => {
        const allDates = new Set();
        Object.values(data).forEach(veg => {
          Object.keys(veg).forEach(date => allDates.add(date));
        });
        const sortedDates = Array.from(allDates).sort();

        const datasets = [];
        for (const [veg, entries] of Object.entries(data)) {
          const dataPoints = sortedDates.map(date => entries[date] || 0);
          datasets.push({
            label: veg,
            data: dataPoints,
            borderColor: getRandomColor(),
            fill: false,
            tension: 0.2
          });
        }

        new Chart(document.getElementById('multiTrendChart').getContext('2d'), {
          type: 'line',
          data: {
            labels: sortedDates,
            datasets: datasets
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Stock Trends by Vegetable'
              }
            },
            scales: {
              x: { title: { display: true, text: 'Date' }},
              y: { title: { display: true, text: 'Quantity' }, beginAtZero: true }
            }
          }
        });
      });

    // 🟡 Single Vegetable Trend
    let singleChart;
    fetch("php/get_vegetable_names.php")
      .then(res => res.json())
      .then(data => {
        const select = document.getElementById("vegSelectFilter");
        data.forEach(veg => {
          const opt = document.createElement("option");
          opt.value = veg.veg_id;
          opt.textContent = veg.name;
          select.appendChild(opt);
        });
      });

    document.getElementById("vegSelectFilter").addEventListener("change", function () {
      const vegId = this.value;
      if (!vegId) return;

      fetch(`php/get_single_vegetable_trend.php?veg_id=${vegId}`)
        .then(res => res.json())
        .then(data => {
          const labels = data.map(row => row.date);
          const values = data.map(row => parseFloat(row.qty));
          const ctx = document.getElementById("singleVegChart").getContext("2d");

          if (singleChart) singleChart.destroy();
          singleChart = new Chart(ctx, {
            type: 'line',
            data: {
              labels: labels,
              datasets: [{
                label: 'Quantity Over Time',
                data: values,
                borderColor: 'rgba(54, 162, 235, 0.9)',
                tension: 0.2,
                fill: false
              }]
            },
            options: {
              responsive: true,
              plugins: {
                title: {
                  display: true,
                  text: 'Selected Vegetable Stock Trend'
                }
              },
              scales: {
                x: { title: { display: true, text: 'Date' }},
                y: { title: { display: true, text: 'Quantity' }, beginAtZero: true }
              }
            }
          });
        });
    });
  </script>
</body>
</html>
