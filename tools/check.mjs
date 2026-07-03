import { readdir, readFile } from 'node:fs/promises';
import { join, relative } from 'node:path';

const root = process.cwd();
const failures = [];

const required = [
  'README.md',
  'README.zh-CN.md',
  'LICENSE',
  '.gitignore',
  'ddys_open/discuz_plugin_ddys_open_SC_UTF8.xml',
  'ddys_open/ddys_open.class.php',
  'ddys_open/admincp.inc.php',
  'ddys_open/index.inc.php',
  'ddys_open/api.inc.php',
  'ddys_open/request.inc.php',
  'ddys_open/install.php',
  'ddys_open/uninstall.php',
  'ddys_open/upgrade.php',
  'ddys_open/check.php',
  'ddys_open/source/bootstrap.inc.php',
  'ddys_open/source/security.func.php',
  'ddys_open/source/cache.func.php',
  'ddys_open/source/client.func.php',
  'ddys_open/source/render.func.php',
  'ddys_open/source/shortcode.func.php',
  'ddys_open/table/table_ddys_open_cache.php',
  'ddys_open/table/table_ddys_open_rate.php',
  'ddys_open/template/page.htm',
  'ddys_open/static/css/frontend.css',
  'ddys_open/static/css/admin.css',
  'ddys_open/static/js/frontend.js',
  'ddys_open/static/js/admin.js',
  'ddys_open/static/images/icon-16.png',
  'ddys_open/static/images/icon-32.png',
  'ddys_open/static/images/icon-192.png',
  'ddys_open/static/images/icon-512.png',
  'ddys_open/static/images/logo.png'
];

const shortcodes = [
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
];

for (const file of required) await mustExist(file);

await checkXml();
await checkPhp();
await checkDocs();
await checkIcons();
await checkForbiddenFiles();
await checkForbiddenText();

if (failures.length) {
  for (const failure of failures) console.error(`- ${failure}`);
  process.exit(1);
}

console.log(JSON.stringify({ ok: true, files: (await listFiles(root)).length, shortcodes: shortcodes.length }, null, 2));

async function checkXml() {
  const xml = await read('ddys_open/discuz_plugin_ddys_open_SC_UTF8.xml');
  for (const text of [
    '<item id="identifier"><![CDATA[ddys_open]]></item>',
    '<item id="directory"><![CDATA[ddys_open/]]></item>',
    '<item id="menu"><![CDATA[低端影视页面嵌入]]></item>',
    '<item id="type"><![CDATA[11]]></item>',
    '<item id="menu"><![CDATA[低端影视移动端嵌入]]></item>',
    '<item id="type"><![CDATA[28]]></item>',
    '<item id="installfile"><![CDATA[install.php]]></item>',
    '<item id="uninstallfile"><![CDATA[uninstall.php]]></item>',
    '<item id="__variables">',
    '<item id="variable"><![CDATA[api_base_url]]></item>',
    '<item id="variable"><![CDATA[api_key]]></item>',
    '<item id="variable"><![CDATA[enable_request_form]]></item>',
    '<item id="variable"><![CDATA[enable_pretty_urls]]></item>',
    '<item id="variable"><![CDATA[pretty_base_path]]></item>'
  ]) {
    if (!xml.includes(text)) failures.push(`plugin XML missing ${text}`);
  }
}

async function checkPhp() {
  const klass = await read('ddys_open/ddys_open.class.php');
  const client = await read('ddys_open/source/client.func.php');
  const render = await read('ddys_open/source/render.func.php');
  const shortcode = await read('ddys_open/source/shortcode.func.php');
  const admin = await read('ddys_open/admincp.inc.php');
  const install = await read('ddys_open/install.php');
  for (const text of ['class plugin_ddys_open', 'discuzcode', 'global_header', 'plugin_ddys_open_forum']) {
    if (!klass.includes(text)) failures.push(`ddys_open.class.php missing ${text}`);
  }
  for (const text of ['ddys_open_proxy_response', 'ddys_open_allowed_route', 'Invalid route parameters', 'ddys_open_handle_request_form', 'Authorization: Bearer']) {
    if (!client.includes(text)) failures.push(`client.func.php missing ${text}`);
  }
  for (const text of ['ddys_open_render_list', 'ddys_open_render_detail', 'ddys_open_render_sources', 'ddys_open_render_calendar', 'ddys_open_render_request_form']) {
    if (!render.includes(text)) failures.push(`render.func.php missing ${text}`);
  }
  for (const shortcodeName of shortcodes) {
    if (!shortcode.includes(`'${shortcodeName}'`)) failures.push(`Missing shortcode ${shortcodeName}`);
    if (!admin.includes(`value="${shortcodeName}"`) && !admin.includes(`[${shortcodeName}`)) failures.push(`Admin generator missing ${shortcodeName}`);
  }
  for (const text of ['ddys_open_cache', 'ddys_open_rate', '$finish = true']) {
    if (!install.includes(text)) failures.push(`install.php missing ${text}`);
  }
  for (const full of (await listFiles(join(root, 'ddys_open'))).filter((file) => file.endsWith('.php'))) {
    const rel = relative(root, full).replace(/\\/g, '/');
    await checkBalancedPhp(rel);
  }
}

async function checkDocs() {
  const en = await read('README.md');
  const zh = await read('README.zh-CN.md');
  if (!en.includes('[DDYS](https://ddys.io/) API')) failures.push('English README must link DDYS website with DDYS text.');
  if (!zh.includes('[低端影视](https://ddys.io/) API')) failures.push('Chinese README must link official website with 低端影视 text.');
  for (const text of [en, zh]) {
    for (const marker of ['/ddys/movie/this-tempting-madness', 'RewriteRule ^ddys/?$', 'rewrite ^/ddys/?$', 'DDYS Discuz Movie']) {
      if (!text.includes(marker)) failures.push(`README missing pretty URL marker ${marker}`);
    }
    for (const shortcodeName of ['ddys_latest', 'ddys_movie', 'ddys_request_form']) {
      if (!text.includes(shortcodeName)) failures.push(`README missing ${shortcodeName}`);
    }
  }
}

async function checkIcons() {
  for (const [size, expected] of [['16', 16], ['32', 32], ['192', 192], ['512', 512]]) {
    const bytes = await readBinary(`ddys_open/static/images/icon-${size}.png`);
    if (bytes.readUInt32BE(16) !== expected || bytes.readUInt32BE(20) !== expected) {
      failures.push(`icon-${size}.png has wrong dimensions`);
    }
  }
}

async function checkBalancedPhp(file) {
  const text = await read(file);
  const pairs = { '}': '{', ')': '(', ']': '[' };
  const stack = [];
  let quote = '';
  let escape = false;
  for (let i = 0; i < text.length; i++) {
    const char = text[i];
    if (quote) {
      if (escape) { escape = false; continue; }
      if (char === '\\') { escape = true; continue; }
      if (char === quote) quote = '';
      continue;
    }
    if (char === '"' || char === "'") { quote = char; continue; }
    if (char === '{' || char === '(' || char === '[') stack.push(char);
    else if (char === '}' || char === ')' || char === ']') {
      const opener = stack.pop();
      if (opener !== pairs[char]) {
        failures.push(`${file} has mismatched bracket near offset ${i}`);
        break;
      }
    }
  }
  if (stack.length) failures.push(`${file} has unclosed bracket(s): ${stack.slice(-5).join('')}`);
}

async function checkForbiddenFiles() {
  const files = await listFiles(root);
  for (const full of files) {
    const rel = relative(root, full).replace(/\\/g, '/');
    if (/(^|\/)(\.env|node_modules|vendor|cache)(\/|$)/i.test(rel) || /\.(zip|log|bak)$/i.test(rel)) {
      failures.push(`Forbidden file: ${rel}`);
    }
  }
}

async function checkForbiddenText() {
  const files = await listFiles(root);
  const patterns = ['ghp' + '_', 'npm' + '_', 'OpenAI', 'AI Agent', 'GPT', 'Open' + ' API', '??' + '??'];
  for (const full of files) {
    const rel = relative(root, full).replace(/\\/g, '/');
    if (/\.(png|jpg|jpeg|webp|gif)$/i.test(rel) || rel === 'tools/check.mjs') continue;
    const text = await read(rel);
    for (const pattern of patterns) {
      if (text.includes(pattern)) failures.push(`${rel} contains restricted text pattern ${pattern}`);
    }
  }
}

async function mustExist(file) {
  try {
    await readFile(join(root, file));
  } catch {
    failures.push(`Missing required file: ${file}`);
  }
}

async function read(file) {
  return readFile(join(root, file), 'utf8');
}

async function readBinary(file) {
  return readFile(join(root, file));
}

async function listFiles(dir) {
  const entries = await readdir(dir, { withFileTypes: true });
  const out = [];
  for (const entry of entries) {
    if (entry.name === '.git' || entry.name === 'node_modules' || entry.name === 'vendor' || entry.name === 'cache') continue;
    const full = join(dir, entry.name);
    if (entry.isDirectory()) out.push(...await listFiles(full));
    else out.push(full);
  }
  return out;
}
