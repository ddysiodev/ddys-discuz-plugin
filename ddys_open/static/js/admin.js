(function () {
  function byId(id) {
    return document.getElementById(id);
  }

  function inputValue(id) {
    var el = byId(id);
    return el ? el.value.trim() : '';
  }

  function quoteAttr(value) {
    return value.replace(/["'\\[\]]/g, '').trim();
  }

  function buildShortcode() {
    var kind = byId('ddys-discuz-shortcode-kind');
    var output = byId('ddys-discuz-shortcode-output');
    if (!kind || !output) return;
    var tag = kind.value || 'ddys_latest';
    var slug = inputValue('ddys-discuz-shortcode-slug');
    var limit = inputValue('ddys-discuz-shortcode-limit');
    var type = inputValue('ddys-discuz-shortcode-type');
    var attrs = [];
    if (slug && ['ddys_movie', 'ddys_sources', 'ddys_related', 'ddys_comments', 'ddys_collection'].indexOf(tag) !== -1) attrs.push('slug="' + quoteAttr(slug) + '"');
    if (limit && ['ddys_latest', 'ddys_hot', 'ddys_collections'].indexOf(tag) !== -1) attrs.push('limit="' + quoteAttr(limit) + '"');
    if (type && ['ddys_latest', 'ddys_hot'].indexOf(tag) !== -1) attrs.push('type="' + quoteAttr(type) + '"');
    output.value = '[' + tag + (attrs.length ? ' ' + attrs.join(' ') : '') + ']';
  }

  document.addEventListener('click', function (event) {
    if (event.target && event.target.id === 'ddys-discuz-shortcode-build') {
      buildShortcode();
    }
  });
  document.addEventListener('change', function (event) {
    if (event.target && event.target.id && event.target.id.indexOf('ddys-discuz-shortcode-') === 0) {
      buildShortcode();
    }
  });
})();
