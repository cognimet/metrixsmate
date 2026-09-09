<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Certificate - {{ $certificate->full_name }}</title>

<style>
@page {
    size: A4 landscape;
    margin: 0;
}

* {
    margin: 0;
    padding: 0;
}

html, body {
    width: 100%;
    height: 100%;
    font-family: Georgia, serif;
}

/* MAIN */
.certificate {
    position: relative;
    width: 100%;
    height: 100%;
    border-left: 6px solid #4f46e5;
    border-right: 6px solid #4f46e5;
}

/* BORDER */
.certificate:before {
    content: "";
    position: absolute;
    top: 18px;
    left: 18px;
    right: 18px;
    bottom: 18px;
    border: 2px solid #d8d8e8;
}

.certificate:after {
    content: "";
    position: absolute;
    top: 22px;
    left: 22px;
    right: 22px;
    bottom: 22px;
    border: 1px solid #ede9fe;
}

/* CONTAINER */
.container {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    text-align: center;
}

/* HEADER */
.header {
    position: absolute;
    top: 50px;
    left: 0;
    right: 0;
}

.brand {
    font-size: 10px;
    letter-spacing: 5px;
    color: #4f46e5;
    font-weight: 800;
    font-family: Arial, sans-serif;
}

.title {
    font-size: 44px;
    color: #1e1b4b;
    margin: 8px 0;
}

.subtitle {
    font-size: 12px;
    color: #9ca3af;
    font-style: italic;
}

.divider {
    width: 140px;
    height: 2px;
    background: #4f46e5;
    margin: 12px auto;
}

/* MIDDLE CONTENT */
.middle {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-35%);
}

/* RECIPIENT */
.recipient-label {
    font-size: 10px;
    letter-spacing: 3px;
    color: #9ca3af;
}

.recipient-name {
    font-size: 36px;
    color: #4f46e5;
    border-bottom: 2px solid #d8d4f8;
    display: inline-block;
    padding-bottom: 6px;
    margin: 8px 0 20px;
}

/* ACHIEVEMENT */
.achievement {
    width: 70%;
    margin: 0 auto 25px;
    font-size: 13px;
    line-height: 1.6;
    color: #4b5563;
}

.achievement strong {
    color: #312e81;
}

/* SCORES */
.scores {
    width: 70%;
    margin: 0 auto;
}

.scores table {
    width: 100%;
    border-collapse: collapse;
}

.scores td {
    text-align: center;
    padding: 12px;
    border-right: 1px solid #e5e7eb;
}

.scores td:last-child {
    border-right: none;
}

.score-number {
    font-size: 28px;
    color: #4f46e5;
    font-weight: 700;
}

.score-label {
    font-size: 9px;
    letter-spacing: 1px;
    color: #9ca3af;
}

/* FOOTER */
.footer {
    position: absolute;
    bottom: 35px;
    left: 40px;
    right: 40px;
}

.footer table {
    width: 100%;
}

.footer td {
    width: 33.33%;
}

.left { text-align: left; }
.center { text-align: center; }
.right { text-align: right; }

.date-label {
    font-size: 9px;
    color: #9ca3af;
}

.date-value {
    font-size: 12px;
    font-weight: 600;
}

.cert-id {
    font-size: 8px;
    color: #9ca3af;
}

/* SEAL */
.seal {
    width: 65px;
    height: 65px;
    border: 2px solid #4f46e5;
    border-radius: 50%;
    margin: auto;
    line-height: 65px;
    font-weight: bold;
    color: #4f46e5;
}

/* RIGHT */
.org-name {
    font-size: 11px;
    font-weight: bold;
}

.verify-link {
    font-size: 8px;
    color: #9ca3af;
}

</style>
</head>

<body>

<div class="certificate">

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <div class="brand">METRIXSMATE</div>
            <div class="title">Certificate Of Excellence</div>
            <div class="subtitle">Professional Assessment Achievement</div>
            <div class="divider"></div>
        </div>

        <!-- MIDDLE -->
        <div class="middle">

            <div class="recipient-label">AWARDED TO</div>
            <div class="recipient-name">{{ $certificate->full_name }}</div>

            <div class="achievement">
                For successfully completing the comprehensive psychometric and cognitive assessment suite,
                demonstrating exceptional proficiency in  <strong> {{ $certificate->top_personality_trait }}</strong> 
                with Holland Code classification of 
                <strong> {{ $certificate->holland_code }}</strong> 
                and distinguished cognitive capabilities in 
                <strong>{{ $certificate->top_cognitive_strength }}</strong>.
            </div>

            <div class="scores">
                <table>
                    <tr>
                        <td>
                            <div class="score-number">{{ $certificate->ocean_score }}%</div>
                            <div class="score-label">PERSONALITY</div>
                        </td>
                        <td>
                            <div class="score-number">{{ $certificate->riasec_score }}%</div>
                            <div class="score-label">APTITUDE</div>
                        </td>
                        <td>
                            <div class="score-number">{{ $certificate->cognitive_score }}%</div>
                            <div class="score-label">COGNITIVE</div>
                        </td>
                        <td>
                            <div class="score-number">{{ $certificate->holland_code }}</div>
                            <div class="score-label">HOLLAND</div>
                        </td>
                    </tr>
                </table>
            </div>

        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <table>
            <tr>
                <td class="left">
                    <div class="date-label">DATE ISSUED</div>
                    <div class="date-value">{{ $certificate->issued_at->format('M d, Y') }}</div>
                    <div class="cert-id">#{{ $certificate->certificate_number }}</div>
                </td>

                <td class="center">
                    <div class="seal">MM</div>
                </td>

                <td class="right">
                    <div class="org-name">MetrixsMate</div>
                    <div class="verify-link">{{ url('/verify-certificate') }}</div>
                </td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>