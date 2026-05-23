<?php
require "auth.php";
include "db.php";

if (!isset($_SESSION['full_name'])) {
    header("Location: index.php");
    exit();
}

$current_user = $_SESSION['full_name'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE charity_name = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders | Cobites</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:#102f15;
    color:white;
    padding:40px;
}

.container{
    max-width:1000px;
    margin:auto;
}

h1 span{
    color:#ff6b35;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    color:black;
    border-radius:15px;
    overflow:hidden;
    margin-top:20px;
}

th{
    background:#ff6b35;
    color:white;
    padding:15px;
    text-align:left;
    font-size:0.8rem;
}

td{
    padding:15px;
    border-bottom:1px solid #eee;
    font-size:0.9rem;
}

.status{
    padding:4px 10px;
    border-radius:10px;
    font-size:0.75rem;
    font-weight:800;
    display:inline-block;
}
.pending{
    background:#fff3cd;
    color:#856404;
}

.accepted,
.preparing,
.onway{
    background:#cfe2ff;
    color:#084298;
}

.delivered{
    background:#d1e7dd;
    color:#0f5132;
}

.cancelled{
    background:#f8d7da;
    color:#842029;
}

.delete-btn{
    color:white;
    background:#c94c4c;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
    font-size:0.75rem;
}
</style>
</head>

<body>

<div class="container">

<a href="charity.php" style="color:#ff6b35;text-decoration:none;">← Back to Charity</a>

<h1>Your Orders, <span><?php echo htmlspecialchars($current_user); ?></span></h1>

<table>
<thead>
<tr>
    <th>Food</th>
    <th>Quantity</th>
    <th>Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php if ($result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>

<tr>
    <td><?= htmlspecialchars($row['food_item_name']) ?></td>
    <td><?= $row['quantity'] ?></td>
    <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>

    <td>
        <span class="status <?= $row['status'] ?>">
            <?= ucfirst($row['status']) ?>
        </span>
    </td>

    <td>
        <?php if ($row['status'] === 'pending'): ?>
            <a class="delete-btn" href="#" data-id="<?= $row['id'] ?>">Delete</a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>

<?php endwhile; ?>
<?php else: ?>

<tr>
    <td colspan="5" style="text-align:center;">No orders yet.</td>
</tr>

<?php endif; ?>

</tbody>
</table>

</div>

<script>
document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();

        let id = this.getAttribute("data-id");

        Swal.fire({
            title: "Delete Order?",
            text: "Stock will be restored automatically.",
            icon: "warning",
            background: "#0b2310",
            color: "#EAF1B1",
            showCancelButton: true,
            confirmButtonText: "Yes, delete",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#c94c4c",
            cancelButtonColor: "#1f3a24",

            preConfirm: () => {
                return fetch("delete_order.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "id=" + id
                })
                .then(res => res.text())
                .then(data => {
                    if (data !== "success") {
                        throw new Error("Delete failed");
                    }
                })
                .catch(() => {
                    Swal.showValidationMessage("Something went wrong");
                });
            }

        }).then((result) => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: "Deleted!",
                    text: "Order removed successfully.",
                    icon: "success",
                    background: "#0b2310",
                    color: "#EAF1B1",
                    confirmButtonColor: "#ff6b35",
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });

            }

        });

    });
});
</script>

</body>
</html>