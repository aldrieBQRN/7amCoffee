<?php
// Optional: dito mo pwedeng ilagay ang PHP logic mo sa login, session, at iba pa.
// Example: session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F21 Cafe POS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    
    <style>
        :root {
            --coffee-primary: #6f4e37;
            --coffee-secondary: #d4a574;
            --coffee-light: #f5e9dc;
            --coffee-dark: #3e2723;
            --coffee-accent: #c19a6b;
        }
        
        .bg-coffee {
            background-color: var(--coffee-primary) !important;
        }
        
        .text-coffee {
            color: var(--coffee-primary) !important;
        }
        
        .border-coffee {
            border-color: var(--coffee-primary) !important;
        }
        
        .btn-coffee {
            background-color: var(--coffee-primary);
            border-color: var(--coffee-primary);
            color: white;
        }
        
        .btn-coffee:hover {
            background-color: var(--coffee-dark);
            border-color: var(--coffee-dark);
            color: white;
        }
        
        .btn-coffee-light {
            background-color: var(--coffee-light);
            border-color: var(--coffee-secondary);
            color: var(--coffee-dark);
        }
        
        .btn-coffee-light:hover {
            background-color: var(--coffee-secondary);
            border-color: var(--coffee-secondary);
            color: white;
        }
        
        .stat-box-coffee {
            background: linear-gradient(135deg, var(--coffee-light) 0%, #fff 100%);
            border-left: 5px solid var(--coffee-secondary);
        }
        
        .coffee-gradient {
            background: linear-gradient(135deg, var(--coffee-primary) 0%, var(--coffee-dark) 100%);
        }
        
        .coffee-text-gradient {
            background: linear-gradient(135deg, var(--coffee-secondary), var(--coffee-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .product-card-coffee {
            transition: all 0.3s ease;
            border: 1px solid var(--coffee-light);
        }
        
        .product-card-coffee:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(111, 78, 55, 0.15) !important;
            border-color: var(--coffee-secondary);
        }
        
        .navbar-coffee {
            background: linear-gradient(135deg, var(--coffee-primary) 0%, var(--coffee-dark) 100%);
            box-shadow: 0 4px 20px rgba(111, 78, 55, 0.3);
        }
        
        .sidebar-coffee {
            background: linear-gradient(180deg, var(--coffee-dark) 0%, var(--coffee-primary) 100%);
            box-shadow: 3px 0 15px rgba(111, 78, 55, 0.2);
        }
        
        .receipt-coffee {
            background: linear-gradient(135deg, #fff 0%, var(--coffee-light) 100%);
            border: 2px solid var(--coffee-secondary);
        }
        
        .coffee-bean {
            color: var(--coffee-accent);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .cursive-font {
            font-family: 'Dancing Script', cursive;
        }
    </style>
</head>
<body>
    
    <div id="login-screen">
        <div class="container-fluid position-absolute top-0 start-0 w-100 h-100 coffee-gradient"></div>
        
        <div class="position-absolute w-100 h-100" style="z-index: 1;">
            <?php for($i=1; $i<=20; $i++): ?>
            <div class="coffee-bean position-absolute" style="
                left: <?= rand(5,95) ?>%;
                top: <?= rand(5,95) ?>%;
                font-size: <?= rand(20,40) ?>px;
                opacity: 0.<?= rand(1,3) ?>;
                animation: bean-float <?= rand(15,30) ?>s infinite ease-in-out <?= rand(0,10) ?>s;">☕</div>
            <?php endfor; ?>
        </div>
        
        <div class="container d-flex justify-content-center align-items-center min-vh-100 position-relative" style="z-index: 2;">
            <div class="card border-coffee shadow-lg" style="max-width: 450px; width: 100%; border-width: 3px !important;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-4 rounded-circle bg-coffee mb-3">
                            <i class="bi bi-cup-hot-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                        <h1 class="cursive-font text-coffee mb-1" style="font-size: 3rem;">7AM Coffee</h1>
                        <p class="text-muted mb-0">Serving the finest coffee since 2023</p>
                        <div class="mt-2">
                            <span class="badge bg-coffee">EST 2023</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-coffee text-white border-coffee">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text" id="user_name" class="form-control border-coffee" placeholder="Username" required>
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-coffee text-white border-coffee">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" id="pass_word" class="form-control border-coffee" placeholder="Password" required>
                        </div>
                        
                        <div class="alert alert-danger d-none" id="login_msg"></div>
                        
                        <button class="btn btn-coffee w-100 py-2" onclick="doLogin()">
                            <i class="bi bi-door-open me-2"></i>Log In
                        </button>
                    </div>

                    <div class="card border-coffee">
                        <div class="card-header bg-coffee text-white py-2">
                            <i class="bi bi-info-circle me-2"></i>Demo Accounts
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">Owner Account</small>
                                    <strong>admin</strong><br>
                                    <small>admin123</small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Staff Account</small>
                                    <strong>staff</strong><br>
                                    <small>staff123</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="app-container" style="display: none;">

        <div class="owner-panel" id="owner_panel" style="display: none;">
            
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="bi bi-list"></i>
            </button>
            
            <aside class="sidebar-coffee position-fixed top-0 start-0 h-100 text-white" id="side_menu" style="width: 260px; z-index: 1000;">
                <div class="d-flex flex-column h-100">
                    <div class="p-4 text-center border-bottom border-coffee-secondary">
                        <div class="d-inline-block p-3 rounded-circle bg-white mb-3">
                            <i class="bi bi-cup-hot-fill text-coffee" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="cursive-font mb-1" style="font-size: 2rem; color:#fff">7AM Coffee</h3>
                        <small class="text-coffee-light opacity-75">Admin Panel</small>
                    </div>
                    
                    <div class="flex-grow-1 p-3">
                        <div class="list-group list-group-flush">
                            <a class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3 active-menu" onclick="switchScreen('dashboard')">
                                <i class="bi bi-speedometer2 me-3"></i>Dashboard
                            </a>
                            <a class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3" onclick="switchScreen('pos')">
                                <i class="bi bi-cart me-3"></i>POS System
                            </a>
                            <a class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3" onclick="switchScreen('sales')">
                                <i class="bi bi-graph-up me-3"></i>Sales Report
                            </a>
                            <a class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3" onclick="switchScreen('inventory')">
                                <i class="bi bi-box-seam me-3"></i>Inventory
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-3 border-top border-coffee-secondary">
                        <a class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3" onclick="doLogout()">
                            <i class="bi bi-box-arrow-right me-3"></i>Logout
                        </a>
                    </div>
                </div>
            </aside>

            <div class="content-area" style="margin-left: 260px; padding: 20px; background-color: #f8f9fa;">
                <div id="dashboard-screen" class="screen-view" style="display: none;">
                    <div class="container-fluid">
                        <div class="row g-4 mb-4">
                            <div class="col-xl-3 col-md-6">
                                <div class="card stat-box-coffee border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="text-muted mb-0">Today's Sales</h6>
                                                <h2 class="coffee-text-gradient mb-0" id="today_sales">₱0.00</h2>
                                            </div>
                                            <i class="bi bi-cash-coin text-coffee" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <small class="text-muted">Total Revenue</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card stat-box-coffee border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="text-muted mb-0">Transactions</h6>
                                                <h2 class="coffee-text-gradient mb-0" id="total_orders">0</h2>
                                            </div>
                                            <i class="bi bi-receipt text-coffee" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <small class="text-muted">Orders Completed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card stat-box-coffee border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="text-muted mb-0">Average Order</h6>
                                                <h2 class="coffee-text-gradient mb-0" id="avg_order">₱0.00</h2>
                                            </div>
                                            <i class="bi bi-calculator text-coffee" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <small class="text-muted">Per Transaction</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card stat-box-coffee border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="text-muted mb-0">Low Stock Items</h6>
                                                <h2 class="coffee-text-gradient mb-0" id="low_stock_count">0</h2>
                                            </div>
                                            <i class="bi bi-exclamation-triangle text-coffee" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <small class="text-muted">Need Restock</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 text-coffee"><i class="bi bi-clock-history me-2"></i>Recent Transactions</h5>
                            </div>
                            <div class="card-body">
                                <div id="recent_transactions" class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Items</th>
                                                <th>Payment</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="sales-screen" class="screen-view" style="display: none;">
                    <div class="container-fluid">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 text-coffee"><i class="bi bi-graph-up me-2"></i>Sales Report</h5>
                                <p class="text-muted mb-0 mt-1">Complete transaction history and analytics</p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="bg-coffee text-white">
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Items</th>
                                                <th>Payment</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sales_list"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="inventory-screen" class="screen-view" style="display: none;">
                    <div class="container-fluid">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0 text-coffee"><i class="bi bi-box-seam me-2"></i>Inventory Management</h5>
                                    <p class="text-muted mb-0 mt-1">Manage your menu items and stock levels</p>
                                </div>
                                <button class="btn btn-coffee" onclick="showAddModal()">
                                    <i class="bi bi-plus-circle me-2"></i>Add New Item
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="bg-coffee text-white">
                                            <tr>
                                                <th>Item Name</th>
                                                <th>Category</th>
                                                <th>Price (₱)</th>
                                                <th>Stock</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inventory_list"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <nav class="navbar navbar-coffee navbar-expand-lg" id="staff_nav" style="display: none;">
            <div class="container-fluid">
                <a class="navbar-brand text-white" href="#">
                    <i class="bi bi-cup-hot-fill me-2"></i>
                    <span class="cursive-font" style="font-size: 1.5rem;">7AM Coffee</span>
                    <small class="text-coffee-light ms-2">EST 2023</small>
                </a>
                
                <div class="d-flex align-items-center">
                    <span class="text-white me-3" id="user_display"></span>
                    <button class="btn btn-outline-light" onclick="doLogout()">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </div>
            </div>
        </nav>

        <div id="pos-screen" class="screen-view">
            <div class="container-fluid py-3">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 text-coffee"><i class="bi bi-cup-hot me-2"></i>Menu Items</h5>
                        </div>
                            <div class="card-body">
                                <div class="row g-2 mb-3" id="filter_buttons">
                                    <div class="col-auto">
                                        <button class="btn btn-coffee active" onclick="filterProducts('All', this)">All</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('Salad', this)">Salad</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('Snacks', this)">Snacks</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('Pasta', this)">Pasta</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('Sandwich', this)">Sandwich</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('Cake', this)">Cake</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('Coffee', this)">Coffee</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('Frappuccino', this)">Frappe</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('NonCoffee', this)">Non-Coffee</button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-coffee" onclick="filterProducts('Tea', this)">Tea</button>
                                    </div>
                                </div>

                                <div class="input-group mb-4">
                                    <span class="input-group-text bg-coffee text-white border-coffee">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" id="search_box" class="form-control border-coffee" placeholder="Search menu by name..." onkeyup="searchProducts()">
                                </div>

                                <div class="row g-3" id="products_list">
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 text-coffee"><i class="bi bi-cart me-2"></i>Order Summary</h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div id="order_items" class="flex-grow-1 mb-3" style="max-height: 400px; overflow-y: auto;">
                                    </div>
                                
                                <div class="border-top pt-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="text-coffee mb-0">TOTAL:</h5>
                                        <h3 class="text-coffee mb-0" id="order_total">₱0.00</h3>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-coffee"><i class="bi bi-credit-card me-2"></i>Payment Method</label>
                                    <select class="form-select border-coffee" id="payment_type">
                                        <option value="Cash">Cash</option>
                                        <option value="Gcash">Gcash</option>
                                        <option value="Paymaya">Paymaya</option>
                                    </select>
                                </div>

                                <div id="notification" class="mb-3"></div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-coffee btn-lg py-3" onclick="processCheckout()">
                                        <i class="bi bi-check-circle me-2"></i>Checkout Order
                                    </button>
                                    <button class="btn btn-outline-coffee" onclick="emptyCart()">
                                        <i class="bi bi-trash me-2"></i>Clear Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="item_modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-coffee">
                <div class="modal-header bg-coffee text-white">
                    <h5 class="modal-title" id="popup_title">Add New Item</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="hideModal()"></button>
                </div>
                <form id="item_form" onsubmit="submitItem(event)">
                    <div class="modal-body">
                        <input type="hidden" id="editing_id">
                        <div class="mb-3">
                            <label class="form-label text-coffee">Item Name</label>
                            <input type="text" id="item_name" class="form-control border-coffee" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-coffee">Category</label>
                            <select id="item_cat" class="form-select border-coffee" required>
                                <option value="">Select Category</option>
                                <option value="Salad">Salad</option>
                                <option value="Snacks">Snacks</option>
                                <option value="Pasta">Pasta</option>
                                <option value="Sandwich">Sandwich</option>
                                <option value="Cake">Cake</option>
                                <option value="Coffee">Coffee</option>
                                <option value="Frappuccino">Frappuccino</option>
                                <option value="NonCoffee">Non-Coffee</option>
                                <option value="Tea">Tea</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-coffee">Price (₱)</label>
                            <input type="number" id="item_price" class="form-control border-coffee" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-coffee">Stock Quantity</label>
                            <input type="number" id="item_qty" class="form-control border-coffee" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-coffee" onclick="hideModal()">Cancel</button>
                        <button type="submit" class="btn btn-coffee">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="receipt_modal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content receipt-coffee">
                <div class="modal-body p-0">
                    <div class="receipt-print p-4" id="print_area">
                        <div class="text-center mb-4">
                            <div class="d-inline-block p-2 rounded-circle bg-coffee mb-2">
                                <i class="bi bi-cup-hot-fill text-white"></i>
                            </div>
                            <h3 class="cursive-font text-coffee mb-1">7AM Coffee</h3>
                            <p class="text-muted mb-2">EST 2023</p>
                            <p class="text-coffee mb-2">Thank you for your order!</p>
                            <small class="text-muted" id="receipt_time"></small>
                        </div>
                        
                        <div class="receipt-lines mb-4" id="receipt_lines"></div>
                        
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="text-coffee" id="receipt_sub">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold text-coffee">TOTAL:</span>
                                <span class="fw-bold text-coffee" id="receipt_total">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Payment:</span>
                                <span class="text-coffee" id="receipt_method"></span>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="text-coffee mb-1">*** ORDER COMPLETED ***</p>
                            <small class="text-muted">Please come again!</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-coffee" onclick="doPrint()">
                        <i class="bi bi-printer me-2"></i>Print
                    </button>
                    <button class="btn btn-outline-coffee" onclick="hideReceipt()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js?v=<?php echo time(); ?>"></script>
    
    <script>
        // Initialize Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        });
    </script>
</body>
</html>