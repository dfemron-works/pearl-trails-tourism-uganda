# 🦁 Pearl Trails Tours & Travel Uganda Ltd — Tourism Management, GIS Mapping & Meteorological System

A full-stack web-based **Tourism Operations, GIS Route Mapping, Financial Accounting, and Meteorological Intelligence Platform** built for **Pearl Trails Tours & Travel Uganda Ltd** (Headquartered at Plot 14, Kampala–Jinja Highway, Nagojje, Mukono District, Uganda).

---

## 🌟 Core System Capabilities

### 1. Tour Package Management & Dynamic Holiday Pricing
* **Catalog & Promotional Engine:** Browse and filter Ugandan safari packages (Bwindi Gorilla Trekking, Murchison Falls, Queen Elizabeth NP, Kidepo Valley, Jinja Source of the Nile).
* **Automated Discount Rules:** Automatically detects weekends (Saturday–Sunday) and official **Ugandan Public Holidays** (`uganda_holidays`) to apply promotional discounts at checkout.
* **Multi-Currency Conversion:** Real-time conversion across **7 currencies** (`USD`, `UGX`, `KES`, `TZS`, `RWF`, `EUR`, `GBP`).

### 2. Interactive GIS Live Safari Map (`safari_map.php`)
* **Pin Categorization:** Interactive mapping across **Hotels & Lodges**, **Tour Sites**, **Camping Sites**, and **Landing Sites / Airstrips**.
* **Distance & Fleet Cost Estimator:** Calculates exact route distances (km), estimated travel hours, and per-kilometer 4x4 vehicle hire costs (`Standard Safari Van`, `4x4 Pop-Up Roof Land Cruiser`, and `VIP Extended Land Cruiser`).
* **Live Pin Meteorology:** Clicking any pinned GPS coordinate displays live temperature and wind data.

### 3. Live Meteorological & Geographical Intelligence (`weather_geography.php` & `weather_report.php`)
* **Logged-In Live Weather Ticker:** Continuous scrolling weather stream in the navbar covering Ugandan safari hubs and global tourist origin cities.
* **Multi-Year Climate Window:** Compares historical (**Past / Happened**) and predicted (**Expected / Forecast**) good vs. harsh weather periods across Uganda.
* **Voucher Weather Auditing:** Logs weather at booking time alongside expected weather on the trip date directly onto printable QR E-Tickets (`voucher.php`).

### 4. Intelligent AI Safari Concierge (`chat_widget.php` & `chat_api.php`)
* Hybrid conversational assistant powered by live MySQL queries (packages, holiday deals, fleet availability, personal booking status), live weather/geography lookups, and external knowledge-graph retrieval.

### 5. Enterprise Administration, Inventory & Accounting
* **Inventory Control (`inventory.php`):** Tracks active staff payroll, 4x4 safari fleet valuation, and electronic/non-electronic office assets with branded print letterheads.
* **Financial Accounting (`finances.php`):** Generates live **Statements of Comprehensive Income (Profit & Loss)** and **Statements of Financial Position (Balance Sheet)** backed by a general ledger.
* **Analytics & Audit Reports (`reports.php`):** Tracks hourly/daily/weekly/monthly/annual system traffic, user moderation status, Chart.js visualizations, and CSV/Excel exports.

---

## 🔌 REST API Documentation (`api.php`)

External partners and mobile apps can query system data using the `X-API-KEY` header or `?api_key=` query parameter.

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api.php?resource=status` | Public health check and endpoint directory |
| `GET` | `/api.php?resource=packages&api_key=YOUR_KEY` | Retrieve all active safari packages and rates |
| `GET` | `/api.php?resource=waypoints&api_key=YOUR_KEY` | Retrieve mapped hotels, tour, camping & landing sites |
| `GET` | `/api.php?resource=weather&place=Bwindi&api_key=YOUR_KEY` | Fetch live weather and 3-day forecast for any location |
| `GET` | `/api.php?resource=holidays&api_key=YOUR_KEY` | Retrieve qualifying Ugandan public holidays |
| `GET` | `/api.php?resource=verify_ticket&booking_id=1&api_key=YOUR_KEY` | Verify tourist E-Ticket voucher status |
| `POST` | `/api.php?resource=create_booking&api_key=YOUR_KEY` | Create a new safari reservation via JSON/Form payload |

---

## 🚀 Installation & Deployment Guide

### Local Development (XAMPP / WAMP / Laragon)
1. Clone this repository into your web server root (`htdocs/pearl_trails_tourism`):
   ```bash
   git clone https://github.com
   ```
2. Open **phpMyAdmin**, create a database named `pearl_trails_db`, and import `database/pearl_trails_db.sql`.
3. Duplicate `db.example.php`, rename it to **`db.php`**, and update your MySQL credentials (`$host`, `$dbname`, `$user`, `$pass`).
4. Ensure the `uploads/` directory has write permissions (`755` on Linux/cPanel).
5. Launch `http://localhost/pearl_trails_tourism/index.php` in your browser.

---

## 🛠️ Technology Stack
* **Backend:** PHP 8.x (PDO Prepared Statements, RESTful JSON APIs, Output-Buffered AJAX)
* **Database:** MySQL / MariaDB (`utf8mb4`)
* **Frontend:** HTML5, CSS3 (Custom Dark Emerald & Neon Theme + `@media print` Letterheads), Vanilla JavaScript, Chart.js, Leaflet.js GIS Mapping
* **External Services:** Open-Meteo Live Weather & Geocoding API, Wikipedia REST Knowledge API

---

## 📍 Company Contact
**Pearl Trails Tours & Travel Uganda Ltd**  
Plot 14, Kampala–Jinja Highway, Nagojje, Mukono District, Uganda  
Email: `info@pearltrails.ug` | Tel / WhatsApp: `+256 785884677`
