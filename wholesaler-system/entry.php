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
    <title>Manage Data</title>
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


      <h1>Manage Data</h1>
      <?php if (isset($_GET['success'])): ?>
        <div id="successMessage" class="success-msg">
          Farmer added successfully.
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['stock_success'])): ?>
        <div id="successMessage" class="success-msg">
          Stock added successfully.
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['veg_success'])): ?>
        <div class="success-msg" id="successMessage">
          Vegetable added successfully!
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['update'])): ?>
        <div class="success-msg">Stock updated successfully!</div>
      <?php endif; ?>

      <?php if (isset($_GET['deleted'])): ?>
        <div class="success-msg" id="successMessage">
          Stock deleted successfully!
        </div>
      <?php endif; ?>

      <h2>Existing Stock</h2>
      <div class="stock-search-box" style="display: flex; justify-content: left; align-items: center;">
        <input type="text" class="searchStock" id="stockSearchInput" placeholder="Search by farmer or vegetable name..." autocomplete="off" />
        <button id="clearStockSearch"  onclick="document.getElementById('stockSearchInput').value=''; loadStocks();">Clear</button>
      </div>

      

     
      
      <table class="data-table">
        <thead>
          <tr>
            <th>Farmer</th>
            <th>Vegetable</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="stockTable"></tbody>
      </table>
      <div id="pagination" class="pagination-container"></div>


<!-- Stock Adding Form -->
      <h2>Add New Stock</h2>
      <form action="php/add_stock.php" method="post">
        <label>Farmer:</label>
        <select name="farmer_id" id="farmerSelect" required></select>

        <label>Vegetable:</label>
        <select name="veg_id" id="vegSelect" required></select>

        <input
          type="number"
          step="0.01"
          name="quantity"
          placeholder="Quantity"
          required
        />
        <input
          type="number"
          step="0.01"
          name="price"
          placeholder="Price"
          required
        />
        <input type="date" name="date" required />
        <input type="submit" value="Add Stock" />
      </form>
        
        <div>
          <!-- Farmer Adding Form -->
          <h2>Add New Farmer</h2>
          <form action="php/add_farmer.php" method="post">
            <input type="text" name="name" placeholder="Name" required />
            <input type="text" name="contact" placeholder="Contact" />
            <input type="text" name="location" placeholder="Location" />
            <input type="submit" value="Add Farmer" />
          </form>
        </div>
        <div>
          <!-- Vegetable Adding Form -->
          <h2>Add Vegetable</h2>
          <form action="php/add_vegetable.php" method="post">
            <input type="text" name="name" placeholder="Name" required />
            <input type="text" name="type" placeholder="Type" />
            <input type="text" name="unit" placeholder="Unit (e.g., kg)" />
            <input type="submit" value="Add Vegetable" />
          </form>
        
        </div>
        
    <!-- JavaScript -->
    <script>

      function loadStocks(page = 1) {
        const search = document.getElementById("stockSearchInput")?.value || "";

        fetch(`php/get_stocks.php?page=${page}&search=${encodeURIComponent(search)}`)
          .then(res => res.json())
          .then(res => {
            const table = document.getElementById("stockTable");
            const pagination = document.getElementById("pagination");
            table.innerHTML = "";
            pagination.innerHTML = "";

            if (res.data.length === 0) {
              table.innerHTML = `<tr><td colspan="6">No matching stocks found.</td></tr>`;
              return;
            }

            res.data.forEach(stock => {
              table.innerHTML += `
                <tr>
                  <td>${stock.farmer}</td>
                  <td>${stock.vegetable}</td>
                  <td>${stock.quantity}</td>
                  <td>${stock.price}</td>
                  <td>${stock.date}</td>
                  <td>
                    <a href="php/delete_stock.php?id=${stock.stock_id}" onclick="return confirm('Are you sure you want to delete this stock record?')" class="delete-link">Delete</a>
                  </td>

                </tr>
              `;
            });

            // Pagination
            if (res.totalPages > 1) {
              if (res.currentPage > 1) {
                pagination.innerHTML += `<button onclick="loadStocks(${res.currentPage - 1})">Prev</button>`;
              }

              for (let i = 1; i <= res.totalPages; i++) {
                pagination.innerHTML += `<button onclick="loadStocks(${i})" ${i === res.currentPage ? 'class="active-page"' : ''}>${i}</button>`;
              }

              if (res.currentPage < res.totalPages) {
                pagination.innerHTML += `<button onclick="loadStocks(${res.currentPage + 1})">Next</button>`;
              }
            }
          });
      }



      function deleteStock(stockId) {
        if (confirm("Are you sure you want to delete this stock?")) {
          fetch(`php/delete_stock.php?stock_id=${stockId}`)
            .then(() => {
              alert("Stock deleted successfully!");
              loadStocks();
            })
            .catch(err => {
              alert("Error deleting stock.");
              console.error(err);
            });
        }
      }



      window.onload = function () {
        // Load farmers
        fetch("php/get_farmers_list.php")
          .then((res) => res.json())
          .then((data) => {
            const select = document.getElementById("farmerSelect");
            data.forEach((farmer) => {
              select.innerHTML += `<option value="${farmer.farmer_id}">${farmer.name}</option>`;
            });
          });

        // Load vegetables
        fetch("php/get_vegetables_list.php")
          .then((res) => res.json())
          .then((data) => {
            const select = document.getElementById("vegSelect");
            data.forEach((veg) => {
              select.innerHTML += `<option value="${veg.veg_id}">${veg.name}</option>`;
            });
          });

        // Success MSG 
        const msg = document.getElementById("successMessage");
        if (msg) {
          setTimeout(() => {
            msg.style.display = "none";
          }, 2000); // hide after 2 seconds
        }
        
        // Load existing stock data
        loadStocks();

      function editStock(id, farmerId, vegId, qty, price, date) {
        // Pre-fill the Add Stock form with selected stock
        document.getElementById("farmerSelect").value = farmerId;
        document.getElementById("vegSelect").value = vegId;
        document.querySelector("input[name='quantity']").value = qty;
        document.querySelector("input[name='price']").value = price;
        document.querySelector("input[name='date']").value = date;

      

        //-- Add hidden input for stock ID
        if (!document.getElementById("stockId")) {
          const hidden = document.createElement("input");
          hidden.type = "hidden";
          hidden.name = "stock_id";
          hidden.id = "stockId";
          form.appendChild(hidden);
        }
        document.getElementById("stockId").value = id;

        // Change button text
        form.querySelector("input[type='submit']").value = "Update Stock";
      }

      window.onload = function () {
        // Existing load farmer/veg dropdowns
        fetch("php/get_farmers_list.php")
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById("farmerSelect");
            data.forEach(f => {
              select.innerHTML += `<option value="${f.farmer_id}">${f.name}</option>`;
            });
          });

        fetch("php/get_vegetables_list.php")
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById("vegSelect");
            data.forEach(v => {
              select.innerHTML += `<option value="${v.veg_id}">${v.name}</option>`;
            });
          });

       
      };
      
      // Autocomplete and Search
      document.getElementById("searchStock").addEventListener("input", function () {
        const query = this.value;
        if (query.length === 0) {
          document.getElementById("stockSuggestions").style.display = "none";
          loadStocks(1);
          return;
        }

        fetch(`php/stock_suggestions.php?term=${encodeURIComponent(query)}`)
          .then(res => res.json())
          .then(data => {
            const box = document.getElementById("stockSuggestions");
            box.innerHTML = "";
            data.forEach(item => {
              const div = document.createElement("div");
              div.textContent = item;
              div.onclick = () => {
                document.getElementById("searchStock").value = item;
                box.style.display = "none";
                loadStocks(1, item);
              };
              box.appendChild(div);
            });
            box.style.display = data.length ? "block" : "none";
          });
      });

      function clearStockSearch() {
        document.getElementById("searchStock").value = "";
        document.getElementById("stockSuggestions").style.display = "none";
        loadStocks(1);
      }


      };

      document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("stockSearchInput");
        if (searchInput) {
          searchInput.addEventListener("input", () => loadStocks(1));
        }
      });
       
      
    </script>


  </body>
</html>
