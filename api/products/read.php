<?php
// api/products/read.php
// Endpoint READ: Mengambil daftar produk & statistik
// Menggabungkan Live Search (Modul 11A) dengan JSON API (Modul 11B)

require_once "../../config/koneksi.php";
require_once "../../config/response.php";

// Pastikan method adalah GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    sendJson(405, false, "Method tidak diizinkan. Gunakan GET.");
}

$keyword  = trim($_GET["q"] ?? "");
$category = trim($_GET["category"] ?? "");

$sql = "SELECT id, code, name, category, price, stock, status, image_url, created_at FROM products WHERE 1=1";
$params = [];
$types = "";

// Pencarian keyword (seperti Modul 11A pada tabel produk)
if ($keyword !== "") {
    $sql .= " AND (code LIKE ? OR name LIKE ? OR category LIKE ?)";
    $search = "%" . $keyword . "%";
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $types .= "sss";
}

// Filter kategori jika diberikan
if ($category !== "" && $category !== "all") {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

if (!$result) {
    sendJson(500, false, "Gagal mengambil data dari database: " . $conn->error);
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $row["id"] = (int) $row["id"];
    $row["price"] = (int) $row["price"];
    $row["stock"] = (int) $row["stock"];
    $products[] = $row;
}

// Ambil data statistik dinamis untuk Summary Cards Dashboard (Modul 11C)
$statsQuery = $conn->query("SELECT 
    COUNT(*) AS total_products,
    SUM(CASE WHEN stock > 0 AND status = 'active' THEN 1 ELSE 0 END) AS active_products,
    SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END) AS low_stock_products
FROM products");

$stats = $statsQuery ? $statsQuery->fetch_assoc() : [];

sendJson(200, true, "Data produk berhasil diambil", [
    "products" => $products,
    "total_found" => count($products),
    "stats" => [
        "total"     => (int) ($stats["total_products"] ?? 0),
        "active"    => (int) ($stats["active_products"] ?? 0),
        "low_stock" => (int) ($stats["low_stock_products"] ?? 0)
    ]
]);
