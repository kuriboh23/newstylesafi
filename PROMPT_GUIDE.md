# Reusable Prompt Guide — Storefront Final Polish

This file contains a ready-to-use prompt for an AI coding agent. Copy the
block below into the agent, replace the `{{PLACEHOLDERS}}`, and run it from the
target repository root.

The prompt is written for a similar **PHP + MySQL storefront with a custom
admin panel**, but the discovery step tells the agent to adapt to the actual
stack instead of assuming file paths.

---

<!-- BEGIN PROMPT -->

<role>
You are a senior full-stack engineer working on an existing production
storefront. You preserve the current architecture, design language, business
logic and user data. You make focused changes, verify them, and report
limitations honestly.
</role>

<objective>
Deliver a final polish pass on {{PROJECT_NAME}}: improve the homepage trust
section, make category ordering and category selection easier in the admin,
fix product-image presentation and add a fullscreen product viewer, harden
security, remove dead code, and rewrite the public README as a secure
GitHub-ready document with a database schema.
</objective>

<context>
- Project root: {{PROJECT_ROOT}}
- Stack: {{STACK}} (for example PHP 8 + MySQL + vanilla JS/CSS)
- Public shop: {{PUBLIC_URL}}
- Admin area: {{ADMIN_URL}}
- Main stylesheet: {{MAIN_CSS}}
- Main JavaScript file: {{MAIN_JS}}
- Homepage view: {{HOMEPAGE_VIEW}}
- Product page view: {{PRODUCT_VIEW}}
- Product card partial: {{PRODUCT_CARD_PARTIAL}}
- Product admin form: {{ADMIN_PRODUCT_FORM}}
- Product list/admin search: {{ADMIN_PRODUCT_LIST}}
- Category model/controller: {{CATEGORY_MODEL}}, {{CATEGORY_CONTROLLER}}
- Database dump and schema: {{DATABASE_DUMP}}, {{DATABASE_SCHEMA}}

Before editing, inspect the real repository and locate the equivalent files,
helpers, routes and conventions. Do not blindly copy paths from this prompt.
Read the existing code first, then adapt the requirements below.
</context>

<guardrails>
- Keep the existing stack and architecture. Do not add a framework, Composer
  package, build step, CSS framework, icon font or external dependency unless
  the repository already uses it.
- Reuse the project's existing helpers for escaping, URLs, CSRF, sessions,
  icons, flash messages and database access.
- Do not invent product data, business claims, phone numbers, prices,
  testimonials, credentials or legal text.
- Do not delete or modify real product images, customer data or uploaded
  files. Only remove code that is proven unreachable and generated logs.
- Keep all customer-facing copy in the storefront's existing language.
- Match the existing naming, formatting and visual tokens. Use the project's
  own colors, spacing and typography variables instead of copying another
  brand.
- Before deleting anything, search the whole repository for references and
  confirm the file, route or method is unreachable.
</guardrails>

<scope>
Implement all of the following, in this order:

1. Homepage trust section
2. Category order default and validation
3. Category icon picker and category filter in the admin
4. Product image fitting and fullscreen viewer
5. Security hardening and dead-code cleanup
6. README rewrite with a schema diagram
</scope>

<detailed_requirements>

## 1. Homepage trust section

Improve the existing "why choose us" section (for example
`Pourquoi nous choisir`) without changing its editable content source.

- Wrap the section in a distinct panel: subtle warm background, border and
  large radius.
- Keep the existing eyebrow, title and lead text.
- Restructure each card so the icon, decorative index number and text body are
  separate elements. The body must wrap the title and description.
- Use a responsive grid: one column on small screens, two columns from the
  small-tablet breakpoint upward. Do not force four narrow columns.
- Give cards a clear hover/focus state: slight lift, soft shadow, accent
  border or accent bar.
- Keep the decorative number low-contrast and out of the reading order
  (`aria-hidden="true"`).
- Do not add invented statistics or promises.

Acceptance: the section reads cleanly at 360 px, 768 px and 1280 px; no card
text is clipped; all icons remain aligned.

## 2. Category order default

Make the admin category order safe and predictable.

- The add-category form must default `Ordre` to **1**, with `min="1"`.
- The edit form must show at least **1** and use `min="1"`.
- In the controller/model, normalize empty, zero or negative values to **1**.
- Change the database column default to **1** in both the full dump and the
  empty-schema file.
- Update the live development database default when possible, without
  re-importing or deleting existing rows.
- Keep ascending sort order semantics: lower numbers appear first.

Acceptance: creating a category without sending a sort value stores `1`;
existing categories can still be reordered manually.

## 3. Admin category picker and filter

### Category picker

Replace the plain category `<select>` in the product form with a progressive
enhancement:

- Server-render a native `<select name="category_id">` first, so the form
  still works without JavaScript.
- Add a custom trigger showing the selected category's inline SVG icon, name
  and a chevron.
- Add a menu of category options. Each option shows the same SVG icon and its
  name; the current option is visually selected and has
  `aria-selected="true"`.
- Start the custom picker with the `hidden` attribute. JavaScript removes it
  and hides the native select only after enhancement succeeds.
- Keep the native select's value synchronized with the chosen option.
- Close the menu on selection, outside click and `Escape`; return focus to the
  trigger; toggle `aria-expanded`.
- Re-render the correct selection after a validation error.
- Use the existing icon library. Add a `chevron-down` icon only if missing.

### Category icon picker (category add/edit forms)

Make the icon field on both the add-category and edit-category forms show a
real preview, not just icon names:

- Create one reusable icon-picker component/partial so both forms stay in
  sync.
- Keep a native `<select>` with every icon label as the no-JavaScript
  fallback.
- Add a custom trigger with a small preview box containing the actual SVG,
  the current icon label and a chevron.
- The dropdown options must show a small SVG icon immediately next to each
  icon name, plus an `Aucune icône` option with a neutral dash or empty state.
- Mark the current option with `aria-selected="true"` and a visible selected
  state; scroll it into view when the menu opens.
- Synchronize the native select, close on selection/outside click/`Escape`,
  return focus to the trigger and toggle `aria-expanded`.
- This picker is separate from the product-category picker: one chooses a
  visual icon, the other chooses a category.

Acceptance: the owner can open the icon menu, see every icon rendered next to
its name, choose one, and see the preview update immediately.

### Product search by category

- Add a category `<select>` to the existing admin product search form.
- Submit it through the existing GET search form, with a
  `data-auto-submit`-style enhancement only if that pattern already exists.
- Read and validate the category ID server-side. Ignore IDs that do not exist.
- Extend the product query with an optional category condition while
  preserving the existing keyword search.
- Group keyword conditions with parentheses before adding `AND` conditions.
- Show the active category in the result count, for example
  `2 produits dans Chemises`.
- Include all categories, including empty or hidden ones, in the admin filter.

### Search/filter toolbar layout

Rebuild the admin product search controls instead of stacking inputs, selects
and buttons in an ad-hoc flex row with an absolutely positioned icon.

- Use one bordered filter panel with a soft background and a deliberate grid:
  wide search field, category selector, then the action group.
- Add small visible labels above the search field and category selector.
- Put the search icon inside the input wrapper, not absolutely positioned
  against the whole form height.
- Align the submit button and the `Nouveau` button with the input baseline.
- Show a `Réinitialiser` link only when a keyword or category filter is active.
- Use consistent 42 px control heights, radius, border and focus ring.
- Collapse the whole filter panel to one column on narrow screens; make the
  submit and `Nouveau` buttons full-width instead of letting individual
  controls wrap awkwardly.
- Do not let labels, icons or buttons overlap at any viewport width.

Acceptance: keyword + category search returns only the intersection; clearing
the category returns all results; the page works without JavaScript; the
filter bar is aligned and readable at 360 px, 768 px and desktop widths.

## 4. Product images and fullscreen viewer

### Product cards

- Product-card images must **fill** their frame. Use `object-fit: cover` and
  a sensible `object-position` so the garment remains the focus.
- Keep a consistent card aspect ratio. Do not distort the image.
- Keep lazy loading and async decoding where already used.

### Product page

- The main product image and gallery should show the entire image
  (`object-fit: contain`) on a neutral background.
- Wrap the main image in a button or equivalent accessible control that opens
  a fullscreen viewer. Add a visible "Agrandir" / "View larger" hint.
- Add a custom overlay viewer, not the browser Fullscreen API:
  - dark backdrop; centered image with `max-width` and `max-height`;
  - close button, previous and next buttons;
  - arrow buttons must be clearly visible over any image: use an opaque dark
    background (around `rgba(24,21,18,.94)`), a bright border, white icon and
    a shadow. Do not use low-opacity glass-only buttons;
  - previous/next hidden when there is only one image;
  - backdrop click and `Escape` close the viewer;
  - left/right arrow keys navigate;
  - lock body scrolling while open;
  - return focus to the trigger on close;
  - update the main image and active thumbnail while navigating.
- Use the existing gallery thumbnails as the image source list; avoid
  duplicate URLs.
- Keep keyboard accessibility and `aria-label` text.

Acceptance: card images fill their boxes without letterboxing; the product
page shows the whole image; the fullscreen arrows are immediately visible
against light and dark photos.

## 5. Security and cleanup

Apply only what is missing, using the project's existing patterns.

- Ensure every state-changing POST verifies CSRF: cart add/remove/update/clear,
  wishlist toggles, checkout and admin actions.
- Send the CSRF token from JavaScript through the project's existing header or
  payload convention, and include a hidden field in normal forms.
- Keep session cookies `HttpOnly`, `SameSite=Lax`, and `Secure` on HTTPS.
  Set the cookie path to the application base path.
- Start the session before HTML output if a CSRF token is rendered in the page
  head, so the cookie is reliably sent.
- Add a temporary admin login throttle, for example five failed attempts and a
  ten-minute lock in the session. Use a generic message; never reveal whether
  the email or password was wrong.
- Send `Cache-Control: no-store, private` for admin pages so the browser
  cannot restore them after logout.
- Add safe headers where absent: `X-Content-Type-Options: nosniff`,
  `X-Frame-Options: DENY`, a restrictive `Permissions-Policy`,
  `Cross-Origin-Opener-Policy: same-origin`, and HSTS only on HTTPS.
- Add `uploads/.htaccess` (Apache) that disables script handlers for PHP,
  PHTML, CGI, shell and similar extensions. Do not rely on it as the only
  defence: validate uploads with `getimagesize()`, allow only JPG/PNG/WebP,
  limit size and rename files randomly.
- Block `app/`, `storage/`, `database/`, `tools/`, `config.php`, `.git`,
  `.env` and other dotfiles in the root `.htaccess`, while allowing
  `.well-known`.
- Remove only proven-dead code: unreachable settings screens/routes, duplicate
  legacy models, unused helper functions and orphaned views.
- Clear generated application logs that contain stale errors. Keep
  `.gitkeep` and runtime guard files.

Do not weaken existing protections or add lint/type suppressions to make
checks pass.

## 6. README rewrite

Rewrite the public README as a GitHub-friendly document:

- Short project description and feature list.
- Stack and requirements.
- Repository structure.
- Local installation and configuration with placeholders only.
- Database schema as a Mermaid `erDiagram` with primary keys, foreign keys and
  the real relationships.
- Clearly state which data is **not** stored (for example no customer/order
  tables when checkout happens through WhatsApp).
- Admin features, deployment steps, security notes and performance notes.
- Log locations.
- No real admin email, password, phone number, API key, database credential
  or customer data. Use values like `vous@exemple.com` and
  `un-mot-de-passe-long-et-unique`.
- Warn that the shipped configuration is a template and must be changed
  before going live.
- Keep the README accurate: do not claim features that are not implemented.

</detailed_requirements>

<acceptance_criteria>
- [ ] Homepage trust section is responsive and visually stronger, with no
      invented content.
- [ ] Category order defaults to 1 in forms, code and SQL; zero values are
      normalized.
- [ ] Product form has an icon category picker with a working native fallback.
- [ ] Category add/edit forms show an icon menu with a small SVG next to
      every icon name and a working native fallback.
- [ ] Admin product search filters by keyword and category together.
- [ ] The admin product filter toolbar is aligned, labelled and stacks
      cleanly on narrow screens.
- [ ] Product-card images fill their frames; product-page images show fully.
- [ ] Fullscreen viewer works with mouse, keyboard and backdrop close.
- [ ] Fullscreen arrows are opaque and clearly visible.
- [ ] All state-changing POSTs reject a missing/invalid CSRF token.
- [ ] Admin login is throttled; admin pages are not cached.
- [ ] Uploaded files cannot execute as scripts on Apache.
- [ ] Dead code is removed only after reference checks.
- [ ] README contains an accurate Mermaid schema and no secrets.
- [ ] Existing public and admin routes still return their expected status.
- [ ] No real product data or uploaded images were deleted.
</acceptance_criteria>

<verification>
Run the smallest reliable checks for the target repository:

1. Lint every changed PHP file with `php -l`.
2. Run `node --check` on changed JavaScript files.
3. Check CSS brace balance or run the project's CSS linter, if present.
4. Run the project's existing tests if they exist.
5. Start or reuse the local server and smoke-test:
   - homepage, catalog, product page, cart, wishlist;
   - admin login, dashboard, products, product create/edit, category
     add/edit pages, social/settings pages that still exist;
   - confirm the category add/edit pages render one icon option per icon plus
     `Aucune icône`, and the product filter toolbar contains both labels.
6. Verify CSRF:
   - POST a cart or wishlist action without a token -> 401/403/419;
   - repeat with a valid token -> success.
7. Verify admin category filtering returns the expected intersection.
8. Verify checkout still redirects to the configured WhatsApp/order target.
9. Create a temporary category without an order value, confirm it stores 1,
   then delete it.
10. Confirm no new PHP warnings or application log entries were generated.
11. Do not claim a browser visual check unless you actually performed one.

Report exactly which checks passed, which failed, and which could not run.
</verification>

<output_format>
Return a concise completion report:

- summary of the implemented changes;
- files created, modified and deleted;
- exact verification commands/results;
- known limitations (for example Apache rules not exercised by the PHP
  development server, no browser visual pass);
- anything that still requires the user's decision.
</output_format>

<!-- END PROMPT -->
