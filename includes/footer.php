<?php
// includes/footer.php
?>
</main><!-- /.main -->
</div><!-- /.layout -->

<!-- ===== GLOBAL SEARCH REDIRECT ===== -->
<script>
document.getElementById('globalSearch').addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && this.value.trim()) {
    window.location = '<?= BASE_URL ?>Chucnang/danh_sach.php?search=' + encodeURIComponent(this.value.trim());
  }
});

// Sidebar toggle (mobile)
document.getElementById('menuToggle').addEventListener('click', function() {
  document.getElementById('sidebar').classList.toggle('open');
});

// Close sidebar on outside click (mobile)
document.addEventListener('click', function(e) {
  var sb = document.getElementById('sidebar');
  var btn = document.getElementById('menuToggle');
  if (window.innerWidth <= 768 && !sb.contains(e.target) && e.target !== btn) {
    sb.classList.remove('open');
  }
});

// Flash auto dismiss
var flash = document.querySelector('.flash');
if (flash) setTimeout(function(){ flash.style.opacity='0'; flash.style.transition='opacity 0.5s'; }, 4000);
</script>
</body>
</html>
