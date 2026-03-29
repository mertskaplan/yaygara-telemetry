<!DOCTYPE html>
<html lang="en">

<!--
    Name: Yaygara Telemetry
    Author: Mert S. Kaplan, mail@mertskaplan.com
    Licence: GNU GPLv3
    Source: https://github.com/mertskaplan/yaygara-telemetry
-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Yaygara Telemetry Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-color: #020617;
            --card-bg: rgba(30, 41, 59, 0.6);
            --card-hover: rgba(40, 53, 75, 0.8);
            --accent-color: #38bdf8;
            --secondary-color: #818cf8;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --danger: #fb7185;
            --warning: #f59e0b;
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            padding: 3rem 2rem;
            min-height: 100vh;
            line-height: 1.5;
            position: relative;
            overflow-x: hidden;
            background-image:
                radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(129, 140, 248, 0.1) 0px, transparent 50%);
        }

        /* Animated Ambient Background Blobs */
        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .ambient-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s infinite alternate ease-in-out;
        }

        .blob-1 {
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: var(--accent-color);
            animation-delay: 0s;
        }

        .blob-2 {
            bottom: -20%;
            right: -10%;
            width: 60vw;
            height: 60vw;
            background: var(--secondary-color);
            animation-delay: -5s;
        }

        .blob-3 {
            top: 40%;
            left: 60%;
            width: 40vw;
            height: 40vw;
            background: var(--success);
            opacity: 0.1;
            animation-delay: -10s;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(50px, 50px) scale(1.1);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: #fff;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Live Status Indicator */
        .status-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.03);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            border: 1px solid var(--border);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: var(--success);
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .kpi-card.easy {
            border-left: 4px solid var(--success);
        }

        .kpi-card.medium {
            border-left: 4px solid var(--warning);
        }

        .kpi-card.hard {
            border-left: 4px solid var(--danger);
        }

        /* Test Mode Indicator */
        .test-mode-badge {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-right: 1rem;
            animation: fadeIn 0.5s ease-out;
        }

        .test-mode-badge::before {
            content: '⚠️';
            font-size: 1rem;
        }

        /* Language Switcher */
        .lang-switcher {
            display: flex;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 2rem;
            padding: 2px;
            margin-right: 1.5rem;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .lang-switcher:hover {
            border-color: var(--border-hover);
        }

        .lang-btn {
            padding: 0.4rem 1rem;
            border-radius: 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }

        .lang-btn.active {
            color: var(--text-main);
            background: rgba(56, 189, 248, 0.15);
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.1);
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .kpi-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        /* Subtle inner shine */
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            pointer-events: none;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            background: var(--card-hover);
            border-color: var(--border-hover);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.3), 0 0 15px rgba(56, 189, 248, 0.1);
        }

        .kpi-card.warning {
            border-left: 4px solid var(--warning);
        }

        .kpi-card.danger {
            border-left: 4px solid var(--danger);
        }

        .kpi-card.info {
            border-left: 4px solid var(--secondary-color);
        }

        .kpi-card.success {
            border-left: 4px solid var(--success);
        }

        .kpi-label {
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: transparent;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            /* Required for Safari */
        }

        .kpi-subtext {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .card {
            background-color: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.05);
            border-radius: 1.5rem;
            pointer-events: none;
        }

        .card:hover {
            border-color: rgba(56, 189, 248, 0.3);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), inset 0 1px 1px rgba(255, 255, 255, 0.1);
        }

        .card h2 {
            font-size: 1.3rem;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .card h2::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background: var(--accent-color);
            border-radius: 50%;
            margin-right: 10px;
        }

        .chart-wrapper {
            position: relative;
            width: 100%;
            height: 350px;
        }

        .chart-wrapper-fixed {
            position: relative;
            width: 100%;
            height: 350px;
        }

        canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* Empty State Styling */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5rem 2rem;
            text-align: center;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            border: 1px dashed rgba(255, 255, 255, 0.2);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
            animation: bounce 2s infinite ease-in-out;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .status-container {
                flex-wrap: wrap;
                width: 100%;
            }

            .lang-switcher {
                margin-right: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Ambient Background -->
    <div class="ambient-bg">
        <div class="ambient-blob blob-1"></div>
        <div class="ambient-blob blob-2"></div>
        <div class="ambient-blob blob-3"></div>
    </div>

    <div class="container">
        <div class="header">
            <div>
                <h1 data-i18n="app-title">Yaygara Telemetry Dashboard</h1>
                <p style="color: var(--text-muted); margin-top: 0.5rem;" data-i18n="app-subtitle">Scalable Deck & Team
                    Insights</p>
            </div>
            <div class="status-container">
                <div class="lang-switcher" id="lang-switcher">
                    <div class="lang-btn" data-lang="tr">TR</div>
                    <div class="lang-btn" data-lang="en">EN</div>
                </div>
                <?php if (isset($_GET['test'])): ?>
                    <div class="test-mode-badge" data-i18n="test-mode-active">TEST MODE ACTIVE</div>
                <?php endif; ?>
                <div class="pulse-dot"></div>
                <span id="last-update" data-i18n="waiting-data">Waiting for data...</span>
            </div>
        </div>

        <?php
        $isTestMode = isset($_GET['test']);
        $storageFile = $isTestMode ? 'telemetry-test.json' : 'telemetry.json';
        $records = [];
        if (file_exists($storageFile)) {
            $lines = file($storageFile);
            foreach ($lines as $line) {
                $record = json_decode(trim($line), true);
                if ($record)
                    $records[] = $record;
            }
        }
        ?>

        <div id="dashboard-content" style="<?php echo empty($records) ? 'display:none;' : ''; ?>">
            <!-- OVERALL SUMMARY STATS -->
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-label" data-i18n="kpi-total-games-label">🎮 Toplam Oyun</div>
                    <div class="kpi-value" id="kpi-total-games">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-total-games-sub">Kayıtlı tüm oyunlar</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label" data-i18n="kpi-total-words-label">🔤 Oynanan Kelime</div>
                    <div class="kpi-value" id="kpi-total-words">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-total-words-sub">Tüm desteler toplamı</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label" data-i18n="kpi-total-duration-label">⏱️ Aktif Oyun Süresi</div>
                    <div class="kpi-value" id="kpi-total-duration">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-total-duration-sub">Toplam dakika</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label" data-i18n="kpi-total-players-label">👥 Tahmini Oyuncu</div>
                    <div class="kpi-value" id="kpi-total-players">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-total-players-sub">Takım sayılarına göre</div>
                </div>
            </div>

            <div class="kpi-row">
                <div class="kpi-card info">
                    <div class="kpi-label" data-i18n="kpi-dominance-label">🏆 T1 Dominance (3-4 Teams)</div>
                    <div class="kpi-value" id="kpi-first-mover-multi">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-dominance-sub">Win rate of Team 1</div>
                </div>
                <!-- 45s Global Turn Metric -->
                <div class="kpi-card success">
                    <div class="kpi-label" data-i18n="kpi-45s-label">🎯 45-Second Turn Avgs</div>
                    <div class="kpi-value" id="kpi-words-45s">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-45s-sub">Guessed words per turn (Global)</div>
                </div>
                <div class="kpi-card danger">
                    <div class="kpi-label" data-i18n="kpi-imbalance-label">⚡ Most Imbalanced Deck</div>
                    <div class="kpi-value" id="kpi-imbalance-deck">-</div>
                    <div class="kpi-subtext" id="kpi-imbalance-val" data-i18n="kpi-imbalance-sub">Avg point gap between
                        1st & 2nd</div>
                </div>
                <div class="kpi-card warning">
                    <div class="kpi-label" data-i18n="kpi-slowest-label">⏳ Slowest Word Pace</div>
                    <div class="kpi-value" id="kpi-slowest-deck">-</div>
                    <div class="kpi-subtext" id="kpi-slowest-val" data-i18n="kpi-slowest-sub">Seconds per word</div>
                </div>
            </div>

            <!-- DIFFICULTY PERFORMANCE METRICS -->
            <div class="kpi-row">
                <div class="kpi-card easy">
                    <div class="kpi-label" data-i18n="kpi-easy-45s-label">🟢 Kolay Deste Ort.</div>
                    <div class="kpi-value" id="kpi-easy-45s">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-45s-sub">Tur başına tahmin edilen kelime</div>
                </div>
                <div class="kpi-card medium">
                    <div class="kpi-label" data-i18n="kpi-medium-45s-label">🟡 Orta Deste Ort.</div>
                    <div class="kpi-value" id="kpi-medium-45s">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-45s-sub">Tur başına tahmin edilen kelime</div>
                </div>
                <div class="kpi-card hard">
                    <div class="kpi-label" data-i18n="kpi-hard-45s-label">🔴 Zor Deste Ort.</div>
                    <div class="kpi-value" id="kpi-hard-45s">-</div>
                    <div class="kpi-subtext" data-i18n="kpi-45s-sub">Tur başına tahmin edilen kelime</div>
                </div>
            </div>

            <div class="stats-grid">

                <!-- [NEW] Full Width Timeline Duration Comparison -->
                <div class="card full-width">
                    <h2 data-i18n="chart-timeline-title">Duration Timelines (Estimated vs Total vs Active)</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-timeline-sub">
                        Comparing the three duration metrics across all decks simultaneously. X-Axis: Decks. Y-Axis:
                        Minutes.
                    </p>
                    <div class="chart-wrapper" id="wrap-durationTimelineChart">
                        <!-- We use fixed height for Line chart instead of scaling per deck, because they scale horizontally -->
                        <canvas id="durationTimelineChart" style="min-height: 126px; display: block;"></canvas>
                    </div>
                </div>

                <!-- Row 2: Imbalance Metric + Win Rate -->
                <div class="card">
                    <h2 data-i18n="chart-imbalance-title">Average Points Gap (Imbalance Metric)</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-imbalance-sub">
                        Average difference in points between the 1st place and 2nd place teams per deck.
                    </p>
                    <div class="chart-wrapper" id="wrap-scoreGapChart">
                        <canvas id="scoreGapChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <h2 data-i18n="chart-winrate-title">First-Mover Advantage (Win Rate)</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-winrate-sub">
                        Win percentage by starting position grouped by total teams in game.
                    </p>
                    <div class="chart-wrapper-fixed">
                        <canvas id="winRateChart"></canvas>
                    </div>
                </div>

                <!-- Grouped horizontal charts for dense data -->
                <div class="card">
                    <h2 data-i18n="chart-delta-title">Duration Delta (Actual Total - Estimated)</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-delta-sub">
                        Difference between the actual total session time and the initial estimation.
                    </p>
                    <div class="chart-wrapper" id="wrap-durationDeltaChart">
                        <canvas id="durationDeltaChart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <h2 data-i18n="chart-focus-title">Engagement Focus (Active / Total %)</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-focus-sub">
                        Percentage of total time spent in active word-guessing turns.
                    </p>
                    <div class="chart-wrapper" id="wrap-focusRatioChart">
                        <canvas id="focusRatioChart"></canvas>
                    </div>
                </div>


                <!-- Row 4: Deck Popularity + Language Distribution -->
                <div class="card">
                    <h2 data-i18n="chart-pop-title">Deck Popularity (Total Plays)</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-pop-sub">
                        Total number of times each deck has been played.
                    </p>
                    <div class="chart-wrapper-fixed">
                        <canvas id="deckPopularityChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <h2 data-i18n="chart-lang-title">Interface Language Distribution</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-lang-sub">
                        Proportion of application languages used by players.
                    </p>
                    <div class="chart-wrapper-fixed">
                        <canvas id="languageChart"></canvas>
                    </div>
                </div>

                <!-- Row 5: Deck Satisfaction + Difficulty Satisfaction -->
                <div class="card">
                    <h2 data-i18n="chart-sat-title">Deck Satisfaction</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-sat-sub">
                        User feedback (likes/dislikes) received for each deck.
                    </p>
                    <div class="chart-wrapper-fixed">
                        <canvas id="deckSatisfactionChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <h2 data-i18n="chart-diffsat-title">Difficulty Satisfaction</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-diffsat-sub">
                        Likes vs Dislikes grouped by deck difficulty.
                    </p>
                    <div class="chart-wrapper-fixed">
                        <canvas id="diffSatChart"></canvas>
                    </div>
                </div>

                <!-- [NEW] Expansion Visualizations -->
                <div class="card full-width">
                    <h2 data-i18n="chart-time-title">Temporal Play Trend</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-time-sub">
                        Distribution of games played by local hour of the day.
                    </p>
                    <div class="chart-wrapper-fixed">
                        <canvas id="timeTrendChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <h2 data-i18n="chart-pass-title">Pass Rate</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-pass-sub">
                        Percentage of words passed out of total attempts per deck.
                    </p>
                    <div class="chart-wrapper" id="wrap-passRateChart">
                        <canvas id="passRateChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <h2 data-i18n="chart-undo-title">Undo Rate (Avg per Game)</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -1rem; margin-bottom: 1rem;"
                        data-i18n="chart-undo-sub">
                        Average number of undo actions performed per session.
                    </p>
                    <div class="chart-wrapper" id="wrap-undoRateChart">
                        <canvas id="undoRateChart"></canvas>
                    </div>
                </div>



            </div>
        </div>

        <!-- Empty State Container -->
        <div id="empty-state" class="empty-state" style="<?php echo empty($records) ? '' : 'display:none;'; ?>">
            <div class="empty-state-icon">📡</div>
            <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;" data-i18n="empty-title">Awaiting Telemetry Data</h2>
            <p style="color: var(--text-muted);" data-i18n="empty-sub">Play some games to generate statistics and
                insights.</p>
        </div>
    </div>

    <script>
        const translations = {
            'tr': {
                'app-title': 'Yaygara Telemetri Paneli',
                'app-subtitle': 'Ölçeklenebilir Deste ve Takım Analizleri',
                'waiting-data': 'Veri bekleniyor...',
                'test-mode-active': 'TEST MODU',
                'kpi-total-games-label': '🎮 Toplam Oyun',
                'kpi-total-games-sub': 'Kayıtlı tüm oyunlar',
                'kpi-total-words-label': '🔤 Oynanan Kelime',
                'kpi-total-words-sub': 'Tüm desteler toplamı',
                'kpi-total-duration-label': '⏱️ Aktif Oyun Süresi',
                'kpi-total-duration-sub': 'Toplam oynanan süre (dk)',
                'kpi-total-players-label': '👥 Tahmini Oyuncu',
                'kpi-total-players-sub': 'Takım sayılarına göre',
                'kpi-imbalance-label': '⚡ En Dengesiz Deste',
                'kpi-imbalance-sub': 'Birinci ve 2. takım arasındaki ortalama puan farkı',
                'kpi-slowest-label': '⏳ En Yavaş Tempodaki Deste',
                'kpi-slowest-sub': 'Kelime başına saniye',
                'kpi-dominance-label': '🏆 T1 Hakimiyeti',
                'kpi-dominance-sub': '1. Takımın kazanma oranı',
                'kpi-45s-label': 'Tur Tahmin Ortalaması',
                'kpi-45s-sub': 'Tur başına tahmin edilen kelime',
                'kpi-easy-45s-label': 'Kolay Deste Tahmin Ortalaması',
                'kpi-medium-45s-label': 'Orta Deste Tahmin Ortalaması',
                'kpi-hard-45s-label': 'Zor Deste Tahmin Ortalaması',
                'chart-timeline-title': 'Süre Çizgileri (Tahmini / Toplam / Aktif)',
                'chart-timeline-sub': 'Üç süre metriklerinin tüm destelerdeki karşılaştırması. X-Ekseni: Desteler. Y-Ekseni: Dakika.',
                'chart-imbalance-title': 'Ortalama Puan Farkı (Dengesizlik Metriği)',
                'chart-imbalance-sub': 'Deste başına 1. ve 2. takım arasındaki ortalama puan farkı. Puan farkının düşük olması o deste için oyunun dengeli ve rekabetçi olduğunu gösterir.',
                'chart-delta-title': 'Süre Farkı (Gerçek Süre - Tahmini Süre)',
                'chart-delta-sub': 'Gerçek oyun süresi ile başlangıçtaki tahmini süre arasındaki sapma farkı. Bu verinin 0\'a yakın olması destenin zorluk derecesinin doğru tahin edildiğini gösterir.',
                'chart-focus-title': 'Etkileşim Odağı (Aktif / Toplam %)',
                'chart-focus-sub': 'Oyun sürenin ne kadarının aktif kelime anlatma sırasında geçtiğini gösteren yüzdelik oran. Bu veri kelime destesinin ne kadar sürelik tartışmalara neden olduğunu gösterebilir.',
                'chart-winrate-title': 'İlk Başlayan Avantajı (Kazanma Oranı)',
                'chart-winrate-sub': 'Toplam takım sayısına ve başlangıç pozisyonuna göre takımların kazanma yüzdeleri. Oyuna başlayan takımın kazanma yüzdesi yüksekse oyun mekaniğinin adil olmadığı söylenebilir.',
                'chart-pop-title': 'Deste Popülerliği (Toplam Oyun)',
                'chart-pop-sub': 'Her bir destenin toplamda kaç kez oynandığı.',
                'chart-sat-title': 'Deste Memnuniyeti',
                'chart-sat-sub': 'Her bir deste için gelen kullanıcı geri bildirimleri (beğeni/beğenmeme).',
                'chart-time-title': 'Zaman İçindeki Oyun Tüketimi (Isı Haritası)',
                'chart-time-sub': 'Haftanın günlerine ve saatlere göre oyun yoğunluğu (Baloncuk boyutu, oynanan maç sayısını ifade eder).',
                'chart-pass-title': 'Pas Geçme Oranı',
                'chart-pass-sub': 'Deste başına gösterilen kelimelerin pas geçilme yüzdesi.',
                'chart-undo-title': 'Hata İndeksi (Ort. Geri Alma)',
                'chart-undo-sub': 'Oyun başına ortalama geri alma (undo) işlemi sayısı.',
                'chart-diffsat-title': 'Zorluk Seviyesi Memnuniyeti',
                'chart-diffsat-sub': 'Destelerin zorluk derecelerine göre alınan beğeni ve beğenmemeler.',
                'chart-lang-title': 'Arayüz Dili Dağılımı',
                'chart-lang-sub': 'Oyuncuların tercih ettiği arayüz dilleri oranı.',
                'empty-title': 'Telemetri Verisi Bekleniyor',
                'empty-sub': 'İstatistik ve analizleri görmek için birkaç oyun oynayın.',
                'minutes': 'Dakika',
                'margin-mins': 'Fark (Dakika)',
                'active-percent': 'Aktif %',
                'win-percent': 'Kazanma Yüzdesi (%)',
                'team': 'Takım',
                'teams-2': '2 Takımlı Oyun',
                'teams-3': '3 Takımlı Oyun',
                'teams-4': '4 Takımlı Oyun',
                'likes': 'Beğeniler',
                'dislikes': 'Beğenmemeler',
                'est-dur': 'Tahmini Süre',
                'total-dur': 'Toplam Süre',
                'active-dur': 'Aktif Süre',
                'avg-gap': 'Ort. Puan Farkı',
                'points-gap': 'puan farkı',
                'sec-word': 'sn/kelime',
                'words': 'kelime',
                'live-status': 'Canlı Durum: ',
                'waiting-live': 'Veri bekleniyor...'
            },
            'en': {
                'app-title': 'Yaygara Telemetry Dashboard',
                'app-subtitle': 'Scalable Deck & Team Insights',
                'waiting-data': 'Waiting for data...',
                'test-mode-active': 'TEST MODE',
                'kpi-total-games-label': '🎮 Total Games',
                'kpi-total-games-sub': 'All time recorded games',
                'kpi-total-words-label': '🔤 Guessed Words',
                'kpi-total-words-sub': 'Sum across all decks',
                'kpi-total-duration-label': '⏱️ Active Play Time',
                'kpi-total-duration-sub': 'Total minutes played',
                'kpi-total-players-label': '👥 Estimated Players',
                'kpi-total-players-sub': 'Calculated by team sizes',
                'kpi-imbalance-label': '⚡ Most Imbalanced Deck',
                'kpi-imbalance-sub': 'Avg point gap between 1st & 2nd',
                'kpi-slowest-label': '⏳ Slowest Word Pace',
                'kpi-slowest-sub': 'Seconds per word',
                'kpi-dominance-label': '🏆 T1 Dominance',
                'kpi-dominance-sub': 'Win rate of Team 1',
                'kpi-45s-label': '🎯 45-Second Turn Avgs',
                'kpi-45s-sub': 'Guessed words per turn',
                'kpi-easy-45s-label': 'Easy Deck Avgs',
                'kpi-medium-45s-label': 'Medium Deck Avgs',
                'kpi-hard-45s-label': 'Hard Deck Avgs',
                'chart-timeline-title': 'Duration Timelines (Estimated vs Total vs Active)',
                'chart-timeline-sub': 'Comparing the three duration metrics across all decks simultaneously. X-Axis: Decks. Y-Axis: Minutes.',
                'chart-imbalance-title': 'Average Points Gap (Imbalance Metric)',
                'chart-imbalance-sub': 'Average difference in points between the 1st place and 2nd place teams per deck. A lower points gap indicates that the game is balanced and competitive for that deck.',
                'chart-delta-title': 'Duration Delta (Actual Total - Estimated)',
                'chart-delta-sub': 'Difference between the actual total session time and the initial estimation. A value close to 0 indicates the deck difficulty was accurately predicted.',
                'chart-focus-title': 'Engagement Focus (Active / Total %)',
                'chart-focus-sub': 'Percentage of total time spent in active word-guessing turns. This data can indicate how much discussion and debate a word deck provokes.',
                'chart-winrate-title': 'First-Mover Advantage (Win Rate)',
                'chart-winrate-sub': 'Win percentages of teams based on their starting position and the total number of teams. If the starting team has a significantly higher win rate, the game mechanics may be considered unfair.',
                'chart-pop-title': 'Deck Popularity (Total Plays)',
                'chart-pop-sub': 'Total number of times each deck has been played.',
                'chart-sat-title': 'Deck Satisfaction',
                'chart-sat-sub': 'User feedback (likes/dislikes) received for each deck.',
                'chart-time-title': 'Temporal Play Trend (Heatmap)',
                'chart-time-sub': 'Game density by days of the week and local hours (Bubble size indicates number of games).',
                'chart-pass-title': 'Pass Rate',
                'chart-pass-sub': 'Percentage of words passed out of total attempts per deck.',
                'chart-undo-title': 'Undo Rate (Avg per Game)',
                'chart-undo-sub': 'Average number of undo actions performed per session.',
                'chart-diffsat-title': 'Difficulty Satisfaction',
                'chart-diffsat-sub': 'Likes vs Dislikes grouped by deck difficulty.',
                'chart-lang-title': 'Interface Language Distribution',
                'chart-lang-sub': 'Proportion of application languages used by players.',
                'empty-title': 'Awaiting Telemetry Data',
                'empty-sub': 'Play some games to generate statistics and insights.',
                'minutes': 'Minutes',
                'margin-mins': 'Margin (Mins)',
                'active-percent': 'Active %',
                'win-percent': 'Win Percentage (%)',
                'team': 'Team',
                'teams-2': '2-Team Game',
                'teams-3': '3-Team Game',
                'teams-4': '4-Team Game',
                'likes': 'Likes',
                'dislikes': 'Dislikes',
                'est-dur': 'Estimated Duration',
                'total-dur': 'Total Duration',
                'active-dur': 'Active Duration',
                'avg-gap': 'Avg Points Gap',
                'points-gap': 'points gap',
                'sec-word': 'sec/word',
                'words': 'words',
                'live-status': 'Live Status: ',
                'waiting-live': 'Waiting for data...'
            }
        };

        let currentLang = localStorage.getItem('yaygara-lang') || 'tr';
        let charts = {};

        function updateLanguageUI(lang) {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang][key]) {
                    el.innerText = translations[lang][key];
                }
            });

            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
            });

            // Update complex dynamic subtexts if needed
            if (window.lastProcessedData) {
                updateKPIs(window.lastProcessedData, lang);
            }

            if (Object.keys(charts).length > 0) {
                renderCharts(window.lastProcessedData, lang);
            }
        }

        document.getElementById('lang-switcher').addEventListener('click', (e) => {
            const btn = e.target.closest('.lang-btn');
            if (btn) {
                currentLang = btn.getAttribute('data-lang');
                localStorage.setItem('yaygara-lang', currentLang);
                updateLanguageUI(currentLang);
            }
        });

        const rawData = <?php echo json_encode($records); ?>;

        if (rawData.length > 0) {
            // --- Data Processor ---
            const deckStats = {};
            const winRates = { 2: [0, 0], 3: [0, 0, 0], 4: [0, 0, 0, 0] };
            const totalGames = { 2: 0, 3: 0, 4: 0 };

            // Global Trackers for 45s turn metric
            let globalTotalWords = 0;
            let globalActiveDurationMin = 0;
            let estPlayersMin = 0;
            let estPlayersMax = 0;

            const diffStats = {
                easy: { totalWords: 0, activeDur: 0, likes: 0, dislikes: 0 },
                medium: { totalWords: 0, activeDur: 0, likes: 0, dislikes: 0 },
                hard: { totalWords: 0, activeDur: 0, likes: 0, dislikes: 0 }
            };

            const langStats = { tr: 0, en: 0, other: 0 };
            const timeTrendStats = Array.from({ length: 7 }, () => new Array(24).fill(0));

            rawData.forEach(session => {
                const d = session.deck_id;
                const difficulty = session.deck_difficulty || 'medium';

                if (!deckStats[d]) {
                    deckStats[d] = {
                        count: 0, est_dur: 0, total_dur: 0, active_dur: 0,
                        scoreGapSum: 0, totalWords: 0, likes: 0, dislikes: 0,
                        totalPasses: 0, totalUndos: 0
                    };
                }

                deckStats[d].count++;
                deckStats[d].est_dur += session.estimated_duration_min;
                deckStats[d].total_dur += session.duration_total_min;
                deckStats[d].active_dur += session.duration_active_min;
                deckStats[d].totalWords += session.total_words_played;
                deckStats[d].totalPasses += (session.total_passes || 0);
                deckStats[d].totalUndos += (session.total_undos || 0);

                if (diffStats[difficulty]) {
                    diffStats[difficulty].totalWords += session.total_words_played;
                    diffStats[difficulty].activeDur += session.duration_active_min;
                }

                if (session.liked === true) {
                    deckStats[d].likes++;
                    if (diffStats[difficulty]) diffStats[difficulty].likes++;
                } else if (session.liked === false) {
                    deckStats[d].dislikes++;
                    if (diffStats[difficulty]) diffStats[difficulty].dislikes++;
                }

                // Lang Tracker
                const lang = session.interface_language;
                if (lang === 'tr' || lang === 'en') langStats[lang]++; else langStats.other++;

                // Temporal Tracker (Local Hour & Day)
                if (session.timestamp) {
                    const dateObj = new Date(session.timestamp);
                    const localHour = dateObj.getHours();
                    let localDay = dateObj.getDay(); // 0 is Sunday
                    localDay = localDay === 0 ? 6 : localDay - 1; // Map to 0 (Mon) - 6 (Sun)

                    if (localHour >= 0 && localHour <= 23 && localDay >= 0 && localDay <= 6) {
                        timeTrendStats[localDay][localHour]++;
                    }
                }

                globalTotalWords += session.total_words_played;
                globalActiveDurationMin += session.duration_active_min;

                if (session.scores && session.scores.length >= 2) {
                    const teamCount = session.scores.length;

                    if (teamCount === 2) { estPlayersMin += 4; estPlayersMax += 6; }
                    else if (teamCount === 3) { estPlayersMin += 6; estPlayersMax += 12; }
                    else if (teamCount >= 4) { estPlayersMin += 12; estPlayersMax += 16; }

                    let maxScore = -1; let winningIndex = -1;

                    session.scores.forEach((team, index) => {
                        if (team.score > maxScore) { maxScore = team.score; winningIndex = index; }
                    });

                    // Sort scores descending to find 1st and 2nd highest
                    const sortedScores = session.scores.map(t => t.score).sort((a, b) => b - a);
                    if (sortedScores.length >= 2) {
                        deckStats[d].scoreGapSum += (sortedScores[0] - sortedScores[1]);
                    }

                    if (winningIndex !== -1 && winRates[teamCount]) {
                        winRates[teamCount][winningIndex]++;
                        totalGames[teamCount]++;
                    }
                }
            });

            // Handle scaling height for 30 decks
            const decks = Object.keys(deckStats).sort();
            const dynamicHeight = Math.max(350, decks.length * 30);

            document.getElementById('wrap-scoreGapChart').style.height = dynamicHeight + 'px';
            document.getElementById('wrap-durationDeltaChart').style.height = dynamicHeight + 'px';
            document.getElementById('wrap-focusRatioChart').style.height = dynamicHeight + 'px';
            // We set PassRate and UndoRate wrapper heights dynamically as well
            document.getElementById('wrap-passRateChart').style.height = dynamicHeight + 'px';
            document.getElementById('wrap-undoRateChart').style.height = dynamicHeight + 'px';

            window.lastProcessedData = {
                deckStats, diffStats, winRates, totalGames, decks,
                globalTotalWords, globalActiveDurationMin, totalGamesCount: rawData.length,
                estPlayersMin, estPlayersMax, langStats, timeTrendStats
            };

            updateKPIs(window.lastProcessedData, currentLang);
            updateLanguageUI(currentLang);
            renderCharts(window.lastProcessedData, currentLang);
        }

        function updateKPIs(data, lang) {
            const { deckStats, diffStats, winRates, totalGames, decks, globalTotalWords, globalActiveDurationMin, totalGamesCount, estPlayersMin, estPlayersMax } = data;
            const t = translations[lang];

            document.getElementById('kpi-total-games').innerText = totalGamesCount;
            document.getElementById('kpi-total-words').innerText = globalTotalWords;
            document.getElementById('kpi-total-duration').innerText = globalActiveDurationMin;
            document.getElementById('kpi-total-players').innerText = estPlayersMin + " - " + estPlayersMax;

            const avgScoreGaps = decks.map(d => parseFloat((deckStats[d].scoreGapSum / deckStats[d].count).toFixed(1)));
            const wordPaces = decks.map(d => parseFloat(((deckStats[d].active_dur * 60) / deckStats[d].totalWords).toFixed(1)));

            // --- Smart KPIs ---
            let maxGapIndex = 0; let maxGap = 0;
            avgScoreGaps.forEach((gap, i) => { if (gap > maxGap) { maxGap = gap; maxGapIndex = i; } });
            document.getElementById('kpi-imbalance-deck').innerText = decks.length > 0 ? decks[maxGapIndex].replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ') : 'N/A';
            document.getElementById('kpi-imbalance-val').innerText = maxGap + " " + t['points-gap'];

            let maxPaceIndex = 0; let maxPace = 0;
            wordPaces.forEach((pace, i) => { if (pace > maxPace) { maxPace = pace; maxPaceIndex = i; } });
            document.getElementById('kpi-slowest-deck').innerText = decks.length > 0 ? decks[maxPaceIndex].replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ') : 'N/A';
            document.getElementById('kpi-slowest-val').innerText = maxPace + " " + t['sec-word'];

            // Team 1 Dominance (First-Mover Advantage) calculation for 2, 3, and 4-team games
            const totalMultiTeamGames = (totalGames[2] || 0) + (totalGames[3] || 0) + (totalGames[4] || 0);
            if (totalMultiTeamGames > 0) {
                const totalT1Wins = (winRates[2] ? winRates[2][0] : 0) +
                    (winRates[3] ? winRates[3][0] : 0) +
                    (winRates[4] ? winRates[4][0] : 0);
                document.getElementById('kpi-first-mover-multi').innerText = ((totalT1Wins / totalMultiTeamGames) * 100).toFixed(1) + "%";
            } else {
                document.getElementById('kpi-first-mover-multi').innerText = "N/A";
            }

            if (globalActiveDurationMin > 0) {
                const wordsPer45s = ((globalTotalWords / (globalActiveDurationMin * 60)) * 45).toFixed(1);
                document.getElementById('kpi-words-45s').innerText = wordsPer45s + " " + t['words'];
            } else { document.getElementById('kpi-words-45s').innerText = "N/A"; }

            ['easy', 'medium', 'hard'].forEach(diff => {
                const stats = diffStats[diff];
                const el = document.getElementById(`kpi-${diff}-45s`);
                if (stats && stats.activeDur > 0) {
                    const val = ((stats.totalWords / (stats.activeDur * 60)) * 45).toFixed(1);
                    el.innerText = val + " " + t['words'];
                } else { el.innerText = "N/A"; }
            });

            document.getElementById('last-update').innerText = t['live-status'] + new Date().toLocaleString();
        }

        function renderCharts(data, lang) {
            const { deckStats, winRates, totalGames, decks } = data;
            const t = translations[lang];

            // Destroy existing charts
            Object.values(charts).forEach(c => c.destroy());

            const avgEstimatedDurs = decks.map(d => parseFloat((deckStats[d].est_dur / deckStats[d].count).toFixed(1)));
            const avgTotalDurs = decks.map(d => parseFloat((deckStats[d].total_dur / deckStats[d].count).toFixed(1)));
            const avgActiveDurs = decks.map(d => parseFloat((deckStats[d].active_dur / deckStats[d].count).toFixed(1)));
            const durationDeltas = decks.map(d => parseFloat(((deckStats[d].total_dur - deckStats[d].est_dur) / deckStats[d].count).toFixed(1)));
            const focusRatios = decks.map(d => parseFloat(((deckStats[d].active_dur / deckStats[d].total_dur) * 100).toFixed(1)));
            const avgScoreGaps = decks.map(d => parseFloat((deckStats[d].scoreGapSum / deckStats[d].count).toFixed(1)));
            const deckPlayCounts = decks.map(d => deckStats[d].count);
            const deckLikes = decks.map(d => deckStats[d].likes);
            const deckDislikes = decks.map(d => deckStats[d].dislikes);

            // --- Premium Chart.js Aesthetics ---
            Chart.defaults.color = '#cbd5e1';
            Chart.defaults.font.family = 'Outfit';
            Chart.defaults.maintainAspectRatio = false;

            const getGradient = (ctx, colorStart, colorEnd) => {
                const gradient = ctx.createLinearGradient(0, 0, 400, 0);
                gradient.addColorStop(0, colorStart);
                gradient.addColorStop(1, colorEnd);
                return gradient;
            };

            const ctxTimeline = document.getElementById('durationTimelineChart').getContext('2d');
            charts.timeline = new Chart(ctxTimeline, {
                type: 'line',
                data: {
                    labels: decks.map(d => d.replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ')),
                    datasets: [
                        { label: t['est-dur'], data: avgEstimatedDurs, borderColor: '#38bdf8', borderDash: [5, 5], fill: false, tension: 0.2 },
                        { label: t['total-dur'], data: avgTotalDurs, borderColor: '#fb7185', backgroundColor: 'rgba(251, 113, 133, 0.1)', fill: true, tension: 0.3 },
                        { label: t['active-dur'], data: avgActiveDurs, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.15)', fill: true, tension: 0.3 }
                    ]
                },
                options: {
                    scales: { y: { title: { display: true, text: t['minutes'] }, beginAtZero: true } }
                }
            });

            const ctxGap = document.getElementById('scoreGapChart').getContext('2d');
            charts.gap = new Chart(ctxGap, {
                type: 'bar',
                data: {
                    labels: decks.map(d => d.replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ')),
                    datasets: [{
                        label: t['avg-gap'],
                        data: avgScoreGaps,
                        backgroundColor: getGradient(ctxGap, 'rgba(129, 140, 248, 0.7)', 'rgba(167, 139, 250, 0.9)'),
                        borderRadius: 6
                    }]
                },
                options: { indexAxis: 'y' }
            });

            const ctxDelta = document.getElementById('durationDeltaChart').getContext('2d');
            charts.delta = new Chart(ctxDelta, {
                type: 'bar',
                data: {
                    labels: decks.map(d => d.replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ')),
                    datasets: [{
                        label: t['margin-mins'],
                        data: durationDeltas,
                        backgroundColor: durationDeltas.map(d => d > 0 ? 'rgba(251, 113, 133, 0.8)' : 'rgba(16, 185, 129, 0.8)'),
                        borderRadius: 4
                    }]
                },
                options: { indexAxis: 'y' }
            });

            const ctxFocus = document.getElementById('focusRatioChart').getContext('2d');
            charts.focus = new Chart(ctxFocus, {
                type: 'bar',
                data: {
                    labels: decks.map(d => d.replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ')),
                    datasets: [{
                        label: t['active-percent'],
                        data: focusRatios,
                        backgroundColor: getGradient(ctxFocus, 'rgba(56, 189, 248, 0.6)', 'rgba(56, 189, 248, 0.9)'),
                        borderRadius: 4
                    }]
                },
                options: { indexAxis: 'y', scales: { x: { min: 0, max: 100 } } }
            });

            const calcPercent = (wins, total) => total > 0 ? (wins / total * 100).toFixed(1) : 0;
            const ctxWin = document.getElementById('winRateChart').getContext('2d');
            charts.win = new Chart(ctxWin, {
                type: 'bar',
                data: {
                    labels: [t['teams-2'] || '2 Teams', t['teams-3'] || '3 Teams', t['teams-4'] || '4 Teams'],
                    datasets: [
                        { label: t['team'] + ' 1', data: [calcPercent(winRates[2][0], totalGames[2]), calcPercent(winRates[3][0], totalGames[3]), calcPercent(winRates[4][0], totalGames[4])], backgroundColor: 'rgba(56, 189, 248, 0.8)' },
                        { label: t['team'] + ' 2', data: [calcPercent(winRates[2][1], totalGames[2]), calcPercent(winRates[3][1], totalGames[3]), calcPercent(winRates[4][1], totalGames[4])], backgroundColor: 'rgba(251, 113, 133, 0.8)' },
                        { label: t['team'] + ' 3', data: [0, calcPercent(winRates[3][2], totalGames[3]), calcPercent(winRates[4][2], totalGames[4])], backgroundColor: 'rgba(16, 185, 129, 0.8)' },
                        { label: t['team'] + ' 4', data: [0, 0, calcPercent(winRates[4][3], totalGames[4])], backgroundColor: 'rgba(245, 158, 11, 0.8)' }
                    ]
                },
                options: { scales: { y: { beginAtZero: true, max: 100, title: { display: true, text: t['win-percent'] } } } }
            });

            const ctxPop = document.getElementById('deckPopularityChart').getContext('2d');
            charts.pop = new Chart(ctxPop, {
                type: 'doughnut',
                data: {
                    labels: decks.map(d => d.replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ')),
                    datasets: [{
                        data: deckPlayCounts,
                        backgroundColor: ['#38bdf8', '#818cf8', '#10b981', '#fb7185', '#f59e0b', '#a78bfa', '#ec4899', '#14b8a6'],
                        borderWidth: 0, hoverOffset: 15
                    }]
                },
                options: { cutout: '65%', plugins: { legend: { position: 'right' } } }
            });

            // Interface Language (Doughnut)
            const ctxLang = document.getElementById('languageChart').getContext('2d');
            charts.language = new Chart(ctxLang, {
                type: 'doughnut',
                data: {
                    labels: ['Türkçe (TR)', 'English (EN)', 'Other'],
                    datasets: [{
                        data: [data.langStats.tr, data.langStats.en, data.langStats.other],
                        backgroundColor: ['#38bdf8', '#818cf8', '#94a3b8'],
                        borderWidth: 0, hoverOffset: 15
                    }]
                },
                options: { cutout: '65%', plugins: { legend: { position: 'right' } } }
            });

            // Temporal Trend (Heatmap-style Bubble Chart)
            const bubbleData = [];
            let maxCountTime = 0;
            for (let d = 0; d < 7; d++) {
                for (let h = 0; h < 24; h++) {
                    if (data.timeTrendStats[d][h] > maxCountTime) maxCountTime = data.timeTrendStats[d][h];
                }
            }

            for (let d = 0; d < 7; d++) {
                for (let h = 0; h < 24; h++) {
                    const count = data.timeTrendStats[d][h];
                    if (count > 0) {
                        bubbleData.push({
                            x: d, // day on X axis
                            y: h, // hour on Y axis
                            r: Math.max(4, (count / maxCountTime) * 12), // normalize size between 4 and 12
                            count: count
                        });
                    }
                }
            }

            const dayNamesTR = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
            const dayNamesEN = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            const wDays = lang === 'tr' ? dayNamesTR : dayNamesEN;

            const ctxTime = document.getElementById('timeTrendChart').getContext('2d');
            charts.timeTrend = new Chart(ctxTime, {
                type: 'bubble',
                data: {
                    datasets: [{
                        label: t['chart-time-title'],
                        data: bubbleData,
                        backgroundColor: 'rgba(167, 139, 250, 0.6)',
                        borderColor: '#a78bfa',
                        borderWidth: 1
                    }]
                },
                options: {
                    layout: { padding: { left: 15, right: 15, top: 15, bottom: 15 } },
                    scales: {
                        x: {
                            min: 0, max: 6,
                            ticks: {
                                stepSize: 1,
                                callback: function (value) { return wDays[value] || ''; }
                            }
                        },
                        y: {
                            min: 0, max: 23,
                            ticks: {
                                stepSize: 1,
                                callback: function (value) { return value + ':00'; }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const raw = context.raw;
                                    return `${wDays[raw.x]} ${raw.y}:00 - ${raw.count} ${t['kpi-total-games-label']}`;
                                }
                            }
                        }
                    }
                }
            });

            // Pass Rate (Horizontal Bar)
            const passRates = decks.map(d => {
                const totalAtt = deckStats[d].totalWords + (deckStats[d].totalPasses || 0);
                return totalAtt > 0 ? parseFloat(((deckStats[d].totalPasses / totalAtt) * 100).toFixed(1)) : 0;
            });
            const ctxPass = document.getElementById('passRateChart').getContext('2d');
            charts.passRate = new Chart(ctxPass, {
                type: 'bar',
                data: {
                    labels: decks.map(d => d.replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ')),
                    datasets: [{
                        label: t['chart-pass-title'],
                        data: passRates,
                        backgroundColor: passRates.map(p => p > 30 ? 'rgba(251, 113, 133, 0.8)' : 'rgba(56, 189, 248, 0.8)'),
                        borderRadius: 4
                    }]
                },
                options: { indexAxis: 'y', scales: { x: { max: 100 } } }
            });

            // Undo Rate (Horizontal Bar)
            const undoRates = decks.map(d => parseFloat((deckStats[d].totalUndos / deckStats[d].count).toFixed(2)));
            const ctxUndo = document.getElementById('undoRateChart').getContext('2d');
            charts.undoRate = new Chart(ctxUndo, {
                type: 'bar',
                data: {
                    labels: decks.map(d => d.replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ')),
                    datasets: [{
                        label: t['chart-undo-title'],
                        data: undoRates,
                        backgroundColor: undoRates.map(u => u > 2 ? 'rgba(245, 158, 11, 0.8)' : 'rgba(16, 185, 129, 0.8)'),
                        borderRadius: 4
                    }]
                },
                options: { indexAxis: 'y' }
            });

            // Difficulty Satisfaction (Stacked Bar)
            const diffTypes = ['easy', 'medium', 'hard'];
            const diffLabels = [t['kpi-easy-45s-label'], t['kpi-medium-45s-label'], t['kpi-hard-45s-label']];
            const diffLikes = diffTypes.map(df => data.diffStats[df] ? data.diffStats[df].likes : 0);
            const diffDislikes = diffTypes.map(df => data.diffStats[df] ? data.diffStats[df].dislikes : 0);

            const ctxDiffSat = document.getElementById('diffSatChart').getContext('2d');
            charts.diffSat = new Chart(ctxDiffSat, {
                type: 'bar',
                data: {
                    labels: diffLabels,
                    datasets: [
                        { label: t['likes'], data: diffLikes, backgroundColor: '#10b981' },
                        { label: t['dislikes'], data: diffDislikes, backgroundColor: '#fb7185' }
                    ]
                },
                options: { scales: { x: { stacked: true }, y: { stacked: true } } }
            });

            const ctxSat = document.getElementById('deckSatisfactionChart').getContext('2d');
            charts.sat = new Chart(ctxSat, {
                type: 'bar',
                data: {
                    labels: decks.map(d => d.replace('.tr', '').replace('.en', '').toLowerCase().replace(/-/g, ' ')),
                    datasets: [
                        { label: t['likes'], data: deckLikes, backgroundColor: '#10b981' },
                        { label: t['dislikes'], data: deckDislikes, backgroundColor: '#fb7185' }
                    ]
                },
                options: { scales: { x: { stacked: true }, y: { stacked: true } } }
            });
        }

        // Initialize display
        if (rawData.length > 0) {
            // Processing logic stays inside initial load check
        } else {
            updateLanguageUI(currentLang);
        }
    </script>
</body>

</html>