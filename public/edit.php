<?php
// public/edit.php

require_once __DIR__ . '/../config/db.php';

// 1. Ambil dan validasi ID dari URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

// 2. Ambil data lama produk dari database
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(["id" => $id]);
$product = $stmt->fetch();

// Jika produk dengan ID tersebut tidak ditemukan
if (!$product) {
    header("Location: index.php");
    exit;
}

$errors = [];
$name = $product["name"];
$category = $product["category"];
$price = $product["price"];
$stock = $product["stock"];

// 3. Proses saat form disubmit (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Normalisasi Input
    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "Umum");
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);

    // Validasi Server-Side
    if (mb_strlen($name) < 3) {
        $errors["name"] = "Nama produk minimal 3 karakter.";
    }
    if ($price === false || $price <= 0) {
        $errors["price"] = "Harga harus > 0.";
    }
    if ($stock === false || $stock < 0) {
        $errors["stock"] = "Stok tidak boleh negatif.";
    }

    // Update Ke Database jika validasi lolos
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "UPDATE products 
                 SET name = :name, category = :category, price = :price, stock = :stock 
                 WHERE id = :id"
            );
            $stmt->execute([
                "name" => $name,
                "category" => $category,
                "price" => $price,
                "stock" => $stock,
                "id" => $id
            ]);

            // Pola PRG (Redirect setelah Update)
            header("Location: index.php?status=updated");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors["name"] = "Nama produk sudah terdaftar, gunakan nama lain.";
            } else {
                $errors["db"] = "Gagal memperbarui data: " . $e->getMessage();
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
    <title>Edit Produk - Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Produk</h1>
        <a href="index.php" class="btn btn-secondary">← Kembali ke Daftar Produk</a>

        <?php if (!empty($errors["db"])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors["db"], ENT_QUOTES, "UTF-8") ?></div>
        <?php endif; ?>

        <form action="edit.php?id=<?= $id ?>" method="POST" class="card form-card">
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

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>