<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Psychometric Assessment Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        /* Performance Level Color Classes */
        .exceptional { color: #15803d; background-color: #f0fdf4; border-color: #bbf7d0; }
        .exceptional-bg { background-color: #f0fdf4; }
        .exceptional-border { border-color: #bbf7d0; }
        .exceptional-progress { background-color: #10b981; }
        
        .high { color: #1d4ed8; background-color: #eff6ff; border-color: #bfdbfe; }
        .high-bg { background-color: #eff6ff; }
        .high-border { border-color: #bfdbfe; }
        .high-progress { background-color: #3b82f6; }
        
        .average { color: #d97706; background-color: #fffbeb; border-color: #fde68a; }
        .average-bg { background-color: #fffbeb; }
        .average-border { border-color: #fde68a; }
        .average-progress { background-color: #f59e0b; }
        
        .below-average { color: #ea580c; background-color: #fff7ed; border-color: #fed7aa; }
        .below-average-bg { background-color: #fff7ed; }
        .below-average-border { border-color: #fed7aa; }
        .below-average-progress { background-color: #f97316; }
        
        .low { color: #dc2626; background-color: #fef2f2; border-color: #fecaca; }
        .low-bg { background-color: #fef2f2; }
        .low-border { border-color: #fecaca; }
        .low-progress { background-color: #ef4444; }

        .header {
            text-align: center;
            border-bottom: 3px solid #1E3A8A;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1E3A8A;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-header {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #1E3A8A;
            margin-bottom: 20px;
        }
        .section-header h2 {
            margin: 0;
            color: #1E3A8A;
            font-size: 20px;
        }
        .section-header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .domain {
            margin-bottom: 25px;
            border: 1px solid;
            border-radius: 8px;
            padding: 20px;
        }
        .domain-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .domain-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        .domain-score {
            font-size: 24px;
            font-weight: bold;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }
        .domain-description {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 15px;
        }
        .insight-box {
            border-left: 4px solid;
            padding: 15px;
            margin-top: 10px;
        }
        .insight-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .insight-text {
            color: #374151;
            font-size: 13px;
        }
        .overview-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .overview-card {
            text-align: center;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .overview-score {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .overview-label {
            font-size: 14px;
            color: #6b7280;
        }
        .career-recommendations {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .career-title {
            color: #166534;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .career-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .career-item {
            background: white;
            border: 1px solid #bbf7d0;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            text-align: center;
        }
        .cognitive-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .page-break {
            page-break-before: always;
        }
        .predictive-insights {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        .insight-card {
            border: 1px solid;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .insight-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .insight-label {
            font-size: 12px;
            color: #6b7280;
        }
        .performance-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 10px;
        }
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4f46e5;
        }
        .brand-info {
            flex: 1;
        }
        .brand-logo {
            font-size: 24px;
            font-weight: 900;
            color: #4f46e5;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        .brand-tagline {
            font-size: 11px;
            color: #14b8a6;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .brand-divider {
            width: 2px;
            height: 40px;
            background: linear-gradient(to bottom, #4f46e5, #14b8a6);
            margin: 0 20px;
        }
        .report-title {
            text-align: right;
        }
        .report-title h1 {
            color: #1f2937;
            font-size: 22px;
            margin: 0;
            font-weight: 700;
        }
        .report-title p {
            color: #6b7280;
            font-size: 12px;
            margin: 5px 0 0 0;
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
            <h1>Assessment Report</h1>
            <p>{{ $user->name }}</p>
        </div>
    </div>

    <div class="header" style="border-bottom: none; margin-bottom: 20px; padding-bottom: 0;">
        <p style="color: #6b7280; font-size: 13px; margin: 0;">Generated on {{ $generatedDate }}</p>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <div class="section-header">
            <h2>Executive Summary</h2>
            <p>Overview of your assessment results across all domains</p>
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
                <div class="overview-score" style="color: #059669;">{{ $riasecResults['holland_code'] }}</div>
                <div class="overview-label">Career Interests<br>RIASEC Profile</div>
            </div>
            <div class="overview-card {{ $cognitiveLevel }}-bg {{ $cognitiveLevel }}-border">
                <div class="overview-score {{ $cognitiveLevel }}">{{ $overallScore['cognitive'] }}%</div>
                <div class="overview-label">Cognitive Abilities<br>Average Performance</div>
            </div>
        </div>
    </div>

    <!-- OCEAN Personality Assessment -->
    <div class="section">
        <div class="section-header">
            <h2>Personality Profile (OCEAN)</h2>
            <p>Your Big Five personality traits and their implications for work and life</p>
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
        <div style="margin-top: 30px;">
            <h3 style="color: #1E3A8A; margin-bottom: 15px;">Predictive Insights</h3>
            <div class="predictive-insights">
                @foreach(['growth_potential', 'organizational_fit_forecast', 'leadership_potential', 'innovation_index'] as $key)
                    @if(isset($oceanResults['predictive_insights'][$key]))
                        @php $insight = $oceanResults['predictive_insights'][$key]; @endphp
                        <div class="insight-card {{ $insight->performance_level }}-bg {{ $insight->performance_level }}-border">
                            <div class="insight-value {{ $insight->performance_level }}">{{ round($insight->percentage, 0) }}%</div>
                            <div class="insight-label">{{ $insight->name }}</div>
                            <div style="font-size: 10px; margin-top: 5px;" class="{{ $insight->performance_level }}">{{ $insight->performance_text }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- RIASEC Career Assessment -->
    <div class="section">
        <div class="section-header">
            <h2>Career Interest Profile (RIASEC)</h2>
            <p>Your career interests and recommended paths based on the Holland Code</p>
        </div>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #059669; margin-bottom: 15px;">Your Holland Code: {{ $riasecResults['holland_code'] }}</h3>
            <div class="cognitive-grid">
                @foreach($riasecResults['domains'] as $domain)
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
                </div>
                @endforeach
            </div>
        </div>

        @if($riasecResults['career_analysis'])
        <div class="career-recommendations">
            <div class="career-title">Recommended Career Paths</div>
            <div class="career-list">
                @foreach(explode(';', $riasecResults['career_analysis']->level_description) as $career)
                <div class="career-item">{{ trim($career) }}</div>
                @endforeach
            </div>
        </div>
        @endif

        @if($riasecResults['work_environment'])
        <div style="margin-top: 20px;">
            <h4 style="color: #059669; margin-bottom: 10px;">Ideal Work Environments:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach(explode(';', $riasecResults['work_environment']->level_description) as $environment)
                <li style="margin-bottom: 5px;">{{ trim($environment) }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Cognitive Assessment -->
    <div class="section">
        <div class="section-header">
            <h2>Cognitive Abilities</h2>
            <p>Your cognitive strengths across different reasoning domains</p>
        </div>

        <div class="cognitive-grid">
            @foreach($cognitiveResults['domains'] as $cognitive)
            <div class="domain {{ $cognitive->performance_level }}-bg {{ $cognitive->performance_level }}-border">
                <div class="domain-header">
                    <div class="domain-name">{{ $cognitive->name }}</div>
                    <div class="domain-score {{ $cognitive->performance_level }}">
                        {{ round($cognitive->percentage, 0) }}%
                        <span class="performance-badge {{ $cognitive->performance_level }}">{{ $cognitive->performance_text }}</span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill {{ $cognitive->performance_level }}-progress" style="width: {{ $cognitive->percentage }}%"></div>
                </div>
                <div class="domain-description">{{ $cognitive->description }}</div>
                <div class="insight-box {{ $cognitive->performance_level }}-border {{ $cognitive->performance_level }}-bg">
                    <div class="insight-title {{ $cognitive->performance_level }}">Performance Level</div>
                    <div class="insight-text">{{ $cognitive->level_description }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="background: #faf5ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 20px; margin-top: 20px;">
            <h4 style="color: #7c3aed; margin-bottom: 15px;">Cognitive Strengths Summary</h4>
            <p style="margin: 0; color: #374151;">
                Your cognitive profile shows an average performance of {{ round($cognitiveResults['average_score'], 0) }}% across all cognitive domains. 
                Your strongest areas are {{ $cognitiveResults['domains']->sortByDesc('percentage')->take(2)->pluck('name')->implode(' and ') }}.
            </p>
        </div>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Learning Styles Assessment -->
    <div class="section">
        <div class="section-header">
            <h2>Learning Styles</h2>
            <p>Your preferred methods for acquiring and processing information</p>
        </div>

        <div style="margin-bottom: 25px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px;">
            <h4 style="color: #166534; margin-top: 0;">Preferred Learning Style: <strong>{{ $learningStyles[0]['name'] }}</strong></h4>
            <p style="margin: 0; color: #374151;">You learn best through {{ strtolower($learningStyles[0]['name']) }} methods. Focus on leveraging this style in your development journey.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px;">
            @foreach($learningStyles as $style)
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center; {{ $style['name'] === $learningStyles[0]['name'] ? 'border: 2px solid #10b981; background: #f0fdf4;' : '' }}">
                <div style="font-size: 20px; font-weight: bold; color: #1f2937; margin-bottom: 5px;">{{ $style['name'] }}</div>
                <div style="background: #e5e7eb; border-radius: 4px; height: 8px; margin: 10px 0;">
                    <div style="background: #10b981; height: 100%; border-radius: 4px; width: {{ $style['score'] }}%;"></div>
                </div>
                <div style="font-size: 18px; font-weight: bold; color: #059669;">{{ $style['score'] }}%</div>
            </div>
            @endforeach
        </div>

        <div style="margin-top: 20px; background: #faf5ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 15px;">
            <h4 style="color: #7c3aed; margin-top: 0;">Learning Recommendations</h4>
            <ul style="margin: 0; padding-left: 20px; color: #374151;">
                <li>Leverage your {{ $learningStyles[0]['name'] }} learning preference in professional training</li>
                <li>Combine with secondary learning styles for comprehensive skill development</li>
                <li>Tailor your professional development activities to match your learning patterns</li>
                <li>Practice diverse learning methods to strengthen weaker learning modalities</li>
            </ul>
        </div>
    </div>

    <!-- Development Recommendations -->
    <div class="section">
        <div class="section-header">
            <h2>Personalized Development Plan</h2>
            <p>Tailored recommendations based on your assessment results</p>
        </div>

        <div class="cognitive-grid">
            <div>
                <h4 style="color: #1E3A8A; margin-bottom: 15px;">Immediate Focus Areas</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Leverage your {{ $learningStyles[0]['name'] }} learning style</li>
                    <li>Build on your top personality strengths</li>
                    <li>Align career goals with {{ $riasecResults['holland_code'] }} interests</li>
                    <li>Practice stress management and emotional regulation</li>
                </ul>
            </div>
            <div>
                <h4 style="color: #1E3A8A; margin-bottom: 15px;">Growth Opportunities</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Leadership development programs</li>
                    <li>Creative problem-solving workshops</li>
                    <li>Technical skill enhancement</li>
                    <li>Continuous learning initiatives</li>
                </ul>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <h4 style="color: #1E3A8A; margin-bottom: 15px;">Career Development Path</h4>
            @if($riasecResults['career_analysis'])
            <p style="margin: 0; color: #374151;">
                Based on your {{ $riasecResults['holland_code'] }} career profile and {{ $learningStyles[0]['name'] }} learning preference, 
                focus on roles that combine your strongest personality traits and career interests. 
                Consider pursuing roles in {{ explode(';', $riasecResults['career_analysis']->level_description)[0] ?? 'your recommended career paths' }} 
                where you can leverage both your natural strengths and preferred learning methods.
            </p>
            @endif
        </div>
    </div>

    <!-- Footer with MetrixsMate Branding -->
    <div class="footer" style="border-top: 2px solid #4f46e5; padding-top: 20px;">
        <div style="margin-bottom: 15px;">
            <div style="font-size: 14px; font-weight: 700; color: #4f46e5; margin-bottom: 5px;">MetrixsMate</div>
            <p style="margin: 0; color: #9ca3af; font-size: 11px;">Empowering careers through personalized psychometric insights</p>
        </div>
        <div style="border-top: 1px solid #e5e7eb; padding-top: 15px;">
            {{-- <p style="margin: 5px 0; color: #9ca3af; font-size: 11px;">📧 support@metrixsmate.com | 🌐 www.metrixsmate.com</p> --}}
            <p style="margin: 5px 0; color: #9ca3af; font-size: 11px;">For personalized guidance, consult with a career counselor or psychologist</p>
            <p style="margin: 10px 0 0 0; color: #d1d5db; font-size: 10px;">This report is confidential and intended for the named individual only. © 2026 MetrixsMate. All rights reserved.</p>
        </div>
    </div>
</body>
</html>