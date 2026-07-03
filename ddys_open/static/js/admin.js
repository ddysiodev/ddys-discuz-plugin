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
    var id = inputValue('ddys-discuz-shortcode-id');
    var username = inputValue('ddys-discuz-shortcode-username');
    var q = inputValue('ddys-discuz-shortcode-q');
    var limit = inputValue('ddys-discuz-shortcode-limit');
    var perPage = inputValue('ddys-discuz-shortcode-per-page');
    var year = inputValue('ddys-discuz-shortcode-year');
    var month = inputValue('ddys-discuz-shortcode-month');
    var type = inputValue('ddys-discuz-shortcode-type');
    var attrs = [];
    if (q && tag === 'ddys_suggest') attrs.push('q="' + quoteAttr(q) + '"');
    if (slug && ['ddys_movie', 'ddys_sources', 'ddys_related', 'ddys_comments', 'ddys_collection'].indexOf(tag) !== -1) attrs.push('slug="' + quoteAttr(slug) + '"');
    if (id && tag === 'ddys_share') attrs.push('id="' + quoteAttr(id) + '"');
    if (username && tag === 'ddys_user') attrs.push('username="' + quoteAttr(username) + '"');
    if (limit && ['ddys_latest', 'ddys_hot'].indexOf(tag) !== -1) attrs.push('limit="' + quoteAttr(limit) + '"');
    if (perPage && ['ddys_movies', 'ddys_comments', 'ddys_collections', 'ddys_collection', 'ddys_shares', 'ddys_requests', 'ddys_activities'].indexOf(tag) !== -1) attrs.push('per_page="' + quoteAttr(perPage) + '"');
    if (year && tag === 'ddys_calendar') attrs.push('year="' + quoteAttr(year) + '"');
    if (month && tag === 'ddys_calendar') attrs.push('month="' + quoteAttr(month) + '"');
    if (type && ['ddys_movies', 'ddys_latest', 'ddys_hot', 'ddys_activities'].indexOf(tag) !== -1) attrs.push('type="' + quoteAttr(type) + '"');
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
