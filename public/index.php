<?php
// public/index.php

require_once __DIR__ . '/../config/db.php';
session_start();

// Menerima parameter GET untuk fitur bonus Search (jika ada)
$q = trim($_GET["q"] ?? "");

// Query untuk mengambil data produk
if ($q !== "") {
    // Jika ada pencarian
    $sql = "SELECT id, name, category, price, stock, created_at FROM products 
            WHERE name LIKE :q OR category LIKE :q 
            ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["q" => "%$q%"]);
} else {
    // Jika tidak ada pencarian (statis)
    $stmt = $pdo->query("SELECT id, name, category, price, stock, created_at FROM products ORDER BY id DESC");
}

$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header class="header-section">
            <h1>Manajemen Produk</h1>
            <a href="create.php" class="btn btn-primary">+ Tambah Produk Baru</a>
        </header>

        <!-- Pesan Notifikasi (Pola PRG) -->
        <?php if (($_GET["status"] ?? "") === "created"): ?>
            <div class="alert alert-success">Produk berhasil disimpan.</div>
        <?php elseif (($_GET["status"] ?? "") === "deleted"): ?>
            <div class="alert alert-success">Produk berhasil dihapus.</div>
        <?php elseif (($_GET["status"] ?? "") === "updated"): ?>
            <div class="alert alert-success">Produk berhasil diperbarui.</div>
        <?php endif; ?>

        <!-- Form Search (Bonus Fitur GET) -->
        <form method="GET" action="index.php" class="search-form">
            <input type="text" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, "UTF-8") ?>" placeholder="Cari nama atau kategori produk...">
            <button type="submit" class="btn btn-secondary">Cari</button>
            <?php if ($q !== ""): ?>
                <a href="index.php" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </form>

        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $p): ?>
                    <div class="card product-card">
                        <!-- Pencegahan XSS dengan htmlspecialchars pada output text -->
                        <h3><?= htmlspecialchars($p["name"], ENT_QUOTES, "UTF-8") ?></h3>
                        <span class="category-badge"><?= htmlspecialchars($p["category"], ENT_QUOTES, "UTF-8") ?></span>
                        
                        <p class="price">Rp <?= number_format($p["price"], 0, ",", ".") ?></p>
                        <p class="stock <?= $p["stock"] == 0 ? 'stock-empty' : '' ?>">
                            Stok: <?= $p["stock"] ?>
                        </p>

                        <div class="card-actions">
                            <a href="edit.php?id=<?= $p["id"] ?>" class="btn btn-sm btn-outline">Edit</a>
                            <!-- Tombol Delete menggunakan POST (akan dibuat di tahap selanjutnya) -->
                            <form method="POST" action="delete.php" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                <input type="hidden" name="id" value="<?= $p["id"] ?>">
                                <!-- Token CSRF akan ditambahkan nanti di tahap Delete -->
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card empty-state">
                    <p>Belum ada produk yang ditambahkan atau tidak ditemukan hasil pencarian.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>