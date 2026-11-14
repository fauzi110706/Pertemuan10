<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Product Dashboard - Modul 11D Fullstack</title>
    <!-- Tailwind Play CDN (Modul 11D Checkpoint 1) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen">

    <!-- NAVBAR DENGAN TAILWIND UTILITIES (Modul 11D Checkpoint 8) -->
    <nav class="bg-slate-900 text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="#" class="font-bold text-lg tracking-wide flex items-center gap-2">
                <span>📦</span> ProductLab <span class="text-xs bg-blue-600 px-2 py-0.5 rounded text-white font-normal">Tailwind</span>
            </a>
            <div class="flex items-center gap-6 text-sm font-medium">
                <a href="#dashboard" class="hover:text-blue-300 transition">Dashboard</a>
                <a href="#products" class="hover:text-blue-300 transition">Products</a>
                <a href="#table" class="hover:text-blue-300 transition">Inventory</a>
                <a href="index.php" class="bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg text-white transition text-xs font-semibold">
                    &larr; Versi Bootstrap
                </a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTAINER (Modul 11D Checkpoint 3) -->
    <main class="max-w-7xl mx-auto px-4 py-8" id="dashboard">

        <!-- Global Alert Notification -->
        <div id="twAlert" class="hidden mb-6 p-4 rounded-xl text-sm font-medium transition duration-300"></div>

        <!-- DASHBOARD HEADER (Modul 11D Checkpoint 4: Flexbox) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Product Dashboard</h1>
                <p class="mt-1 text-slate-500">Kelola tampilan produk menggunakan Tailwind CSS + PHP REST API.</p>
            </div>
            <a href="#form-section" class="inline-block px-5 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm text-center">
                + Tambah Product
            </a>
        </div>

        <!-- GRID STATISTIK RESPONSIVE (Modul 11D Checkpoint 5) -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Product</p>
                <p class="mt-2 text-3xl font-bold text-slate-900" id="twStatTotal">0</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-500">Stock Ready</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600" id="twStatActive">0</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-500">Low Stock (&le; 5)</p>
                <p class="mt-2 text-3xl font-bold text-red-600" id="twStatLowStock">0</p>
            </div>
        </section>

        <!-- LIVE SEARCH & FILTER (Modul 11A + Tailwind) -->
        <section class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-2 relative">
                    <input
                        type="search"
                        id="twSearchInput"
                        placeholder="Live search produk, kode, atau kategori (Fetch API)..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <span id="twSearchSpinner" class="hidden absolute right-3 top-3 text-xs text-blue-600 font-semibold animate-pulse">
                        Loading...
                    </span>
                </div>
                <div>
                    <select id="twCategoryFilter" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">Semua Kategori</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Stationery">Stationery</option>
                        <option value="Furniture">Furniture</option>
                    </select>
                </div>
            </div>
            <div class="mt-2 flex justify-between items-center">
                <span id="twStatusText" class="text-xs text-slate-500">Memuat produk...</span>
                <span class="text-xs text-slate-400">Terhubung ke api/products/read.php</span>
            </div>
        </section>

        <!-- PRODUCT CARDS (Modul 11D Checkpoint 6) -->
        <section id="products" class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-900">Products Grid</h2>
                <span class="text-xs text-slate-500 font-mono">grid-cols-1 sm:grid-cols-2 lg:grid-cols-3</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="twCardsContainer">
                <div class="col-span-full py-12 text-center text-slate-400">Memuat kartu produk...</div>
            </div>
        </section>

        <!-- INVENTORY TABLE (Modul 11D Checkpoint 9) -->
        <section id="table" class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-900">Inventory Table</h2>
                <span id="twTableCount" class="text-xs font-semibold px-2.5 py-1 bg-slate-200 text-slate-700 rounded-full">0 Products</span>
            </div>
            <div class="overflow-x-auto bg-white rounded-xl shadow-sm border border-slate-200">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Stock</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="twTableBody">
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">Memuat data tabel...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- FORM ADD/EDIT DENGAN UTILITY + FOCUS STATE (Modul 11D Checkpoint 10) -->
        <section id="form-section" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-slate-900" id="twFormTitle">Add Product (Form)</h2>
            <form id="twForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="twFormId" value="">
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Product Name</label>
                    <input
                        type="text"
                        id="twFormName"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh: Wireless Headphones"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <select id="twFormCategory" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Elektronik">Elektronik</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Stationery">Stationery</option>
                        <option value="Furniture">Furniture</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Price (Rp)</label>
                    <input
                        type="number"
                        id="twFormPrice"
                        required
                        min="1000"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="350000"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Stock</label>
                    <input
                        type="number"
                        id="twFormStock"
                        required
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="15"
                    >
                </div>

                <div class="md:col-span-2 pt-2 flex items-center gap-3">
                    <button
                        type="submit"
                        id="twFormSaveBtn"
                        class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-300 transition text-sm shadow-sm"
                    >
                        Save Product
                    </button>
                    <button
                        type="button"
                        id="twFormCancelBtn"
                        class="hidden px-4 py-2.5 rounded-lg border border-slate-300 text-slate-600 font-medium hover:bg-slate-100 transition text-sm"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </section>

    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        Web Programming 1 &bull; Modul 11D Tailwind CSS &bull; Integrated with PHP MySQL API
    </footer>

    <!-- Tailwind JS Controller -->
    <script src="assets/js/tailwind-app.js"></script>
</body>
</html>
