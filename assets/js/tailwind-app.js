/**
 * assets/js/tailwind-app.js
 * Frontend Controller untuk Halaman Tailwind (Modul 11D)
 * Mengonsumsi API yang sama persis (api/products/*) menggunakan AJAX Fetch
 */

const twSearchInput      = document.getElementById("twSearchInput");
const twSearchSpinner    = document.getElementById("twSearchSpinner");
const twStatusText       = document.getElementById("twStatusText");
const twCategoryFilter   = document.getElementById("twCategoryFilter");
const twCardsContainer   = document.getElementById("twCardsContainer");
const twTableBody        = document.getElementById("twTableBody");
const twTableCount       = document.getElementById("twTableCount");

// Summary stats
const twStatTotal        = document.getElementById("twStatTotal");
const twStatActive       = document.getElementById("twStatActive");
const twStatLowStock     = document.getElementById("twStatLowStock");

// Form elements
const twForm             = document.getElementById("twForm");
const twFormId           = document.getElementById("twFormId");
const twFormName         = document.getElementById("twFormName");
const twFormCategory     = document.getElementById("twFormCategory");
const twFormPrice        = document.getElementById("twFormPrice");
const twFormStock        = document.getElementById("twFormStock");
const twFormSaveBtn      = document.getElementById("twFormSaveBtn");
const twFormTitle        = document.getElementById("twFormTitle");
const twFormCancelBtn    = document.getElementById("twFormCancelBtn");

// Alert
const twAlert            = document.getElementById("twAlert");

document.addEventListener("DOMContentLoaded", () => {
    loadProducts();

    let timeout = null;
    twSearchInput.addEventListener("input", () => {
        clearTimeout(timeout);
        if (twSearchSpinner) twSearchSpinner.classList.remove("hidden");
        timeout = setTimeout(() => {
            loadProducts(twSearchInput.value, twCategoryFilter ? twCategoryFilter.value : "");
        }, 250);
    });

    if (twCategoryFilter) {
        twCategoryFilter.addEventListener("change", () => {
            loadProducts(twSearchInput.value, twCategoryFilter.value);
        });
    }

    if (twForm) {
        twForm.addEventListener("submit", handleFormSubmit);
    }

    if (twFormCancelBtn) {
        twFormCancelBtn.addEventListener("click", resetForm);
    }
});

async function loadProducts(keyword = "", category = "") {
    showStatus("Memuat data produk...", "text-slate-500");
    if (twSearchSpinner) twSearchSpinner.classList.remove("hidden");

    try {
        const params = new URLSearchParams();
        if (keyword.trim() !== "") params.append("q", keyword.trim());
        if (category.trim() !== "" && category !== "all") params.append("category", category.trim());

        const res = await fetch("api/products/read.php?" + params.toString());
        if (!res.ok) throw new Error(`HTTP Error ${res.status}`);

        const result = await res.json();
        if (!result.success) throw new Error(result.message);

        const products = result.data.products || [];
        const stats = result.data.stats || { total: 0, active: 0, low_stock: 0 };

        // Update stats
        if (twStatTotal) twStatTotal.textContent = stats.total;
        if (twStatActive) twStatActive.textContent = stats.active;
        if (twStatLowStock) twStatLowStock.textContent = stats.low_stock;

        // Render cards
        renderCards(products);

        // Render table
        renderTable(products);

        if (twTableCount) twTableCount.textContent = `${products.length} Products`;

        if (keyword.trim() === "") {
            showStatus(`Menampilkan semua ${products.length} produk.`, "text-slate-600");
        } else {
            showStatus(`Pencarian "${keyword}": ${products.length} ditemukan.`, "text-blue-600 font-semibold");
        }

    } catch (err) {
        console.error(err);
        showStatus("Gagal menghubungi API server.", "text-red-600 font-bold");
        showAlert(err.message, "error");
    } finally {
        if (twSearchSpinner) twSearchSpinner.classList.add("hidden");
    }
}

function renderCards(products) {
    if (!twCardsContainer) return;

    if (products.length === 0) {
        twCardsContainer.innerHTML = `
            <div class="col-span-full bg-white p-8 text-center rounded-xl border border-slate-200">
                <p class="text-slate-500 font-medium">Tidak ada produk ditemukan.</p>
            </div>
        `;
        return;
    }

    let html = "";
    products.forEach(p => {
        const formattedPrice = new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(p.price);
        const lowStock = p.stock <= 5;

        html += `
            <article class="overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200 flex flex-col transition hover:-translate-y-1 hover:shadow-md">
                <img
                    src="${escapeHtml(p.image_url || 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=500&auto=format&fit=crop&q=60')}"
                    alt="${escapeHtml(p.name)}"
                    class="h-44 w-full object-cover bg-slate-100"
                    onerror="this.src='https://placehold.co/600x360?text=Product';"
                >
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="px-2 py-0.5 rounded-full font-medium ${p.category === 'Elektronik' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'}">
                                ${escapeHtml(p.category)}
                            </span>
                            <span class="text-slate-400 font-mono">${escapeHtml(p.code)}</span>
                        </div>
                        <h3 class="mt-1 text-lg font-semibold text-slate-800 line-clamp-1" title="${escapeHtml(p.name)}">
                            ${escapeHtml(p.name)}
                        </h3>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-lg font-bold text-slate-900">${formattedPrice}</p>
                        <span class="text-xs px-2 py-1 rounded font-semibold ${lowStock ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700'}">
                            Stok: ${p.stock}
                        </span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <button onclick="editProduct(${p.id})" class="px-3 py-1.5 rounded-lg border border-blue-600 text-blue-600 font-medium text-sm hover:bg-blue-50">
                            Edit
                        </button>
                        <button onclick="deleteProduct(${p.id}, '${escapeHtml(p.name)}')" class="px-3 py-1.5 rounded-lg border border-red-600 text-red-600 font-medium text-sm hover:bg-red-50">
                            Hapus
                        </button>
                    </div>
                </div>
            </article>
        `;
    });

    twCardsContainer.innerHTML = html;
}

function renderTable(products) {
    if (!twTableBody) return;

    if (products.length === 0) {
        twTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada data produk</td>
            </tr>
        `;
        return;
    }

    let html = "";
    products.forEach(p => {
        const formattedPrice = new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(p.price);
        const lowStock = p.stock <= 5;

        html += `
            <tr class="border-t border-slate-200 hover:bg-slate-50 transition">
                <td class="px-4 py-3 font-mono font-medium text-slate-600">${escapeHtml(p.code)}</td>
                <td class="px-4 py-3 font-medium text-slate-900">${escapeHtml(p.name)}</td>
                <td class="px-4 py-3 text-slate-600">${escapeHtml(p.category)}</td>
                <td class="px-4 py-3 font-semibold text-slate-900">${formattedPrice}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded ${lowStock ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'}">
                        ${p.stock}
                    </span>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <button onclick="editProduct(${p.id})" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">Edit</button>
                    <button onclick="deleteProduct(${p.id}, '${escapeHtml(p.name)}')" class="text-red-600 hover:text-red-800 text-sm font-semibold">Hapus</button>
                </td>
            </tr>
        `;
    });

    twTableBody.innerHTML = html;
}

async function handleFormSubmit(e) {
    e.preventDefault();

    const id = twFormId.value.trim();
    const isEdit = id !== "";

    const payload = {
        name: twFormName.value.trim(),
        category: twFormCategory.value,
        price: parseInt(twFormPrice.value, 10),
        stock: parseInt(twFormStock.value, 10),
        status: "active"
    };

    if (isEdit) payload.id = parseInt(id, 10);

    const endpoint = isEdit ? "api/products/update.php" : "api/products/create.php";

    try {
        twFormSaveBtn.disabled = true;
        twFormSaveBtn.textContent = "Menyimpan...";

        const res = await fetch(endpoint, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message);

        showAlert(data.message, "success");
        resetForm();
        await loadProducts(twSearchInput.value, twCategoryFilter ? twCategoryFilter.value : "");

    } catch (err) {
        console.error(err);
        showAlert(err.message, "error");
    } finally {
        twFormSaveBtn.disabled = false;
        twFormSaveBtn.textContent = isEdit ? "Update Product" : "Save Product";
    }
}

async function editProduct(id) {
    try {
        const res = await fetch(`api/products/detail.php?id=${id}`);
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message);

        const p = data.data;
        twFormId.value = p.id;
        twFormName.value = p.name;
        twFormCategory.value = p.category;
        twFormPrice.value = p.price;
        twFormStock.value = p.stock;

        twFormTitle.textContent = `Edit Product: ${p.name}`;
        twFormSaveBtn.textContent = "Update Product";
        twFormCancelBtn.classList.remove("hidden");

        // Scroll to form
        document.getElementById("form-section").scrollIntoView({ behavior: "smooth" });

    } catch (err) {
        showAlert(err.message, "error");
    }
}

async function deleteProduct(id, name) {
    if (!confirm(`Hapus produk "${name}"?`)) return;

    try {
        const res = await fetch("api/products/delete.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message);

        showAlert(data.message, "success");
        await loadProducts(twSearchInput.value, twCategoryFilter ? twCategoryFilter.value : "");
    } catch (err) {
        showAlert(err.message, "error");
    }
}

function resetForm() {
    twForm.reset();
    twFormId.value = "";
    twFormTitle.textContent = "Add Product (Form)";
    twFormSaveBtn.textContent = "Save Product";
    twFormCancelBtn.classList.add("hidden");
}

function showStatus(text, colorClass) {
    if (twStatusText) {
        twStatusText.textContent = text;
        twStatusText.className = "text-xs " + colorClass;
    }
}

function showAlert(msg, type = "success") {
    if (!twAlert) return;
    twAlert.classList.remove("hidden", "bg-emerald-100", "text-emerald-800", "bg-red-100", "text-red-800");

    if (type === "success") {
        twAlert.classList.add("bg-emerald-100", "text-emerald-800");
    } else {
        twAlert.classList.add("bg-red-100", "text-red-800");
    }

    twAlert.textContent = msg;
    setTimeout(() => {
        twAlert.classList.add("hidden");
    }, 4000);
}

function escapeHtml(s) {
    if (!s) return "";
    return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}
