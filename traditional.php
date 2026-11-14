<?php
// traditional.php
// Modul 11A - Checkpoint 3: Traditional Search (Baseline Pembanding dengan AJAX)
// Karakteristik: Form Submit -> PHP Query -> HTML Document Baru -> FULL PAGE RELOAD

require_once "config/koneksi.php";

$keyword = trim($_GET["q"] ?? "");

if ($keyword !== "") {
    $search = "%" . $keyword . "%";
    $stmt = $conn->prepare(
        "SELECT id, code, name, category, price, stock
         FROM products
         WHERE code LIKE ?
            OR name LIKE ?
            OR category LIKE ?
         ORDER BY name"
    );
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query(
        "SELECT id, code, name, category, price, stock
         FROM products
         ORDER BY name"
    );
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traditional Product Search - Modul 11A Checkpoint 3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow-sm p-4 border-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Traditional Product Search</h1>
                <p class="text-muted small mb-0">Submit form ini menghasilkan <strong>Full Page Reload</strong> (Address bar berubah menjadi <code>?q=...</code>).</p>
            </div>
            <a href="index.php" class="btn btn-outline-primary btn-sm">
                &larr; Kembali ke Dashboard AJAX
            </a>
        </div>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-sm-9">
                <input
                    type="search"
                    name="q"
                    class="form-control"
                    value="<?= htmlspecialchars($keyword) ?>"
                    placeholder="Cari kode, produk, atau kategori..."
                >
            </div>
            <div class="col-sm-3 d-grid">
                <button type="submit" class="btn btn-primary">Search (Reload Page)</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row["code"]) ?></strong></td>
                                <td><?= htmlspecialchars($row["name"]) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row["category"]) ?></span></td>
                                <td>Rp <?= number_format($row["price"], 0, ",", ".") ?></td>
                                <td><?= htmlspecialchars($row["stock"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Produk tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
