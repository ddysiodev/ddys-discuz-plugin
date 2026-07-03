import assert from 'node:assert/strict';
import test from 'node:test';
import { readFile, readdir } from 'node:fs/promises';
import { join } from 'node:path';

const root = process.cwd();

test('plugin root matches Discuz install directory', async () => {
  const names = (await readdir(root, { withFileTypes: true })).map((entry) => entry.name);
  assert.ok(names.includes('ddys_open'));
  const pluginNames = (await readdir(join(root, 'ddys_open'), { withFileTypes: true })).map((entry) => entry.name);
  assert.ok(pluginNames.includes('discuz_plugin_ddys_open_SC_UTF8.xml'));
  assert.ok(pluginNames.includes('ddys_open.class.php'));
});

test('plugin XML declares modules, variables, and lifecycle scripts', async () => {
  const xml = await read('ddys_open/discuz_plugin_ddys_open_SC_UTF8.xml');
  assert.match(xml, /<item id="identifier"><!\[CDATA\[ddys_open\]\]><\/item>/);
  assert.match(xml, /<item id="installfile"><!\[CDATA\[install\.php\]\]><\/item>/);
  assert.match(xml, /<item id="uninstallfile"><!\[CDATA\[uninstall\.php\]\]><\/item>/);
  assert.match(xml, /<item id="name"><!\[CDATA\[ddys_open\]\]><\/item>\s*<item id="menu"><!\[CDATA\[低端影视页面嵌入\]\]><\/item>[\s\S]*?<item id="type"><!\[CDATA\[11\]\]><\/item>/);
  assert.match(xml, /<item id="name"><!\[CDATA\[ddys_open\]\]><\/item>\s*<item id="menu"><!\[CDATA\[低端影视移动端嵌入\]\]><\/item>[\s\S]*?<item id="type"><!\[CDATA\[28\]\]><\/item>/);
  assert.match(xml, /<item id="__variables">/);
  assert.match(xml, /api_base_url/);
  assert.match(xml, /enable_request_form/);
});

test('forum hooks parse post shortcodes and expose page widgets', async () => {
  const klass = await read('ddys_open/ddys_open.class.php');
  assert.match(klass, /class plugin_ddys_open/);
  assert.match(klass, /function discuzcode/);
  assert.match(klass, /class plugin_ddys_open_forum/);
  assert.match(klass, /index_middle/);
  assert.match(klass, /forumdisplay_bottom/);
  assert.match(klass, /viewthread_bottom/);
});

test('all planned shortcodes are implemented', async () => {
  const shortcode = await read('ddys_open/source/shortcode.func.php');
  for (const name of [
    'ddys_movies',
    'ddys_latest',
    'ddys_hot',
    'ddys_search',
    'ddys_suggest',
    'ddys_calendar',
    'ddys_movie',
    'ddys_sources',
    'ddys_related',
    'ddys_comments',
    'ddys_collections',
    'ddys_collection',
    'ddys_shares',
    'ddys_share',
    'ddys_requests',
    'ddys_activities',
    'ddys_user',
    'ddys_types',
    'ddys_genres',
    'ddys_regions',
    'ddys_request_form'
  ]) {
    assert.ok(shortcode.includes(`'${name}'`), name);
  }
});

test('server-side proxy and request form are guarded', async () => {
  const client = await read('ddys_open/source/client.func.php');
  const request = await read('ddys_open/request.inc.php');
  const security = await read('ddys_open/source/security.func.php');
  assert.match(client, /ddys_open_allowed_route/);
  assert.match(client, /Invalid route parameters/);
  assert.match(client, /ddys_open_handle_request_form/);
  assert.match(client, /Authorization: Bearer/);
  assert.match(request, /ddys_open_check_formhash/);
  assert.doesNotMatch(security, /submitcheck\('ddys_submit'\)/);
});

test('install script creates cache and rate-limit tables', async () => {
  const install = await read('ddys_open/install.php');
  assert.match(install, /ddys_open_cache/);
  assert.match(install, /ddys_open_rate/);
  assert.match(install, /\$finish = true/);
});

test('readmes use language-specific official website links', async () => {
  const en = await read('README.md');
  const zh = await read('README.zh-CN.md');
  assert.match(en, /\[DDYS\]\(https:\/\/ddys\.io\/\) API/);
  assert.match(zh, /\[低端影视\]\(https:\/\/ddys\.io\/\) API/);
});

test('icons use copied DDYS sizes', async () => {
  for (const [size, expected] of [['16', 16], ['32', 32], ['192', 192], ['512', 512]]) {
    const bytes = await readBinary(`ddys_open/static/images/icon-${size}.png`);
    assert.equal(bytes.readUInt32BE(16), expected);
    assert.equal(bytes.readUInt32BE(20), expected);
  }
});

async function read(file) {
  return readFile(join(root, file), 'utf8');
}

async function readBinary(file) {
  return readFile(join(root, file));
}
