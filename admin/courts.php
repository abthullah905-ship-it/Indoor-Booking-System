<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$courts = $conn->query("SELECT * FROM courts ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$base_path = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courts - Admin</title>
    <link rel="icon" href="uploads/nav_logo1.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h2>Manage Courts</h2>
            <button onclick="openAddCourtModal()" class="btn btn-primary">+ Add New Court</button>
        </div>
        
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Sport</th>
                            <th>Price / Hr</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($courts) > 0): ?>
                            <?php foreach($courts as $c): ?>
                            <tr>
                                <td><?php echo $c['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($c['name']); ?></strong>
                                    <?php if($c['description']): ?>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary);"><?php echo htmlspecialchars(substr($c['description'], 0, 30)) . '...'; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo ucfirst(htmlspecialchars($c['sport_type'])); ?></td>
                                <td>Rs. <?php echo number_format($c['price_per_hour']); ?></td>
                                <td>
                                    <?php if($c['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="toggleCourtStatus(<?php echo $c['id']; ?>)" class="btn btn-outline btn-sm mb-1 w-100">Toggle Status</button>
                                    <button onclick="openEditCourtModal(<?php echo $c['id']; ?>, '<?php echo addslashes(htmlspecialchars($c['name'])); ?>', <?php echo $c['price_per_hour']; ?>, '<?php echo addslashes(htmlspecialchars($c['sport_type'])); ?>', '<?php echo addslashes(htmlspecialchars($c['description'])); ?>')" class="btn btn-primary btn-sm mb-1 w-100">Edit</button>
                                    <button onclick="deleteCourt(<?php echo $c['id']; ?>)" class="btn btn-danger btn-sm w-100">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No courts found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Court Modal -->
<div class="modal-overlay" id="court-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="court-modal-title">Add Court</h3>
            <button class="modal-close" onclick="closeModal('court-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="court-form" onsubmit="saveCourt(event)">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="court_id" value="">
                <input type="hidden" name="action" id="court_action" value="add_court">
                
                <div class="form-group">
                    <label class="form-label">Court Name</label>
                    <input type="text" name="name" id="court_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Price per Hour (Rs.)</label>
                    <input type="number" name="price" id="court_price" class="form-control" required min="1">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Sport Type</label>
                    <select name="sport_type" id="court_sport" class="form-control">
                        <option value="football">Football / Futsal</option>
                        <option value="cricket">Cricket</option>
                        <option value="badminton">Badminton</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="court_desc" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Court</button>
            </form>
        </div>
    </div>
</div>

<script>
    const CSRF_TOKEN = '<?php echo $_SESSION['csrf_token']; ?>';
    
    function openAddCourtModal() {
        document.getElementById('court_id').value = '';
        document.getElementById('court_action').value = 'add_court';
        document.getElementById('court_name').value = '';
        document.getElementById('court_price').value = '';
        document.getElementById('court_sport').value = 'football';
        document.getElementById('court_desc').value = '';
        document.getElementById('court-modal-title').innerText = 'Add New Court';
        openModal('court-modal');
    }
    
    function openEditCourtModal(id, name, price, sport, desc) {
        document.getElementById('court_id').value = id;
        document.getElementById('court_action').value = 'update_info';
        document.getElementById('court_name').value = name;
        document.getElementById('court_price').value = price;
        document.getElementById('court_sport').value = sport;
        document.getElementById('court_desc').value = desc;
        document.getElementById('court-modal-title').innerText = 'Edit Court';
        openModal('court-modal');
    }
    
    function saveCourt(event) {
        event.preventDefault();
        const form = event.target;
        const action = document.getElementById('court_action').value;
        const url = action === 'add_court' ? '../api/add_court.php' : '../api/update_court.php';
        
        fetch(url, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    }
</script>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/admin.js"></script>
</body>
</html>
