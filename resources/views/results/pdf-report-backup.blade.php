<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Assessment Results</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
            line-height: 1.5;
            color: #1f2937;
            background-color: #ffffff;
            margin: 0;
            padding: 12px;
            font-size: 11px;
        }
        
        html {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* ─── HEADER ─── */
        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 10px;
            color: #6b7280;
        }

        /* ─── OVERVIEW CARDS ─── */
        .overview-cards {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .overview-card {
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .overview-card .label {
            font-size: 10px;
            color: #6b7280;
            font-weight: 500;
        }

        .overview-card .score {
            font-size: 28px;
            font-weight: 700;
            color: #4f46e5;
            margin: 4px 0;
        }

        .overview-card.green .score { color: #059669; }
        .overview-card.purple .score { color: #7c3aed; }

        /* ─── TWO COLUMN LAYOUT ─── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        /* ─── CAREER OPPORTUNITIES BOX ─── */
        .career-box {
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            color: white;
            padding: 14px;
            border-radius: 8px;
            page-break-inside: avoid;
        }

        .career-box h3 {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
        }

        .career-box p {
            font-size: 9px;
            margin-bottom: 10px;
            color: rgba(255,255,255,0.9);
        }

        .career-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .career-item {
            background: rgba(255,255,255,0.15);
            border-left: 3px solid rgba(255,255,255,0.5);
            padding: 6px 8px;
            border-radius: 3px;
            font-size: 10px;
            color: white;
            font-weight: 500;
        }

        /* ─── LEARNING STYLES BOX ─── */
        .learning-box {
            background: white;
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 8px;
            page-break-inside: avoid;
        }

        .learning-box h3 {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 2px;
            color: #1f2937;
        }

        .learning-box > p {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .learning-styles-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .learning-style-card {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            background: #f9fafb;
        }

        .learning-style-card.preferred {
            border: 2px solid #10b981;
            background: #ecfdf5;
        }

        .learning-style-card .name {
            font-size: 10px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .learning-style-card .score {
            font-size: 18px;
            font-weight: 700;
            color: #059669;
        }

        /* ─── SECTION STYLING ─── */
        .section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 6px;
        }

        .section-subtitle {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        /* ─── DOMAIN CARDS ─── */
        .domain-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 4px solid;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .domain-card.exceptional { border-left-color: #059669; }
        .domain-card.high { border-left-color: #3b82f6; }
        .domain-card.average { border-left-color: #f59e0b; }
        .domain-card.below-average { border-left-color: #f97316; }
        .domain-card.low { border-left-color: #ef4444; }

        .domain-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .domain-name {
            font-size: 11px;
            font-weight: 600;
            color: #1f2937;
        }

        .domain-score {
            font-size: 18px;
            font-weight: 700;
        }

        .domain-score.exceptional { color: #059669; }
        .domain-score.high { color: #3b82f6; }
        .domain-score.average { color: #f59e0b; }
        .domain-score.below-average { color: #f97316; }
        .domain-score.low { color: #ef4444; }

        .progress-bar {
            width: 100%;
            height: 5px;
            background: #e5e7eb;
            border-radius: 3px;
            margin-bottom: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        .progress-fill.exceptional { background: #059669; }
        .progress-fill.high { background: #3b82f6; }
        .progress-fill.average { background: #f59e0b; }
        .progress-fill.below-average { background: #f97316; }
        .progress-fill.low { background: #ef4444; }

        .domain-description {
            font-size: 9px;
            color: #4b5563;
            line-height: 1.3;
            margin-bottom: 6px;
        }

        .insight-box {
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 6px 8px;
            border-radius: 3px;
            font-size: 8px;
            line-height: 1.3;
        }

        .insight-box.exceptional { background: #d1fae5; border-left-color: #059669; }
        .insight-box.high { background: #dbeafe; border-left-color: #3b82f6; }

        /* ─── INSIGHT CARDS GRID ─── */
        .insights-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .insight-card {
            text-align: center;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #f9fafb;
        }

        .insight-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
        }

        .insight-card .label {
            font-size: 9px;
            font-weight: 600;
            color: #6b7280;
            margin-top: 4px;
        }

        /* ─── HOLLAND CODE LETTERS ─── */
        .holland-codes {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .holland-letter {
            text-align: center;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #f9fafb;
        }

        .holland-letter .code {
            font-size: 18px;
            font-weight: 700;
            color: #6366f1;
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            border-radius: 50%;
            background: #eef2ff;
            margin-bottom: 4px;
        }

        .holland-letter .name {
            font-size: 9px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .holland-letter .score {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
        }

        /* ─── COLORED BOXES ─── */
        .box-green {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .box-green h4 {
            color: #059669;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .box-green ul {
            list-style: none;
            padding: 0;
        }

        .box-green li {
            font-size: 9px;
            color: #047857;
            margin-bottom: 4px;
            padding-left: 16px;
            position: relative;
        }

        .box-green li:before {
            content: "✓";
            position: absolute;
            left: 0;
            font-weight: bold;
            color: #059669;
        }

        /* ─── STRENGTH/DEV CARDS ─── */
        .strength-cards {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .strength-card {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            background: #f9fafb;
        }

        .strength-card .label {
            font-size: 8px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .strength-card .score {
            font-size: 20px;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 3px;
        }

        .strength-card.green .score { color: #059669; }
        .strength-card.blue .score { color: #3b82f6; }
        .strength-card.purple .score { color: #7c3aed; }

        .strength-card .name {
            font-size: 10px;
            font-weight: 600;
            color: #1f2937;
        }

        /* ─── FOOTER ─── */
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }

        /* ─── UTILITIES ─── */
        .text-center { text-align: center; }
        .mt-2 { margin-top: 8px; }
        .mb-2 { margin-bottom: 8px; }
    </style>
</head>
<body>
            margin-bottom: 14px;
            padding-bottom: 12px;
            padding-top: 8px;
            border-bottom: 3px solid #1a237e;
            page-break-inside: avoid;
        }
        
        .brand-info {
            flex: 1;
        }
        
        .brand-logo {
            font-size: 26px;
            font-weight: 900;
            color: #1a237e;
            margin-bottom: 3px;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        
        .brand-tagline {
            font-size: 10px;
            color: #0288d1;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }
        
        .brand-divider {
            width: 2px;
            height: 35px;
            background: linear-gradient(to bottom, #1a237e, #0288d1);
            margin: 0 22px;
        }
        
        .report-title {
            text-align: right;
        }
        
        .report-title h1 {
            color: #1a237e;
            font-size: 24px;
            margin: 0;
            font-weight: 800;
            line-height: 1.2;
        }
        
        .report-title p {
            color: #424242;
            font-size: 12px;
            margin: 4px 0 0 0;
            font-weight: 500;
        }

        /* Generated Date */
        .generated-date {
            text-align: center;
            color: #757575;
            font-size: 10px;
            margin-bottom: 14px;
            padding: 6px;
            background-color: #fafafa;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        /* Section Styling */
        .section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        
        .section-header {
            background: linear-gradient(135deg, #f5f7fa 0%, #efefef 100%);
            padding: 12px 14px;
            border-left: 5px solid #1a237e;
            margin-bottom: 14px;
            border-radius: 3px;
            page-break-inside: avoid;
        }
        
        .section-header h2 {
            margin: 0;
            color: #1a237e;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }
        
        .section-header p {
            margin: 3px 0 0 0;
            color: #666;
            font-size: 9px;
            font-weight: 500;
        }

        /* Overview Grid & Tables */
        .overview-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .overview-card {
            text-align: center;
            padding: 16px 14px;
            border: 1.5px solid #e0e0e0;
            border-radius: 6px;
            background-color: #fafafa;
            page-break-inside: avoid;
        }
        
        .overview-card.exceptional-bg {
            background-color: #e8f5e9;
            border-color: #81c784;
        }
        
        .overview-card.high-bg {
            background-color: #e3f2fd;
            border-color: #64b5f6;
        }
        
        .overview-card.average-bg {
            background-color: #fff3e0;
            border-color: #ffb74d;
        }
        
        .overview-score {
            font-size: 32px;
            font-weight: 900;
            margin: 6px 0;
            letter-spacing: -1px;
        }
        
        .overview-label {
            font-size: 10px;
            color: #555;
            font-weight: 600;
            line-height: 1.3;
        }

        /* Domain Cards - Enhanced */
        .domain {
            margin-bottom: 12px;
            border: 1.5px solid;
            border-radius: 6px;
            padding: 12px;
            page-break-inside: avoid;
        }
        
        .domain-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .domain-name {
            font-size: 12px;
            font-weight: 800;
            color: #1a237e;
            flex: 1;
        }
        
        .domain-score {
            font-size: 24px;
            font-weight: 900;
            margin-left: 8px;
            min-width: 50px;
            text-align: right;
        }
        
        .performance-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 700;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: rgba(255,255,255,0.6);
        }
        
        .progress-bar {
            width: 100%;
            height: 5px;
            background-color: #e0e0e0;
            border-radius: 3px;
            margin: 8px 0;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        
        .domain-description {
            font-size: 10px;
            color: #555;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .insight-box {
            border-left: 4px solid;
            padding: 10px 12px;
            margin-top: 8px;
            border-radius: 3px;
            background-color: rgba(255,255,255,0.4);
        }
        
        .insight-title {
            font-weight: 800;
            margin-bottom: 2px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .insight-text {
            color: #333;
            font-size: 10px;
            line-height: 1.4;
        }

        /* Results Table */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 10px;
        }
        
        .results-table thead {
            background-color: #f5f7fa;
            border-bottom: 2px solid #1a237e;
        }
        
        .results-table th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 800;
            color: #1a237e;
        }
        
        .results-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .results-table tbody tr:last-child td {
            border-bottom: 2px solid #1a237e;
        }
        
        .results-table .score-col {
            text-align: center;
            font-weight: 700;
            width: 15%;
        }
        
        .results-table .level-col {
            text-align: center;
            width: 18%;
        }

        /* Grid Layouts */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }
        
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }
        
        .grid-4 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 16px;
        }

        /* Compact Card */
        .card {
            border: 1.5px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            background-color: #fafafa;
            page-break-inside: avoid;
        }
        
        .card-title {
            font-weight: 700;
            font-size: 9px;
            margin-bottom: 4px;
            color: #1a237e;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .card-value {
            font-size: 18px;
            font-weight: 900;
            color: #1a237e;
        }
        
        .card-progress {
            background-color: #e0e0e0;
            border-radius: 2px;
            height: 4px;
            margin: 6px 0 4px 0;
            overflow: hidden;
        }
        
        .card-progress-fill {
            height: 100%;
            border-radius: 2px;
        }

        /* Career Recommendations */
        .career-recommendations {
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
            border: 1.5px solid #81c784;
            border-radius: 6px;
            padding: 12px;
            margin-top: 10px;
            page-break-inside: avoid;
        }
        
        .career-title {
            color: #0d7a3d;
            font-weight: 800;
            margin-bottom: 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .career-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }
        
        .career-item {
            background: white;
            border: 1px solid #81c784;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 9px;
            text-align: center;
            font-weight: 600;
            color: #0d7a3d;
        }

        /* Recommendation Lists */
        .rec-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .rec-list li {
            padding: 4px 0 4px 16px;
            position: relative;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        
        .rec-list li:before {
            content: "▸";
            position: absolute;
            left: 0;
            color: #1a237e;
            font-weight: bold;
        }

        /* Page Breaks */
        .page-break {
            page-break-before: always;
            margin-top: 0;
            padding-top: 0;
        }

        /* Multi-column Sections */
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 12px;
        }

        /* Footer */
        .footer {
            margin-top: 16px;
            border-top: 2px solid #1a237e;
            padding-top: 12px;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .footer-brand {
            font-size: 12px;
            font-weight: 900;
            color: #1a237e;
            margin-bottom: 3px;
        }
        
        .footer-tagline {
            font-size: 9px;
            color: #666;
            margin: 0;
            font-weight: 500;
        }
        
        .footer-note {
            font-size: 8px;
            color: #999;
            margin-top: 4px;
            line-height: 1.3;
        }

        /* Predictive Insights */
        .predictive-insights {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        
        .insight-card {
            border: 1.5px solid;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .insight-value {
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 3px;
        }
        
        .insight-label {
            font-size: 10px;
            color: #555;
            font-weight: 700;
        }

        /* Headings */
        h3 {
            color: #1a237e;
            font-size: 11px;
            font-weight: 800;
            margin: 10px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        h4 {
            color: #424242;
            font-size: 11px;
            font-weight: 700;
            margin: 8px 0 6px 0;
        }

        /* Print Optimization */
        @media print {
            body {
                margin: 0;
                padding: 12px;
            }
            .brand-header {
                margin-bottom: 12px;
            }
            .section {
                margin-bottom: 12px;
            }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="brand-info">
            <div class="brand-logo">MetrixsMate</div>
            <div class="brand-tagline">Discover Your Potential</div>
        </div>
        <div class="brand-divider"></div>
        <div class="report-title">
            <h1>Assessment Report</h1>
            <p>{{ $user->name }}</p>
        </div>
    </div>

    <div class="generated-date">
        Generated on {{ $generatedDate }}
    </div>

    <!-- Executive Summary - Overview Cards -->
    <div class="section">
        <div class="section-header">
            <h2>Executive Summary</h2>
            <p>Your overall assessment results at a glance</p>
        </div>
        
        @php
            $personalityLevel = $overallScore['personality'] >= 85 ? 'exceptional' : ($overallScore['personality'] >= 70 ? 'high' : ($overallScore['personality'] >= 40 ? 'average' : ($overallScore['personality'] >= 20 ? 'below-average' : 'low')));
            $cognitiveLevel = $overallScore['cognitive'] >= 85 ? 'exceptional' : ($overallScore['cognitive'] >= 70 ? 'high' : ($overallScore['cognitive'] >= 40 ? 'average' : ($overallScore['cognitive'] >= 20 ? 'below-average' : 'low')));
        @endphp
        
        <div class="overview-grid">
            <div class="overview-card {{ $personalityLevel }}-bg {{ $personalityLevel }}-border">
                <div class="overview-score {{ $personalityLevel }}">{{ $overallScore['personality'] }}%</div>
                <div class="overview-label">Personality Profile<br>OCEAN Assessment</div>
            </div>
            <div class="overview-card">
                <div class="overview-score" style="color: #0d7a3d;">{{ $riasecResults['holland_code'] }}</div>
                <div class="overview-label">Career Interests<br>RIASEC Profile</div>
            </div>
            <div class="overview-card {{ $cognitiveLevel }}-bg {{ $cognitiveLevel }}-border">
                <div class="overview-score {{ $cognitiveLevel }}">{{ $overallScore['cognitive'] }}%</div>
                <div class="overview-label">Cognitive Abilities<br>Average Performance</div>
            </div>
        </div>
    </div>

    <!-- Career Opportunities + Learning Styles -->
    <div class="grid-2" style="margin-bottom: 18px;">
        <!-- Career Opportunities -->
        <div style="background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%); color: white; border-radius: 6px; padding: 16px; page-break-inside: avoid;">
            <h3 style="color: white; margin-top: 0; margin-bottom: 10px;">Career Opportunities</h3>
            <p style="font-size: 10px; color: rgba(255,255,255,0.9); margin: 0 0 12px 0;">Based on your {{ $riasecResults['holland_code'] }} profile</p>
            @if($riasecResults['career_analysis'])
            <ul class="rec-list" style="color: rgba(255,255,255,0.95);">
                @foreach(explode(';', $riasecResults['career_analysis']->level_description) as $career)
                <li style="color: rgba(255,255,255,0.95);">{{ trim($career) }}</li>
                @endforeach
            </ul>
            @endif
        </div>

        <!-- Learning Styles -->
        <div style="background: white; border: 1.5px solid #e0e0e0; border-radius: 6px; padding: 16px; page-break-inside: avoid;">
            <h3 style="margin-top: 0; margin-bottom: 10px;">Learning Styles</h3>
            <p style="font-size: 10px; color: #666; margin: 0 0 12px 0;">Your preferred methods for acquiring information</p>
            <div class="grid-2">
                @foreach($learningStyles as $style)
                <div class="card" style="{{ $style['name'] === $learningStyles[0]['name'] ? 'border-color: #4caf50; border-width: 2px; background-color: #e8f5e9;' : '' }}">
                    <div class="card-title">{{ $style['name'] }}</div>
                    <div class="card-value" style="font-size: 18px;">{{ $style['score'] }}%</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- OCEAN Personality Assessment -->
    <div class="section">
        <div class="section-header">
            <h2>Personality Profile (OCEAN)</h2>
            <p>Your Big Five personality traits and implications</p>
        </div>

        @foreach($oceanResults['domains'] as $domain)
        <div class="domain {{ $domain->performance_level }}-bg {{ $domain->performance_level }}-border">
            <div class="domain-header">
                <div class="domain-name">{{ $domain->name }}</div>
                <div class="domain-score {{ $domain->performance_level }}">
                    {{ round($domain->percentage, 0) }}%
                    <span class="performance-badge {{ $domain->performance_level }}">{{ $domain->performance_text }}</span>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill {{ $domain->performance_level }}-progress" style="width: {{ $domain->percentage }}%"></div>
            </div>
            <div class="domain-description">{{ $domain->description }}</div>
            <div class="insight-box {{ $domain->performance_level }}-border {{ $domain->performance_level }}-bg">
                <div class="insight-title {{ $domain->performance_level }}">Actionable Insight</div>
                <div class="insight-text">{{ $domain->actionable_insights }}</div>
            </div>
        </div>
        @endforeach

        <!-- Predictive Insights -->
        @if($oceanResults['predictive_insights']->isNotEmpty())
        <div style="margin-top: 16px;">
            <h3>Predictive Insights</h3>
            <div class="grid-3" style="margin-bottom: 0;">
                @foreach(['growth_potential', 'organizational_fit_forecast', 'leadership_potential'] as $key)
                    @if(isset($oceanResults['predictive_insights'][$key]))
                        @php $insight = $oceanResults['predictive_insights'][$key]; @endphp
                        <div class="insight-card {{ $insight->performance_level }}-bg {{ $insight->performance_level }}-border">
                            <div class="insight-value {{ $insight->performance_level }}">{{ round($insight->percentage, 0) }}%</div>
                            <div class="insight-label">{{ $insight->name }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- RIASEC Career Assessment -->
    <div class="section">
        <div class="section-header">
            <h2>Career Interest Profile (RIASEC)</h2>
            <p>Your Holland Code and career domain breakdown</p>
        </div>

        <div style="margin-bottom: 18px; text-align: center;">
            <h3 style="color: #0d7a3d; margin: 0 0 14px 0;">Holland Code: <strong style="font-size: 18px;">{{ $riasecResults['holland_code'] }}</strong></h3>
            <div class="grid-3">
                @foreach($riasecResults['domains'] as $domain)
                <div class="domain {{ $domain->performance_level }}-bg {{ $domain->performance_level }}-border">
                    <div style="font-size: 12px; font-weight: 800; color: #1a237e; margin-bottom: 8px;">{{ $domain->name }}</div>
                    <div class="progress-bar">
                        <div class="progress-fill {{ $domain->performance_level }}-progress" style="width: {{ $domain->percentage }}%"></div>
                    </div>
                    <div style="font-size: 16px; font-weight: 900; color: {{ $domain->performance_level == 'exceptional' ? '#0d7a3d' : ($domain->performance_level == 'high' ? '#0066cc' : '#555') }};margin-top:6px;">{{ round($domain->percentage, 0) }}%</div>
                </div>
                @endforeach
            </div>
        </div>

        @if($riasecResults['work_environment'])
        <div style="margin-top: 12px; background: #e8f5e9; border: 1.5px solid #81c784; border-radius: 6px; padding: 14px;">
            <h4 style="color: #0d7a3d; margin-top: 0; margin-bottom: 8px;">Ideal Work Environments</h4>
            <ul class="rec-list" style="font-size: 10px;">
                @foreach(explode(';', $riasecResults['work_environment']->level_description) as $environment)
                <li style="padding: 3px 0 3px 16px;">{{ trim($environment) }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Cognitive Assessment -->
    <div class="section">
        <div class="section-header">
            <h2>Cognitive Abilities</h2>
            <p>Your cognitive strengths across reasoning domains</p>
        </div>

        <div class="grid-2">
            @foreach($cognitiveResults['domains'] as $cognitive)
            <div class="domain {{ $cognitive->performance_level }}-bg {{ $cognitive->performance_level }}-border">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <div style="font-size: 12px; font-weight: 800; color: #1a237e;">{{ $cognitive->name }}</div>
                    <div style="font-size: 18px; font-weight: 900;">{{ round($cognitive->percentage, 0) }}%</div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill {{ $cognitive->performance_level }}-progress" style="width: {{ $cognitive->percentage }}%"></div>
                </div>
                <div class="domain-description" style="margin-bottom: 8px;">{{ $cognitive->level_description }}</div>
            </div>
            @endforeach
        </div>

        <div style="background: #f5f7fa; border: 1.5px solid #1a237e; border-radius: 6px; padding: 12px; margin-top: 12px;">
            <h4 style="color: #1a237e; margin: 0 0 6px 0;">Summary</h4>
            <p style="margin: 0; color: #555; font-size: 10px; line-height: 1.5;">
                Your cognitive profile shows an average of <strong>{{ round($cognitiveResults['average_score'], 0) }}%</strong>. Strongest areas: <strong>{{ $cognitiveResults['domains']->sortByDesc('percentage')->take(2)->pluck('name')->implode(' & ') }}</strong>.
            </p>
        </div>
    </div>

    <!-- Learning & Development Plan -->
    <div class="section">
        <div class="section-header">
            <h2>Personalized Development Plan</h2>
            <p>Tailored recommendations for your growth</p>
        </div>

        <div class="card exceptional-bg" style="margin-bottom: 14px; border-color: #81c784; text-align: left; padding: 14px;">
            <h4 style="color: #0d7a3d; margin: 0 0 6px 0;">Your Preferred Learning Style: <strong>{{ $learningStyles[0]['name'] }}</strong></h4>
            <p style="margin: 0; color: #0d7a3d; font-size: 10px; line-height: 1.5;">Leverage this style in your professional development journey.</p>
        </div>

        <div class="two-column">
            <div style="background: #e3f2fd; border: 1.5px solid #64b5f6; border-radius: 6px; padding: 12px;">
                <h4 style="color: #0066cc; margin: 0 0 8px 0;">Immediate Focus</h4>
                <ul class="rec-list" style="font-size: 10px;">
                    <li>Leverage {{ $learningStyles[0]['name'] }} learning</li>
                    <li>Build on top personality traits</li>
                    <li>Align career goals with interests</li>
                </ul>
            </div>
            <div style="background: #e8f5e9; border: 1.5px solid #81c784; border-radius: 6px; padding: 12px;">
                <h4 style="color: #0d7a3d; margin: 0 0 8px 0;">Growth Opportunities</h4>
                <ul class="rec-list" style="font-size: 10px;">
                    <li>Leadership development</li>
                    <li>Problem-solving skills</li>
                    <li>Technical enhancement</li>
                </ul>
            </div>
        </div>

        <div style="margin-top: 12px; background: #f5f7fa; border: 1.5px solid #1a237e; border-radius: 6px; padding: 12px;">
            <h4 style="color: #1a237e; margin: 0 0 6px 0;">Career Path Guidance</h4>
            @if($riasecResults['career_analysis'])
            <p style="margin: 0; color: #555; font-size: 10px; line-height: 1.5;">
                Focus on roles combining your <strong>{{ $riasecResults['holland_code'] }}</strong> interests with your {{ $learningStyles[0]['name'] }} learning style. Consider careers in: <strong>{{ explode(';', $riasecResults['career_analysis']->level_description)[0] ?? 'your recommended fields' }}</strong>.
            </p>
            @endif
        </div>
    </div>

    <!-- Footer with MetrixsMate Branding -->
    <div class="footer">
        <div class="footer-brand">MetrixsMate</div>
        <p class="footer-tagline">Empowering careers through personalized psychometric insights</p>
        <div class="footer-note">
            For personalized guidance, consult with a career counselor or psychologist<br>
            <span style="font-size: 8px;">This report is confidential and intended for the named individual only. © 2026 MetrixsMate. All rights reserved.</span>
        </div>
    </div>
</body>
</html>