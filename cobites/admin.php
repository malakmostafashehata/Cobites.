<?php
require "auth.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
if (isset($_POST['ajax']) && $_POST['ajax'] === "charity") {

    $search = "%" . ($_POST['search'] ?? '') . "%";
    $status = $_POST['status'] ?? '';

    $sql = "SELECT * FROM users 
            WHERE role='charity'
            AND (full_name LIKE ? OR email LIKE ?)";

    $params = [$search, $search];
    $types = "ss";

    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
$fileName = basename($row['charity_file']);
$file = "/cobites/uploads/charity_file/" . $fileName;
echo "
<tr>
    <td><span class='badge'>Charity</span></td>
    <td>{$row['full_name']}</td>
    <td>{$row['phone']}</td>
    <td>{$row['address']}</td>

    <td>
      <button class='view-file btn'
    data-file='{$file}'
    style='background:#3b82f620;color:#3b82f6;border:1px solid #3b82f640;padding:6px 10px;border-radius:8px;'>
    👁 View
</button>
    </td>

    <td><span class='badge'>{$row['status']}</span></td>

    <td>
        <button class='accept-charity btn'
            data-id='{$row['id']}'>
            ✔ Accept
        </button>
    </td>
</tr>
";
}

    exit();
}if (isset($_POST['ajax']) && $_POST['ajax'] === "acceptCharity") {

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("
        UPDATE users SET status='accepted'
        WHERE id=? AND role='charity'
    ");

    $stmt->bind_param("i", $id);

    echo $stmt->execute() ? "Accepted successfully" : "Error";

    exit();
}

if (isset($_POST['ajax']) && $_POST['ajax'] === "orders") {

    $search = "%" . ($_POST['search'] ?? '') . "%";
    $status = $_POST['status'] ?? '';
    $date   = $_POST['date'] ?? '';

    $sql = "SELECT * FROM orders 
            WHERE (id LIKE ? OR charity_name LIKE ?)";

    $params = [$search, $search];
    $types = "ss";

    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }

    if (!empty($date)) {
        $sql .= " AND DATE(created_at) = ?";
        $params[] = $date;
        $types .= "s";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        echo "
        <tr>
            <td>{$row['id']}</td>
            <td>{$row['charity_name']}</td>
            <td>{$row['food_item_name']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['best_before']}</td>
            <td>{$row['charity_address']}</td>
            <td>{$row['donor_address']}</td>
            <td>{$row['charity_phone']}</td>
            <td>{$row['donor_phone']}</td>
            <td>
                <span class='badge'>{$row['status']}</span>
            </td>
            <td>{$row['created_at']}</td>
        </tr>
        ";
    }

    exit();}if (isset($_POST['ajax']) && $_POST['ajax'] === "contacts") {

    $search = "%" . ($_POST['search'] ?? '') . "%";
    $status = $_POST['status'] ?? '';
    $date   = $_POST['date'] ?? '';

    $sql = "SELECT * FROM contact_messages 
            WHERE (name LIKE ? OR email LIKE ? OR message LIKE ?)";

    $params = [$search, $search, $search];
    $types = "sss";

    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }

    if (!empty($date)) {
        $sql .= " AND DATE(created_at) = ?";
        $params[] = $date;
        $types .= "s";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $email = $row['email'];
echo "
<tr>
    <td>{$row['id']}</td>
    <td>{$row['name']}</td>
    <td>{$row['email']}</td>
    <td>{$row['message']}</td>
    <td>{$row['created_at']}</td>
    <td><span class='badge'>{$row['status']}</span></td>
    <td>
        <a href='#' class='reply-contact'
           data-email='{$email}'
           data-id='{$row['id']}'>
           Reply
        </a>
    </td>
</tr>
";
    
    }

    exit();
}if (isset($_POST['ajax']) && $_POST['ajax'] === "acceptContact") {

    $id = intval($_POST['id']);

    $stmt = $conn->prepare(
        "UPDATE contact_messages SET status='accepted' WHERE id=?"
    );

    $stmt->bind_param("i", $id);

    echo $stmt->execute() ? "Accepted successfully" : "Error";

    exit();
}

if (isset($_POST['ajax']) && $_POST['ajax'] === "users") {

    $search = "%" . ($_POST['search'] ?? '') . "%";
    $role = $_POST['role'] ?? '';
    $volunteer_type = $_POST['volunteer_type'] ?? '';

    $stmt = $conn->prepare("
        SELECT * FROM users
        WHERE role != 'admin'
        AND (full_name LIKE ? OR email LIKE ?)
        AND (? = '' OR role = ?)
        AND (? = '' OR volunteer_type = ?)
        ORDER BY id DESC
    ");

    $stmt->bind_param(
        "ssssss",
        $search, $search,
        $role, $role,
        $volunteer_type, $volunteer_type
    );

    $stmt->execute();
    $result = $stmt->get_result();

    while ($user = $result->fetch_assoc()) {

        $volType = ($user['role'] === 'volunteer')
            ? ucfirst($user['volunteer_type'])
            : "-";

        echo "
        <tr>
            <td><span class='badge'>{$user['role']}</span></td>
            <td>{$volType}</td>
            <td>{$user['full_name']}</td>
            <td>{$user['email']}</td>
            <td>{$user['phone']}</td>
            <td>{$user['address']}</td>
            <td>
                <i class='fa-solid fa-trash delete-user'
                   data-id='{$user['id']}'
                   style='cursor:pointer;color:#c94c4c;'></i>
            </td>
        </tr>
        ";
    }

    exit();
}

if (isset($_POST['ajax']) && $_POST['ajax'] === "donations") {

    $search = "%" . ($_POST['search'] ?? '') . "%";
    $type = $_POST['type'] ?? '';
    $date = $_POST['date'] ?? '';

    $sql = "SELECT * FROM donations WHERE food_item_name LIKE ?";
    $params = [$search];
    $types = "s";

    if (!empty($type)) {
        $sql .= " AND category = ?";
        $params[] = $type;
        $types .= "s";
    }

    if (!empty($date)) {
        $sql .= " AND DATE(created_at) = ?";
        $params[] = $date;
        $types .= "s";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
      
          echo "
<tr>
    <td>{$row['id']}</td>
    <td>{$row['food_item_name']}</td>
    <td>{$row['quantity']}</td>
    <td>{$row['best_before']}</td>
    <td>{$row['food_condition']}</td>
    <td>{$row['donor_name']}</td>
    <td>{$row['pickup_address']}</td>
    <td>{$row['donor_phone']}</td>
    <td>{$row['category']}</td>
    <td>{$row['remaining_quantity']}</td>
    <td>{$row['created_at']}</td>
</tr>
";
        
    }

    exit();
}
if (isset($_POST['ajax']) && $_POST['ajax'] === "deleteUser") {

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("
        DELETE FROM users
        WHERE id = ? AND role != 'admin'
    ");

    $stmt->bind_param("i", $id);

    echo $stmt->execute()
        ? "User deleted successfully"
        : "Error deleting user";

    exit();
}
if (isset($_POST['ajax']) && $_POST['ajax'] === "createUser") {

    $errors = [];

    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
$role = $_POST['role'];
$volunteer_type = $_POST['volunteer_type'] ?? null;
    if (empty($name) || strlen($name) < 3) {
        $errors[] = "Name must be at least 3 characters";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    if (!in_array($role, ['volunteer', 'charity', 'delivery'])) {
        $errors[] = "Invalid role selected";
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $errors[] = "Email already exists";
    }

    if (!empty($errors)) {
        echo implode("<br>", $errors);
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
    INSERT INTO users
    (full_name, email, password, phone, address, role, volunteer_type)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssss",
    $name, $email, $password, $phone, $address, $role, $volunteer_type
);

    echo $stmt->execute()
        ? "User created successfully"
        : "Error creating user";

    exit();
}
$totalUsers = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role != 'admin'
")->fetch_assoc()['total'];

$volunteers = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role='volunteer'
")->fetch_assoc()['total'];

$charities = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role='charity'
")->fetch_assoc()['total'];

$delivery = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role='delivery'
")->fetch_assoc()['total'];

$donations = $conn->query("
    SELECT COUNT(*) AS total
    FROM donations
")->fetch_assoc()['total'];

$Order = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
")->fetch_assoc()['total'];

$users = $conn->query("
    SELECT * FROM users
    ORDER BY id DESC
")->fetch_all(MYSQLI_ASSOC);
$contact_messages = $conn->query("
    SELECT COUNT(*) AS total
    FROM contact_messages
")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COBITES Admin Dashboard</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
:root {
  --brand: #728C5A;
  --brand-hover: #5f754a;
  --brand-orange: #ff6b35;

  --bg-dark: #102F15;
  --sidebar-bg: #0b2310;
  --card-bg: #EBFADC;

  --text-main: #EAF1B1;
  --text-dim: #a9b897;
  --text-dark: #102F15;

  --border-color: rgba(234,241,177,.15);

  --danger: #c94c4c;
  --success: #6fa36a;

  --transition: all .3s ease;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

body {
  background: var(--bg-dark);
  color: var(--text-main);
  min-height: 100vh;
  overflow: hidden;
}

.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 40px;
  border-bottom: 1px solid var(--border-color);
}

.brand h2 {
  font-family: 'Playfair Display', serif;
}

.btn {
  padding: 10px 18px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  transition: var(--transition);
}

.btn-home-nav {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #fff;
  text-decoration: none;
  font-weight: 700;
  border: 2px solid var(--brand-orange);
  padding: 10px 22px;
  border-radius: 50px;
  transition: .3s;
}

.btn-home-nav:hover {
  background: var(--brand-orange);
}

.layout {
  display: flex;
  height: calc(100vh - 70px);
  padding: 20px;
  gap: 20px;
}

.left-panel {
  width: 260px;
  background: var(--sidebar-bg);
  border-radius: 20px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.side-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 10px;
  cursor: pointer;
  transition: var(--transition);
  color: var(--text-main);
}

.side-btn:hover {
  background: var(--brand);
  color: #fff;
}

.center-pane {
  flex: 1;
  background: rgba(235,250,220,.05);
  border-radius: 20px;
  padding: 30px;
  overflow-y: auto;
  border: 1px solid var(--border-color);
}

.card {
  background: var(--card-bg);
  color: var(--text-dark);
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 15px 35px rgba(0,0,0,.25);
  transition: var(--transition);
}

.card:hover {
  transform: translateY(-5px);
}
#orders-page .table-container {
    overflow-x: auto;
    max-width: 100%;
}
#orders-page .main-table th,
#orders-page .main-table td {
    padding: 4px 6px;
    font-size: 10px;
    line-height: 1.2;
}
.stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 30px;
}

.table-container {
  background: #0b2310;
  border-radius: 15px;
  overflow: hidden;
}

.main-table {
  width: 100%;
  border-collapse: collapse;
}

.main-table th,
.main-table td {
  padding: 14px;
  border-bottom: 1px solid var(--border-color);
  text-align: center;
  vertical-align: middle;
}

.main-table th {
  color: var(--text-dim);
  font-size: 12px;
}

.input-field {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid var(--border-color);
  background: #0b2310;
  color: var(--text-main);
  outline: none;
}

.input-field:focus {
  border-color: var(--brand);
}

.add-btn {
  background: var(--brand);
  color: #fff;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
  border: none;
}

.add-btn:hover {
  background: var(--brand-hover);
}

.badge {
  padding: 5px 10px;
  border-radius: 8px;
  background: var(--text-main);
  color: var(--text-dark);
  font-size: 12px;
  font-weight: 600;
}

.page-content {
  display: none;
  animation: fadeIn .3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}

.form-card {
  max-width: 700px;
  margin: auto;
  background: #0b2310;
  padding: 30px;
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,0.1);
}

.modern-form .field {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
}

.modern-form label {
  font-size: 12px;
  color: var(--text-dim);
  margin-bottom: 6px;
}

.toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: var(--brand);
  color: #fff;
  padding: 12px 18px;
  border-radius: 12px;
  opacity: 0;
  transform: translateY(20px);
  transition: .3s ease;
  z-index: 9999;
  font-size: 14px;
}

.toast.show {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 1000px) {
  .stats {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 700px) {
  .layout {
    flex-direction: column;
  }

  .left-panel {
    width: 100%;
    flex-direction: row;
    overflow-x: auto;
  }

  #usersTable th,
  #usersTable td {
    text-align: left;
    white-space: nowrap;
  }
}

#donationsTable {
  table-layout: auto;
}

#donationsTable td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 150px;
}
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="brand"><h2>COBITES <span style="font-weight:300; color:var(--text-dim)">| Management</span></h2></div>
        <div class="header-actions">

    <a href="homepage.php" class="btn-home-nav">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Home
    </a>

</div>
    </div>

    <div class="layout">
      <aside class="left-panel">
   <div class="side-btn" onclick="showPage('dash-page')">
<i class="fa-solid fa-chart-pie"></i> Dashboard
</div>

<div class="side-btn" onclick="showPage('users-page')">
<i class="fa-solid fa-users"></i> Users
</div>

<div class="side-btn" onclick="showPage('donations-page')">
<i class="fa-solid fa-hand-holding-heart"></i> Donations
</div>

<div class="side-btn" onclick="showPage('orders-page')">
<i class="fa-solid fa-cart-shopping"></i> Orders
</div>

<div class="side-btn" onclick="showPage('contact-page')">
<i class="fa-solid fa-circle-exclamation"></i> Contact
</div>

<div class="side-btn" onclick="showPage('charity-page')">
<i class="fa-solid fa-handshake-angle"></i> Charity</div>

<div class="side-btn" onclick="showPage('settings-page')" style="margin-top:auto;">
</div>
</aside>
        <main class="center-pane">
            
            <div id="dash-page" class="page-content">
                <div class="stats">
                  <div class="card">
<h3>Total Users</h3>
<p><?= $totalUsers ?></p>
</div>

<div class="card">
<h3>Volunteers</h3>
<p><?= $volunteers ?></p>
</div>

<div class="card">
<h3>Charities</h3>
<p><?= $charities ?></p>
</div>

<div class="card">
<h3>Deliverys</h3>
<p><?= $delivery ?></p>
</div>

<div class="card">
<h3>Donations</h3>
<p><?= $donations ?></p>
</div>
<div class="card">
<h3>Orders</h3>
<p><?= $Order ?></p>
</div>
<div class="card">
<h3>contact</h3>
<p><?= $contact_messages ?></p>
</div>
            </div>
                        </div>

<div id="users-page" class="page-content">
    <div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin: 0;">Users Management</h2>
            <p style="color: var(--text-dim); font-size: 13px; margin: 5px 0 0 0;">Manage your  volunteers , charitys , and deliverys .</p>
        </div>
        <div class="add-btn" onclick="showPage('create-user-page')">
    <i class="fa fa-plus"></i> Create New User
</div>
    </div>

    <div class="toolbar" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
<input id="searchUser" class="input-field"
placeholder="Search by name or email...">

<select id="roleFilter" class="input-field">
    <option value="">All Roles</option>
    <option value="volunteer">Volunteer</option>
    
    <option value="charity">Charity</option>
    <option value="delivery">Delivery</option>
</select>
<select id="volunteerTypeFilter" class="input-field">
    <option value="">All Volunteer Types</option>
    <option value="individual">Individual</option>
    <option value="hotel">Hotel</option>
    <option value="restaurant">Restaurant</option>
</select>
      <button onclick="exportCurrentTable()" 
class="btn"
style="background:#10b98120;color:#10b981;border:1px solid #10b98140;padding:10px 15px;border-radius:12px;cursor:pointer;">
    <i class="fa-solid fa-file-excel"></i> 
</button>
    </div>

 <div class="table-container">
    <table class="main-table" id="usersTable">
        <thead>
            <tr>
                <th>User Role</th>
                <th>Volunteer Type</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
    
            <tbody id="usersBody"></tbody>
    </table>
</div>
</div>

<div id="create-user-page" class="page-content">
    <div style="margin-bottom: 25px;">
        <div onclick="scrollToTable()" style="color:var(--accent-blue); cursor:pointer; font-size:14px; display:flex; align-items:center; gap:8px;">
    <i class="fa fa-arrow-left"></i> Back to Users List
</div>
        <h2 style="margin: 15px 0 0 0;">Create New User Account</h2>
    </div>
<div class="form-card">

    <h2 class="form-title">
        <i class="fa fa-user-plus"></i> Create New User
    </h2>

    <form id="createUserForm" class="modern-form">

        <div class="grid-2">

            <div class="field">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter full name" required>
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter email" required>
            </div>

        </div>

        <div class="grid-2">

            <div class="field">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="field">
                <label>Phone</label>
                <input type="text" name="phone" placeholder="Enter phone">
            </div>

        </div>

        <div class="field">
            <label>Address</label>
            <input type="text" name="address" placeholder="Enter address">
        </div>

        <div class="field">
            <label>Role</label>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="volunteer">Volunteer</option>
                <option value="charity">Charity</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>
<div class="field">
    <label>Volunteer Type</label>
    <select name="volunteer_type">
        <option value="">Select Type </option>
        <option value="individual">Individual</option>
        <option value="hotel">Hotel</option>
        <option value="restaurant">Restaurant</option>
    </select>
</div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fa fa-check"></i> Create User
            </button>

        </div>

    </form>

</div>
            </div>
       <div id="donations-page" class="page-content">
    <div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin: 0;">Donations</h2>
            <p style="color: var(--text-dim); font-size: 13px; margin: 5px 0 0 0;">Track and manage incoming food donations.</p>
        </div>
        
    </div>

    <div class="toolbar" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
        <input id="donationSearch" class="input-field" placeholder="Search Donations..." style="flex: 2; margin-top: 0;">

<select id="donationType" class="input-field" style="flex: 1; margin-top: 0; cursor: pointer;">
    <option value="">All Types</option>
    <option value="savory">savory</option>
    <option value="sweet">sweet</option>
    <option value="grocery">Grocery</option>
</select>

<input id="donationDate" type="date" class="input-field" style="flex: 1; margin-top: 0;">
       <button onclick="exportCurrentTable()" 
class="btn"
style="background:#10b98120;color:#10b981;border:1px solid #10b98140;padding:10px 15px;border-radius:12px;cursor:pointer;">
    <i class="fa-solid fa-file-excel"></i> 
</button>
    </div>

    <div class="table-container">
        <table class="main-table" id="donationsTable">
            <thead>
                <tr>
                    <th>ID</th><th>Food Item Name
</th><th>Quantity</th><th>Best Before
</th> <th>Condition
</th><th>Donor Name</th><th>  Pickup Address
</th><th>Donor Phone</th><th>Category</th><th>Remaining Quantity</th><th>Created At</th>
                </tr>
            </thead>
        
                <tbody id="donationsBody"></tbody>   
        </table>
    </div>
</div>        
          <div id="orders-page" class="page-content">
    <div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
       
         <div>
            <h2 style="margin: 0;">Orders</h2>
            <p style="color: var(--text-dim); font-size: 13px; margin: 5px 0 0 0;">Track and manage incoming food orders.</p>
        </div>
    </div>

    <div class="toolbar" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
   <input id="orderSearch" class="input-field" placeholder="Search Orders...">

<select id="orderStatus" class="input-field">
    <option value="">All</option>
    <option value="pending">Pending</option>
    <option value="accepted">Accepted</option>
</select>

<input id="orderDate" type="date" class="input-field">
           <button onclick="exportCurrentTable()" 
class="btn"
style="background:#10b98120;color:#10b981;border:1px solid #10b98140;padding:10px 15px;border-radius:12px;cursor:pointer;">
    <i class="fa-solid fa-file-excel"></i> 
</button>
    </div>
<div class="table-container" style="background :#0b2310; border-radius: 12px; padding: 10px; border: 1px solid rgba(255,255,255,0.1);">
    <table class="main-table" style="width: 100%; border-collapse: collapse; color: white; table-layout: fixed;">
        <thead>
            <tr style="text-align: left; font-size: 12px; color: #94a3b8; border-bottom: 1px solid rgba(255,255,255,0.1);">
             <th style="padding: 4px 6px; width: 40px; font-size: 10px;">ID</th>
<th style="width: 80px; font-size: 10px;">CHARITY</th>
<th style="width: 85px; font-size: 10px;">FOOD</th>
<th style="width: 50px; font-size: 10px;">QTY</th>
<th style="width: 80px; font-size: 10px;">BEST</th>
<th style="width: 110px; font-size: 10px;">CHARITY ADDR</th>
<th style="width: 110px; font-size: 10px;">DONOR ADDR</th>
<th style="width: 80px; font-size: 10px;">C PHONE</th>
<th style="width: 80px; font-size: 10px;">D PHONE</th>
<th style="width: 70px; font-size: 10px;">STATUS</th>
<th style="width: 90px; font-size: 10px;">DATE</th>
            </tr>
        </thead>
<tbody id="ordersBody"></tbody>
           
    </table>
</div>
</div><div id="contact-page" class="page-content">

         <div>
            <h2 style="margin: 0;">Contact</h2>
            <p style="color: var(--text-dim); font-size: 13px; margin: 5px 0 0 0;">Track and manage incoming contact messages.</p>
        </div>
 <div class="toolbar" style="display:flex;gap:10px;align-items:center;margin-bottom:20px;">

    <input id="contactSearch" class="input-field" placeholder="Search Contacts...">

    <select id="contactStatus" class="input-field">
        <option value="">All</option>
        <option value="pending">Pending</option>
        <option value="accepted">Accepted</option>
    </select>

    <input id="contactDate" type="date" class="input-field">

    <button onclick="exportCurrentTable()" 
        class="btn"
        style="background:#10b98120;color:#10b981;border:1px solid #10b98140;padding:10px 15px;border-radius:12px;cursor:pointer;">
        <i class="fa-solid fa-file-excel"></i>
    </button>

</div>

    <div class="table-container"
         style="background:#0b2310;border-radius:12px;border:1px solid rgba(255,255,255,0.1);overflow:hidden;">

        <table class="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
<tbody id="contactsBody"></tbody>

</table>
</div>
</div>


<div id="charity-page" class="page-content">

    <div style="margin-bottom: 15px;">
        <h2 style="margin: 0;">Charity</h2>
        <p style="color: var(--text-dim); font-size: 13px; margin: 5px 0 0 0;">
            Track and manage incoming charity applications.
        </p>
    </div>
    <div class="toolbar" style="display:flex;gap:10px;align-items:center;margin-bottom:20px;">

        <input id="charitySearch" class="input-field" placeholder="Search charity..." style="width:200px;">

        <select id="charityStatus" class="input-field" style="width:160px;">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="accepted">Accepted</option>
        </select>

        <button onclick="exportCurrentTable()" class="btn"
            style="background:#10b98120;color:#10b981;border:1px solid #10b98140;padding:10px 15px;border-radius:12px;">
            <i class="fa-solid fa-file-excel"></i>
        </button>

    </div>

    <div class="table-container">

        <table class="main-table">

            <thead>
                <tr>
                    <th>Role</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="charityBody"></tbody>

        </table>

    </div>

</div>
</div>
        </main>
    </div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
function loadUsers(){
    $.post("", {
        ajax:"users",
        search: $("#searchUser").val(),
        role: $("#roleFilter").val(),
        volunteer_type: $("#volunteerTypeFilter").val()
    }, function(data){
        $("#usersBody").html(data);
    });
}

$(document).ready(function(){

    loadUsers();

    $("#searchUser").on("keyup", loadUsers);
    $("#roleFilter").on("change", loadUsers);
    $("#volunteerTypeFilter").on("change", loadUsers);

});
</script>

<script>

$("#createUserForm").submit(function(e){

    e.preventDefault();

    $.ajax({
        url:"",
        method:"POST",
        data: $(this).serialize()+"&ajax=createUser",

       success:function(res){

    showToast(res);

    $("#createUserForm")[0].reset();


    loadUsers();
}
    });

});
</script><script>
function showToast(message, type = "success") {
    let toast = $("#toast");

    toast.text(message);

    if(type === "error"){
        toast.css("background","#c94c4c");
    } else {
        toast.css("background","#728C5A");
    }

    toast.addClass("show");

    setTimeout(() => {
        toast.removeClass("show");
    }, 3000);
}
</script>
    <div id="toast" class="toast"></div>
    
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script><script>
function exportCurrentTable() {

    let table = null;
    if (document.getElementById("users-page").style.display !== "none") {
        table = document.getElementById("usersTable");
    }

    else if (document.getElementById("donations-page").style.display !== "none") {
        table = document.getElementById("donationsTable");
    }

    else if (document.getElementById("orders-page").style.display !== "none") {
        table = document.querySelector("#orders-page table");
    }

    else if (document.getElementById("contact-page").style.display !== "none") {
        table = document.querySelector("#contact-page table");
    }
else if (document.getElementById("charity-page").style.display !== "none") {
        table = document.querySelector("#charity-page table");
    }
    if (!table) {
        alert("No table found in this page!");
        return;
    }

    let workbook = XLSX.utils.table_to_book(table, { sheet: "Data" });
    XLSX.writeFile(workbook, "export.xlsx");
}
</script>
<script>
function scrollToTable() {

    showPage('users-page');

    setTimeout(() => {

        const table = document.getElementById("usersTable");

        if (table) {
            table.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        }

    }, 100);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
function showPage(id){
    document.querySelectorAll(".page-content")
        .forEach(p => p.style.display = "none");

    document.getElementById(id).style.display = "block";

    if(id === "contact-page") loadContacts();
    if(id === "donations-page") loadDonations();
    if(id === "orders-page") loadOrders();
    if(id === "charity-page") loadCharity();

}
</script>
<script>
$(document).on("click", ".delete-user", function(){

    let id = $(this).data("id");

    Swal.fire({
        title: "Delete User?",
        text: "This action cannot be undone!",
        icon: "warning",

        background: "#0b2310",
        color: "#EAF1B1",

        showCancelButton: true,
        confirmButtonText: "Yes, delete",
        cancelButtonText: "Cancel",

        confirmButtonColor: "#728C5A",
        cancelButtonColor: "#1f3a24",

        customClass: {
            popup: "custom-swal"
        }
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: "",
                method: "POST",
                data: {
                    ajax: "deleteUser",
                    id: id
                },
                success: function(res){

                    Swal.fire({
                        title: "Deleted!",
                        text: res,
                        icon: "success",
                        background: "#0b2310",
                        color: "#EAF1B1",
                        confirmButtonColor: "#728C5A"
                    });

                    loadUsers();
                }
            });

        }
    });

});
$(document).on("click", ".accept-contact", function () {

    let id = $(this).data("id");

    $.post("", {
        ajax: "acceptContact",
        id: id
    }, function (res) {
        showToast(res);
        loadContacts(); 
    });

});$(document).ready(function () {

    loadContacts();

    $("#contactSearch").on("keyup", loadContacts);
    $("#contactStatus").on("change", loadContacts);
    $("#contactDate").on("change", loadContacts);

});
</script>
<script>
function loadDonations(){

    $.post("", {
        ajax: "donations",
        search: $("#donationSearch").val(),
        type: $("#donationType").val(),
        date: $("#donationDate").val()
    }, function(data){
        $("#donationsBody").html(data);
    });

}

$(document).ready(function(){

    loadDonations();

    $("#donationSearch").on("keyup", loadDonations);
    $("#donationType").on("change", loadDonations);
    $("#donationDate").on("change", loadDonations);

});
</script>
<script>
function loadOrders(){

    $.post("", {
        ajax: "orders",
        search: $("#orderSearch").val(),
        status: $("#orderStatus").val(),
        date: $("#orderDate").val()
    }, function(data){
        $("#ordersBody").html(data);
    });

}

$(document).ready(function(){

    loadOrders();

    $("#orderSearch").on("keyup", loadOrders);
    $("#orderStatus").on("change", loadOrders);
    $("#orderDate").on("change", loadOrders);

});
$(document).ready(function(){
    showPage('dash-page');
});
function loadContacts(){
    $.post("", {
        ajax: "contacts",
        search: $("#contactSearch").val(),
        status: $("#contactStatus").val(),
        date: $("#contactDate").val()
    }, function(data){
        $("#contactsBody").html(data);
    });
}
$(document).on("click", ".reply-contact", function (e) {
    e.preventDefault();

    let id = $(this).data("id");
    let email = $(this).data("email");

    // update status first
    $.post("", {
        ajax: "acceptContact",
        id: id
    }, function () {

        loadContacts();
        window.open(
            "https://outlook.live.com/mail/0/deeplink/compose?to=" + email +
            "&subject=Reply from Admin"
        );

    });

});
$("#createUserForm").submit(function(e){
    e.preventDefault();

    let name = $("input[name='full_name']").val().trim();
    let email = $("input[name='email']").val().trim();
    let password = $("input[name='password']").val();
    let role = $("select[name='role']").val();

    if(name.length < 3){
        showToast("Name too short", "error");
        return;
    }

    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailPattern.test(email)){
        showToast("Invalid email", "error");
        return;
    }

    if(password.length < 6){
        showToast("Password too short", "error");
        return;
    }

    if(role === ""){
        showToast("Select role", "error");
        return;
    }

    $.ajax({
        url:"",
        method:"POST",
        data: $(this).serialize()+"&ajax=createUser",
        success:function(res){
            showToast(res);
            $("#createUserForm")[0].reset();
            loadUsers();
        }
    });
});
function loadCharity(){

    $.post("", {
        ajax: "charity",
        search: $("#charitySearch").val(),
        status: $("#charityStatus").val()
    }, function(data){
        $("#charityBody").html(data);
    });

}

$(document).ready(function(){

    loadCharity();

    $("#charitySearch").on("keyup", loadCharity);
    $("#charityStatus").on("change", loadCharity);

});$(document).on("click", ".accept-charity", function(){

    let id = $(this).data("id");

    $.post("", {
        ajax: "acceptCharity",
        id: id
    }, function(res){
        showToast(res);
        loadCharity();
    });

});$(document).on("click", ".view-file", function () {

    let file = $(this).data("file");

    $("#fileFrame").attr("src", file);
    $("#fileModal").css("display", "flex");
});

function closeFile(){
    $("#fileModal").hide();
    $("#fileFrame").attr("src", "");
}

</script>
<div id="fileModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">

    <div style="width:80%; height:90%; background:#0b2310; border-radius:12px; position:relative;">
        
        <button onclick="closeFile()" 
        style="position:absolute; top:10px; right:10px; background:red; color:#fff; border:none; padding:5px 10px; border-radius:6px;">
            ✖
        </button>

        <iframe id="fileFrame" style="width:100%; height:100%; border:none;"></iframe>

    </div>
</div>

</body>
</html>