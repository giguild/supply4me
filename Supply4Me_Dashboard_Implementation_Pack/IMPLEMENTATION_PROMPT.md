# Supply 4 Me Admin Dashboard — Implementation Prompt

You are redesigning and implementing the existing **Supply 4 Me** ERP/Admin Dashboard.

## Primary objective
Rebuild the admin dashboard into a modern, premium, high-end logistics/SaaS interface with a subtle **glassy / glassmorphism** feel while preserving the existing ERP information architecture and functionality.

The interface MUST support a real **Light Mode / Dark Mode toggle**. Both themes must be fully designed; dark mode must not simply invert the light theme.

Use the supplied media files as visual references:
- `01_branding_board.jpg` — authoritative Supply 4 Me branding, logo, palette and personality.
- `02_existing_dashboard_dark.png` and `03_existing_dashboard_light.png` — current application structure and navigation.
- `04_reference_sales_dashboard.png`, `05_reference_invoice_dashboard.png`, `06_reference_dark_dashboard.png` — inspiration only for layout quality, hierarchy, density and premium SaaS presentation.
- `07_target_dashboard_dark.png` — target dark-mode visual direction.
- `08_target_dashboard_light.png` — target light-mode visual direction.
- `09_target_dashboard_light_alt.png` — additional light-mode reference.

Do not copy third-party brand identities from the inspiration images. Use them only as compositional and UX inspiration.

---

## Brand system
Supply 4 Me personality:
- Reliable
- Efficient
- Professional
- Progressive
- Partner-focused

Core brand palette from the supplied board:
- Brand burnt orange: `#9F5124`
- Charcoal: `#2D2C2C`
- Mid gray: `#616262`
- Warm off-white: `#F1EEEE`
- Soft white: `#F9F9FA`

Use the Supply 4 Me logo/mark from the provided branding asset. Do not replace it with a generic logo.

Typography should visually approximate the supplied Avenir Next branding. If Avenir Next is unavailable, use a high-quality system/web fallback such as Inter, Manrope, or a similar clean geometric sans-serif. Do not require proprietary font files.

---

## Theme behavior
Implement a persistent theme toggle in the top-right header.

Requirements:
1. Toggle instantly between `light` and `dark` without page reload.
2. Persist the user's selection using local storage or the application's existing preference system.
3. On first visit, respect `prefers-color-scheme` unless the product already has a saved preference.
4. Add `data-theme="light"` / `data-theme="dark"` or equivalent at the root.
5. Use semantic design tokens / CSS variables. Do NOT scatter hard-coded theme colors across components.
6. Charts, tooltips, tables, borders, icons, hover states, skeletons, menus and modals must all respond to the theme.
7. Avoid flashing the wrong theme on page load.
8. Theme control must be keyboard accessible and have an accessible label.

Suggested semantic tokens:

```css
:root,
[data-theme="light"] {
  --bg: #F4F1EE;
  --bg-elevated: rgba(255,255,255,0.72);
  --surface: rgba(255,255,255,0.68);
  --surface-strong: rgba(255,255,255,0.90);
  --surface-muted: rgba(249,249,250,0.76);
  --sidebar: rgba(255,255,255,0.74);
  --text: #202124;
  --text-muted: #667085;
  --border: rgba(45,44,44,0.10);
  --grid: rgba(45,44,44,0.09);
  --brand: #9F5124;
  --brand-hover: #87431E;
  --brand-soft: rgba(159,81,36,0.12);
  --shadow: 0 14px 40px rgba(54,39,29,0.08);
  --glass-blur: 18px;
}

[data-theme="dark"] {
  --bg: #08121F;
  --bg-elevated: rgba(15,27,42,0.78);
  --surface: rgba(18,31,47,0.66);
  --surface-strong: rgba(21,35,52,0.88);
  --surface-muted: rgba(14,26,40,0.72);
  --sidebar: rgba(8,18,31,0.86);
  --text: #F7F8FA;
  --text-muted: #A9B4C2;
  --border: rgba(255,255,255,0.10);
  --grid: rgba(255,255,255,0.09);
  --brand: #C5652C;
  --brand-hover: #D7773B;
  --brand-soft: rgba(197,101,44,0.16);
  --shadow: 0 18px 54px rgba(0,0,0,0.26);
  --glass-blur: 20px;
}
```

These are implementation starting points; preserve accessible contrast and tune visually against the target images.

---

## Visual language
Create a refined glassmorphism system, not an exaggerated glass effect.

Use:
- translucent surfaces
- `backdrop-filter: blur(...)` where supported
- 1px low-contrast borders
- soft shadows
- occasional warm orange glow near active controls
- 14–20px card radii
- generous but efficient spacing
- restrained gradients
- smooth 180–250ms transitions

Avoid:
- excessive neon
- fully transparent cards over busy images
- heavy gradients everywhere
- excessive drop shadows
- giant empty cards
- tiny unreadable text
- pure black backgrounds
- flat gray-on-gray dark mode

Dark mode should feel sophisticated: deep navy/charcoal rather than pure black, with warm orange highlights.

Light mode should feel warm, premium and airy: off-white/stone background, white translucent surfaces, brown/orange accents and soft neutral shadows.

---

## Desktop dashboard composition
Preserve the current ERP navigation but improve hierarchy and organization.

### Left sidebar
Keep these functional areas visible:
- Dashboard
- Customers
- Suppliers
- Products
- Featured Products
- Orders
- Invoices
- Payments
- Stock
- Warehouses

Operations / Receiving / Shipping:
- GRN
- Pick Lists
- Packing Lists
- Shipments / Deliveries
- Carriers
- Drivers
- Delivery Routes

Administration:
- Users
- Roles
- Sales Reps
- Branches

Reports:
- Sales Report
- Inventory Report
- Financial Report

Footer / utility:
- Settings
- Payment Info, if still part of the current application
- Collapse sidebar control

Use small section labels, consistent icons and clear active-state treatment. The active Dashboard item should use the brand orange with a soft glow/gradient, not a generic blue highlight.

On smaller desktop/tablet widths allow the sidebar to collapse to an icon rail.

### Top bar
Include:
- global search: “Search orders, customers, products…”
- optional `Ctrl/Cmd + K` shortcut indicator
- light/dark theme toggle
- notification icon + badge
- current user avatar/name/role
- date control or contextual date filter

### Hero / welcome area
Use a restrained branded logistics visual or translucent background image inspired by the target mockups.

Text:
- `Welcome back,`
- `Super Admin`
- `Here's what's happening with your business today.`

Optional brand line:
- `Moving business forward, together.`

Do not make the hero so tall that it pushes core operational data below the fold.

### KPI row
Create premium metric cards for:
- Total Orders
- Total Customers
- Total Products
- Pending Payments
- Monthly Revenue

Each card can include:
- icon tile
- primary figure
- supporting label
- trend pill
- compact sparkline where meaningful

Avoid fake data. Bind to existing application values. If there is no trend data, omit the trend rather than inventing it.

### Analytics section
Main card: `Sales Overview`
- line/area chart
- Orders / Revenue / Customers series where supported by actual data
- timeframe dropdown such as Last 7 Days / 30 Days / 90 Days / 12 Months
- theme-aware chart colors and grids

Secondary card: `Order Status`
- donut or radial visualization
- Pending
- Processing
- Completed
- Cancelled
- central total order count

Do not fabricate values. Derive them from the existing data source.

### Quick Actions
Keep operational shortcuts prominent:
- New Order
- Add Customer
- Add Product
- Create Invoice
- Record Payment
- New GRN
- New Pick List
- New Packing List
- Create Shipment
- New Delivery

The panel may scroll or adapt based on viewport height. Use compact colored icon tiles but keep the overall brand coherent.

### Operational tables
Create premium table cards for:
- Recent Orders
- Recent Payments

Use:
- clear row spacing
- status pills
- aligned currency values
- hover state
- accessible focus state
- `View All` action

Status colors should be semantic and consistent across themes.

### Lower insights
Add if supported by actual data:
- `Top Products`
- `Low Stock Alerts`

If low-stock count is zero, show an elegant empty state rather than an empty table.

### Brand promo card
Use a small strong brand panel inspired by the target design, such as:
- `Reliable Supply. Real Growth.`
- `Quality products. Trusted distribution. A stronger tomorrow.`

CTA may link to Products or Catalogue if that destination exists.

---

## Responsiveness
Implement responsive behavior for:
- 1440px+ desktop
- 1280px laptop
- 1024px tablet landscape
- smaller tablet/mobile if the existing admin app supports it

Desktop: multi-column analytics layout.
Tablet: reduce to 2 columns, collapse sidebar where needed.
Mobile: cards stack, tables become horizontally scrollable or convert to compact row cards without losing functionality.

Do not simply scale the desktop UI down.

---

## Interaction polish
Implement:
- subtle card hover elevation where clickable
- button hover/pressed/focus states
- animated theme transition without distracting full-screen flashes
- skeleton/loading states using theme tokens
- empty states
- error states
- success/warning/error status pills
- useful tooltips for icon-only controls
- visible keyboard focus ring
- reduced-motion support

Use `prefers-reduced-motion` to reduce nonessential animation.

---

## Engineering constraints
1. Work inside the existing codebase and current framework; do not unnecessarily rewrite the application.
2. Preserve existing API calls, routes, authentication, authorization and data logic.
3. Refactor presentational components where useful, but avoid breaking business behavior.
4. Create reusable UI primitives for cards, buttons, badges, table shells, metric cards, section headers and glass panels.
5. Use the project’s existing icon library if present; otherwise add one consistent icon system rather than mixing libraries.
6. Do not hard-code dashboard numbers shown in the reference mockups.
7. Do not use AI-generated text inside the implemented UI unless it is static branded copy explicitly approved above.
8. Ensure all Naira values render with the correct `₦` symbol and locale formatting.
9. Maintain semantic HTML and WCAG-conscious color contrast.
10. Do not embed the target screenshots into the page as UI. Recreate the interface as real components.

---

## Theme implementation recommendation
If using React/Next.js, create a root-level theme provider or use the project's existing theme system.

Conceptually:

```ts
type Theme = 'light' | 'dark';

// preference resolution
savedTheme ?? systemPreference ?? 'light'
```

Apply theme at the document root and persist updates. If SSR is involved, prevent hydration mismatch / flash-of-incorrect-theme using an early theme initialization strategy compatible with the existing framework.

Every design component must consume semantic tokens instead of checking `theme === 'dark'` for dozens of individual color declarations.

---

## Media usage
Use the supplied branding and visual files as REFERENCES and source material where appropriate.

Recommended mapping:
- `01_branding_board.jpg`: logo/brand reference, palette, personality.
- `07_target_dashboard_dark.png`: visual QA target for dark mode.
- `08_target_dashboard_light.png`: visual QA target for light mode.
- `02_existing_dashboard_dark.png` / `03_existing_dashboard_light.png`: functionality and existing IA reference.
- `04`, `05`, `06`: inspiration only.

If a clean production logo file already exists in the repository, use that instead of cropping it from the branding board. If not, create a clean logo asset from supplied approved branding without altering the logo design.

Do not ship screenshots or third-party inspiration images as decorative production content unless rights and intended use are confirmed.

---

## Acceptance criteria
The implementation is complete when:
- Light and dark modes both match the same premium design system.
- Toggle works and persists.
- No hard-coded mock data replaces live ERP data.
- Existing navigation and actions continue to work.
- Dashboard is visually close to `07_target_dashboard_dark.png` and `08_target_dashboard_light.png` while following Supply 4 Me branding.
- Cards have a restrained glass effect in both themes.
- Charts/tables/statuses are theme-aware.
- Sidebar is visually upgraded and responsive/collapsible.
- Layout works cleanly on common laptop and desktop sizes.
- Accessibility basics are implemented.
- No major console errors, hydration warnings or layout overflows remain.

## Final verification
Before finishing:
1. Run the existing test suite.
2. Run lint/typecheck/build commands used by the repository.
3. Manually test theme persistence after refresh.
4. Test keyboard navigation of top bar, sidebar, quick actions and tables.
5. Test both themes at 1440px, 1280px and 1024px widths.
6. Verify that no reference-image numbers were hard-coded.
7. Report files changed, test results and any remaining limitations.
