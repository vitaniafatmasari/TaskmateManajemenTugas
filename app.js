// TaskMate - Interaksi halaman
document.addEventListener('DOMContentLoaded', function () {
  // Sidebar mobile
  var btn = document.getElementById('btnToggleSidebar');
  var sb = document.getElementById('appSidebar');
  var bd = document.getElementById('sidebarBackdrop');
  if (btn && sb) {
    btn.addEventListener('click', function () {
      sb.classList.add('show');
      if (bd) bd.classList.add('show');
    });
    if (bd) {
      bd.addEventListener('click', function () {
        sb.classList.remove('show');
        bd.classList.remove('show');
      });
    }
  }

  // Konfirmasi hapus tugas
  document.querySelectorAll('[data-confirm-hapus]').forEach(function (el) {
    el.addEventListener('click', function (ev) {
      var msg = el.getAttribute('data-confirm-hapus') || 'Yakin ingin menghapus tugas ini?';
      if (!confirm(msg)) {
        ev.preventDefault();
      }
    });
  });

  // Validasi tenggat >= tanggal diberikan
  var fTgl = document.getElementById('tanggal_diberikan');
  var fTenggat = document.getElementById('tenggat_waktu');
  function syncMin() {
    if (fTgl && fTenggat && fTgl.value) {
      fTenggat.min = fTgl.value;
      if (fTenggat.value && fTenggat.value < fTgl.value) {
        fTenggat.value = fTgl.value;
      }
    }
  }
  if (fTgl) fTgl.addEventListener('change', syncMin);
  if (fTenggat) fTenggat.addEventListener('change', syncMin);
  syncMin();
});
