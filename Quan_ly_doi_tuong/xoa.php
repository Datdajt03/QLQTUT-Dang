<?php
// Quan_ly_doi_tuong/xoa.php – Xóa đối tượng
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);

$id  = (int)($_GET['id'] ?? 0);
$ref = $_GET['ref'] ?? 'danh_sach';

if (!$id) { redirect(BASE_URL . 'Quan_ly_doi_tuong/danh_sach.php'); }

$db  = getDB();
$row = $db->prepare("SELECT ho_ten FROM doi_tuong WHERE id = ?");
$row->execute([$id]);
$dt  = $row->fetch();

if ($dt) {
    logHistory($id, 'Xóa', 'Xóa đối tượng: ' . $dt['ho_ten']);
    $db->prepare("DELETE FROM doi_tuong WHERE id = ?")->execute([$id]);
    setFlash('success', 'Đã xóa đối tượng "' . $dt['ho_ten'] . '"');
} else {
    setFlash('danger', 'Không tìm thấy đối tượng');
}

redirect(BASE_URL . 'Quan_ly_doi_tuong/danh_sach.php');
