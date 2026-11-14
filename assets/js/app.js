/**
 * assets/js/app.js
 * Frontend Controller yang Menggabungkan:
 * - Modul 11A: AJAX Fetch, Live Search, Loading State, Error Handling, DOM Update
 * - Modul 11B: JSON Endpoint & API Contract Consumption (GET, POST CRUD)
 * - Modul 11C: Bootstrap 5.3 Responsive Dashboard Components & Modal Behavior
 */

// DOM Elements
const searchInput          = document.getElementById("searchInput");
const searchSpinner        = document.getElementById("searchSpinner");
const statusText           = document.getElementById("statusText");
const categoryFilter       = document.getElementById("categoryFilter");
const productCardsContainer = document.getElementById("productCardsContainer");
const productTableBody     = document.getElementById("productTableBody");
const tableCountBadge      = document.getElementById("tableCountBadge");

// Summary Cards Elements
const statTotal            = document.getElementById("statTotal");
const statActive           = document.getElementById("statActive");
const statLowStock         = document.getElementById("statLowStock");

// Alert Element
const alertContainer       = document.getElementById("alertContainer");

// Modal & Form Elements
const productModalElement  = document.getElementById("productModal");
let productModalInstance   = null;
const productModalForm     = document.getElementById("productModalForm");
const modalTitle           = document.getElementById("modalTitle");
const modalProductId       = document.getElementById("modalProductId");
const modalProductCode     = document.getElementById("modalProductCode");
const modalProductName     = document.getElementById("modalProductName");
const modalCategory        = document.getElementById("modalCategory");
const modalPrice           = document.getElementById("modalPrice");
const modalStock           = document.getElementById("modalStock");
const modalStatus          = document.getElementById("modalStatus");
const modalSaveBtn         = document.getElementById("modalSaveBtn");

// Inline Preview Form Elements (Modul 11C Checkpoint 9)
const previewForm          = document.getElementById("previewForm");
const previewName          = document.getElementById("previewName");
const previewCategory      = document.getElementById("previewCategory");
const previewPrice         = document.getElementById("previewPrice");
const previewStock         = document.getElementById("previewStock");
const previewSaveBtn       = document.getElementById("previewSaveBtn");

// Inisialisasi setelah DOM siap
document.addEventListener("DOMContentLoaded", function () {
    if (typeof bootstrap !== "undefined" && productModalElement) {
        productModalInstance = new bootstrap.Modal(productModalElement);
    }

    // Muat data produk pertama kali (Modul 11A Checkpoint 6)
    loadProducts();

    // Event Listener Live Search dengan Debounce (Modul 11A Checkpoint 7)
    let searchTimeout = null;
    searchInput.addEventListener("input", function () {
        clearTimeout(searchTimeout);
        const query = searchInput.value;
        if (searchSpinner) searchSpinner.style.display = "block";
        searchTimeout = setTimeout(() => {
            loadProducts(query, categoryFilter ? categoryFilter.value : "");
        }, 250); // delay 250ms agar tidak spam server
    });

    // Event Listener Filter Kategori
    if (categoryFilter) {
        categoryFilter.addEventListener("change", function () {
            loadProducts(searchInput.value, categoryFilter.value);
        });
    }

    // Event Listener Submit Modal Form (CREATE & UPDATE)
    if (productModalForm) {
        productModalForm.addEventListener("submit", handleModalFormSubmit);
    }

    // Event Listener Tombol "+ Product" di Header (Buka modal dalam mode CREATE)
    const btnOpenAddModal = document.getElementById("btnOpenAddModal");
    if (btnOpenAddModal) {
        btnOpenAddModal.addEventListener("click", openCreateModal);
    }

    // Event Listener Tombol Save pada Form Preview (Modul 11C Checkpoint 9)
    if (previewSaveBtn) {
        previewSaveBtn.addEventListener("click", handlePreviewSave);
    }
});

/**
 * Membaca data produk dari API Backend (api/products/read.php)
 * Menggunakan fetch() asynchronous + response.json()
 * Memperbarui Summary Cards, Product Cards Grid, dan Product Table secara dinamis
 */
async function loadProducts(keyword = "", category = "") {
    showStatus("Loading data...", "text-secondary");
    if (searchSpinner) searchSpinner.style.display = "block";

    try {
        const params = new URLSearchParams();
        if (keyword.trim() !== "") params.append("q", keyword.trim());
        if (category.trim() !== "" && category !== "all") params.append("category", category.trim());

        const url = "api/products/read.php?" + params.toString();
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP Error: ${response.status} (${response.statusText})`);
        }

        const resJson = await response.json();

        if (!resJson.success) {
            throw new Error(resJson.message || "Gagal memuat produk.");
        }

        const products = resJson.data.products || [];
        const stats = resJson.data.stats || { total: 0, active: 0, low_stock: 0 };

        // 1. Update Summary Cards (Modul 11C Checkpoint 6)
        updateSummaryStats(stats);

        // 2. Render Product Cards Grid (Modul 11C Checkpoint 4)
        renderProductCards(products);

        // 3. Render Product Table (Modul 11C Checkpoint 8)
        renderProductTable(products);

        // 4. Update Badge & Status
        if (tableCountBadge) {
            tableCountBadge.textContent = `${products.length} Products`;
        }

        if (keyword.trim() === "") {
            showStatus(`Menampilkan seluruh produk (${products.length} produk).`, "text-muted");
        } else {
            showStatus(`Hasil pencarian: "${keyword}" (${products.length} ditemukan).`, "text-primary fw-medium");
        }

    } catch (error) {
        console.error("Fetch Error:", error);
        showStatus("Gagal mengambil data dari server. Periksa koneksi database/server.", "text-danger fw-bold");
        renderErrorMessage(error.message);
    } finally {
        if (searchSpinner) searchSpinner.style.display = "none";
    }
}

/**
 * Update nilai statistik pada Summary Cards (Modul 11C Checkpoint 6)
 */
function updateSummaryStats(stats) {
    if (statTotal) statTotal.textContent = stats.total ?? 0;
    if (statActive) statActive.textContent = stats.active ?? 0;
    if (statLowStock) statLowStock.textContent = stats.low_stock ?? 0;
}

/**
 * Render Product Cards Grid (Modul 11C Checkpoint 4: 12-kolom responsive grid)
 * col-12 -> mobile (1 card)
 * col-md-6 -> tablet (2 card)
 * col-lg-4 -> desktop (3 card)
 */
function renderProductCards(products) {
    if (!productCardsContainer) return;

    if (products.length === 0) {
        productCardsContainer.innerHTML = `
            <div class="col-12">
                <div class="alert alert-light border text-center py-5">
                    <p class="h6 text-secondary mb-1">Tidak ada produk yang ditemukan</p>
                    <small class="text-muted">Coba kata kunci lain atau tambahkan produk baru.</small>
                </div>
            </div>
        `;
        return;
    }

    let cardsHtml = "";
    products.forEach(p => {
        const formattedPrice = formatRupiah(p.price);
        const stockBadge = p.stock <= 5 
            ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Stok Kritis: ${p.stock}</span>`
            : `<span class="badge bg-success-subtle text-success border border-success-subtle">Stok: ${p.stock}</span>`;

        cardsHtml += `
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 product-card shadow-sm border">
                    <img 
                        src="${escapeHtml(p.image_url || 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=500&auto=format&fit=crop&q=60')}" 
                        class="card-img-top" 
                        alt="${escapeHtml(p.name)}"
                        onerror="this.src='https://placehold.co/600x360?text=Product';"
                    >
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle badge-category">
                                ${escapeHtml(p.category)}
                            </span>
                            <span class="text-muted small">${escapeHtml(p.code)}</span>
                        </div>
                        <h3 class="h5 card-title fw-semibold text-truncate mb-2" title="${escapeHtml(p.name)}">
                            ${escapeHtml(p.name)}
                        </h3>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Harga</small>
                                <strong class="text-dark fs-5">${formattedPrice}</strong>
                            </div>
                            <div>
                                ${stockBadge}
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary flex-fill" onclick="editProduct(${p.id})">
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id}, '${escapeHtml(p.name)}')">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    productCardsContainer.innerHTML = cardsHtml;
}

/**
 * Render Product Table (Modul 11C Checkpoint 8: Table Responsive + Table Striped + Table Hover)
 */
function renderProductTable(products) {
    if (!productTableBody) return;

    if (products.length === 0) {
        productTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    Produk tidak ditemukan.
                </td>
            </tr>
        `;
        return;
    }

    let rowsHtml = "";
    products.forEach(p => {
        const formattedPrice = formatRupiah(p.price);
        const stockBadge = p.stock <= 5 
            ? `<span class="badge bg-danger">Low (${p.stock})</span>`
            : `<span class="badge bg-success">${p.stock}</span>`;

        const statusBadge = p.status === 'active'
            ? `<span class="badge bg-success-subtle text-success">Active</span>`
            : `<span class="badge bg-secondary-subtle text-secondary">Inactive</span>`;

        rowsHtml += `
            <tr>
                <td class="fw-semibold text-secondary">${escapeHtml(p.code)}</td>
                <td class="fw-medium text-dark">${escapeHtml(p.name)}</td>
                <td><span class="badge bg-light text-dark border">${escapeHtml(p.category)}</span></td>
                <td class="fw-bold">${formattedPrice}</td>
                <td>${stockBadge}</td>
                <td>${statusBadge}</td>
                <td class="text-nowrap">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editProduct(${p.id})">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id}, '${escapeHtml(p.name)}')">
                        Hapus
                    </button>
                </td>
            </tr>
        `;
    });

    productTableBody.innerHTML = rowsHtml;
}

/**
 * Buka modal dalam mode CREATE produk baru
 */
function openCreateModal() {
    if (productModalForm) productModalForm.reset();
    if (modalProductId) modalProductId.value = "";
    if (modalTitle) modalTitle.textContent = "Add Product (Tambah Produk)";
    if (modalSaveBtn) modalSaveBtn.textContent = "Simpan Produk";
    if (modalStatus) modalStatus.value = "active";
    if (productModalInstance) productModalInstance.show();
}

/**
 * Handle form modal CREATE / UPDATE
 */
async function handleModalFormSubmit(event) {
    event.preventDefault();

    const id = modalProductId.value.trim();
    const isEdit = id !== "";

    const payload = {
        name: modalProductName.value.trim(),
        category: modalCategory.value,
        price: parseInt(modalPrice.value, 10),
        stock: parseInt(modalStock.value, 10),
        status: modalStatus.value
    };

    if (modalProductCode && modalProductCode.value.trim() !== "") {
        payload.code = modalProductCode.value.trim();
    }

    if (isEdit) {
        payload.id = parseInt(id, 10);
    }

    const endpoint = isEdit ? "api/products/update.php" : "api/products/create.php";

    try {
        if (modalSaveBtn) {
            modalSaveBtn.disabled = true;
            modalSaveBtn.textContent = "Menyimpan...";
        }

        const response = await fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Gagal menyimpan data.");
        }

        // Tutup modal
        if (productModalInstance) {
            productModalInstance.hide();
        }

        showAlert(result.message || (isEdit ? "Produk berhasil diperbarui." : "Produk baru berhasil disimpan."), "success");
        
        // Reload daftar produk tanpa refresh halaman
        await loadProducts(searchInput.value, categoryFilter ? categoryFilter.value : "");

    } catch (error) {
        console.error("Save Error:", error);
        showAlert(error.message || "Terjadi kesalahan saat menyimpan data.", "danger");
    } finally {
        if (modalSaveBtn) {
            modalSaveBtn.disabled = false;
            modalSaveBtn.textContent = isEdit ? "Simpan Perubahan" : "Simpan Produk";
        }
    }
}

/**
 * Buka modal dan ambil detail produk untuk mode EDIT
 */
async function editProduct(id) {
    try {
        showStatus(`Mengambil data produk ID: ${id}...`, "text-secondary");
        const response = await fetch(`api/products/detail.php?id=${id}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Gagal mengambil data produk.");
        }

        const p = result.data;

        // Isi form modal
        if (modalProductId) modalProductId.value = p.id;
        if (modalProductCode) modalProductCode.value = p.code;
        if (modalProductName) modalProductName.value = p.name;
        if (modalCategory) modalCategory.value = p.category;
        if (modalPrice) modalPrice.value = p.price;
        if (modalStock) modalStock.value = p.stock;
        if (modalStatus) modalStatus.value = p.status;

        if (modalTitle) modalTitle.textContent = `Edit Produk: ${p.name}`;
        if (modalSaveBtn) modalSaveBtn.textContent = "Simpan Perubahan";

        if (productModalInstance) {
            productModalInstance.show();
        }
        showStatus("Mode edit produk dibuka.", "text-muted");

    } catch (error) {
        console.error("Edit Error:", error);
        showAlert(error.message || "Gagal memuat detail produk untuk diedit.", "danger");
    }
}

/**
 * Menghapus produk dengan AJAX (api/products/delete.php)
 */
async function deleteProduct(id, name) {
    if (!confirm(`Apakah Anda yakin ingin menghapus produk "${name}"?`)) {
        return;
    }

    try {
        showStatus(`Menghapus produk "${name}"...`, "text-warning");

        const response = await fetch("api/products/delete.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Gagal menghapus produk.");
        }

        showAlert(result.message || "Produk berhasil dihapus.", "success");
        await loadProducts(searchInput.value, categoryFilter ? categoryFilter.value : "");

    } catch (error) {
        console.error("Delete Error:", error);
        showAlert(error.message || "Gagal menghapus produk.", "danger");
    }
}

/**
 * Handle tombol simpan dari Inline Form Preview (Modul 11C Checkpoint 9)
 */
async function handlePreviewSave() {
    const name = previewName ? previewName.value.trim() : "";
    const category = previewCategory ? previewCategory.value : "";
    const price = previewPrice ? parseInt(previewPrice.value, 10) : 0;
    const stock = previewStock ? parseInt(previewStock.value, 10) : 0;

    if (!name) {
        showAlert("Nama produk pada form preview harus diisi!", "warning");
        if (previewName) previewName.focus();
        return;
    }

    const payload = {
        name: name,
        category: category,
        price: isNaN(price) ? 0 : price,
        stock: isNaN(stock) ? 0 : stock,
        status: "active"
    };

    try {
        if (previewSaveBtn) {
            previewSaveBtn.disabled = true;
            previewSaveBtn.textContent = "Menyimpan...";
        }

        const response = await fetch("api/products/create.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Gagal menyimpan produk.");
        }

        showAlert(`Produk "${name}" dari Form Preview berhasil ditambahkan!`, "success");
        if (previewForm) previewForm.reset();
        await loadProducts();

    } catch (error) {
        console.error("Preview Save Error:", error);
        showAlert(error.message, "danger");
    } finally {
        if (previewSaveBtn) {
            previewSaveBtn.disabled = false;
            previewSaveBtn.textContent = "Save Preview";
        }
    }
}

/**
 * Tampilkan Alert Notification Bootstrap
 * @param {string} message
 * @param {string} type ('success', 'warning', 'danger', 'info')
 */
function showAlert(message, type = "success") {
    if (!alertContainer) return;

    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <span>${escapeHtml(message)}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    alertContainer.innerHTML = alertHtml;

    // Auto dismiss setelah 4 detik
    setTimeout(() => {
        const activeAlert = alertContainer.querySelector(".alert");
        if (activeAlert && typeof bootstrap !== "undefined") {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(activeAlert);
            bsAlert.close();
        }
    }, 4000);
}

/**
 * Menampilkan status pencarian / operasi
 */
function showStatus(message, textClass = "text-muted") {
    if (statusText) {
        statusText.className = "small " + textClass;
        statusText.textContent = message;
    }
}

/**
 * Menampilkan pesan error pada card & table jika server mati
 */
function renderErrorMessage(msg) {
    if (productCardsContainer) {
        productCardsContainer.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger shadow-sm">
                    <h5 class="alert-heading">Koneksi Server / Database Gagal</h5>
                    <p class="mb-1">${escapeHtml(msg)}</p>
                    <hr>
                    <small>Pastikan Laragon/XAMPP aktif dan database <code>ajax_lab</code> telah di-import.</small>
                </div>
            </div>
        `;
    }
    if (productTableBody) {
        productTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-danger py-4">
                    Gagal mengambil data dari server. (${escapeHtml(msg)})
                </td>
            </tr>
        `;
    }
}

/**
 * Format angka ke mata uang Rupiah
 */
function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0
    }).format(number);
}

/**
 * Escape karakter HTML untuk mencegah XSS
 */
function escapeHtml(string) {
    if (string === null || string === undefined) return "";
    return String(string)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
