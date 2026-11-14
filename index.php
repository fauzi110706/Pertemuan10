<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap Product Dashboard - Fullstack Integration</title>

    <!-- Bootstrap 5.3 CSS CDN (Modul 11C Checkpoint 1) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons (Optional for richer UI) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS (Modul 11C Checkpoint 12) -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>

    <!-- ============================================================== -->
    <!-- NAVBAR RESPONSIVE (Modul 11C Checkpoint 7)                    -->
    <!-- ============================================================== -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="#">
                <i class="bi bi-box-seam"></i> ProductLab
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNav"
                aria-controls="mainNav"
                aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link active fw-medium" href="#dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#table">Table</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#form-preview">Form Preview</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-outline-dark btn-sm rounded-pill px-3" href="tailwind.php">
                            <i class="bi bi-palette"></i> Coba Versi Tailwind
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Container Utama Responsive (Modul 11C Checkpoint 2) -->
    <main class="container py-4" id="dashboard">

        <!-- Notification / Alert Container (Modul 11C Checkpoint 10) -->
        <div id="alertContainer" class="mb-3"></div>

        <!-- ============================================================== -->
        <!-- HEADER DASHBOARD DENGAN FLEXBOX (Modul 11C Checkpoint 5)      -->
        <!-- ============================================================== -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Product Dashboard</h1>
                <p class="text-body-secondary mb-0">Kelola tampilan produk dan data inventaris secara real-time via AJAX & API.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" id="btnOpenAddModal">
                    <i class="bi bi-plus-lg"></i> + Product
                </button>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- SUMMARY CARDS DINAMIS (Modul 11C Checkpoint 6)                -->
        <!-- ============================================================== -->
        <section class="mb-5 dashboard-section">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100 border-0 border-start border-4 border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-body-secondary mb-1 small text-uppercase fw-semibold">Total Product</p>
                                    <p class="display-6 fw-bold mb-0 text-dark" id="statTotal">0</p>
                                </div>
                                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                                    <i class="bi bi-boxes fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100 border-0 border-start border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-body-secondary mb-1 small text-uppercase fw-semibold">Active / Ready</p>
                                    <p class="display-6 fw-bold mb-0 text-success" id="statActive">0</p>
                                </div>
                                <div class="bg-success-subtle text-success p-3 rounded-circle">
                                    <i class="bi bi-check-circle fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100 border-0 border-start border-4 border-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-body-secondary mb-1 small text-uppercase fw-semibold">Low Stock (&le; 5)</p>
                                    <p class="display-6 fw-bold mb-0 text-danger" id="statLowStock">0</p>
                                </div>
                                <div class="bg-danger-subtle text-danger p-3 rounded-circle">
                                    <i class="bi bi-exclamation-triangle fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================== -->
        <!-- LIVE AJAX SEARCH BAR & FILTER (Modul 11A Checkpoint 4, 7 & 8)  -->
        <!-- ============================================================== -->
        <section class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-8 search-wrapper">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input
                                type="search"
                                id="searchInput"
                                class="form-control border-start-0 ps-0"
                                placeholder="Ketik keyword untuk live search (kode, nama, atau kategori)..."
                                autocomplete="off"
                            >
                        </div>
                        <div class="spinner-border spinner-border-sm text-primary search-spinner" id="searchSpinner" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <select id="categoryFilter" class="form-select">
                            <option value="all">Semua Kategori</option>
                            <option value="Elektronik">Elektronik</option>
                            <option value="Aksesoris">Aksesoris</option>
                            <option value="Stationery">Stationery</option>
                            <option value="Furniture">Furniture</option>
                        </select>
                    </div>
                </div>
                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <p id="statusText" class="small text-muted mb-0">Memuat data dari server...</p>
                    <small class="text-muted fst-italic">AJAX Fetch tanpa reload halaman</small>
                </div>
            </div>
        </section>

        <!-- ============================================================== -->
        <!-- PRODUCT CARDS (Modul 11C Checkpoint 4: Responsive Grid 12 Kolom)-->
        <!-- ============================================================== -->
        <section id="products" class="mb-5 dashboard-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 mb-0 fw-bold">Product Cards</h2>
                <small class="text-muted">Grid: 1 col (mobile) &rarr; 2 col (tablet) &rarr; 3 col (desktop)</small>
            </div>
            
            <div class="row g-4" id="productCardsContainer">
                <!-- Diisi otomatis secara dinamis oleh assets/js/app.js -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2">Memuat kartu produk...</p>
                </div>
            </div>
        </section>

        <!-- ============================================================== -->
        <!-- PRODUCT TABLE RESPONSIVE (Modul 11C Checkpoint 8)             -->
        <!-- ============================================================== -->
        <section id="table" class="mb-5 dashboard-section">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-bold">Product Table</h2>
                    <span class="badge text-bg-secondary" id="tableCountBadge">Loading...</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th style="min-width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <!-- Diisi otomatis secara dinamis oleh assets/js/app.js -->
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                        Menunggu JavaScript Fetch...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================== -->
        <!-- FORM BOOTSTRAP PREVIEW (Modul 11C Checkpoint 9)               -->
        <!-- ============================================================== -->
        <section id="form-preview" class="mb-5 dashboard-section">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h2 class="h5 mb-0 fw-bold">Product Form Preview</h2>
                    <small class="text-muted">Form responsive langsung terhubung ke API backend via AJAX</small>
                </div>
                <div class="card-body p-4">
                    <form id="previewForm" class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="previewName" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input id="previewName" type="text" class="form-control" placeholder="Contoh: Mechanical Keyboard" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="previewCategory" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select id="previewCategory" class="form-select" required>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Stationery">Stationery</option>
                                <option value="Furniture">Furniture</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="previewPrice" class="form-label fw-semibold">Price (Rp) <span class="text-danger">*</span></label>
                            <input id="previewPrice" type="number" class="form-control" placeholder="Contoh: 550000" min="1000" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="previewStock" class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                            <input id="previewStock" type="number" class="form-control" placeholder="Contoh: 10" min="0" required>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="button" id="previewSaveBtn" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i> Save Preview
                            </button>
                            <button type="reset" class="btn btn-outline-secondary ms-2 px-3">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </main>

    <!-- ============================================================== -->
    <!-- MODAL ADD / EDIT PRODUCT (Modul 11C Checkpoint 11)            -->
    <!-- ============================================================== -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-light">
                    <h2 class="modal-title fs-5 fw-bold" id="modalTitle">Add Product</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="productModalForm">
                    <div class="modal-body p-4">
                        <!-- Hidden ID untuk mode update -->
                        <input type="hidden" id="modalProductId" value="">

                        <div class="mb-3">
                            <label for="modalProductCode" class="form-label fw-semibold">Kode Produk (Opsional)</label>
                            <input id="modalProductCode" type="text" class="form-control" placeholder="Auto-generate jika kosong (cth: P009)">
                        </div>

                        <div class="mb-3">
                            <label for="modalProductName" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input id="modalProductName" type="text" class="form-control" placeholder="Nama produk" required>
                        </div>

                        <div class="mb-3">
                            <label for="modalCategory" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select id="modalCategory" class="form-select" required>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Stationery">Stationery</option>
                                <option value="Furniture">Furniture</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="modalPrice" class="form-label fw-semibold">Price (Rp) <span class="text-danger">*</span></label>
                                <input id="modalPrice" type="number" class="form-control" placeholder="Harga" min="1" required>
                            </div>
                            <div class="col-6">
                                <label for="modalStock" class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                                <input id="modalStock" type="number" class="form-control" placeholder="Jumlah" min="0" required>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="modalStatus" class="form-label fw-semibold">Status</label>
                            <select id="modalStatus" class="form-select">
                                <option value="active">Active (Tersedia)</option>
                                <option value="inactive">Inactive (Non-aktif)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="modalSaveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- FOOTER (Modul 11C Checkpoint 13)                              -->
    <!-- ============================================================== -->
    <footer class="bg-white border-top py-4 mt-5">
        <div class="container text-center text-muted small">
            <p class="mb-1"><strong>Web Programming 1 - Modul 11 Full Integration</strong></p>
            <p class="mb-0">Menggabungkan Modul 11A (AJAX Live Search), 11B (PHP JSON API), 11C (Bootstrap Dashboard) & 11D (Tailwind).</p>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle CDN (Modul 11C Checkpoint 1) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom AJAX Controller JS (Menghubungkan Modul 11A, 11B, dan 11C) -->
    <script src="assets/js/app.js"></script>
</body>
</html>
