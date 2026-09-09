<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ strtoupper($assessment_type) }} Assessment Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
            line-height: 1.7;
            color: #2c3e50;
            background-color: #ffffff;
            margin: 0;
            padding: 18px;
            font-size: 11px;
        }
        
        html {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Professional Color Palette */
        .exceptional { color: #0d7a3d; background-color: #e8f5e9; border-color: #81c784; }
        .exceptional-bg { background-color: #e8f5e9; }
        .exceptional-border { border-left-color: #4caf50; }
        .exceptional-progress { background-color: #4caf50; }
        
        .high { color: #0066cc; background-color: #e3f2fd; border-color: #64b5f6; }
        .high-bg { background-color: #e3f2fd; }
        .high-border { border-left-color: #2196f3; }
        .high-progress { background-color: #2196f3; }
        
        .average { color: #cc8800; background-color: #fff3e0; border-color: #ffb74d; }
        .average-bg { background-color: #fff3e0; }
        .average-border { border-left-color: #ff9800; }
        .average-progress { background-color: #ff9800; }
        
        .below-average { color: #d84315; background-color: #ffebee; border-color: #ef5350; }
        .below-average-bg { background-color: #ffebee; }
        .below-average-border { border-left-color: #f44336; }
        .below-average-progress { background-color: #f44336; }
        
        .low { color: #b71c1c; background-color: #ffcdd2; border-color: #ef5350; }
        .low-bg { background-color: #ffcdd2; }
        .low-border { border-left-color: #d32f2f; }
        .low-progress { background-color: #d32f2f; }

        /* Premium Header */
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            padding-bottom: 20px;
            padding-top: 12px;
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
            margin: 0 0 18px 0;
            padding: 8px;
            background-color: #fafafa;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        /* Section Styling */
        .section {
            margin-bottom: 22px;
            page-break-inside: avoid;
        }
        
        .section-header {
            background: linear-gradient(135deg, #f5f7fa 0%, #efefef 100%);
            padding: 14px 16px;
            border-left: 5px solid #1a237e;
            margin-bottom: 16px;
            border-radius: 3px;
            page-break-inside: avoid;
        }
        
        .section-header h2 {
            margin: 0;
            color: #1a237e;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }
        
        .section-header p {
            margin: 4px 0 0 0;
            color: #666;
            font-size: 10px;
            font-weight: 500;
        }

        /* Overview Grid */
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
            font-size: 36px;
            font-weight: 900;
            margin: 8px 0;
            letter-spacing: -1px;
        }
        
        .overview-label {
            font-size: 11px;
            color: #555;
            font-weight: 600;
            line-height: 1.4;
        }

        /* Domain Cards */
        .domain {
            margin-bottom: 18px;
            border: 1.5px solid;
            border-radius: 6px;
            padding: 16px;
            page-break-inside: avoid;
        }
        
        .domain-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        
        .domain-name {
            font-size: 13px;
            font-weight: 800;
            color: #1a237e;
            flex: 1;
        }
        
        .domain-score {
            font-size: 28px;
            font-weight: 900;
            margin-left: 12px;
            min-width: 60px;
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
            height: 6px;
            background-color: #e0e0e0;
            border-radius: 3px;
            margin: 10px 0;
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
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .insight-box {
            border-left: 4px solid;
            padding: 12px 14px;
            margin-top: 10px;
            border-radius: 3px;
            background-color: rgba(255,255,255,0.4);
        }
        
        .insight-title {
            font-weight: 800;
            margin-bottom: 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .insight-text {
            color: #333;
            font-size: 10px;
            line-height: 1.5;
        }

        /* Grid Layouts */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 15px;
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 16px;
        }
        
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        /* Compact Card */
        .card {
            border: 1.5px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            background-color: #fafafa;
            page-break-inside: avoid;
        }
        
        .card-title {
            font-weight: 700;
            font-size: 10px;
            margin-bottom: 6px;
            color: #1a237e;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .card-value {
            font-size: 22px;
            font-weight: 900;
            color: #1a237e;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
            margin-top: 0;
            padding-top: 0;
        }

        /* Footer */
        .footer {
            margin-top: 28px;
            border-top: 2px solid #1a237e;
            padding-top: 16px;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .footer-brand {
            font-size: 13px;
            font-weight: 900;
            color: #1a237e;
            margin-bottom: 6px;
        }
        
        .footer-tagline {
            font-size: 10px;
            color: #666;
            margin: 0 0 4px 0;
            font-weight: 500;
        }
        
        .footer-note {
            font-size: 9px;
            color: #999;
            margin-top: 8px;
            line-height: 1.4;
        }

        /* Headings */
        h3 {
            color: #1a237e;
            font-size: 12px;
            font-weight: 800;
            margin: 14px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        h4 {
            color: #424242;
            font-size: 11px;
            font-weight: 700;
            margin: 10px 0 8px 0;
        }

        /* Recommendations Lists */
        .rec-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .rec-list li {
            padding: 6px 0 6px 18px;
            position: relative;
            font-size: 10px;
            color: #333;
            line-height: 1.5;
        }
        
        .rec-list li:before {
            content: "▸";
            position: absolute;
            left: 0;
            color: #1a237e;
            font-weight: bold;
        }

        /* Print Optimization */
        @media print {
            body {
                margin: 0;
                padding: 14px;
            }
            .brand-header {
                margin-bottom: 24px;
            }
            .section {
                margin-bottom: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Header with MetrixsMate Branding -->
    <div class="brand-header">
        <div class="brand-info">
            <div class="brand-logo">MetrixsMate</div>
            <div class="brand-tagline">Discover Your Potential</div>
        </div>
        <div class="brand-divider"></div>
        <div class="report-title">
            <h1>{{ ucfirst($assessment_type) }} Assessment Report</h1>
            <p>{{ $user->name }}</p>
        </div>
    </div>

    <p class="generated-date">Generated on {{ $generatedDate }}</p>

    <!-- User Information -->
    <div class="section">
        <div style="background-color: #f5f7fa; padding: 14px; border-radius: 4px; margin-bottom: 15px; border: 1.5px solid #e0e0e0;">
            <p style="margin: 5px 0; font-size: 10px; color: #555;"><strong>Student:</strong> {{ $user->name }}</p>
            <p style="margin: 5px 0; font-size: 10px; color: #555;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin: 5px 0; font-size: 10px; color: #555;"><strong>Assessment Type:</strong> {{ strtoupper($assessment_type) }}</p>
            <p style="margin: 5px 0; font-size: 10px; color: #555;"><strong>Assessment Date:</strong> {{ $generatedDate }}</p>
        </div>
    </div>

    @if($domains->count() > 0)
    <!-- Overall Score -->
    <div class="section">
        <div class="section-header">
            <h2>Overall Performance</h2>
            <p>Assessment Results Summary</p>
        </div>
        <div class="card" style="text-align: center; grid-template-columns: none; padding: 24px; background: #f5f7fa; border-color: #1a237e; margin-bottom: 16px;">
            <div style="display: inline-block;">
                <div class="overview-score" style="font-size: 44px; color: #1a237e;">{{ $totalPercent }}%</div>
                <p style="margin: 8px 0 0 0; color: #555; font-size: 11px; font-weight: 600;">Overall Performance Score</p>
                <p style="margin: 3px 0; color: #999; font-size: 10px;">Average across all domains</p>
            </div>
        </div>
    </div>

    <!-- Domain Results -->
    <div class="section">
        <div class="section-header">
            <h2>Domain Results</h2>
            <p>Detailed Analysis of Assessment Domains</p>
        </div>

        @foreach($domains as $domain)
        @php
            $pct = round($domain->percentage, 2);
            $colorClass = $pct >= 85 ? 'exceptional' : ($pct >= 70 ? 'high' : ($pct >= 40 ? 'average' : ($pct >= 20 ? 'below-average' : 'low')));
        @endphp
        <div class="domain {{ $colorClass }}-bg {{ $colorClass }}-border">
            <div class="domain-header">
                <div class="domain-name">{{ $domain->name }}</div>
                <div class="domain-score {{ $colorClass }}">
                    {{ $pct }}%
                    <span class="performance-badge {{ $colorClass }}">
                        @if($pct >= 85) Exceptional
                        @elseif($pct >= 70) High
                        @elseif($pct >= 40) Average
                        @elseif($pct >= 20) Below Average
                        @else Low
                        @endif
                    </span>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill {{ $colorClass }}-progress" style="width: {{ $pct }}%;"></div>
            </div>
            @if($domain->description)
            <div class="domain-description">{{ $domain->description }}</div>
            @endif
            @if($domain->level_description)
            <div style="color: #4b5563; font-size: 13px; line-height: 1.5;">{{ $domain->level_description }}</div>
            @endif
            @if($domain->actionable_insights)
            <div class="insight-box {{ $colorClass }}-border {{ $colorClass }}-bg">
                <div class="insight-title {{ $colorClass }}">Actionable Insights</div>
                <div class="insight-text">{{ $domain->actionable_insights }}</div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($facets->count() > 0)
    <div class="section">
        <div class="section-header">
            <h2>Facets Analysis</h2>
            <p>Detailed Trait Breakdown</p>
        </div>
        <div class="grid-3">
            @foreach($facets as $facet)
            @php
                $pct = round($facet->percentage, 2);
                $colorClass = $pct >= 85 ? 'exceptional' : ($pct >= 70 ? 'high' : ($pct >= 40 ? 'average' : ($pct >= 20 ? 'below-average' : 'low')));
            @endphp
            <div class="card {{ $colorClass }}-bg {{ $colorClass }}-border">
                <div class="card-title">{{ $facet->name }}</div>
                <div class="card-value">{{ $pct }}%</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($ccsSkills->count() > 0)
    <div class="section">
        <div class="section-header">
            <h2>CCS Skills Assessment</h2>
            <p>Core Competencies and Skills</p>
        </div>
        <div class="grid-3">
            @foreach($ccsSkills as $skill)
            @php
                $pct = round($skill->percentage, 2);
                $colorClass = $pct >= 85 ? 'exceptional' : ($pct >= 70 ? 'high' : ($pct >= 40 ? 'average' : ($pct >= 20 ? 'below-average' : 'low')));
            @endphp
            <div class="card {{ $colorClass }}-bg {{ $colorClass }}-border">
                <div class="card-title">{{ $skill->name }}</div>
                <div class="card-value">{{ $pct }}%</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recommendations -->
    <div class="section">
        <div class="section-header">
            <h2>Recommendations</h2>
            <p>Personalized Development Insights</p>
        </div>
        <div class="grid">
            <div style="background: #e3f2fd; border: 1.5px solid #64b5f6; border-radius: 6px; padding: 14px;">
                <h4 style="color: #0066cc; margin-top: 0; margin-bottom: 10px;">Immediate Focus Areas</h4>
                <ul class="rec-list">
                    <li>Build on your strongest domains</li>
                    <li>Address performance gaps systematically</li>
                    <li>Develop targeted improvement strategies</li>
                </ul>
            </div>
            <div style="background: #e8f5e9; border: 1.5px solid #81c784; border-radius: 6px; padding: 14px;">
                <h4 style="color: #0d7a3d; margin-top: 0; margin-bottom: 10px;">Growth Opportunities</h4>
                <ul class="rec-list">
                    <li>Leverage existing strengths</li>
                    <li>Participate in skill development programs</li>
                    <li>Seek mentorship and guidance</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-brand">MetrixsMate</div>
        <p class="footer-tagline">Empowering careers through personalized psychometric insights</p>
        <p class="footer-tagline">For personalized guidance, consult with a career counselor or psychologist</p>
        <p class="footer-note">This report is confidential and intended for the named individual only. © 2026 MetrixsMate. All rights reserved.</p>
    </div>
</body>
</html>
