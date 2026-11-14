<?php
// api/products/stats.php
// Endpoint STATS: Mengambil ringkasan statistik produk untuk Dashboard Cards

require_once "../../config/koneksi.php";
require_once "../../config/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    sendJson(405, false, "Method tidak diizinkan. Gunakan GET.");
}

$statsQuery = $conn->query("SELECT 
    COUNT(*) AS total_products,
    SUM(CASE WHEN stock > 0 AND status = 'active' THEN 1 ELSE 0 END) AS active_products,
    SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END) AS low_stock_products
FROM products");

if (!$statsQuery) {
    sendJson(500, false, "Gagal mengambil statistik: " . $conn->error);
}

$stats = $statsQuery->fetch_assoc();

sendJson(200, true, "Statistik produk berhasil dimuat", [
    "total"     => (int) ($stats["total_products"] ?? 0),
    "active"    => (int) ($stats["active_products"] ?? 0),
    "low_stock" => (int) ($stats["low_stock_products"] ?? 0)
]);
