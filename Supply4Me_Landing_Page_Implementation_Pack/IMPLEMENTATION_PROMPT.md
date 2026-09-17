# SUPPLY 4 ME — PREMIUM LANDING PAGE IMPLEMENTATION PROMPT

## 0. Read the pack first

Before changing code, inspect every file in this implementation pack.

Priority:
1. `01_branding_board.jpg` — official brand source of truth.
2. `02_approved_landing_page.png` — approved full-page visual direction.
3. `06_hero_reference.png` through `11_testimonials_footer_reference.png` — section-level references.
4. `12_dashboard_reference_dark.png` and `13_dashboard_reference_light.png` — reference the existing ERP's premium visual language so the public website and logged-in product feel related.
5. `SOURCE_NOTES.md` — business/product context.
6. `MEDIA_USAGE.md` — rules for assets.
7. `DESIGN_TOKENS.css` — suggested visual tokens.

Do not treat screenshots as webpages. Rebuild the interface using real semantic components.

---

# 1. Objective

Build a premium, modern public landing page for **Supply 4 Me**.

The page must explain what Supply 4 Me is, show how the service works, introduce the catalogue, build trust with business buyers, and provide two obvious actions:

PRIMARY CTA:
`Access App`

SECONDARY CTA:
`Go to Shop`

If the production product uses a direct PWA install/download route, the primary CTA may become `Download App`, but it must link directly to Supply 4 Me's own app/PWA flow.

### Critical restriction
DO NOT use:
- Google Play badges
- Google Play logos
- Apple App Store badges
- Apple App Store logos

Do not imply that Supply 4 Me is listed in an app store unless that is actually true.

---

# 2. Product positioning

Supply 4 Me is not a generic consumer e-commerce shop.

Position it as a **technology-driven FMCG distribution and supply-chain partner for businesses**.

Core message:
Supply 4 Me connects businesses with quality FMCG products, reliable distribution and opportunities to grow.

Primary audiences include:
- supermarkets
- provision stores
- restaurants
- hotels
- pharmacies
- wholesalers
- informal traders
- other business buyers

Initial operating focus is Benin City, with a platform designed to scale across Edo State and wider Nigeria.

Company vision:
`To become Nigeria's leading technology-driven FMCG distribution company.`

Company mission:
`To build the easiest and most reliable wholesale distribution network across Nigeria.`

Official tagline:
`MOVING BUSINESS. GROWING TOGETHER.`

---

# 3. Official branding

Use the branding board exactly.

Official palette:
- Orange / copper: `#9F5124`
- Charcoal: `#2D2C2C`
- Mid-grey: `#616262`
- Warm off-white: `#F1EFEE`
- White: `#F9F9FA`

Typography direction:
- Avenir Next Bold for headings
- Avenir Next LT Pro Regular for body text

If those fonts are not licensed/available in the existing project, use the closest already-approved project/system alternative.
Do not illegally bundle font files.

Brand personality:
- Reliable
- Efficient
- Professional
- Progressive
- Partner-Focused

The page must feel:
- premium
- operational
- trustworthy
- modern
- Nigerian-market aware
- business-first
- polished rather than flashy

---

# 4. Logo rules

Use the supplied logo files:
- `03_logo_dark.png`
- `04_logo_light.png`
- `05_logo_cleanish.png`

Do not:
- redraw the mark
- generate a substitute
- alter the cube geometry
- change "SUPPLY 4 ME"
- modify the official colour relationship
- stretch the logo
- rebuild it with text/icons

Use the correct light/dark logo variant depending on background.

---

# 5. Overall visual direction

Reference `02_approved_landing_page.png`.

Use a premium mixed-surface layout:
- dark immersive hero
- warm white catalogue/business sections
- dark glass workflow and statistics bands
- orange accents throughout
- high-quality logistics/FMCG photography
- generous whitespace
- subtle glassmorphism
- rounded cards
- thin highlights and borders
- restrained depth and glow

The glass effect must support hierarchy, not make the site look like a gaming UI.

Suggested:
```css
backdrop-filter: blur(18px);
-webkit-backdrop-filter: blur(18px);
```

Keep the page readable if backdrop-filter is unavailable.

---

# 6. Header / navigation

Desktop header:

LEFT:
Official Supply 4 Me logo

CENTRE:
- Home
- Shop
- For Businesses
- About Us
- Contact

RIGHT:
- Search icon/control
- `Go to Shop`
- `Access App`

Use a translucent dark glass header over the hero.

Active nav item:
- Supply 4 Me orange
- small underline/indicator
- no oversized pill

The header should become more opaque on scroll.

Mobile:
- compact logo
- menu trigger
- retain Access App / Shop access without overwhelming the viewport

---

# 7. Hero

Use `06_hero_reference.png` as the composition reference.

Eyebrow:
`TRUSTED FMCG SUPPLY PARTNER`

Headline:
`Everyday Supplies.`
`Greater Opportunities.`

Highlight `Greater Opportunities.` in the brand orange.

Supporting copy should stay close to:

`Supply 4 Me connects retailers and businesses with genuine FMCG products at competitive prices — powered by a reliable distribution network across Nigeria.`

Primary CTA:
`Access App`

Secondary CTA:
`Go to Shop`

Do not add store badges.

Trust/value row:
- Genuine Products
- Competitive Prices
- Reliable Delivery
- Built for Businesses

Hero visual:
- premium warehouse/logistics environment
- stacked cartons / fulfilment context
- delivery vehicle
- mobile/PWA interface shown on a device
- official Supply 4 Me branding used only from supplied assets

When an actual current customer-app screenshot exists, use it inside the phone/device mockup.
Do not fake current product availability inside production UI.

---

# 8. Shop by Category

Use `07_categories_reference.png`.

Heading:
`Shop by Category`

Supporting text:
`Everything your business needs, all in one place.`

Suggested category cards:
- Beverages
- Food Items
- Personal Care
- Household
- Baby & Kids
- And More

Each card:
- strong category image
- category name
- short descriptor
- small orange arrow CTA

Use real catalogue category imagery/data where possible.

CTA:
`View All Categories`

Link to the actual shop/category route.

---

# 9. How It Works

Use `08_how_it_works_reference.png`.

Dark full-width section.

Heading:
`How It Works`

Subheading:
`Simple. Fast. Reliable.`

Four steps:

1. `Browse & Order`
   `Find the products you need and place your order.`

2. `Make Payment`
   `Pay securely and receive confirmation.`

3. `We Prepare`
   `Our team picks and packs your order.`

4. `Fast Delivery`
   `Receive your products on time.`

The underlying platform workflow includes invoice/pro-forma generation, payment evidence/verification, warehouse fulfilment and delivery. The marketing page should explain this simply without exposing internal admin complexity.

Use line-connected circular icons with orange outlines.

Decorative phrase:
`From Order to Opportunity.`

Keep decorative script secondary and accessible.

---

# 10. Built for Growing Businesses

Use `09_business_reference.png`.

Heading:
`Built for`
`Growing Businesses`

Copy:

`Whether you run a small shop or a growing chain, Supply 4 Me gives you access to quality products, competitive prices and a reliable supply network across Nigeria.`

Benefits:
- Wide range of FMCG products
- Competitive wholesale prices
- Reliable and secure delivery
- Support for retailers and businesses
- Trusted supplier network

CTAs:
- `Access App`
- `Go to Shop`

Right-hand visual:
- premium real-world warehouse/fulfilment imagery
- optional glass trust callouts

Examples:
`Trusted by Retailers`
`Genuine Products`
`Reliable Delivery Network`

Do not state unsupported nationwide adoption figures.

---

# 11. Metrics / proof band

Reference `10_stats_app_cta_reference.png`.

This section may show verified business metrics.

IMPORTANT:
Do not hard-code or publish mock values such as:
- 10,000+ retailers
- 500+ categories
- 36 states
- 99% on-time delivery

unless the business has supplied evidence that those figures are current and approved.

Preferred implementation:
- metrics come from CMS/config/environment/server data
- hide unverified metrics
- allow Admin/marketing content to update them later

Potential metrics:
- retailers supplied
- active product lines/categories
- service locations
- on-time delivery rate

---

# 12. App / PWA CTA

Heading:
`Take Supply 4 Me With You Everywhere`

Copy:
`Access your account, place orders, track deliveries and grow your business — all in one app.`

CTA:
`Access App`

Optional secondary:
`Go to Shop`

Again:
NO GOOGLE PLAY BADGE.
NO APP STORE BADGE.

If PWA install is supported:
- use the browser/PWA install flow where available
- otherwise open the authenticated customer app

A device mockup can show the real Supply 4 Me customer interface.

---

# 13. Testimonials / trust stories

Use the structure from `11_testimonials_footer_reference.png`.

Heading:
`What Our Partners Say`

Subheading:
`Real businesses. Real growth.`

Only publish testimonials that have been:
- supplied by real customers/partners
- approved for public use

Do not ship invented testimonials from the design mockup.

Implementation options:
- fetch from CMS
- use approved static content
- hide this section until approved testimonials exist

---

# 14. Footer

Premium dark footer.

LEFT:
- official Supply 4 Me logo
- concise description

Suggested:
`Supply 4 Me is a technology-driven FMCG distribution platform connecting businesses with quality products, reliable distribution and opportunities to grow.`

Quick Links:
- Home
- Shop
- For Businesses
- About Us
- Contact

Support:
- Help Centre
- Delivery Information
- Returns & Refunds
- Terms & Conditions
- Privacy Policy

Get Started:
- brief app message
- `Access App`

Social icons:
only link accounts that actually exist.

Bottom line:
- copyright/year dynamically generated
- `Reliable Supply Chains. A Brighter Tomorrow.` may be used as a secondary campaign line if approved
- official tagline can remain `MOVING BUSINESS. GROWING TOGETHER.`

No app-store badges.

---

# 15. Relationship to the ERP dashboard

Reference:
- `12_dashboard_reference_dark.png`
- `13_dashboard_reference_light.png`

The landing page should NOT look like an admin dashboard.

However, both public and logged-in products should clearly come from the same brand family.

Reuse visual DNA:
- orange accent
- charcoal/navy depth
- glass surfaces
- rounded geometry
- crisp icons
- generous spacing
- premium typography
- restrained glow
- official logo

Do not reuse ERP tables/cards unnecessarily on the marketing site.

---

# 16. Real product data

Where the landing page displays:
- categories
- product images
- prices
- featured products

prefer the real Supply 4 Me API/catalogue/CMS.

Do not hard-code AI-generated products as live inventory.

Provide:
- loading state
- unavailable-image fallback
- empty state

If homepage product data cannot load, preserve the marketing page rather than breaking the entire page.

---

# 17. App and shop routing

The two primary user journeys must be obvious.

`Go to Shop`
→ real catalogue/shop route

`Access App`
→ real Supply 4 Me customer app/PWA route

For unauthenticated visitors:
- app route can open sign-in/register
- preserve intended return route after login if existing architecture supports it

Do not route customers into the ERP admin area.

---

# 18. Responsive behaviour

Desktop:
- immersive hero
- split hero composition
- 6 category cards where space permits
- business visual beside copy

Tablet:
- reduce hero visual scale
- category grid wraps
- content becomes 2-column where appropriate

Mobile:
- stacked hero
- hero device below copy
- CTAs full-width or paired responsibly
- horizontal category carousel OR 2-column cards
- How It Works becomes vertical
- business image below copy
- footer stacks cleanly

Avoid shrinking desktop layouts until text becomes tiny.

---

# 19. Performance

This is a public landing page.

Required:
- responsive image sources
- WebP/AVIF where appropriate
- lazy-load below-fold imagery
- preload only critical hero media
- avoid enormous full-resolution images on mobile
- keep CLS low
- reserve image dimensions
- keep initial JS light
- use server rendering/static generation where supported by current stack
- optimise fonts without bundling unlicensed font files

---

# 20. SEO

Add appropriate:
- page title
- meta description
- Open Graph metadata
- social image
- canonical URL
- structured organisation/business data where correct
- meaningful alt text

Suggested page title:
`Supply 4 Me | Reliable FMCG Distribution for Growing Businesses`

Suggested description:
`Supply 4 Me connects retailers and businesses with quality FMCG products, competitive wholesale pricing and reliable distribution.`

Do not make unsupported nationwide-service claims.

---

# 21. Accessibility

Required:
- semantic landmarks
- keyboard navigation
- clear focus states
- accessible menu
- strong contrast
- descriptive alt text
- logical heading order
- 44px minimum practical touch targets
- reduced-motion support
- no essential text embedded only inside images

All important copy must be actual HTML text, not baked into background graphics.

---

# 22. Motion

Use subtle premium motion:
- gentle hero reveal
- card hover lift
- orange underline/indicator animation
- subtle button glow
- section fade/slide on entry

Keep animations short and restrained.

Respect:
`prefers-reduced-motion: reduce`

Avoid:
- constant floating elements
- heavy parallax
- excessive particles
- looping glowing effects

---

# 23. Components

Adapt names to the existing stack.

Suggested structure:

LandingPage
├── MarketingHeader
├── HeroSection
├── TrustBenefits
├── CategorySection
│   └── CategoryCard
├── HowItWorksSection
│   └── ProcessStep
├── BusinessGrowthSection
├── MetricsStrip
├── AppCTASection
├── TestimonialsSection
│   └── TestimonialCard
└── MarketingFooter

Shared:
- BrandLogo
- PrimaryButton
- SecondaryButton
- GlassCard
- SectionHeading
- ResponsiveImage

---

# 24. Content/data separation

Do not bury marketing copy and stats across components.

Prefer a central configuration/CMS structure, e.g.:

```ts
landingPage = {
  hero: {...},
  categories: [...],
  benefits: [...],
  process: [...],
  metrics: [...],
  testimonials: [...]
}
```

This should allow content updates without redesigning components.

---

# 25. Do not do these things

DO NOT:
- use Google Play branding
- use Apple App Store branding
- use fake store availability
- redraw the Supply 4 Me logo
- use a generated replacement logo
- invent business statistics
- invent customer testimonials
- claim national coverage unless verified
- embed the approved full-page screenshot as the live site
- turn the site into a generic consumer fashion/e-commerce template
- remove the B2B/wholesale positioning
- make the landing page visually unrelated to the ERP
- hard-code fake product inventory
- use huge unoptimised images

---

# 26. Acceptance criteria

Before declaring completion verify:

[ ] Official logo is used correctly.
[ ] Official colour palette is followed.
[ ] Hero closely matches approved design direction.
[ ] No Google Play badge appears.
[ ] No Apple/App Store badge appears.
[ ] Access App route works.
[ ] Go to Shop route works.
[ ] Mobile navigation works.
[ ] Categories link to real routes/data.
[ ] Page works without fake metrics.
[ ] No invented testimonials ship to production.
[ ] Images are optimised.
[ ] Page is responsive.
[ ] Keyboard navigation works.
[ ] No console errors.
[ ] No broken links.
[ ] Lighthouse/core performance issues have been checked.
[ ] Existing customer app/shop functionality remains intact.

---

# 27. Developer completion report

At the end provide:

1. files created/changed
2. components created/reused
3. routes connected
4. APIs/CMS data used
5. media assets used
6. any reference element intentionally omitted
7. performance checks
8. accessibility checks
9. desktop screenshot
10. mobile screenshot
11. confirmation that no Google Play/App Store branding was used
