<?php
// api/products/delete.php
// Endpoint DELETE: Menghapus produk
// Sesuai Modul 11B Checkpoint 8

require_once "../../config/koneksi.php";
require_once "../../config/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendJson(405, false, "Method tidak diizinkan. Gunakan POST.");
}

$data = getRequestData();
$id = (int) ($data["id"] ?? 0);

if ($id <= 0) {
    sendJson(400, false, "ID produk tidak valid");
}

$check = $conn->prepare("SELECT id, name FROM products WHERE id = ? LIMIT 1");
$check->bind_param("i", $id);
$check->execute();
$product = $check->get_result()->fetch_assoc();

if (!$product) {
    sendJson(404, false, "Produk dengan ID $id tidak ditemukan");
}

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    sendJson(500, false, "Gagal menghapus produk: " . $stmt->error);
}

sendJson(200, true, "Produk '" . $product["name"] . "' berhasil dihapus", [
    "id" => $id
]);
