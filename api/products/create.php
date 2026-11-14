<?php
// api/products/create.php
// Endpoint CREATE: Menambahkan produk baru
// Sesuai Modul 11B Checkpoint 6

require_once "../../config/koneksi.php";
require_once "../../config/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendJson(405, false, "Method tidak diizinkan. Gunakan POST.");
}

$data = getRequestData();

$name     = trim($data["name"] ?? "");
$category = trim($data["category"] ?? "");
$price    = (int) ($data["price"] ?? 0);
$stock    = isset($data["stock"]) ? (int) $data["stock"] : 0;
$status   = trim($data["status"] ?? "active");
$code     = trim($data["code"] ?? "");
$image    = trim($data["image_url"] ?? "");

// Validasi input
if ($name === "") {
    sendJson(400, false, "Nama produk wajib diisi");
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

// Generate kode produk otomatis jika tidak diinput (misal: P009, P010)
if ($code === "") {
    $countRes = $conn->query("SELECT MAX(id) AS max_id FROM products");
    $maxId = (int) (($countRes ? $countRes->fetch_assoc()["max_id"] : 0) ?? 0);
    $code = "P" . str_pad($maxId + 1, 3, "0", STR_PAD_LEFT);
} else {
    // Cek apakah kode produk sudah ada
    $checkCode = $conn->prepare("SELECT id FROM products WHERE code = ? LIMIT 1");
    $checkCode->bind_param("s", $code);
    $checkCode->execute();
    if ($checkCode->get_result()->fetch_assoc()) {
        sendJson(400, false, "Kode produk '$code' sudah digunakan. Gunakan kode lain.");
    }
}

// Placeholder gambar default jika kosong
if ($image === "") {
    $image = "https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=500&auto=format&fit=crop&q=60";
}

$stmt = $conn->prepare(
    "INSERT INTO products (code, name, category, price, stock, status, image_url)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssiiss", $code, $name, $category, $price, $stock, $status, $image);

if (!$stmt->execute()) {
    sendJson(500, false, "Gagal menyimpan produk ke database: " . $stmt->error);
}

sendJson(201, true, "Produk '$name' berhasil ditambahkan", [
    "id"        => $stmt->insert_id,
    "code"      => $code,
    "name"      => $name,
    "category"  => $category,
    "price"     => $price,
    "stock"     => $stock,
    "status"    => $status,
    "image_url" => $image
]);
