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
    <title>Farmers</title>
    <link rel="stylesheet" href="css/style.css" />
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
      <h1>Farmers</h1>
      <?php if (isset($_GET['deleted'])): ?>
        <div id="successMessage" style="color: red; background: #e8f5e9; padding: 10px; border-left: 5px solid rgb(175, 76, 76);">
          Farmer deleted successfully.
        </div>
      <?php endif; ?>


      <div class="filter-box">
        <input type="text" id="searchFarmer" placeholder="Search by farmer name..." autocomplete="off">
        <div id="suggestions" class="suggestions-box"></div>
        <button id="clearButton" onclick="document.getElementById('searchFarmer').value=''; loadFarmers();">Clear</button>
      </div>

      

      <table>
        <thead>
          <tr>
            <th>Farmer Name</th>
            <th>Contact</th>
            <th>Location</th>
            <th>Vegetables Sold</th>
            <th>Quantities</th>
            <th>Total Price (Rs.)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="farmerTableBody">
          <?php include 'php/load_farmers.php'; ?>
        </tbody>
      </table>


    </div>

    <!-- Java Script -->
    <script>
      document.getElementById('searchFarmer').addEventListener('input', function () {
        const query = this.value.trim();
        const suggestionsBox = document.getElementById('suggestions');

        if (query.length === 0) {
          suggestionsBox.style.display = 'none';
          loadFarmers(); // Load all if cleared
          return;
        }

        fetch('php/search_farmers.php?q=' + encodeURIComponent(query))
          .then(res => res.json())
          .then(data => {
            suggestionsBox.innerHTML = '';
            if (data.length === 0) {
              suggestionsBox.style.display = 'none';
              return;
            }

            data.forEach(item => {
              const div = document.createElement('div');
              div.textContent = item.name;
              div.addEventListener('click', () => {
                document.getElementById('searchFarmer').value = item.name;
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
                loadFarmers(item.name); // filter by selected name
              });
              suggestionsBox.appendChild(div);
            });

            suggestionsBox.style.display = 'block';
          });
      });

      // Load all or filtered farmers
      function loadFarmers(search = '') {
        fetch('php/load_farmers.php' + (search ? '?search=' + encodeURIComponent(search) : ''))
          .then(res => res.text())
          .then(html => {
            document.getElementById('farmerTableBody').innerHTML = html;
          });
      }

      // Load all by default on page load
      window.onload = function () {
        loadFarmers();
      };

      window.onload = function () {
        const msg = document.getElementById("successMessage");
        if (msg) {
          setTimeout(() => {
            msg.style.display = "none";
          }, 2000); // 2 seconds
        }
      };
      </script>


  </body>
</html>
