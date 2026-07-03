# DDYS Discuz! 插件

[English](README.md) | 中文

[低端影视](https://ddys.io/) API 的官方 Discuz! X 插件。

- GitHub 仓库：[ddysiodev/ddys-discuz-plugin](https://github.com/ddysiodev/ddys-discuz-plugin)
- 插件标识符：`ddys_open`
- 安装目录：`source/plugin/ddys_open`
- 目标环境：Discuz! X3.4 / X3.5，UTF-8 简体包

## 功能

- 原生 Discuz! 插件目录和 XML 导入文件。
- 插件变量配置 API Base URL、API Key、缓存时间、前台样式、来源链接、导航入口、页面嵌入组件和求片表单。
- 后台诊断页：连接测试、缓存清理、当前设置、短代码生成器。
- 通过插件类解析帖子里的短代码。
- 可选在论坛首页、版块页、主题页输出低端影视组件。
- 通过 `plugin.php?id=ddys_open:index` 提供独立展示页。
- 通过 `plugin.php?id=ddys_open:api` 提供本地 JSON 代理。
- 通过 `plugin.php?id=ddys_open:request` 提供服务端求片提交。
- 独立数据库缓存表和限流表。
- 图标复制自主站图标集。

## 安装

1. 把 `ddys_open` 目录复制到 `source/plugin/ddys_open`。
2. 进入 Discuz 后台，导入 `discuz_plugin_ddys_open_SC_UTF8.xml`。
3. 安装并启用插件。
4. 在插件变量里确认 API Base URL、缓存、展示和求片表单配置。
5. 打开插件后台页，执行连接测试并生成短代码。

## 短代码

```text
[ddys_movies type="movie" per_page="24"]
[ddys_latest type="movie" limit="12"]
[ddys_hot limit="10"]
[ddys_search]
[ddys_suggest q="interstellar"]
[ddys_calendar year="2026" month="7"]
[ddys_movie slug="this-tempting-madness"]
[ddys_sources slug="this-tempting-madness"]
[ddys_related slug="this-tempting-madness"]
[ddys_comments slug="this-tempting-madness" per_page="20"]
[ddys_collections per_page="10"]
[ddys_collection slug="best-sci-fi" per_page="12"]
[ddys_shares per_page="10"]
[ddys_share id="1"]
[ddys_requests per_page="10"]
[ddys_activities per_page="10"]
[ddys_user username="demo"]
[ddys_types]
[ddys_genres]
[ddys_regions]
[ddys_request_form]
```

## 公开页面

```text
plugin.php?id=ddys_open:index
plugin.php?id=ddys_open:index&view=hot
plugin.php?id=ddys_open:index&view=search
plugin.php?id=ddys_open:index&view=calendar
plugin.php?id=ddys_open:index&view=movie&slug=this-tempting-madness
plugin.php?id=ddys_open:index&view=collections
plugin.php?id=ddys_open:index&view=requests
```

## 本地检查

```bash
node tools/check.mjs
node --test tests/*.test.mjs
```

检查会覆盖插件目录结构、XML 字段、短代码覆盖、数据表安装脚本、前台资源、图标尺寸，以及是否误带敏感文本或临时文件。

