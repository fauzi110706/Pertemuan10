<?php
// api/products/update.php
// Endpoint UPDATE: Memperbarui data produk
// Sesuai Modul 11B Checkpoint 7

require_once "../../config/koneksi.php";
require_once "../../config/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendJson(405, false, "Method tidak diizinkan. Gunakan POST.");
}

$data = getRequestData();

$id       = (int) ($data["id"] ?? 0);
$name     = trim($data["name"] ?? "");
$category = trim($data["category"] ?? "");
$price    = (int) ($data["price"] ?? 0);
$stock    = isset($data["stock"]) ? (int) $data["stock"] : 0;
$status   = trim($data["status"] ?? "active");
$code     = trim($data["code"] ?? "");
$image    = trim($data["image_url"] ?? "");

if ($id <= 0) {
    sendJson(400, false, "ID produk tidak valid");
}
if ($name === "") {
    sendJson(400, false, "Nama produk tidak boleh kosong");
}
if ($category === "") {
    sendJson(400, false, "Kategori produk wajib dipilih");
}
if ($price <= 0) {
    sendJson(400, false, "Harga harus lebih besar dari 0");
}
if ($stock < 0) {
    sendJson(400, false, "Jumlah stok tidak boleh negatif");
}
if (!in_array($status, ["active", "inactive"], true)) {
    $status = "active";
}

// Cek apakah produk dengan ID ini ada
$check = $conn->prepare("SELECT id, code, image_url FROM products WHERE id = ? LIMIT 1");
$check->bind_param("i", $id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();

if (!$existing) {
    sendJson(404, false, "Produk dengan ID $id tidak ditemukan");
}

if ($code === "") {
    $code = $existing["code"];
}
if ($image === "") {
    $image = $existing["image_url"] ?: "https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=500&auto=format&fit=crop&q=60";
}

$stmt = $conn->prepare(
    "UPDATE products
     SET code = ?, name = ?, category = ?, price = ?, stock = ?, status = ?, image_url = ?
     WHERE id = ?"
);
$stmt->bind_param("sssiissi", $code, $name, $category, $price, $stock, $status, $image, $id);

if (!$stmt->execute()) {
    sendJson(500, false, "Gagal memperbarui produk: " . $stmt->error);
}

sendJson(200, true, "Produk '$name' berhasil diperbarui", [
    "id"        => $id,
    "code"      => $code,
    "name"      => $name,
    "category"  => $category,
    "price"     => $price,
    "stock"     => $stock,
    "status"    => $status,
    "image_url" => $image
]);
