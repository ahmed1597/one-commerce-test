# One Commerce Test – Shopify Integration (Laravel 12 + Nuxt 3)

This solution demonstrates a senior-level Shopify integration with a modern full-stack setup. It covers data synchronization, webhook handling with verification, REST APIs for frontend consumption, and a styled Nuxt 3 frontend. The goal is to showcase clean architecture, best practices (SOLID, clean code, testing), and a pragmatic approach to real-world Shopify integrations.

## 🧩 Architecture & Design Decisions

### 1. Backend (Laravel 12)

*   **Framework:** Laravel 12 (PHP 8.2) for robustness, rapid development, and ecosystem maturity.
*   **APIs:** Exposes clean REST endpoints (`/api/products`, `/api/sync/products`) consumed by the frontend.
*   **Persistence Layer:** Products, Variants, and Images are modeled in normalized tables with proper relationships.
*   **Sync Mechanism:** Uses Shopify Admin REST API (`/products.json`) for fetching product data. Implements delta sync using `updated_at_min` to avoid redundant requests. Supports idempotent upserts (no duplication when syncing multiple times).
*   **Webhooks:** Registered via GraphQL Admin API (Shopify’s current requirement). Subscribed to `PRODUCTS_CREATE`, `PRODUCTS_UPDATE`, and `PRODUCTS_DELETE`. Implemented with HMAC verification using the shared secret (`X-Shopify-Hmac-Sha256`) to ensure authenticity. Processed asynchronously through Laravel Jobs (configurable to sync mode for development).

### 2. Frontend (Nuxt 3 / Vue 3)

*   **Framework:** Nuxt 3 for SSR/SPA hybrid capabilities and DX.
*   **Data Fetching:** Uses `$fetch` against the backend REST APIs.
*   **UI/UX:** Styled product listing grid with loading/error/empty states. Displays product title, description, image (or placeholder), variant count, price, and status. Minimal but polished to demonstrate frontend engineering practices.
*   **Refresh Strategy:** The frontend queries the backend periodically (every few seconds), ensuring updated data without manual refresh.

### 3. API Strategy (REST + GraphQL)

*   **REST:** Chosen for product sync (`/products.json`) and backend-to-frontend APIs. Advantage: simpler, stable, and fast for bulk product retrieval.
*   **GraphQL:** Required for webhook subscription (`webhookSubscriptionCreate`). Demonstrates ability to mix paradigms and use Shopify’s latest best practices.

### 4. Middleware & Security

*   Custom CORS middleware ensures only API routes expose cross-origin headers.
*   Webhook HMAC verification implemented manually to show understanding of request signing and replay protection.

### 5. Testing

*   **Backend:** Feature tests with Pest. Validates product listing, filtering, archived handling. Verifies webhook processing is idempotent. Ensures sync respects `updated_at_min`.
*   **Frontend:** Playwright E2E tests. Validates rendering of product list, error handling, and empty state.

### 6. Best Practices

*   **SOLID principles:** Clear separation of concerns (ShopifyClient, SyncProductsAction, Controllers).
*   **Idempotency:** Ensured for sync & webhook ingestion.
*   **Resilience:** Handles pagination, rate-limiting (429 retry pattern sketched), and replay-safe webhooks.
*   **Scalability:** Local DB ensures frontend can scale without hitting Shopify’s API rate limits. Architecture supports horizontal scaling with queues.
*   **Modern Tooling:** Laravel 12, Nuxt 3, Pest, Playwright — all latest stable stacks.
*   **Deployment readiness:** Designed with HTTPS/ngrok for local, Horizon/queues for production, and simple environment configuration.

## 🔄 Data Flow

### Initial Sync (POST /api/sync/products)

*   Backend calls Shopify REST Admin API.
*   Products, variants, and images are upserted into local DB.

### Webhook Delivery

*   Shopify triggers product create/update/delete.
*   Laravel verifies HMAC, dispatches processing job.
*   Local DB updated accordingly.

### Frontend Consumption

*   Nuxt frontend fetches `/api/products?with=variants,images`.
*   Products displayed with status, pricing, and images.
*   Updates visible within seconds of Shopify changes.

## 🚀 Running the Solution

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Configure `.env` with DB and Shopify credentials:

```
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/backend/database/database.sqlite
SHOPIFY_SHOP=one-commerce-test.myshopify.com
SHOPIFY_ACCESS_TOKEN=shpat_xxx
SHOPIFY_API_VERSION=2025-07
SHOPIFY_WEBHOOK_SECRET=xxx
```

Run migrations & seed:

```bash
php artisan migrate --seed
```

Start backend:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Expose HTTPS for Shopify webhooks:

```bash
ngrok http 8000
php artisan shopify:webhooks:register-graphql
```

### Frontend

```bash
cd frontend
npm install
cp .env.example .env
```

`.env`:

```
NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000/api
```

Start frontend:

```bash
npm run dev
```

Visit: `http://localhost:3000`

### Tests

**Backend (Pest):**

```bash
php artisan test
```

**Frontend (Playwright):**

```bash
npx playwright install --with-deps
npm run test:e2e
```