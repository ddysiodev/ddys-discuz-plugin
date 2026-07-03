(function () {
  function bindRequestForm(form) {
    form.addEventListener('submit', function (event) {
      if (!window.fetch || !window.FormData) return;
      event.preventDefault();
      var status = form.querySelector('.ddys-discuz-status');
      if (status) status.textContent = '提交中...';
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        credentials: 'same-origin',
        headers: { Accept: 'application/json' }
      }).then(function (response) {
        return response.json();
      }).then(function (payload) {
        if (status) status.textContent = payload && payload.success === false ? (payload.message || '提交失败') : '已提交。';
      }).catch(function (error) {
        if (status) status.textContent = error.message || '提交失败';
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    Array.prototype.forEach.call(document.querySelectorAll('[data-ddys-discuz-request-form]'), bindRequestForm);
  });
})();
