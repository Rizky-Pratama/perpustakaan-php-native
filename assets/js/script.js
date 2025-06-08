// Script JS untuk Sistem Perpustakaan
document.addEventListener('DOMContentLoaded', function () {
  // Auto close alerts after 5 seconds
  setTimeout(function () {
    const alerts = document.querySelectorAll('.alert:not(.alert-no-autoclose)');
    alerts.forEach(function (alert) {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 5000);

  // Enable tooltips
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  const tooltipList = [...tooltipTriggerList].map(
    tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)
  );

  // Add shadow effect to navbar on scroll
  window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 10) {
      navbar.classList.add('shadow');
    } else {
      navbar.classList.remove('shadow');
    }
  });

  // Table row hover effect
  const tableRows = document.querySelectorAll('.table-hover tbody tr');
  tableRows.forEach(row => {
    row.addEventListener('mouseover', function () {
      this.style.backgroundColor = 'rgba(0,0,0,0.03)';
    });
    row.addEventListener('mouseout', function () {
      this.style.backgroundColor = '';
    });
  });

  // Form validation
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener(
      'submit',
      event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      },
      false
    );
  });
});
