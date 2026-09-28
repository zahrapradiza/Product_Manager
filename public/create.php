<?php
// public/create.php

// Menyalakan penampil error (hapus 2 baris ini nanti kalau tugas sudah selesai)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Memanggil koneksi database
require_once __DIR__ . '/../config/db.php';

$errors = [];
$name = '';
$category = '';
$price = '';
$stock = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Normalisasi
    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "Umum");
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);

    // 2. Validasi Server-Side
    if (mb_strlen($name) < 3) {
        $errors["name"] = "Nama produk minimal 3 karakter.";
    }
    if ($price === false || $price <= 0) {
        $errors["price"] = "Harga harus > 0.";
    }
    if ($stock === false || $stock < 0) {
        $errors["stock"] = "Stok tidak boleh negatif.";
    }

    // 3. Simpan & Pola PRG (Post-Redirect-Get)
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO products (name, category, price, stock) VALUES (:name, :category, :price, :stock)"
            );
            $stmt->execute([
                "name" => $name,
                "category" => $category,
                "price" => $price,
                "stock" => $stock
            ]);

            header("Location: index.php?status=created");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { 
                $errors["name"] = "Nama produk sudah terdaftar, gunakan nama lain.";
            } else {
                $errors["db"] = "Gagal menyimpan: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Tambah Produk Baru</h1>
        <a href="index.php" class="btn btn-secondary">← Kembali ke Daftar Produk</a>

        <?php if (!empty($errors["db"])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors["db"], ENT_QUOTES, "UTF-8") ?></div>
        <?php endif; ?>

        <form action="create.php" method="POST" class="card">
            <div class="form-group">
                <label for="name">Nama Produk</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>" required minlength="3">
                <?php if (!empty($errors["name"])): ?>
                    <span class="error-text"><?= $errors["name"] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" id="category" name="category" value="<?= htmlspecialchars($category, ENT_QUOTES, "UTF-8") ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Harga (Rp)</label>
                <input type="number" id="price" name="price" step="0.01" min="1" value="<?= htmlspecialchars($price, ENT_QUOTES, "UTF-8") ?>" required>
                <?php if (!empty($errors["price"])): ?>
                    <span class="error-text"><?= $errors["price"] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="stock">Stok</label>
                <input type="number" id="stock" name="stock" min="0" value="<?= htmlspecialchars($stock, ENT_QUOTES, "UTF-8") ?>" required>
                <?php if (!empty($errors["stock"])): ?>
                    <span class="error-text"><?= $errors["stock"] ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </form>
    </div>
</body>
</html>