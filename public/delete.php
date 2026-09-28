<?php
// public/delete.php

require_once __DIR__ . '/../config/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    $csrf_token = $_POST["csrf"] ?? "";

    // 1. Validasi Token CSRF menggunakan hash_equals
    if (empty($_SESSION["csrf"]) || !hash_equals($_SESSION["csrf"], $csrf_token)) {
        http_response_code(403);
        exit("Akses ditolak: Token CSRF tidak valid.");
    }

    // 2. Eksekusi query DELETE dengan Prepared Statement
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(["id" => $id]);
    }

    // 3. Redirect kembali ke index (Pola PRG)
    header("Location: index.php?status=deleted");
    exit;
} else {
    // Jika ada yang mencoba mengakses delete.php lewat URL GET
    header("Location: index.php");
    exit;
} 