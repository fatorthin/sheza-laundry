# PRD: Sheza Laundry Management System (Custom TALL Stack)

## 1. Project Overview

**Project Name:** Sheza Laundry
**Tech Stack:** TALL Stack (Tailwind CSS, Alpine.js, Laravel, Livewire)
**Database:** MySQL
**Primary Theme Color:** #f39c12
**Language:** Bahasa Indonesia (UI/UX)
**Currency:** Indonesian Rupiah (IDR)

## 2. Core Concept & Objectives

Create a high-performance, mobile-first Progressive Web App (PWA) for laundry management. The app must be fully responsive and dynamic across Mobile, Tablet, and Desktop.
**Constraint:** Do NOT use Filament. Build custom UI components using Livewire and Tailwind to ensure 100% design flexibility.

## 3. Localization & Formatting

- **Language:** All user-facing text must be in Bahasa Indonesia.
- **Currency:** Use `Rp` prefix (e.g., Rp 10.000).
- **Date/Time:** WIB (Asia/Jakarta) timezone.

## 4. User Personas & Roles

- **Admin/Cashier:** Full access to manage services, members, and the order lifecycle.
- **Member:** Public users who can check order status via the Landing Page.

## 5. Functional Requirements

### 5.1. PWA & Landing Page

- **Mobile-First:** Bottom navigation bar for mobile users.
- **Responsive Layout:** Automatically switch to a Sidebar navigation for Tablet and Desktop views.
- **PWA Features:** Manifest file and Service Worker for "Add to Home Screen" support.
- **Order Tracking:** A public search field for members to input an "Order ID" and see real-time progress.

### 5.2. User & Member Management

- Simple Authentication for Admin/Cashier.
- Member database: Name, Phone Number (WhatsApp), and Order History.

### 5.3. Service Management (POS)

- Custom POS interface to add items to a "basket".
- Support for "Kiloan" (Weight-based) and "Satuan" (Unit-based) services.
- **Critical Logic:** For "Kiloan", the weight is NOT entered at the start.

### 5.4. Order Lifecycle (Post-Process Weighing)

1. **Entry:** Record customer and items (e.g., 10 shirts). Weight = 0, Total = Rp 0. Status: "Baru".
2. **Process:** Update status to "Dicuci" or "Disetrika".
3. **Weight Finalization:** Once processing is done, staff inputs the final weight (kg).
4. **Automated Billing:** System calculates `Weight x Rate` and updates status to "Siap Diambil".
5. **Notification:** Trigger an automated WhatsApp message via self-hosted API Gateway.

### 5.5. Invoicing & Printing

- **WhatsApp Integration:** Connect to a self-hosted API gateway to send digital invoice links.
- **Thermal Printing:** Generate a clean, monochromatic layout for 58mm/80mm thermal printers.

## 6. Technical Architecture

- **Database (MySQL):**
  - `users` (admin/staff)
  - `members` (customers)
  - `services` (categories and prices)
  - `orders` (tracking weight, items, and status)
- **Livewire Components:** Use for real-time order filtering, POS basket management, and status updates.
- **Tailwind CSS:** Custom configuration for color `#f39c12` and dynamic breakpoints (`sm`, `md`, `lg`).

## 7. UI/UX Design Guidelines

- **Color Palette:** Primary `#f39c12`, Secondary (White/Light Gray).
- **Style:** Modern, clean, professional, `rounded-xl` corners.
- **Navigation:** Bottom bar (Mobile) vs. Sidebar (Desktop).
