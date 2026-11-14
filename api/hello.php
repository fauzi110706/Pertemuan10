<?php
// api/hello.php
// Modul 11B - Checkpoint 3: Hello JSON API

require_once "../config/response.php";

sendJson(
    200,
    true,
    "Product API aktif dan siap digunakan",
    [
        "module"  => "Web Programming 1 - Fullstack Integration (11A, 11B, 11C, 11D)",
        "version" => "1.0.0",
        "author"  => "Horizon University Indonesia",
        "endpoints" => [
            "GET  /api/products/read.php"    => "Mengambil semua produk atau cari via ?q=keyword",
            "GET  /api/products/stats.php"   => "Mengambil statistik total, active, low stock",
            "GET  /api/products/detail.php"  => "Mengambil detail produk via ?id=1",
            "POST /api/products/create.php"  => "Menambahkan produk baru",
            "POST /api/products/update.php"  => "Memperbarui data produk",
            "POST /api/products/delete.php"  => "Menghapus produk"
        ]
    ]
);
