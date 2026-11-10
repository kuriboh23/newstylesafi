# PRD: Newstylesafi — Mobile-First Men's Fashion E-Commerce Website

**Document version:** 1.1 — Client reskin  
**Date:** 10 November 2026  
**Status:** Adapted from Shoop Moda Style PRD; ready for client-specific validation  
**Prepared for:** Newstylesafi, Safi, Morocco  
**Base project:** Shoop Moda Style (`shoopmoda`)  
**Target platform:** Namecheap shared hosting (PHP + MySQL)  
**Version control:** Existing Shoop Moda GitHub repository

---

## 1. Executive Summary

Newstylesafi is a men's clothing business in Safi, Morocco. The goal of this project is to adapt the existing Shoop Moda Style e-commerce website into a branded, mobile-first online store for Newstylesafi.

This is a **reskin and configuration project, not a ground-up rebuild**. The existing PHP, MySQL, HTML, CSS and vanilla JavaScript architecture should be reused wherever it already satisfies the requirements. Existing working cart, wishlist, product, checkout and admin functionality must not be rewritten without a clear technical reason.

The website should help customers browse men's clothing, understand product prices and available sizes/colors, and submit structured orders through WhatsApp. It should also give Newstylesafi a consistent official online destination that can be linked from Instagram.

The design direction is based on the supplied Newstylesafi announcement artwork: a bold black-and-yellow identity, strong typography, and clear brand presentation. The site must not imply that a website alone prevents impersonation; it should clearly display the store's confirmed official contact and social links.

No customer account system or online payment gateway is required for Version 1.0.

---

## 2. Project Goals

### Primary goals
- Rebrand the existing Shoop Moda storefront for Newstylesafi.
- Preserve and reuse the existing technical foundation and working features.
- Present men's clothing in a clean, premium, easy-to-browse catalog.
- Reduce repetitive Instagram/WhatsApp questions by showing product details clearly.
- Let customers select product variants and submit an organized WhatsApp order.
- Give the owner a simple admin area to manage products, categories and availability.
- Provide a clear official-store identity with verified contact and Instagram links.

### Success criteria
- Responsive layout from 320px mobile width upward.
- Fast loading on mobile data and shared hosting.
- Product price, size, color and availability information is easy to find.
- WhatsApp order messages contain accurate selected product and customer details.
- Owner can add/edit products without developer assistance.
- No critical security vulnerabilities.
- Existing Shoop Moda functionality remains intact unless a change is explicitly required.

---

## 3. Brand and Audience

### Business identity
- **Brand name:** Newstylesafi
- **Business category:** Men's clothing / men's fashion
- **Location:** Safi, Morocco
- **Instagram:** https://www.instagram.com/newstylesafi/
- **Official WhatsApp:** To be confirmed by the client before launch.
- **Official address/store hours:** To be confirmed if they are to appear on the website.

### Target audience
- Customers in Morocco interested in men's clothing.
- Instagram users discovering Newstylesafi through social posts and stories.
- Returning customers who want to check products and availability before contacting the store.

### Customer needs
- Browse products comfortably on a phone.
- See clear photos, prices, sizes, colors and availability.
- Know how to contact the genuine Newstylesafi store.
- Place an order without repeatedly asking for basic product information.

---

## 4. Scope

### In scope — Version 1.0
- Newstylesafi branding and visual reskin of the existing Shoop Moda website.
- Mobile-first home page.
- Product listing and category pages.
- Product detail page with images, price and available variants.
- Search and basic product sorting/filtering supported by the existing project.
- Cookie/localStorage-based cart and wishlist, reusing existing implementation.
- Checkout form and structured WhatsApp order generation.
- Existing simple password-protected admin panel, adapted to the new brand.
- Product and category management.
- Manual product/variant availability management.
- Basic SEO metadata and social sharing metadata.
- Clear official Instagram and confirmed WhatsApp links.
- Basic contact/store information page or section.

### Out of scope unless separately agreed
- Customer accounts and customer login.
- Online card payments or payment gateway integration.
- Automatic Instagram catalog synchronization.
- Real-time stock synchronization with a physical POS.
- Delivery-company API integration or live shipment tracking.
- Advanced analytics or CRM.
- Automated email/SMS notifications.
- Customer review system or unverified ratings.
- Multi-language management interface.
- Complex discount/promotion engine.

---

## 5. Functional Requirements

### 5.1 Home page
- Strong Newstylesafi brand header with logo/wordmark.
- Hero section using approved Newstylesafi campaign imagery and a clear “Shop now” action.
- Product category navigation. Categories must be confirmed by the owner; examples could include T-shirts, shirts, trousers, jackets, sets and accessories.
- New arrivals or featured products section.
- Optional promotional/discount section only when the owner provides a real promotion and end date.
- Product grid, two columns on typical mobile screens.
- Clear links to Instagram, WhatsApp and store information.
- Mobile-friendly navigation and footer.

### 5.2 Product listing, categories and search
- Category pages and search results.
- Product cards show product image, name, current price and sale price only when applicable.
- Wishlist control if supported by the existing project.
- Basic category/filter chips and sorting, reusing existing functionality.
- Clear empty-search and no-results states.
- Do not display fake review counts, ratings or popularity claims.

### 5.3 Product detail page
- Product image gallery with thumbnails or swipe interaction.
- Product name, price and genuine sale price where applicable.
- Description and product details.
- Size selector showing only sizes offered for that product.
- Color selector showing only colors offered for that product.
- Quantity selector.
- Availability indicator based on the stock information maintained by the owner.
- Add-to-cart action and clear feedback.
- WhatsApp contact action as a fallback for product questions.
- On mobile, keep the primary purchase action easy to reach.

### 5.4 Wishlist
- Reuse the existing wishlist implementation.
- Store wishlist data in localStorage/cookies where already implemented.
- Allow customers to remove saved products and return to product pages.
- No customer account required.

### 5.5 Cart
- Show product image, name, selected size, selected color, unit price and quantity.
- Allow quantity changes and item removal.
- Show subtotal and any configured delivery fee or discount.
- Do not invent delivery prices or discount rules; values must come from confirmed business settings.
- Validate that required variant choices have been made before checkout.

### 5.6 Checkout
- Collect customer name, phone number, city and full delivery address.
- Shipping/delivery option only if the owner confirms available options and prices.
- Show an order summary before the customer proceeds to WhatsApp.
- Generate a pre-filled WhatsApp message with customer details, product names, selected sizes/colors, quantities, line totals and order total.
- Use the client's confirmed WhatsApp number. Do not reuse Shoop Moda's number.
- The customer must review and send the WhatsApp message; opening WhatsApp does not itself confirm an order.
- Clearly state that the store will confirm order and availability with the customer.

### 5.7 WhatsApp order message
The generated message should be concise and readable, containing:
- Newstylesafi order heading.
- Customer name and phone.
- City and delivery address.
- Each product name.
- Selected size and color, where applicable.
- Quantity and line total.
- Subtotal, confirmed delivery fee and discount if configured.
- Grand total.
- A short request for the store to confirm the order.

The WhatsApp destination number is a launch-blocking client detail and must be verified.

### 5.8 Admin panel
Reuse the existing Shoop Moda admin panel wherever possible.

Required management capabilities:
- Password-protected admin access.
- Create, edit, publish/unpublish and delete products.
- Product name, description, category, price and optional sale price.
- Upload/manage multiple product images.
- Configure available sizes and colors per product.
- Manage stock/availability per variant where supported by the existing data model; otherwise document the limitation and implement the smallest safe extension.
- Create and edit categories.
- Configure confirmed WhatsApp number and store contact details.
- Configure delivery fee and promotional settings only when supplied by the owner.
- Optional order log only if already supported or separately approved.

The admin interface should use Newstylesafi branding but remain simple and practical.

---

## 6. Visual Design and UI Guidelines

### Mandatory brand direction
The supplied Newstylesafi announcement artwork establishes the visual direction:
- **Primary background:** near-black `#080808`
- **Primary accent:** vivid yellow `#FFD400` (may be tuned to match the approved logo/artwork)
- **Primary text on dark backgrounds:** `#FFFFFF`
- **Secondary text:** `#BDBDBD`
- **Dark text on yellow:** `#080808`
- **Borders/dividers:** subtle charcoal `#292929`

These are proposed web palette values based on the supplied artwork, not official brand color codes. Confirm exact logo and colors with the owner if available.

### Typography
- Use a strong, modern sans-serif such as Inter, Poppins or a system font stack.
- Use bold headings and highly readable product details.
- Avoid excessive decorative fonts.
- Ensure adequate contrast and readable text sizes on mobile.

### Design principles
- Premium, confident, masculine clothing-store presentation.
- Black surfaces balanced with white space and yellow highlights.
- Yellow should emphasize important actions, labels and selected states, not overwhelm every screen.
- Product photography should remain the focus.
- Consistent buttons, cards, inputs, badges and navigation.
- Touch targets at least 44px where practical.
- Avoid copying the announcement poster as the entire website layout; translate its brand cues into a usable shopping interface.

### Key screens to adapt
- Home page.
- Category/product listing.
- Product details and variant selection.
- Wishlist.
- Cart.
- Checkout.
- Search states.
- Admin login and dashboard.

---

## 7. Technical Architecture and Reuse Strategy

### Existing stack to preserve
- Backend: existing PHP 8.x codebase, vanilla PHP.
- Frontend: HTML5, CSS and vanilla JavaScript.
- Database: existing MySQL database.
- Hosting: existing Namecheap shared hosting plan.
- Version control: existing Shoop Moda GitHub repository.

Do not migrate to React, a new framework, WordPress or another platform for this reskin.

### Reskin approach
1. Inspect the current Shoop Moda repository and identify the existing entry points, shared components, stylesheets, database schema and admin pages.
2. Create a safe Git branch or commit before changing the theme.
3. Separate brand-specific values into a central configuration where practical: store name, logo, colors, Instagram URL, WhatsApp number, currency and contact information.
4. Replace Shoop Moda-specific text, imagery, categories and metadata with Newstylesafi content.
5. Adapt shared CSS/components to the black-and-yellow visual identity.
6. Reuse existing product, cart, wishlist, checkout and admin logic.
7. Modify database tables only when required for Newstylesafi's product variants or stock handling.
8. Test all existing flows before deployment.

### Data storage
Keep the existing storage approach unless inspection reveals a concrete issue:
- localStorage/cookies: cart, wishlist and recent searches.
- MySQL: products, categories, product images, variant/stock data and global settings.
- Optional order log only if the current implementation supports it and the owner wants it.

Do not store sensitive customer information unnecessarily in browser storage.

### Important implementation rule
A visual reskin must not silently break existing Shoop Moda features. Make small, testable changes and preserve the current working architecture.

---

## 8. Security, Performance and Accessibility

### Security
- Use PDO prepared statements for database queries.
- Escape output to prevent XSS.
- Validate and sanitize submitted data.
- Use CSRF protection for admin and state-changing forms.
- Hash admin passwords securely; never store plaintext passwords.
- Use secure cookie attributes where cookies are used.
- Protect admin routes and uploaded files.
- Validate image file type, size and extension on upload.
- Apply sensible rate limits to sensitive actions.
- Do not expose database credentials or secrets in frontend code or GitHub.

### Performance
- Mobile-first responsive layout.
- Compress product images; prefer WebP where supported.
- Lazy-load below-the-fold images.
- Use minimal JavaScript and avoid unnecessary dependencies.
- Reuse local assets and existing caching configuration.
- Keep database queries efficient for shared hosting.

### Accessibility
- Semantic HTML.
- Keyboard-accessible navigation and controls.
- Visible focus states.
- Good text/background contrast.
- Labels and validation messages for form fields.
- Useful alt text for product images.

### Compatibility
- Current versions of Chrome, Safari, Firefox and Edge.
- Responsive from 320px mobile screens to desktop.

---

## 9. SEO and Brand Trust

- Use page titles and meta descriptions containing Newstylesafi and relevant product/category terms.
- Add Open Graph metadata for product and home-page sharing.
- Use clean URLs if supported by the existing project.
- Add the official Instagram link and confirmed WhatsApp contact consistently.
- Provide a clear About/Contact section with only owner-confirmed business details.
- Make the official website link easy to find from Instagram bio and social posts.
- Do not claim that the site is verified by Instagram, guarantees authenticity, or prevents all impersonation.
- Do not publish an address, opening hours, delivery promise or return policy until confirmed by the owner.

---

## 10. Client Information Required Before Launch

The following details are not established by the source Shoop Moda PRD or the supplied Newstylesafi poster/story. Obtain them from the owner rather than guessing.

- [ ] Official Newstylesafi logo file (or approval to create a text-based wordmark).
- [ ] Confirmed WhatsApp order number, including country code.
- [ ] Product categories and preferred category names.
- [ ] Product photos, names, descriptions and prices.
- [ ] Available sizes and colors for each product.
- [ ] Stock/availability information and how often the owner will update it.
- [ ] Delivery areas, fees and delivery options.
- [ ] Return/exchange policy, if it should be displayed.
- [ ] Store address and opening hours, if they should be displayed.
- [ ] Preferred website language: French, Arabic or both.
- [ ] Confirmation of the proposed black-and-yellow website palette.

Until these details are confirmed, use clearly marked placeholders in the demo and do not present invented business information as fact.

---

## 11. Testing and Acceptance Checklist

### Branding
- [ ] No leftover Shoop Moda names, logos, phone numbers or women's-fashion copy.
- [ ] Newstylesafi branding appears consistently.
- [ ] Black-and-yellow styling is consistent and readable.
- [ ] Instagram link points to the correct profile.

### Shopping flow
- [ ] Home page and categories load correctly.
- [ ] Product images, prices and descriptions display correctly.
- [ ] Size and color selections work.
- [ ] Cart totals update correctly.
- [ ] Wishlist works after navigation and refresh.
- [ ] Checkout validates required customer fields.
- [ ] WhatsApp message contains the correct items and totals.
- [ ] WhatsApp destination uses the number confirmed by the owner.
- [ ] The customer is told that the store must confirm the order.

### Admin and technical checks
- [ ] Admin login works and is protected.
- [ ] Product/category create, edit and delete flows work.
- [ ] Variant availability can be updated as agreed.
- [ ] Image upload validation works.
- [ ] No PHP errors or broken links.
- [ ] No critical console errors.
- [ ] Mobile layout tested at narrow widths.
- [ ] Existing database and project files are backed up before deployment.

---

## 12. Delivery Plan

1. **Audit:** inspect the current Shoop Moda codebase and confirm which features already work.
2. **Backup:** commit the working version and create a reskin branch.
3. **Brand configuration:** add Newstylesafi's confirmed identity, contact details and palette.
4. **Visual reskin:** update shared components, home page, product cards, product details, cart, checkout and admin styling.
5. **Content setup:** add approved categories and sample products supplied by the owner.
6. **Functional testing:** test variants, cart, checkout, WhatsApp and admin.
7. **Client review:** show the demo and collect corrections.
8. **Launch:** deploy only after client content, WhatsApp number and delivery details are confirmed.

---

## 13. Commercial Scope Notes

This document defines the proposed Version 1.0 website scope. It does not establish a final price, delivery date, hosting/domain ownership, maintenance agreement or payment schedule.

Those terms should be agreed with Newstylesafi before full implementation. Any features outside the scope above should be quoted separately.

---

## 14. Approval

This PRD adapts the Shoop Moda Style project for Newstylesafi while preserving its existing technical foundation. Client-specific information marked for confirmation must be supplied or approved before launch.

**Client:** Newstylesafi  
**Project:** Newstylesafi website reskin based on Shoop Moda  
**Version:** 1.1
