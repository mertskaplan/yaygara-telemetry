# Yaygara Telemetry

![Static Badge](https://img.shields.io/badge/Open_Source-%E2%99%A5-turquoise) [![GitHub License](https://img.shields.io/github/license/mertskaplan/yaygara-telemetry)](https://github.com/mertskaplan/yaygara-telemetry/blob/main/LICENSE) [![GitHub last commit](https://badgen.net/github/last-commit/mertskaplan/yaygara-telemetry)](https://GitHub.com/mertskaplan/yaygara-telemetry/commit/) 

Yaygara Telemetry is a lightweight, database-free, privacy-first analytics dashboard designed specifically for the [Yaygara](https://github.com/mertskaplan/yaygara) party game. It allows developers to seamlessly collect, process, and analyze game balance, team performance, and deck satisfaction using flat-file NDJSON logging.

**Live Game:** [yaygara.mertskaplan.com](https://yaygara.mertskaplan.com)
**Live Telemetry:** [lab.mertskaplan.com/yaygara-telemetry](https://lab.mertskaplan.com/yaygara-telemetry)

## ✨ Key Features

- **Zero Database Setup:** Uses entirely flat-file NDJSON storage (`telemetry.json`). No SQL, No Redis, no complex setups. Just simple PHP and JSON.
- **Premium Visualization:** Advanced charts embedded (Heatmaps, Bubble charts, Line charts, Stacked bars) natively via Chart.js.
- **Game Balance Insights:** Accurately measures "First-Mover Advantage", "Duration Delta", "Most Imbalanced Decks", and "Slowest Word Pace".
- **Difficulty Validations:** Advanced tracking of pass rates, undo logs, and performance cross-referenced against deck difficulties.
- **Multi-language Support:** View the dashboard metrics in both English and Turkish with flawless translations.
- **Privacy First:** No personally identifiable information (PII) is tracked. Only game mechanics, durations, and generic session metrics.
- **Secure Endpoints:** Restricts API POST requests to specific, configurable origins (via CORS constraints).

## 🛠 Tech Stack

- **Backend:** Vanilla PHP 8+
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Visualization:** Chart.js
- **Datastore:** NDJSON (Newline Delimited JSON)

## 🚀 Getting Started

### 💻 Local Development

1. **Clone the repository:**
   ```bash
   git clone https://github.com/mertskaplan/yaygara-telemetry.git
   cd yaygara-telemetry
   ```

2. **Start the PHP development server:**
   ```bash
   php -S localhost:8080
   ```

3. **View the dashboard:**
   Open your browser and navigate to `http://localhost:8080/`.
   _Tip: To view the dashboard with mock data, append `?test` to the URL (e.g., `http://localhost:8080/?test`)._

## ⚙️ CORS Configuration (Important!)

Before deploying or running tests with the main app, you must configure the allowed cross-origin domains. Browsers will block telemetry POST requests if the telemetry server is on a different domain without these headers properly configured.

Open `api/index.php` and edit the `$allowedOrigins` array at the very top of the file:

```php
$allowedOrigins = [
    'https://yaygara.mertskaplan.com',  // Your production Game URL
    'http://localhost:8080'             // Your local dev environment (PHP built-in server)
];
```

## 🌐 Deployment (Production)

Deploying Yaygara Telemetry is incredibly straightforward because it runs on standard PHP hosting without requiring database drivers.

1. Upload the project files (`index.php`, `api/index.php`) to your server via FTP.
2. Ensure the `/api` directory and the root directory are **writable** by the web server (e.g., `chmod 755` or `777`), as the application needs write permissions to generate and append to `telemetry.json`.
3. Configure the `api/index.php` to restrict `$allowedOrigins` to your game's explicitly hosted production URL(s) to secure your endpoint from third-party spam.
4. Access your dashboard securely.

## 📊 Metrics Tracked

- `session_id`: Unique cryptographic identifier for the game session.
- `deck_id` & `deck_difficulty`: The word deck used, along with its designated difficulty (easy, medium, hard).
- `timestamp`: UTC timestamp of the completed game to calculate temporal trend heatmaps.
- `total_words_played`, `deck_total_words`, `total_passes`, `total_undos`: Player efficiency vectors.
- `duration_total_min` vs `duration_active_min`: Comparison of total play time vs active interaction time to measure engagement.
- `scores`: Array containing team-wise point data.
- `liked`: Boolean storing user feedback / deck satisfaction.

## 📂 Project Structure

- `index.php`: The main Frontend Dashboard and JS metric processor.
- `api/index.php`: The secure API endpoint for receiving POST telemetry from the game client.
- `telemetry.json`: The production data file dynamically generated upon first POST request.
- `telemetry-test.json`: Development test-suite file containing mock records to test UI charts.

## 📄 License

Yaygara Telemetry is free software, and its source code is licensed under the **[GNU General Public License v3.0 (GPLv3)](https://github.com/mertskaplan/yaygara-telemetry/blob/main/LICENSE)**.

Built with ❤️ by [Mert S. Kaplan](https://mertskaplan.com).

## ☕ Support

Free software projects like Yaygara have infrastructure and sustainability costs. To ensure similar projects can be developed ad-free and available to everyone, you can provide support via **[Kreosus](https://kreosus.com/mertskaplan)**.
