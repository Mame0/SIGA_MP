# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Active work

- **Módulo de Vacaciones de Conductores** (dentro de Abastecimiento) — diseño aprobado, pendiente de implementar. Especificación completa en [DOC_MODULO_VACACIONES.md](DOC_MODULO_VACACIONES.md): tablas `mp_vaca_*`, `vacaciones_controller.php` (API JSON estilo `almacen_controller.php`), motor de tope de flota (4 ausencias/día) y reprogramación en cadena. Conductores = `mp_maes_personal` con `codi_carg = 6`.

## What this is

SIGA — the internal administrative management system of the **Ministerio Público / Distrito Fiscal de Arequipa (MPFN-DF Arequipa)**, Peru. It is a large, organically-grown **plain PHP + MySQL** web application (no framework, no Composer, no build/test tooling). UI text is Spanish.

The app is served by Apache under the path `/siga/` (see `BASE_URL` in `include/cabecera.php` and `RewriteBase /` in `.htaccess`). There is no build step: edit a `.php` file and it is live on the next request.

## Running / environment

- **Stack:** PHP (PDO/mysql) + MySQL/MariaDB, Apache with `mod_rewrite`. Run via XAMPP/WAMP or any Apache+PHP host with the docroot pointing at the `siga/` directory.
- **No tests, no linter, no package manager.** Don't look for `composer.json`, `phpunit`, or npm scripts — there are none. "Running a test" means hitting the page in a browser while logged in.
- **DB credentials** live in `classes/.credentials/db.php.ini` (parsed with `parse_ini_file`). `Db.class.php` looks for this file at `.credentials/db.php.ini`, then `classes/.credentials/db.php.ini`, then `../.credentials/db.php.ini`. Keys: `host`, `usuario`, `clave`, `dbnombre`. Never commit real credentials.
- **Schema reference:** `DB_SCHEMA.sql` (and `DB_SCHEMA_ALMACEN.sql`) are the current schema. The 179 MB `../mpfnarequipa_siga.sql` at the project root is a full DB dump (data + schema) — useful for grepping table/column definitions, but do not load it casually.

## Architecture

**Page model.** Each feature is a flat top-level `.php` file in `siga/` (~271 of them), named `module_action.php` — e.g. `admin_user.php`, `compras_nueva.php`, `cpbi_bienes_registro.php`, `asistencia_calcularhorasextra.php`, `concurso_postulante.php`, `almacen_listado.php`. A single file typically mixes SQL, business logic, and HTML output. Pages that mutate data POST/GET to themselves or to a dedicated handler (e.g. `almacen_controller.php`); AJAX endpoints are likewise standalone `.php` files that echo HTML/JSON.

**Bootstrap (`include/cabecera.php`).** Required at the top of ~222 pages via `require_once 'include/cabecera.php'`. It: starts the session, defines `BASE_URL`, instantiates the global `$Db`, loads the active language file, enforces a 1-hour session timeout (`MAX_SESSION_TIEMPO`), redirects to `index.php` if `$_SESSION['iden_oper']` is unset, and defines shared helpers (`destruir_session`, `formato_fecha_letras`, `dar_formato_carpeta`). **New/modernized pages** instead use `include/page_header.php` + `include/page_footer.php` (Bootstrap 5 / bootstrap-icons, brand color `#073A6B`) — only a handful so far. When adding a page, follow whichever convention the surrounding module uses.

**Database layer (`classes/Db.class.php`).** A thin PDO wrapper used everywhere via the global `$Db`. Core methods:
- `query($sql, $params=[])` — raw parameterized query, returns `fetchAll()`. Use named placeholders (`:name`).
- `select($table, $where=[], $limit, $start, $order_by=[])` — array-driven SELECT (`$limit==1` returns a single row, not an array of rows).
- `insert($table, $params)` (returns lastInsertId), `update($table, $params, $wheres)`, `delete($table, $params)`, `selectJoin(...)`, plus `beginTransaction/commit/rollBack`.
- Domain helpers also live here: `get_options()` (builds `<select>` option arrays from `n_codigo`/`x_nombre`), `get_options_dpto/prov/dist` (RENIEC ubigeo cascading selects), `get_perfil_reparto`, BLOB signature read/write (`selectUserBlob`/`dataUserBlob`).
- Prefer these methods (they parameterize). When writing raw SQL, always bind values — do not interpolate user input.

**Auth & authorization.**
- Login: `index.php` (form) → `login.php` checks `mp_admi_oper` with `pass_oper = md5($password)`, then populates `$_SESSION` (`iden_oper`, `logi_oper`, `codi_depe`, `codi_perf`, etc.) and all rows of `mp_admi_conf`. `home.php` is the post-login shell.
- Authorization is **role/menu based**: `mp_admi_oper_role` → `mp_admi_role_subm` → `mp_admi_subm` (submenus) → `mp_admi_menu`. `home.php` builds the user's menu from these joins and loads each feature page into an **iframe** named `body_iframe` via `load_page(url)`. Individual feature pages rely on `cabecera.php`'s session check for gating; they do not re-check per-menu permissions.

**Localization.** `include/languages/{spanish,english,quechua}.php` define `CONST_*` constants used throughout the UI. The active language is read from `mp_admi_conf` (`iden_conf = 2`), defaulting to `spanish`. New user-facing strings should be added as `CONST_*` entries, with a Spanish fallback `define()` guarded by `if (!defined(...))` as some modern pages do inline.

**Other subsystems.**
- `classes/` — PDF generation (`TCPDF`, `FPDI`, `pdfwrite`, `jpgraph`, and `Pdf_*.class.php` subclasses), `Html.class.php` (form/table HTML builders), `SFTP`/`Sftp.class.php`.
- `ws/` — SOAP/web-service integrations (RENIEC via `nusoap`/`.wsdl`, SIJ judicial system).
- `include/` — shared partials (`cabecera.php`, `denuncia.inc`, `registrar_acceso.php` audit logging) and large static ubigeo coordinate JS for maps.
- Front-end assets: `css/`, `js/`, `libmenu/` (AdminLTE, Bootstrap, jQuery, FontAwesome), `chart.js/`, `mapas/`.

## Database naming conventions (important when writing SQL)

- Tables are prefixed by domain: `mp_admi_*` (admin: users, roles, menus, personnel, config), plus module families. Personnel master is `mp_admi_oper`; legacy/master code tables often expose `n_codigo`, `x_nombre`, `n_estado` columns.
- Columns use a 4-letter-prefix Hungarian style: `iden_*` (PK/id), `codi_*` (foreign code), `nomb_*` (name), `appa_/apma_*` (apellido paterno/materno), `ndoc_*` (DNI), `esta_/n_estado` (status flag, `1`=enabled), `orde_*` (sort order), `flag_*`. Match the existing column style of the table you are touching.
- `mp_admi_conf` is a key/value config table (`nomb_conf`/`valo_conf`); its rows are loaded into `$_SESSION` at login.

## Working in this repo — cautions

- **`siga/siga/` is a near-complete duplicate** of the application (another full copy of the ~271 pages + assets). Confirm which copy is actually deployed before editing; changes to one are not reflected in the other.
- **Lots of legacy/scratch files** coexist with live code: `*_old.php`, `*_old2.php`, `temp_*.php`, `temp_db_out_*.txt`, `*0.php` (e.g. `casos_analisis_carga0.php`). These are abandoned versions or debugging artifacts — don't treat them as canonical, and prefer the unsuffixed/most-recently-modified variant.
- The codebase predates consistent escaping; some pages interpolate SQL directly. When modifying or adding queries, route them through `Db.class.php` with bound parameters and `htmlspecialchars` output.
