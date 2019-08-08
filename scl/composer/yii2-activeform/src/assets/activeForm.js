var formChanged = false;
window.onload = function() {
  const form = document.querySelector('form[data-require-confirm]');

  if (!form) return;

  /** Select2 support **/

  const _jQuery = window.$ || window.jQuery;

  if (_jQuery) {
    [].forEach.call(form.querySelectorAll('span.select2'), function(el) {
      const select = el.parentNode.querySelector('select');

      _jQuery(select).on('change', function() {
        formChanged = true;
      });
    });
  }

  /** Select2 support **/

  [].forEach.call(form.querySelectorAll('input, select'), function (el) {
    el.addEventListener('change', function () {
      formChanged = true;
    })
  });

  form.addEventListener('submit', function (e) {
    formChanged = false;
  });

  window.addEventListener('beforeunload', function (e) {
    if (!formChanged) {
      return true;
    }


    var confirmationMessage = 'It looks like you have been editing something. '
        + 'If you leave before saving, your changes will be lost.';

    (e || window.event).returnValue = confirmationMessage; //Gecko + IE
    return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
  });
};
