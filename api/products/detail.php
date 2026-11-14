<?php
// api/products/detail.php
// Endpoint DETAIL: Mengambil data satu produk berdasarkan id (?id=...)
// Sesuai Modul 11B Checkpoint 5

require_once "../../config/koneksi.php";
require_once "../../config/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    sendJson(405, false, "Method tidak diizinkan. Gunakan GET.");
}

$id = (int) ($_GET["id"] ?? 0);
if ($id <= 0) {
    sendJson(400, false, "ID produk tidak valid atau tidak disertakan");
}

$stmt = $conn->prepare(
    "SELECT id, code, name, category, price, stock, status, image_url, created_at
     FROM products
     WHERE id = ?
     LIMIT 1"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    sendJson(404, false, "Produk dengan ID $id tidak ditemukan");
}

$product["id"] = (int) $product["id"];
$product["price"] = (int) $product["price"];
$product["stock"] = (int) $product["stock"];

sendJson(200, true, "Detail produk berhasil diambil", $product);
