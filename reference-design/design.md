#design.md

# Content for the markdown file
content = """# UI Design Prompts for Sheza Laundry (Google Stitch AI)

This document contains a series of detailed prompts designed to generate the user interface for the Sheza Laundry application using the **TALL Stack** (Tailwind CSS, Alpine.js, Laravel, Livewire).

---

## 1. Global Setup & Master Layout
**Goal:** Define the visual identity and core navigation structure.

**Prompt:**
"Configure a global layout for a web application using Tailwind CSS and Alpine.js. The primary brand color is a custom hex `#f39c12` (use this for primary buttons, active states, and accents). The typography should use a modern sans-serif font like Inter. Create a responsive sidebar navigation for the Admin layout, and a mobile-first bottom navigation bar for the PWA customer-facing layout."

---

## 2. PWA Mobile-First Landing Page
**Goal:** A customer-facing page optimized for mobile installation.

**Prompt:**
"Build a mobile-first, PWA-optimized Landing Page for 'Sheza Laundry' using Tailwind CSS and Alpine.js. The primary color accent is `#f39c12`. 

Include the following sections:
1. A Hero section with a welcoming headline, a brief description, and an 'Install App' prompt (simulated PWA install button).
2. A 'Check Order Status' card prominently placed at the top, featuring an input field for 'Order Number' or 'Phone Number' and a submit button.
3. A 'Our Services' grid displaying cards for services like 'Wash & Fold' (Cuci Kiloan), 'Dry Cleaning' (Satuan), and 'Ironing Only' (Setrika). Each card should show an estimated price.
4. A clean footer with contact information and a floating WhatsApp action button.
Ensure the design feels like a native mobile app rather than a traditional desktop website."

---

## 3. POS (Point of Sale) Dashboard
**Goal:** A fast, intuitive interface for the cashier to process orders.

**Prompt:**
"Create a modern Point of Sale (POS) interface for a laundry business using Tailwind CSS and Alpine.js. The layout should be optimized for both desktop and tablet screens. Primary theme color: `#f39c12`.

The UI must be divided into two main columns:
**Left Column (Service Catalog):**
- A search bar and category filter chips (e.g., All, Kiloan, Satuan, Shoes).
- A grid of clickable service cards. Each card displays the service name, price, and an icon.

**Right Column (Current Order Cart):**
- Customer selection area (search for existing member or 'Add New').
- A list of added items. Use Alpine.js to handle a quantity counter (+ / - buttons) for each item.
- A subtotal, tax, and grand total calculation area.
- A 'Payment Method' selector (Cash, Transfer, QRIS).
- A large, prominent 'Process Order & Print' primary button.
Keep the design clean with plenty of whitespace and high contrast for fast cashier operations."

---

## 4. Order Management & Tracking
**Goal:** Managing the lifecycle of laundry orders.

**Prompt:**
"Design an Order Management dashboard page for a laundry app using Tailwind CSS. 

I need a Kanban-style board or a clean data table layout to track laundry statuses. The statuses are: 'New Order', 'Washing', 'Ironing', 'Ready for Pickup', and 'Completed'.

For each order card/row, display:
- Order ID and Customer Name.
- Total items/weight and total price.
- A badge indicating payment status (Paid in green, Unpaid in red).
- An action dropdown (built with Alpine.js `x-data`) containing options: 'Update Status', 'Send WA Reminder', and 'Print Label'.
Use a clean, administrative aesthetic with `#f39c12` for primary actions and subtle gray backgrounds for the board columns."

---

## 5. Thermal Receipt Print Template
**Goal:** A layout compatible with 58mm/80mm thermal printers.

**Prompt:**
"Create a minimal, print-optimized thermal receipt template for a POS system. The layout must be strictly constrained to a maximum width of `384px` (simulating an 80mm thermal paper roll) and centered on the screen for preview purposes. 

Use basic HTML, minimal Tailwind utility classes (focus on black text, white background, no shadows, no rounded corners), and a monospace font.

Include:
- Centered header: 'SHEZA LAUNDRY', store address, and contact number.
- Receipt details: Date, Order ID, Customer Name, and Cashier Name.
- A dotted divider line (`border-dashed`).
- A line-item table (Item Name, Qty, Price, Subtotal).
- Total calculation section.
- Footer text: 'Thank you for trusting us!' and a placeholder for a QR Code."
"""

# Write to file
with open('design.md', 'w') as f:
    f.write(content)