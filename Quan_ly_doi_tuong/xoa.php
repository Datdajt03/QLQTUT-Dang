<?php
// Quan_ly_doi_tuong/xoa.php – Xóa đối tượng
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);

// Support batch delete (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = array_map('intval', array_filter($_POST['ids']));
    if (!empty($ids)) {
        $db = getDB();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        // Log histories
        $stmtName = $db->prepare("SELECT id, ho_ten FROM doi_tuong WHERE id IN ($placeholders)");
        $stmtName->execute($ids);
        $items = $stmtName->fetchAll();
        
        foreach ($items as $item) {
            logHistory($item['id'], 'Xóa hàng loạt', 'Xóa đối tượng: ' . $item['ho_ten']);
        }
        
        $stmtDel = $db->prepare("DELETE FROM doi_tuong WHERE id IN ($placeholders)");
        $stmtDel->execute($ids);
        
        setFlash('success', 'Đã xóa thành công ' . count($items) . ' đối tượng được chọn!');
    } else {
        setFlash('warning', 'Chưa chọn đối tượng nào để xóa');
    }
    redirect(BASE_URL . 'Quan_ly_doi_tuong/danh_sach.php');
}

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
