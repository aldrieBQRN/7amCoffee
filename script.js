let activeCategory = 'All';
let orderList = [];
let transactionHistory = []; // This will now be populated from the Database
let loggedInUser = null;
let menuItems = {};
let nextId = 1;

// Product icons with coffee theme
const icons = {
    1: '🥗', 2: '🥗', 3: '🥗',
    4: '🍟', 5: '🥓', 6: '🧀', 7: '🍗', 8: '🧇', 9: '🧇', 10: '🧇',
    11: '🍝', 12: '🍝', 13: '🍝', 14: '🍝', 15: '🍝',
    16: '🥪', 17: '🥪', 18: '🥪', 19: '🥪', 20: '🥪', 21: '🌭', 22: '🥪',
    23: '🍰', 24: '🍰', 25: '🍰', 26: '🍰', 27: '🍰',
    28: '☕', 29: '☕', 30: '☕', 31: '☕', 32: '☕', 33: '☕',
    34: '🧊', 35: '🧊', 36: '🧊', 37: '🧊', 38: '🧊', 39: '🧊',
    40: '🍫', 41: '🧃', 42: '🧃', 43: '🧃', 44: '🧃', 45: '🧃', 
    46: '🥛', 47: '🫖', 48: '🥛', 49: '🧊',
    50: '🫖', 51: '🫖', 52: '🫖'
};

function getIcon(itemId) {
    return icons[itemId] || '🍽️';
}

// Helper to find item
function findItem(itemId) {
    for (const cat in menuItems) {
        const found = menuItems[cat].find(i => i.id === itemId);
        if (found) return found;
    }
    return null;
}

// Get all items as flat array
function getAllItems() {
    return Object.values(menuItems).flat();
}

// Setup menu by fetching from Database
function setupMenu() {
    return fetch('actions/get_inventory.php?t=' + new Date().getTime())
        .then(response => response.json())
        .then(data => {
            menuItems = {};
            
            data.forEach(item => {
                if (!menuItems[item.category]) {
                    menuItems[item.category] = [];
                }
                menuItems[item.category].push(item);
            });

            const allIds = data.map(i => i.id);
            if (allIds.length > 0) {
                nextId = Math.max(...allIds) + 1;
            }

            filterProducts(activeCategory, document.querySelector(`.filter-btn[onclick*="${activeCategory}"]`));
            return true;
        })
        .catch(error => {
            console.error('Error loading inventory:', error);
            const productArea = document.getElementById('products_list');
            if(productArea) {
                 productArea.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <h5 class="text-danger mt-3">Error loading menu</h5>
                        <p class="text-muted">Unable to connect to database</p>
                    </div>
                 `;
            }
        });
}

// Fetch Sales History from Database
function fetchSalesHistory() {
    return fetch('actions/get_sales.php?t=' + new Date().getTime())
        .then(response => response.json())
        .then(data => {
            transactionHistory = data.map(row => ({
                id: row.id,
                date: row.date_time,
                items: row.items,
                total: row.total,
                payment: row.payment
            }));
            return true;
        })
        .catch(error => {
            console.error('Error loading sales:', error);
        });
}

// Mobile menu toggle
function toggleMobileMenu() {
    const menu = document.getElementById('side_menu');
    if (window.innerWidth <= 992) {
        menu.classList.toggle('show');
    }
}

// Switch between screens
function switchScreen(screenName) {
    // Hide all screens
    document.querySelectorAll('.screen-view').forEach(view => view.style.display = 'none');
    
    // Remove active class from all menu items
    document.querySelectorAll('.menu-link, .list-group-item').forEach(link => {
        link.classList.remove('active', 'active-menu');
    });
    
    // Show target screen
    const targetScreen = document.getElementById(`${screenName}-screen`);
    if (targetScreen) {
        targetScreen.style.display = 'block';
        targetScreen.classList.add('fade-in');
    }

    // === FIX START: ROBUST LAYOUT HANDLING ===
    const posScreen = document.getElementById('pos-screen');

    // 1. POS Screen: Needs margin because it sits OUTSIDE the main content area
    if (posScreen) {
        if (loggedInUser && loggedInUser.role === 'Owner') {
            posScreen.classList.add('owner-screen-layout');
        } else {
            posScreen.classList.remove('owner-screen-layout');
        }
    }

    // 2. Admin Screens: Ensure they DO NOT have the margin class
    // This prevents double spacing/indentation since they are already inside .content-area
    ['inventory-screen', 'dashboard-screen', 'sales-screen'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.remove('owner-screen-layout');
    });
    // === FIX END ===

    // Add active class to current menu item
    const menuItems = document.querySelectorAll('.list-group-item');
    menuItems.forEach(item => {
        if (item.textContent.includes(screenName.charAt(0).toUpperCase() + screenName.slice(1))) {
            item.classList.add('active-menu');
        }
    });

    // Close mobile menu if open
    if (window.innerWidth <= 992) {
        const menu = document.getElementById('side_menu');
        if (menu) menu.classList.remove('show');
    }
    
    // Refresh data based on screen
    if (screenName === 'inventory') loadInventory();
    else if (screenName === 'pos') searchProducts();
    else if (screenName === 'dashboard') {
        fetchSalesHistory().then(() => loadDashboard());
    }
    else if (screenName === 'sales') {
        fetchSalesHistory().then(() => loadSales());
    }
}

// Login function
function doLogin() {
    const username = document.getElementById('user_name').value.trim();
    const password = document.getElementById('pass_word').value.trim();
    const msgBox = document.getElementById('login_msg');

    if (!username || !password) {
        msgBox.textContent = 'Please enter both username and password.';
        msgBox.classList.remove('d-none');
        return;
    }

    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const normalizedRole = data.role.charAt(0).toUpperCase() + data.role.slice(1);
            
            loggedInUser = {
                id: data.id,
                username: username,
                role: normalizedRole
            };

            msgBox.classList.add('d-none');
            document.getElementById('login-screen').style.display = 'none';
            document.getElementById('app-container').style.display = 'block';
            
            const ownerPanel = document.getElementById('owner_panel');
            const posView = document.getElementById('pos-screen');
            const staffNav = document.getElementById('staff_nav');
            
            setupMenu();
            fetchSalesHistory().then(() => {
                if (loggedInUser.role === 'Owner') {
                    ownerPanel.style.display = 'flex';
                    posView.style.display = 'none';
                    staffNav.style.display = 'none';
                    switchScreen('dashboard');
                } else {
                    ownerPanel.style.display = 'none';
                    staffNav.style.display = 'block';
                    posView.style.display = 'block';
                    document.getElementById('user_display').textContent = loggedInUser.role;
                    switchScreen('pos');
                }
            });
            
            document.getElementById('user_name').value = '';
            document.getElementById('pass_word').value = '';
            refreshCart();
        } else {
            msgBox.textContent = data.message || 'Invalid username or password.';
            msgBox.classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Login Error:', error);
        msgBox.textContent = 'An error occurred. Please try again.';
        msgBox.classList.remove('d-none');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const usernameField = document.getElementById('user_name');
    const passwordField = document.getElementById('pass_word');
    if (usernameField && passwordField) {
        [usernameField, passwordField].forEach(field => {
            field.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') doLogin();
            });
        });
    }
});

function doLogout() {
    loggedInUser = null;
    orderList = [];
    activeCategory = 'All';
    transactionHistory = [];
    document.getElementById('app-container').style.display = 'none';
    document.getElementById('login-screen').style.display = 'flex';
    
    document.querySelectorAll('#filter_buttons .btn').forEach(b => {
        b.classList.remove('btn-coffee', 'active');
        b.classList.add('btn-outline-coffee');
    });
    
    const allBtn = document.querySelector('.filter-btn[onclick*="All"]');
    if (allBtn) {
        allBtn.classList.remove('btn-outline-coffee');
        allBtn.classList.add('btn-coffee', 'active');
    }
}

function loadDashboard() {
    if (!loggedInUser || loggedInUser.role !== 'Owner') return;
    
    const totalRevenue = transactionHistory.reduce((sum, t) => sum + t.total, 0);
    const orderCount = transactionHistory.length;
    const averageValue = orderCount > 0 ? totalRevenue / orderCount : 0;
    const lowStockCount = getAllItems().filter(item => item.stock <= 5 && item.stock > 0).length;

    document.getElementById('today_sales').textContent = `₱${totalRevenue.toFixed(2)}`;
    document.getElementById('total_orders').textContent = orderCount;
    document.getElementById('avg_order').textContent = `₱${averageValue.toFixed(2)}`;
    document.getElementById('low_stock_count').textContent = lowStockCount;

    const recentDiv = document.getElementById('recent_transactions');
    if (transactionHistory.length === 0) {
        recentDiv.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No transactions found.</p>
            </div>
        `;
    } else {
        const recent = transactionHistory.slice(0, 5);
        const tableBody = recentDiv.querySelector('tbody') || recentDiv;
        
        if (tableBody.tagName === 'TBODY') {
            tableBody.innerHTML = recent.map(t => `
                <tr>
                    <td>${t.date}</td>
                    <td>${t.items}</td>
                    <td><span class="badge bg-coffee">${t.payment}</span></td>
                    <td class="text-end"><strong>₱${t.total.toFixed(2)}</strong></td>
                </tr>
            `).join('');
        } else {
            // FIX: Removed the outer <div class="table-responsive"> to prevent nesting issues
            // The parent div in index.php already has this class.
            recentDiv.innerHTML = `
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
                        ${recent.map(t => `
                            <tr>
                                <td>${t.date}</td>
                                <td>${t.items}</td>
                                <td><span class="badge bg-coffee">${t.payment}</span></td>
                                <td class="text-end"><strong>₱${t.total.toFixed(2)}</strong></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
    }
}

function loadSales() {
    if (!loggedInUser || loggedInUser.role !== 'Owner') return;
    const salesTable = document.getElementById('sales_list');
    
    if (transactionHistory.length === 0) {
        salesTable.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <i class="bi bi-receipt text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2">No sales transactions found</p>
                </td>
            </tr>
        `;
    } else {
        salesTable.innerHTML = transactionHistory.map(t => `
            <tr>
                <td>${t.date}</td>
                <td>${t.items}</td>
                <td><span class="badge bg-coffee">${t.payment}</span></td>
                <td class="text-end"><strong>₱${t.total.toFixed(2)}</strong></td>
            </tr>
        `).join('');
    }
}

function loadInventory() {
    if (!loggedInUser || loggedInUser.role !== 'Owner') return;
    
    const table = document.getElementById('inventory_list');
    
    const allItems = getAllItems();
    allItems.sort((a, b) => b.id - a.id);
    
    if (allItems.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2">No inventory items found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    table.innerHTML = allItems.map(item => {
        const lowStock = item.stock <= 5 && item.stock > 0;
        const noStock = item.stock <= 0;
        
        let stockBadge = '';
        if (noStock) {
            stockBadge = '<span class="badge bg-danger">Out of Stock</span>';
        } else if (lowStock) {
            stockBadge = '<span class="badge bg-warning">Low Stock</span>';
        } else {
            stockBadge = '<span class="badge bg-success">In Stock</span>';
        }
        
        return `
            <tr>
                <td>${item.name}</td>
                <td><span class="badge bg-coffee">${item.category}</span></td>
                <td><strong class="text-coffee">₱${item.price.toFixed(2)}</strong></td>
                <td>
                    <span class="${lowStock || noStock ? 'fw-bold' : ''}">
                        ${item.stock} ${stockBadge}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-coffee me-2" onclick="openEditModal(${item.id})">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeItem(${item.id})">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function showAddModal() {
    document.getElementById('popup_title').textContent = 'Add New Item';
    const form = document.getElementById('item_form');
    if (form) {
        form.reset();
        document.getElementById('editing_id').value = '';
        
        const modalElement = document.getElementById('item_modal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}

function hideModal() {
    const modalElement = document.getElementById('item_modal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
}

function openEditModal(itemId) {
    const item = findItem(itemId);
    if (!item) return;
    
    document.getElementById('popup_title').textContent = 'Edit Item';
    document.getElementById('editing_id').value = item.id;
    document.getElementById('item_name').value = item.name;
    document.getElementById('item_cat').value = item.category;
    document.getElementById('item_price').value = item.price;
    document.getElementById('item_qty').value = item.stock;
    
    const modalElement = document.getElementById('item_modal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function submitItem(e) {
    e.preventDefault();
    
    const editingId = document.getElementById('editing_id').value;
    const itemName = document.getElementById('item_name').value.trim();
    const category = document.getElementById('item_cat').value;
    const price = document.getElementById('item_price').value;
    const quantity = document.getElementById('item_qty').value;
    
    const formData = new FormData();
    formData.append('name', itemName);
    formData.append('category', category);
    formData.append('price', price);
    formData.append('stock', quantity);
    
    let url = 'actions/add_item.php';
    if (editingId) {
        formData.append('id', editingId);
        url = 'actions/update_item.php';
    }

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            hideModal();
            setupMenu().then(() => {
                loadInventory();
            });
        } else {
            showNotification('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'danger');
    });
}

function removeItem(itemId) {
    if (!confirm('Are you sure you want to delete this item? This cannot be undone.')) return;
    
    const formData = new FormData();
    formData.append('id', itemId);

    fetch('actions/delete_item.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setupMenu().then(() => {
                loadInventory();
            });
        } else {
            showNotification('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to delete item.', 'danger');
    });
}

function filterProducts(category, btn) {
    activeCategory = category;
    
    document.querySelectorAll('#filter_buttons .btn').forEach(b => {
        b.classList.remove('btn-coffee', 'active');
        b.classList.add('btn-outline-coffee');
    });
    
    if (btn) {
        btn.classList.remove('btn-outline-coffee');
        btn.classList.add('btn-coffee', 'active');
    } else {
        const defaultBtn = document.querySelector(`.filter-btn[onclick*="${category}"]`);
        if (defaultBtn) {
            defaultBtn.classList.remove('btn-outline-coffee');
            defaultBtn.classList.add('btn-coffee', 'active');
        }
    }
    searchProducts();
}

function searchProducts() {
    const productArea = document.getElementById('products_list');
    productArea.innerHTML = '';
    const searchText = document.getElementById('search_box').value.toLowerCase();
    
    let filtered = getAllItems().filter(item => {
        const catMatch = activeCategory === 'All' || item.category === activeCategory;
        const nameMatch = item.name.toLowerCase().includes(searchText);
        return catMatch && nameMatch;
    });
    
    if (filtered.length === 0) {
        productArea.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No items found</h5>
                <p class="text-muted">Try a different search or category</p>
            </div>
        `;
        return;
    }

    filtered.forEach(item => {
        const outOfStock = item.stock <= 0;
        const lowStock = item.stock > 0 && item.stock <= 5;
        
        const col = document.createElement('div');
        col.className = 'col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3';
        
        const card = document.createElement('div');
        card.className = `product-card-coffee card h-100 ${outOfStock ? 'opacity-75' : 'clickable'}`;
        card.setAttribute('data-id', item.id);
        
        if (!outOfStock) {
            card.setAttribute('onclick', `addToOrder(${item.id})`);
            card.style.cursor = 'pointer';
        } else {
            card.style.cursor = 'not-allowed';
        }

        card.innerHTML = `
            <div class="card-body d-flex flex-column p-3">
                <div class="d-flex align-items-start mb-2">
                    <div class="product-icon-coffee me-3 flex-shrink-0">
                        ${getIcon(item.id)}
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold text-coffee" style="font-size: 0.9rem;">${item.name}</h6>
                        <span class="badge ${lowStock ? 'bg-warning' : outOfStock ? 'bg-danger' : 'bg-coffee'}">
                            ${item.category}
                        </span>
                    </div>
                </div>
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="${lowStock ? 'text-warning' : outOfStock ? 'text-danger' : 'text-muted'}">
                                <i class="bi ${outOfStock ? 'bi-x-circle' : lowStock ? 'bi-exclamation-triangle' : 'bi-check-circle'}"></i>
                                ${outOfStock ? 'Out of Stock' : lowStock ? `Only ${item.stock} left` : 'In Stock'}
                            </small>
                        </div>
                        <h5 class="text-coffee mb-0">₱${item.price.toFixed(2)}</h5>
                    </div>
                </div>
            </div>
        `;
        
        col.appendChild(card);
        productArea.appendChild(col);
    });
}

function addToOrder(itemId) {
    const product = findItem(itemId);
    if (!product) {
        showNotification('Error: Item not found.', 'danger');
        return;
    }
    if (product.stock <= 0) {
        showNotification(`${product.name} is out of stock.`, 'danger');
        return;
    }
    const existing = orderList.find(i => i.id === itemId);
    if (existing) {
        const newQty = existing.qty + 1;
        if (newQty > product.stock) {
            showNotification(`Not enough stock for ${product.name}. Only ${product.stock} available.`, 'danger');
            return;
        }
        existing.qty += 1;
    } else {
        orderList.push({
            id: product.id,
            name: product.name,
            price: product.price,
            qty: 1,
            category: product.category
        });
    }
    showNotification(`${product.name} added to cart!`, 'success');
    refreshCart();
}

function changeQuantity(itemId, delta) {
    const orderItem = orderList.find(i => i.id === itemId);
    const product = findItem(itemId);
    if (orderItem) {
        const updatedQty = orderItem.qty + delta;
        if (updatedQty > 0) {
            if (delta > 0 && updatedQty > product.stock) {
                showNotification(`Cannot add more ${orderItem.name}. Only ${product.stock} available.`, 'danger');
                return;
            }
            orderItem.qty = updatedQty;
        } else {
            orderList = orderList.filter(i => i.id !== itemId);
        }
        refreshCart();
        document.getElementById('notification').innerHTML = '';
    }
}

function refreshCart() {
    const cartArea = document.getElementById('order_items');
    const totalDisplay = document.getElementById('order_total');
    let sum = 0;
    
    if (orderList.length === 0) {
        cartArea.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-cart text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">Cart is empty</p>
                <small class="text-muted">Add items from the menu to get started</small>
            </div>
        `;
        totalDisplay.textContent = '₱0.00';
        return;
    }
    
    let html = '';
    orderList.forEach(item => {
        const lineTotal = item.price * item.qty;
        sum += lineTotal;
        html += `
            <div class="card mb-2 border-coffee">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-coffee" style="font-size: 0.9rem;">${item.name}</h6>
                            <small class="text-muted">₱${item.price.toFixed(2)} each</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="btn-group btn-group-sm me-3">
                                <button class="btn btn-outline-coffee" onclick="changeQuantity(${item.id}, -1)">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <span class="btn btn-coffee disabled px-3">${item.qty}</span>
                                <button class="btn btn-outline-coffee" onclick="changeQuantity(${item.id}, 1)">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <h6 class="mb-0 text-coffee">₱${lineTotal.toFixed(2)}</h6>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    cartArea.innerHTML = html;
    totalDisplay.textContent = `₱${sum.toFixed(2)}`;
}

function emptyCart() {
    if (orderList.length === 0) {
        showNotification('Cart is already empty.', 'warning');
        return;
    }
    
    const confirmModal = `
        <div class="modal fade" id="confirmClearModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-coffee">
                    <div class="modal-header bg-coffee text-white">
                        <h5 class="modal-title">Clear Cart</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to clear all items from the cart?</p>
                        <p class="text-muted"><small>This action cannot be undone.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-coffee" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-coffee" onclick="confirmClearCart()">Clear Cart</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    let modalElement = document.getElementById('confirmClearModal');
    if (!modalElement) {
        modalElement = document.createElement('div');
        modalElement.innerHTML = confirmModal;
        document.body.appendChild(modalElement);
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function confirmClearCart() {
    orderList = [];
    refreshCart();
    showNotification('Cart cleared!', 'success');
    
    const modalElement = document.getElementById('confirmClearModal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) modal.hide();
}

function processCheckout() {
    if (orderList.length === 0) {
        showNotification('Cannot checkout an empty cart.', 'danger');
        return;
    }

    const grandTotal = orderList.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const paymentType = document.getElementById('payment_type').value;

    for (const orderItem of orderList) {
        const product = findItem(orderItem.id);
        if (orderItem.qty > product.stock) {
            showNotification(`Checkout failed! Not enough stock for ${orderItem.name}. Available: ${product.stock}`, 'danger');
            return;
        }
    }

    const orderData = {
        items: orderList,
        payment_method: paymentType,
        total: grandTotal
    };

    const checkoutBtn = document.querySelector('button[onclick="processCheckout()"]');
    let originalText = '';
    if (checkoutBtn) {
        originalText = checkoutBtn.innerHTML;
        checkoutBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
        checkoutBtn.disabled = true;
    }

    fetch('actions/process_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (checkoutBtn) {
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
        }
        
        if (data.success) {
            for (const orderItem of orderList) {
                const product = findItem(orderItem.id);
                if (product) {
                    product.stock -= orderItem.qty;
                }
            }

            fetchSalesHistory().then(() => {
                showNotification('Order saved successfully!', 'success');
                
                if (loggedInUser.role === 'Staff') displayReceipt(orderList, grandTotal, paymentType);
                
                orderList = [];
                refreshCart();
                searchProducts();
                
                if (loggedInUser.role === 'Owner') loadDashboard();
            });

        } else {
            showNotification('Database Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (checkoutBtn) {
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
        }
        showNotification('Connection error. Please try again.', 'danger');
    });
}

function displayReceipt(items, total, payment) {
    const timeField = document.getElementById('receipt_time');
    const linesArea = document.getElementById('receipt_lines');
    const subField = document.getElementById('receipt_sub');
    const totalField = document.getElementById('receipt_total');
    const methodField = document.getElementById('receipt_method');
    
    const now = new Date();
    timeField.textContent = now.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    subField.textContent = `₱${total.toFixed(2)}`;
    totalField.textContent = `₱${total.toFixed(2)}`;
    methodField.textContent = payment;
    
    linesArea.innerHTML = items.map(item => `
        <div class="d-flex justify-content-between mb-2">
            <div>
                <div class="fw-bold" style="font-size: 0.9rem;">${item.name}</div>
                <small class="text-muted">${item.qty} × ₱${item.price.toFixed(2)}</small>
            </div>
            <div class="text-coffee fw-bold">₱${(item.price * item.qty).toFixed(2)}</div>
        </div>
    `).join('');
    
    const modalElement = document.getElementById('receipt_modal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function hideReceipt() {
    const modalElement = document.getElementById('receipt_modal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
}

function doPrint() {
    window.print();
}

function showNotification(msg, type) {
    const notifBox = document.getElementById('notification');
    if (!notifBox) return;
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'} me-2"></i>
        ${msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    notifBox.innerHTML = '';
    notifBox.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode === notifBox) {
            alertDiv.classList.remove('show');
            setTimeout(() => {
                if (alertDiv.parentNode === notifBox) {
                    notifBox.removeChild(alertDiv);
                }
            }, 150);
        }
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    const defaultBtn = document.querySelector('.filter-btn[onclick*="All"]');
    if (defaultBtn) {
        defaultBtn.classList.remove('btn-outline-coffee');
        defaultBtn.classList.add('btn-coffee', 'active');
    }
    
    const loginScreen = document.getElementById('login-screen');
    if (loginScreen) {
        for (let i = 1; i <= 15; i++) {
            const bean = document.createElement('div');
            bean.className = 'coffee-bean position-absolute';
            bean.style.left = `${Math.random() * 100}%`;
            bean.style.top = `${Math.random() * 100}%`;
            bean.style.fontSize = `${Math.random() * 20 + 20}px`;
            bean.style.opacity = `0.${Math.floor(Math.random() * 3 + 1)}`;
            bean.style.animation = `bean-float ${Math.random() * 15 + 15}s infinite ease-in-out ${Math.random() * 10}s`;
            bean.textContent = '☕';
            loginScreen.appendChild(bean);
        }
    }
});