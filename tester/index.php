<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Endpoint Tester - Modul 11B Checkpoint 9</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; margin: 32px; background: #f8fafc; color: #1e293b; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        section { margin-bottom: 28px; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fdfdfd; }
        h1 { margin-top: 0; color: #0f172a; }
        h2 { margin-top: 0; font-size: 1.15rem; color: #2563eb; }
        input, textarea, select, button { display: block; width: 100%; box-sizing: border-box; margin: 10px 0; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        button { background: #2563eb; color: white; border: none; font-weight: 600; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .links a { display: inline-block; margin-right: 12px; margin-bottom: 8px; padding: 8px 14px; background: #e0e7ff; color: #3730a3; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500; }
        .links a:hover { background: #c7d2fe; }
        .top-nav { margin-bottom: 20px; }
        .top-nav a { text-decoration: none; color: #64748b; font-weight: 600; font-size: 14px; }
        .top-nav a:hover { color: #2563eb; }
    </style>
</head>
<body>

<div class="container">
    <div class="top-nav">
        <a href="../index.php">&larr; Kembali ke Dashboard Utama (Frontend Bootstrap)</a>
    </div>

    <h1>Product API Endpoint Tester</h1>
    <p style="color: #64748b; font-size: 14px;">
        Halaman pengujian endpoint API backend tanpa AJAX (Modul 11B Checkpoint 9). Form akan melakukan HTTP POST langsung ke endpoint dan menampilkan respons JSON mentah dari server.
    </p>

    <div class="links">
        <a href="../api/products/read.php" target="_blank">GET All Products (read.php)</a>
        <a href="../api/products/stats.php" target="_blank">GET Stats (stats.php)</a>
        <a href="../api/products/detail.php?id=1" target="_blank">GET Detail ID 1 (detail.php)</a>
    </div>

    <!-- FORM CREATE -->
    <section>
        <h2>CREATE Product (POST api/products/create.php)</h2>
        <form action="../api/products/create.php" method="POST" target="_blank">
            <input type="text" name="code" placeholder="Kode Produk (Opsional, cth: P099)">
            <input type="text" name="name" placeholder="Nama Produk (Wajib)" required>
            <select name="category">
                <option value="Elektronik">Elektronik</option>
                <option value="Aksesoris">Aksesoris</option>
                <option value="Stationery">Stationery</option>
                <option value="Furniture">Furniture</option>
            </select>
            <input type="number" name="price" placeholder="Harga (Rp)" required min="1000">
            <input type="number" name="stock" placeholder="Stok" required min="0">
            <select name="status">
                <option value="active">active</option>
                <option value="inactive">inactive</option>
            </select>
            <button type="submit">Submit CREATE Product</button>
        </form>
    </section>

    <!-- FORM UPDATE -->
    <section>
        <h2>UPDATE Product (POST api/products/update.php)</h2>
        <form action="../api/products/update.php" method="POST" target="_blank">
            <input type="number" name="id" placeholder="ID Produk yang akan diubah (Wajib)" required>
            <input type="text" name="name" placeholder="Nama Baru (Wajib)" required>
            <select name="category">
                <option value="Elektronik">Elektronik</option>
                <option value="Aksesoris">Aksesoris</option>
                <option value="Stationery">Stationery</option>
                <option value="Furniture">Furniture</option>
            </select>
            <input type="number" name="price" placeholder="Harga Baru" required min="1000">
            <input type="number" name="stock" placeholder="Stok Baru" required min="0">
            <select name="status">
                <option value="active">active</option>
                <option value="inactive">inactive</option>
            </select>
            <button type="submit">Submit UPDATE Product</button>
        </form>
    </section>

    <!-- FORM DELETE -->
    <section>
        <h2>DELETE Product (POST api/products/delete.php)</h2>
        <form action="../api/products/delete.php" method="POST" target="_blank">
            <input type="number" name="id" placeholder="ID Produk yang akan dihapus" required>
            <button type="submit" style="background: #dc2626;">Submit DELETE Product</button>
        </form>
    </section>
</div>

</body>
</html>
