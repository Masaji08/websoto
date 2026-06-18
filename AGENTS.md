# Agents.md — Soto Pak Harto (QR Menu & Payment)

## Quick start

```bash
composer setup                    # install deps, .env, key:generate, migrate, seed, npm install, build
php artisan reverb:start          # WebSocket server (separate terminal)
npm run dev                       # Vite dev server (separate terminal)
php artisan serve                 # Laravel dev server (separate terminal)
```

## Seeded credentials

| Email | Password | Role |
|---|---|---|
| admin@soto.com | password | admin |
| kasir@soto.com | password | cashier |

## Default warung name

`Soto Seger Boyolali Pak Antok` — set in DB settings table (key `nama_warung`)

## Architecture

- **Laravel 13** + Blade + Alpine.js + Tailwind CSS 4
- **SQLite** by default (switch to MySQL via `.env`)
- **Session/Cache/Queue**: all database-backed
- **Realtime**: Laravel Reverb with Pusher protocol
- **Payments**: Midtrans Snap (QRIS, Transfer Bank, Tunai)
- **QR codes**: SVG format (no Imagick/GD needed)

## Key paths

| Route | Purpose | Auth |
|---|---|---|
| `/menu/{table:slug}` | Customer menu | Public |
| `/menu/{table:slug}/checkout` | Checkout | Public |
| `/menu/{table:slug}/order/{order_number}` | Order status | Public |
| `/kasir/orders` | Kasir dashboard | auth + role:cashier,admin |
| `/admin/menu-items` | CRUD menu items | auth + role:cashier,admin |
| `/admin/tables` | CRUD tables + QR | auth + role:admin |
| `/admin/reports` | Sales reports | auth + role:admin |

## Testing

```bash
composer test                     # config:clear + php artisan test
```

- PHPUnit 12, SQLite in-memory for tests
- Feature tests use `RefreshDatabase`
- 25 tests pass

## Models

- `Table` — `slug` is route key, `is_active` guards access
- `Category` — `sort_order` controls display order
- `MenuItem` — `price` in integer (Rupiah), `image_path` relative to storage
- `Order` — status enum: pending → confirmed → processing → ready → completed
- `OrderItem` — links orders to menu items

## Color scheme (tradisional, nyaman, premium)

- Orange `#FF8C42` — primary, buttons, accent text
- Dark brown `#6D4C41` — secondary accent, active nav, hover states, gradient ends
- Warm brown `#4E342E` — sidebar background
- Cream `#FFF3E0` — highlights, badges, selected states, step tracker
- White `#FFFFFF` / `bg` — page backgrounds
- Dark slate `#1F2937` — text color
- Muted gray `#6B7280` — secondary text
- Border `#E5E0D8` — card borders, inputs

## Events (broadcast)

| Event | Channel | Purpose |
|---|---|---|
| `NewOrderReceived` | `kasir-orders` | New order alert to kasir |
| `OrderStatusUpdated` | `kasir-orders`, `order-{number}` | Status change to both dashboards |

## Conventions

- PHP 8 attributes (`#[Fillable]`, `#[Hidden]`) on models
- `CheckRole` middleware (`role:` alias) guards admin/kasir routes
- Order numbers: random letter + dash + 3-digit number (e.g. `A-042`)
- Midtrans config in `config/midtrans.php`
- QR codes stored as SVG in `storage/app/public/qrcodes/`
- Rate limiting: 10 order requests/min per IP

## Payment flow

- QRIS: order dibuat tanpa broadcast ke kasir. Notif `NewOrderReceived` dikirim **hanya** setelah callback Midtrans sukses (`payment_status = paid`). Order QRIS unpaid tidak muncul di board kasir.
- Cash: broadcast `NewOrderReceived` langsung saat order dibuat.
- Midtrans callback menyimpan `transaction_id`, `payment_type`, `transaction_time` ke kolom terpisah di `orders`.

## Services

- `MidtransService` — create Snap transaction, handle callback
- `QrCodeService` — generate SVG QR codes via `simplesoftwareio/simple-qrcode`
- `Storage::disk('public')` for file uploads — use symlink (`php artisan storage:link`)

## Settings (key-value, managed via `/admin/settings`)

| Key | Description |
|---|---|
| `nama_warung` | Warung name |
| `deskripsi` | Short description |
| `alamat` | Full address (with line breaks) |
| `penerimaan_pesanan` | Accepted order types (syukuran, arisan, etc.) |
| `kontak_arl` | Phone number for Arl contact |
| `kontak_ssb` | Phone number for SSB contact |
| `jam_operasional` | Operating hours |
| `nomor_wa` | WhatsApp number (digits only, with country code, e.g. `62812...`) |
| `logo` | Logo image path |

## Pages with WhatsApp floating button

- `/menu/{table:slug}` — bottom-right green button, opens `wa.me/{nomor_wa}?text=...`
- `/menu/{table:slug}/checkout` — same button
- `/menu/{table:slug}/order/{order_number}` — same button, includes order number in message
