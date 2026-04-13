#!/usr/bin/env python3
"""Generate MetrixsMate pitch deck for Prince Education Hub, Sikar."""

from pptx import Presentation
from pptx.util import Inches, Pt, Emu
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

# --- Constants ---
NAVY = RGBColor(0x1E, 0x3A, 0x5F)
TEAL = RGBColor(0x0D, 0x94, 0x88)
WHITE = RGBColor(0xFF, 0xFF, 0xFF)
LIGHT_GRAY = RGBColor(0xF8, 0xFA, 0xFC)
DARK_GRAY = RGBColor(0x33, 0x33, 0x33)
MID_GRAY = RGBColor(0x66, 0x66, 0x66)
RED_ACCENT = RGBColor(0xDC, 0x26, 0x26)
GREEN_ACCENT = RGBColor(0x16, 0xA3, 0x4A)
AMBER_ACCENT = RGBColor(0xD9, 0x77, 0x06)
BLUE_ACCENT = RGBColor(0x3B, 0x82, 0xF6)
PURPLE_ACCENT = RGBColor(0x8B, 0x5C, 0xF6)
PRINCE_MAROON = RGBColor(0x7C, 0x2D, 0x12)
SLIDE_W = Inches(13.333)
SLIDE_H = Inches(7.5)

prs = Presentation()
prs.slide_width = SLIDE_W
prs.slide_height = SLIDE_H


# --- Helpers ---
def add_confidential(slide):
    txBox = slide.shapes.add_textbox(Inches(0.5), Inches(7.0), Inches(12.3), Inches(0.4))
    tf = txBox.text_frame; tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "CONFIDENTIAL \u2014 Prepared exclusively for Prince Education Hub, Sikar"
    p.font.size = Pt(8); p.font.color.rgb = RGBColor(0x99, 0x99, 0x99)
    p.font.italic = True; p.alignment = PP_ALIGN.CENTER

def add_slide_number(slide, num):
    txBox = slide.shapes.add_textbox(Inches(12.5), Inches(7.0), Inches(0.7), Inches(0.4))
    tf = txBox.text_frame
    p = tf.paragraphs[0]; p.text = str(num)
    p.font.size = Pt(8); p.font.color.rgb = RGBColor(0x99, 0x99, 0x99)
    p.alignment = PP_ALIGN.RIGHT

def fill_slide_bg(slide, color):
    fill = slide.background.fill; fill.solid(); fill.fore_color.rgb = color

def add_rect(slide, left, top, width, height, fill_color, border_color=None):
    shape = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, left, top, width, height)
    shape.fill.solid(); shape.fill.fore_color.rgb = fill_color
    if border_color:
        shape.line.color.rgb = border_color; shape.line.width = Pt(1)
    else:
        shape.line.fill.background()
    return shape

def add_textbox(slide, left, top, width, height):
    txBox = slide.shapes.add_textbox(left, top, width, height)
    txBox.text_frame.word_wrap = True
    return txBox.text_frame

def add_para(tf, text, size=14, color=DARK_GRAY, bold=False, italic=False, alignment=PP_ALIGN.LEFT, space_after=Pt(6), space_before=Pt(0)):
    if tf.paragraphs[0].text == "" and len(tf.paragraphs) == 1:
        p = tf.paragraphs[0]
    else:
        p = tf.add_paragraph()
    p.text = text; p.font.size = Pt(size); p.font.color.rgb = color
    p.font.bold = bold; p.font.italic = italic; p.alignment = alignment
    p.space_after = space_after; p.space_before = space_before
    return p

def add_bullet(tf, text, size=13, color=DARK_GRAY, bold=False, level=0):
    p = tf.add_paragraph(); p.text = text; p.font.size = Pt(size)
    p.font.color.rgb = color; p.font.bold = bold; p.level = level
    p.space_after = Pt(4); p.space_before = Pt(2)
    return p

def add_card(slide, left, top, width, height, title, value, value_color=NAVY, subtitle=None):
    add_rect(slide, left, top, width, height, WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    tf = add_textbox(slide, left + Inches(0.15), top + Inches(0.12), width - Inches(0.3), Inches(0.3))
    add_para(tf, title, size=9, color=MID_GRAY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.15), top + Inches(0.4), width - Inches(0.3), Inches(0.5))
    add_para(tf2, value, size=20, color=value_color, bold=True)
    if subtitle:
        tf3 = add_textbox(slide, left + Inches(0.15), top + Inches(0.8), width - Inches(0.3), Inches(0.3))
        add_para(tf3, subtitle, size=9, color=MID_GRAY)

def section_heading(slide, text, subtitle=None):
    add_rect(slide, Inches(0), Inches(0), SLIDE_W, Inches(1.3), NAVY)
    tf = add_textbox(slide, Inches(0.8), Inches(0.25), Inches(11), Inches(0.6))
    add_para(tf, text, size=28, color=WHITE, bold=True)
    if subtitle:
        tf2 = add_textbox(slide, Inches(0.8), Inches(0.8), Inches(11), Inches(0.4))
        add_para(tf2, subtitle, size=13, color=RGBColor(0xA0, 0xD2, 0xDB), italic=True)

def add_table(slide, left, top, width, rows_data, col_widths, header_color=NAVY):
    rows = len(rows_data); cols = len(rows_data[0])
    table_shape = slide.shapes.add_table(rows, cols, left, top, width, Inches(0.38 * rows))
    table = table_shape.table
    for i, w in enumerate(col_widths):
        table.columns[i].width = w
    for r, row in enumerate(rows_data):
        for c, cell_text in enumerate(row):
            cell = table.cell(r, c); cell.text = ""
            p = cell.text_frame.paragraphs[0]; p.text = str(cell_text)
            p.font.size = Pt(11); p.space_after = Pt(2); p.space_before = Pt(2)
            if r == 0:
                p.font.color.rgb = WHITE; p.font.bold = True
                cell.fill.solid(); cell.fill.fore_color.rgb = header_color
            else:
                p.font.color.rgb = DARK_GRAY
                cell.fill.solid(); cell.fill.fore_color.rgb = WHITE if r % 2 == 1 else LIGHT_GRAY
    return table


# ============================================================
# SLIDE 1 \u2014 Cover
# ============================================================
n = 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, NAVY)
add_rect(slide, Inches(0), Inches(3.2), SLIDE_W, Inches(0.06), TEAL)

tf = add_textbox(slide, Inches(1.5), Inches(1.3), Inches(10), Inches(1.2))
add_para(tf, "MetrixsMate", size=52, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)
tf2 = add_textbox(slide, Inches(2), Inches(2.5), Inches(9), Inches(0.5))
add_para(tf2, "AI-Powered Psychometric Assessment & Career Intelligence Platform", size=18, color=TEAL, alignment=PP_ALIGN.CENTER)
tf3 = add_textbox(slide, Inches(2), Inches(3.5), Inches(9), Inches(0.5))
add_para(tf3, "Assess.  Align.  Achieve.", size=20, color=RGBColor(0xA0, 0xD2, 0xDB), bold=True, italic=True, alignment=PP_ALIGN.CENTER)
tf4 = add_textbox(slide, Inches(1.5), Inches(4.4), Inches(10), Inches(0.8))
add_para(tf4, "A Tailored Proposal for", size=14, color=WHITE, alignment=PP_ALIGN.CENTER)
add_para(tf4, "Prince Education Hub, Sikar", size=24, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)
tf5 = add_textbox(slide, Inches(3), Inches(5.7), Inches(7), Inches(0.4))
add_para(tf5, "CONFIDENTIAL", size=12, color=RGBColor(0xFF, 0x99, 0x99), bold=True, alignment=PP_ALIGN.CENTER)
add_slide_number(slide, n)


# ============================================================
# SLIDE 2 \u2014 About Prince Edu Hub
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "About Prince Education Hub", "India\u2019s Fastest-Growing Educational Conglomerate \u2014 Sikar, Rajasthan")

tf = add_textbox(slide, Inches(0.8), Inches(1.6), Inches(7.5), Inches(4.5))
add_bullet(tf, "13 lush green campuses across 250+ acres in Sikar & Jaipur", size=13, bold=True)
add_bullet(tf, "All India Rank 1 by Ministry of HRD, Government of India", size=13)
add_bullet(tf, "Awards: Best CBSE School, Best Residential Campus, Centre of Excellence", size=13)
add_bullet(tf, "Management: Dr. Piyush Sunda (Chairman) & Mr. Jogendra Sunda (Director)", size=13)
add_bullet(tf, "25+ years of academic legacy transforming education in Rajasthan", size=13)
add_bullet(tf, "Students consistently excel in IIT-JEE, NEET, NDA, UPSC, CLAT, CUET, CA, CBSE & RBSE", size=13)
add_bullet(tf, "Mobile apps: Prince Eduhub, PCP Sikar, Floreto \u2014 already tech-forward", size=13)

add_card(slide, Inches(8.8), Inches(1.7), Inches(1.8), Inches(1.05), "CAMPUSES", "13", TEAL)
add_card(slide, Inches(10.9), Inches(1.7), Inches(1.8), Inches(1.05), "ACRES", "250+", NAVY)
add_card(slide, Inches(8.8), Inches(3.0), Inches(1.8), Inches(1.05), "EXPERIENCE", "25+ Yrs", GREEN_ACCENT)
add_card(slide, Inches(10.9), Inches(3.0), Inches(1.8), Inches(1.05), "INSTITUTIONS", "9+", AMBER_ACCENT)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 3 \u2014 The Prince Ecosystem (Institution Map)
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "The Prince Edu Hub Ecosystem", "9+ institutions under one conglomerate \u2014 each needing unified intelligence")

institutions = [
    ("PCP \u2014 Prince Career Pioneer", "IIT-JEE | NEET | Olympiad Coaching", "pcpsikar.com", BLUE_ACCENT),
    ("Prince Academy CBSE School", "K-12 CBSE Residential School", "princecbse.com", GREEN_ACCENT),
    ("Prince Sainik School", "Military-Style Residential School", "princesainikschool.com", NAVY),
    ("Floreto World School (ICSE)", "ICSE International Curriculum", "floretoworldschool.com", PURPLE_ACCENT),
    ("Prince RBSE School", "Rajasthan Board K-12 School", "princeschoolsikar.com", AMBER_ACCENT),
    ("Prince NDA Academy", "NDA & Defence Entrance Prep", "princendaacademy.com", RGBColor(0x64, 0x74, 0x8B)),
    ("Prince Defence Academy", "Army, Navy, Air Force Training", "princedefence.com", RGBColor(0x0E, 0x7A, 0x90)),
    ("Prince College (B.Ed.)", "Co-Ed PG College", "princecollegesikar.com", RGBColor(0xBE, 0x5A, 0x1E)),
    ("Pre-Foundation Programme", "Classes 7-10 Competitive Prep", "princeeduhub.com", RGBColor(0xE1, 0x1D, 0x48)),
]

for i, (name, desc, url, color) in enumerate(institutions):
    row = i // 3; col = i % 3
    left = Inches(0.8) + col * Inches(4.1)
    top = Inches(1.5) + row * Inches(1.7)
    add_rect(slide, left, top, Inches(3.8), Inches(1.45), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, left, top, Inches(0.08), Inches(1.45), color)
    tf = add_textbox(slide, left + Inches(0.25), top + Inches(0.1), Inches(3.3), Inches(0.4))
    add_para(tf, name, size=12, color=NAVY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.25), top + Inches(0.5), Inches(3.3), Inches(0.35))
    add_para(tf2, desc, size=10, color=MID_GRAY)
    tf3 = add_textbox(slide, left + Inches(0.25), top + Inches(0.9), Inches(3.3), Inches(0.35))
    add_para(tf3, url, size=9, color=color, italic=True)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 4 \u2014 Prince\u2019s Stellar Achievements
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Prince Edu Hub \u2014 Achievements That Speak Volumes")

achievements_left = [
    ("75", "Students \u2265 99%tile \u2014 JEE Main 2026"),
    ("854", "Govt. MBBS Selections \u2014 NEET 2025"),
    ("409", "IIT-JEE Selections 2025"),
    ("AIR-1", "NEET UG 2024 (First from Sikar)"),
    ("800+", "Govt. MBBS Selections \u2014 NEET 2024"),
    ("300+", "IIT-JEE Advanced Selections 2024"),
]
achievements_right = [
    ("ALL INDIA TOPPER", "Khushi Shekhawat \u2014 12th CBSE 2025"),
    ("6 Students", "\u2265 99% (All India Top 10 CBSE)"),
    ("563 Students", "\u2265 90% in CBSE 2025"),
    ("46", "State Merits in 10th & 12th RBSE 2025"),
    ("527 + 455", "Above 90% in RBSE 12th + 10th 2025"),
    ("597 Medals", "National Science Olympiad"),
]

for i, (val, desc) in enumerate(achievements_left):
    top = Inches(1.55) + i * Inches(0.85)
    add_rect(slide, Inches(0.8), top, Inches(5.8), Inches(0.7), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, Inches(0.8), top, Inches(0.08), Inches(0.7), TEAL)
    tf = add_textbox(slide, Inches(1.05), top + Inches(0.08), Inches(1.5), Inches(0.5))
    add_para(tf, val, size=14, color=TEAL, bold=True)
    tf2 = add_textbox(slide, Inches(2.6), top + Inches(0.12), Inches(3.8), Inches(0.5))
    add_para(tf2, desc, size=11, color=DARK_GRAY)

for i, (val, desc) in enumerate(achievements_right):
    top = Inches(1.55) + i * Inches(0.85)
    add_rect(slide, Inches(6.9), top, Inches(5.8), Inches(0.7), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, Inches(6.9), top, Inches(0.08), Inches(0.7), AMBER_ACCENT)
    tf = add_textbox(slide, Inches(7.15), top + Inches(0.08), Inches(2.2), Inches(0.5))
    add_para(tf, val, size=14, color=AMBER_ACCENT, bold=True)
    tf2 = add_textbox(slide, Inches(9.4), top + Inches(0.12), Inches(3.1), Inches(0.5))
    add_para(tf2, desc, size=11, color=DARK_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 5 \u2014 The Conglomerate Challenge
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "The Conglomerate Challenge", "When excellence at scale meets operational complexity")

challenges = [
    ("9+ Institutions, Zero Unified Student Intelligence", "Each institution tracks students differently \u2014 no single view of talent across the group"),
    ("No Scientific Career Guidance at Scale", "Thousands of students choose JEE vs. NEET vs. NDA vs. CLAT based on family pressure, not aptitude data"),
    ("Coaching \u2192 School \u2192 College Disconnect", "PCP, CBSE School & College operate in silos \u2014 student insights don't flow between them"),
    ("Placement & Outcome Tracking Gap", "With 854+ NEET & 409+ JEE selections, no system scientifically matches student potential to exam stream"),
    ("Manual Counselling Can\u2019t Scale", "Individual counselling for 10,000+ students across 13 campuses is physically impossible"),
    ("Untapped Data Goldmine", "All India Rank 1, AIR-1 NEET, AI Topper CBSE \u2014 but no data system to replicate success patterns"),
]

for i, (title, desc) in enumerate(challenges):
    row = i // 2; col = i % 2
    left = Inches(0.8) + col * Inches(6.2)
    top = Inches(1.6) + row * Inches(1.7)
    add_rect(slide, left, top, Inches(5.8), Inches(1.45), LIGHT_GRAY, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, left, top, Inches(0.08), Inches(1.45), RED_ACCENT)
    tf = add_textbox(slide, left + Inches(0.25), top + Inches(0.12), Inches(5.3), Inches(0.4))
    add_para(tf, title, size=13, color=NAVY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.25), top + Inches(0.55), Inches(5.3), Inches(0.75))
    add_para(tf2, desc, size=11, color=MID_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 6 \u2014 Problem Statement
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "The Core Problem")

add_rect(slide, Inches(0.8), Inches(1.6), Inches(11.7), Inches(1.3), RGBColor(0xEF, 0xF6, 0xFF), TEAL)
tf = add_textbox(slide, Inches(1.1), Inches(1.7), Inches(11.1), Inches(1.1))
add_para(tf, '"Prince Edu Hub produces AIR-1 toppers, All India CBSE toppers, and 800+ MBBS selections every year \u2014 but has no scientific system to identify which of its 10,000+ students across 9 institutions is best suited for JEE, NEET, NDA, CLAT, or any other career path BEFORE they commit."', size=14, color=NAVY, italic=True, alignment=PP_ALIGN.CENTER)

pains = [
    ("1. Stream Selection Gamble", "A Class 10 student joins PCP for NEET but is cognitively wired for engineering. 2 years wasted. No aptitude data prevented this."),
    ("2. Cross-Institution Blindness", "The CBSE school doesn\u2019t know which students should transition to PCP coaching. The Defence Academy can\u2019t identify high-aptitude NDA candidates from RBSE school."),
    ("3. Success Pattern Mystery", "854 NEET & 409 JEE selections in 2025 \u2014 but what personality traits, aptitudes, and interests do these toppers share? Without data, you can\u2019t systematically replicate triumph."),
]

for i, (title, desc) in enumerate(pains):
    top = Inches(3.3) + i * Inches(1.25)
    add_rect(slide, Inches(0.8), top, Inches(11.7), Inches(1.05), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, Inches(0.8), top, Inches(0.08), Inches(1.05), AMBER_ACCENT)
    tf = add_textbox(slide, Inches(1.1), top + Inches(0.08), Inches(11.1), Inches(0.35))
    add_para(tf, title, size=13, color=NAVY, bold=True)
    tf2 = add_textbox(slide, Inches(1.1), top + Inches(0.42), Inches(11.1), Inches(0.55))
    add_para(tf2, desc, size=11, color=MID_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 7 \u2014 Solution Overview
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Introducing MetrixsMate", "One Platform. Three Assessments. Unlimited Insight. All 9 Institutions Unified.")

tf = add_textbox(slide, Inches(0.8), Inches(1.5), Inches(11.5), Inches(0.6))
add_para(tf, "MetrixsMate is an AI-powered psychometric assessment & career intelligence platform that scientifically profiles every student across three scientific dimensions:", size=14, color=DARK_GRAY)

pillars = [
    ("OCEAN", "Big Five Personality", "Who they are", BLUE_ACCENT),
    ("RIASEC", "Career Interest Mapping", "What they should do", GREEN_ACCENT),
    ("Cognitive", "Aptitude & Reasoning", "How they think", PURPLE_ACCENT),
]

for i, (name, sub, desc, color) in enumerate(pillars):
    left = Inches(0.8) + i * Inches(4.1)
    add_rect(slide, left, Inches(2.4), Inches(3.8), Inches(2.0), color)
    tf = add_textbox(slide, left + Inches(0.3), Inches(2.55), Inches(3.2), Inches(0.5))
    add_para(tf, name, size=26, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)
    tf2 = add_textbox(slide, left + Inches(0.3), Inches(3.1), Inches(3.2), Inches(0.4))
    add_para(tf2, sub, size=13, color=WHITE, alignment=PP_ALIGN.CENTER)
    tf3 = add_textbox(slide, left + Inches(0.3), Inches(3.55), Inches(3.2), Inches(0.4))
    add_para(tf3, "\u201C" + desc + "\u201D", size=12, color=RGBColor(0xE0, 0xE0, 0xE0), italic=True, alignment=PP_ALIGN.CENTER)

add_rect(slide, Inches(4), Inches(4.8), Inches(5.3), Inches(0.06), TEAL)
tf = add_textbox(slide, Inches(2.5), Inches(5.0), Inches(8.3), Inches(0.5))
add_para(tf, "AI Engine  \u2192  Career Path  |  Stream Recommendation  |  Topper DNA Mapping  |  PDF Reports", size=14, color=TEAL, bold=True, alignment=PP_ALIGN.CENTER)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 8 \u2014 Key Features
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "What MetrixsMate Delivers")

features = [
    ("OCEAN Personality Assessment", "Scientifically validated Big Five personality profiling"),
    ("RIASEC Career Mapping", "Holland-code based career interest inventory"),
    ("Cognitive Aptitude Test", "IQ, reasoning & problem-solving evaluation"),
    ("AI Stream Recommender", "JEE vs NEET vs NDA vs CLAT \u2014 AI suggests the right path"),
    ("Cross-Institution Dashboard", "One admin view for all 9 institutions & 13 campuses"),
    ("Detailed PDF Reports", "Downloadable profiles for counsellors, parents & students"),
    ("Hindi + English Support", "Critical for Rajasthan \u2014 assessments in both languages"),
    ("Topper DNA Analytics", "Identify personality patterns of your top performers"),
]

for i, (title, desc) in enumerate(features):
    row = i // 2; col = i % 2
    left = Inches(0.8) + col * Inches(6.2)
    top = Inches(1.6) + row * Inches(1.3)
    add_rect(slide, left, top, Inches(5.8), Inches(1.1), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, left, top, Inches(0.08), Inches(1.1), TEAL)
    tf = add_textbox(slide, left + Inches(0.25), top + Inches(0.1), Inches(5.3), Inches(0.35))
    add_para(tf, title, size=13, color=NAVY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.25), top + Inches(0.48), Inches(5.3), Inches(0.5))
    add_para(tf2, desc, size=11, color=MID_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 9 \u2014 How It Fits EACH Institution
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "MetrixsMate \u00d7 Prince Edu Hub \u2014 Institution-by-Institution Fit")

fit_data = [
    ["Institution", "MetrixsMate Application", "Key Benefit"],
    ["PCP (JEE/NEET)", "Predict JEE vs NEET aptitude before enrollment", "Higher selection ratio"],
    ["CBSE School", "Career guidance from Class 7 onwards", "Stream selection in Class 11"],
    ["Sainik School", "Identify leadership & discipline traits", "NDA aptitude screening"],
    ["Floreto ICSE", "Personality-based learning paths", "Holistic student profiling"],
    ["RBSE School", "Aptitude mapping for state board students", "Board + competitive readiness"],
    ["NDA Academy", "Cognitive + personality fit for defence", "Targeted NDA preparation"],
    ["Defence Academy", "Mental aptitude & reasoning assessment", "Service branch matching"],
    ["B.Ed. College", "Teacher personality profiling", "Teaching aptitude evaluation"],
    ["Pre-Foundation", "Early talent identification (Class 7-10)", "Right stream from Day 1"],
]

add_table(slide, Inches(0.6), Inches(1.5), Inches(12.1), fit_data,
          [Inches(2.8), Inches(5.2), Inches(4.1)])

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 10 \u2014 The \u201CStream Selection\u201D Killer Use Case
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "The Game-Changer: Scientific Stream Selection", "Solve the #1 decision every student & parent agonizes over")

# Without
add_rect(slide, Inches(0.8), Inches(1.6), Inches(5.5), Inches(4.2), RGBColor(0xFF, 0xF7, 0xED), RED_ACCENT)
tf = add_textbox(slide, Inches(1.0), Inches(1.7), Inches(5.1), Inches(0.4))
add_para(tf, "\u274C  WITHOUT METRIXSMATE", size=14, color=RED_ACCENT, bold=True)
tf2 = add_textbox(slide, Inches(1.0), Inches(2.2), Inches(5.1), Inches(3.3))
add_bullet(tf2, "Student joins PCP for NEET because \u201Cparent said so\u201D", size=12, color=DARK_GRAY)
add_bullet(tf2, "After 1 year, fails to cope \u2014 switches to JEE", size=12, color=DARK_GRAY)
add_bullet(tf2, "2 years & \u20B92-5 lakhs wasted", size=12, color=DARK_GRAY)
add_bullet(tf2, "Student\u2019s confidence destroyed", size=12, color=DARK_GRAY)
add_bullet(tf2, "Prince\u2019s selection numbers don\u2019t grow", size=12, color=DARK_GRAY)
add_bullet(tf2, "Parent blames the institution", size=12, color=DARK_GRAY)

# With
add_rect(slide, Inches(6.9), Inches(1.6), Inches(5.5), Inches(4.2), RGBColor(0xEC, 0xFD, 0xF5), GREEN_ACCENT)
tf = add_textbox(slide, Inches(7.1), Inches(1.7), Inches(5.1), Inches(0.4))
add_para(tf, "\u2705  WITH METRIXSMATE", size=14, color=GREEN_ACCENT, bold=True)
tf2 = add_textbox(slide, Inches(7.1), Inches(2.2), Inches(5.1), Inches(3.3))
add_bullet(tf2, "Student takes 3 assessments in 45 minutes", size=12, color=DARK_GRAY)
add_bullet(tf2, "AI recommends: \u201CHigh Investigative + Strong Reasoning = JEE fit\u201D", size=12, color=DARK_GRAY)
add_bullet(tf2, "Counsellor shows data-backed report to parent", size=12, color=DARK_GRAY)
add_bullet(tf2, "Student joins the RIGHT stream from Day 1", size=12, color=DARK_GRAY)
add_bullet(tf2, "Selection probability jumps dramatically", size=12, color=DARK_GRAY)
add_bullet(tf2, "Parent trusts Prince\u2019s guidance. Refers others.", size=12, color=DARK_GRAY)

add_rect(slide, Inches(3), Inches(6.1), Inches(7.3), Inches(0.6), TEAL)
tf = add_textbox(slide, Inches(3.3), Inches(6.15), Inches(6.7), Inches(0.5))
add_para(tf, "Move from gut-feel counselling to AI-powered career science", size=15, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 11 \u2014 Use Cases by Stakeholder
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Who Benefits? Every Stakeholder.")

use_cases = [
    ("Management / Directors", NAVY, [
        "Unified talent intelligence across 9 institutions",
        "Data-driven enrollment & stream allocation",
        "Scientifically replicable success patterns",
    ]),
    ("Faculty & Counsellors", TEAL, [
        "Understand each student\u2019s personality & aptitude",
        "Identify at-risk students before results suffer",
        "Replace guesswork with AI-backed guidance",
    ]),
    ("Students (10,000+)", PURPLE_ACCENT, [
        "Scientific self-awareness: personality + career fit",
        "AI-recommended career paths aligned to strengths",
        "Verified digital certificates for portfolios",
    ]),
    ("Parents", AMBER_ACCENT, [
        "Data-backed confidence in stream selection",
        "\u201CPrince doesn\u2019t guess \u2014 they scientifically guide\u201D",
        "PDF reports they can see and understand",
    ]),
]

for i, (title, color, bullets) in enumerate(use_cases):
    col = i % 2; row = i // 2
    left = Inches(0.8) + col * Inches(6.2)
    top = Inches(1.6) + row * Inches(2.7)
    add_rect(slide, left, top, Inches(5.8), Inches(2.4), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, left, top, Inches(5.8), Inches(0.5), color)
    tf = add_textbox(slide, left + Inches(0.3), top + Inches(0.07), Inches(5.2), Inches(0.4))
    add_para(tf, title, size=15, color=WHITE, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.3), top + Inches(0.6), Inches(5.2), Inches(1.7))
    for b in bullets:
        add_bullet(tf2, "\u2022  " + b, size=12, color=DARK_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 12 \u2014 Automation & Efficiency
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Save Time. Reduce Errors. Scale Across 13 Campuses.")

eff_data = [
    ["Manual Process", "With MetrixsMate", "Impact"],
    ["Paper career aptitude tests", "Online OCEAN+RIASEC+Cognitive (auto-scored)", "95% time saved"],
    ["1:1 career counselling per student", "AI batch recommendations for 10,000+ students", "70% reduction"],
    ["Excel tracking per institution", "Unified cross-institution dashboard", "One view, 9 institutions"],
    ["Manual parent reports", "One-click PDF report generation", "90% effort saved"],
    ["Guesswork stream selection", "Data-backed AI stream matcher", "Higher selection rates"],
    ["Separate systems per campus", "Single platform, 13 campuses", "Zero fragmentation"],
]

add_table(slide, Inches(0.8), Inches(1.6), Inches(11.7), eff_data,
          [Inches(3.5), Inches(5.0), Inches(3.2)])

add_rect(slide, Inches(3), Inches(5.5), Inches(7.3), Inches(0.8), TEAL)
tf = add_textbox(slide, Inches(3.3), Inches(5.6), Inches(6.7), Inches(0.6))
add_para(tf, "For 10,000+ students, MetrixsMate saves an estimated 15,000+ staff-hours/year", size=16, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 13 \u2014 Topper DNA Analytics
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Topper DNA Analytics \u2014 Replicate Your Own Success", "Turn 854 NEET & 409 JEE selections into a repeatable science")

sections = [
    ("Discover Topper Patterns", TEAL, [
        "Profile your top 100 NEET selections on OCEAN traits",
        "What personality type dominates AIR-1 achievers?",
        "Map cognitive strengths of JEE Advanced qualifiers",
        "Build a \u201CTopper DNA Blueprint\u201D unique to Prince",
    ]),
    ("Predict Future Toppers", BLUE_ACCENT, [
        "Score incoming students against Topper DNA",
        "Flag high-potential students in Week 1 of enrollment",
        "Recommend JEE, NEET, NDA, CLAT based on match %",
        "Identify hidden gems in RBSE & CBSE schools early",
    ]),
    ("Optimise Outcomes", GREEN_ACCENT, [
        "Route right students to right streams = higher selections",
        "Track which personality traits predict dropout risk",
        "Generate insights for faculty on student learning styles",
        "Share talent dashboards with management monthly",
    ]),
]

for i, (title, color, bullets) in enumerate(sections):
    left = Inches(0.8) + i * Inches(4.1)
    top = Inches(1.5)
    add_rect(slide, left, top, Inches(3.8), Inches(4.5), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, left, top, Inches(3.8), Inches(0.5), color)
    tf = add_textbox(slide, left + Inches(0.2), top + Inches(0.07), Inches(3.4), Inches(0.4))
    add_para(tf, title, size=13, color=WHITE, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.2), top + Inches(0.65), Inches(3.4), Inches(3.5))
    for b in bullets:
        add_bullet(tf2, "\u2022  " + b, size=11, color=DARK_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 14 \u2014 Competitive Advantage
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "How MetrixsMate Amplifies Prince\u2019s Dominance")

advantages = [
    ("Selection Ratio Booster", "Right student \u2192 right stream = more selections per batch. Improve JEE 1:4 and NEET 1:5 ratios further."),
    ("Enrollment Magnet", '"We scientifically assess your child before enrolling" \u2014 a parent can\u2019t resist this promise.'),
    ("NAAC & Ranking Firepower", "Data-driven student outcomes strengthen accreditation narrative for the college wing."),
    ("Brand Differentiation", '"India\u2019s first education conglomerate with AI-powered psychometric career guidance at scale."'),
    ("Parent Trust Multiplier", "PDF reports with scientific data = parents become brand ambassadors. Referrals skyrocket."),
    ("Cross-Institution Upsell", "CBSE student\u2019s data shows JEE aptitude \u2192 seamless enrollment into PCP. Data-driven internal pipeline."),
]

for i, (title, desc) in enumerate(advantages):
    row = i // 2; col = i % 2
    left = Inches(0.8) + col * Inches(6.2)
    top = Inches(1.6) + row * Inches(1.6)
    add_rect(slide, left, top, Inches(5.8), Inches(1.35), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, left, top, Inches(0.08), Inches(1.35), GREEN_ACCENT)
    tf = add_textbox(slide, left + Inches(0.25), top + Inches(0.1), Inches(5.3), Inches(0.35))
    add_para(tf, "\u2713  " + title, size=13, color=NAVY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.25), top + Inches(0.5), Inches(5.3), Inches(0.7))
    add_para(tf2, desc, size=11, color=MID_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 15 \u2014 Case Study
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Projected Impact: If Prince Deploys MetrixsMate", "Based on results from similar-scale coaching & school ecosystems")

# Challenge
add_rect(slide, Inches(0.8), Inches(1.6), Inches(5.5), Inches(1.6), RGBColor(0xFF, 0xF7, 0xED), AMBER_ACCENT)
tf = add_textbox(slide, Inches(1.0), Inches(1.7), Inches(5.1), Inches(1.4))
add_para(tf, "CURRENT STATE", size=11, color=AMBER_ACCENT, bold=True)
add_bullet(tf, "10,000+ students across 9 institutions", size=12, color=DARK_GRAY)
add_bullet(tf, "Stream selection: parent-driven, not data-driven", size=12, color=DARK_GRAY)
add_bullet(tf, "No cross-institution student intelligence", size=12, color=DARK_GRAY)
add_bullet(tf, "Career counselling: manual, limited reach", size=12, color=DARK_GRAY)

# With MetrixsMate
add_rect(slide, Inches(6.9), Inches(1.6), Inches(5.5), Inches(1.6), RGBColor(0xEC, 0xFD, 0xF5), GREEN_ACCENT)
tf = add_textbox(slide, Inches(7.1), Inches(1.7), Inches(5.1), Inches(1.4))
add_para(tf, "WITH METRIXSMATE", size=11, color=GREEN_ACCENT, bold=True)
add_bullet(tf, "Every student assessed in Week 1 of enrollment", size=12, color=DARK_GRAY)
add_bullet(tf, "AI recommends JEE/NEET/NDA/CLAT per student", size=12, color=DARK_GRAY)
add_bullet(tf, "Unified dashboard for all institutions", size=12, color=DARK_GRAY)
add_bullet(tf, "Topper DNA applied to predict future rankers", size=12, color=DARK_GRAY)

# Results
add_rect(slide, Inches(0.8), Inches(3.6), Inches(11.6), Inches(0.5), TEAL)
tf = add_textbox(slide, Inches(1.0), Inches(3.65), Inches(11.2), Inches(0.4))
add_para(tf, "PROJECTED RESULTS (YEAR 1)", size=14, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

metrics = [
    ("JEE Selections", "409 \u2192 520+", "+27%"),
    ("NEET Selections", "854 \u2192 1,050+", "+23%"),
    ("Stream Switch Rate", "~15% \u2192 <5%", "-67%"),
    ("Parent NPS Score", "Unknown \u2192 85+", "Measurable"),
]

for i, (label, value, delta) in enumerate(metrics):
    left = Inches(0.8) + i * Inches(3.05)
    add_card(slide, left, Inches(4.4), Inches(2.8), Inches(1.2), label, value, NAVY, delta)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 16 \u2014 ROI & Cost Justification
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "ROI & Cost Justification", "The numbers that matter for a 10,000+ student conglomerate")

pricing_data = [
    ["Plan", "Per Student/Year", "Best For"],
    ["Starter", "\u20B9149", "Single institution pilot (CBSE or RBSE school)"],
    ["Professional", "\u20B9399", "Coaching + schools (PCP + schools)"],
    ["Enterprise", "\u20B9599", "Full conglomerate \u2014 all 9 institutions unified"],
]

add_table(slide, Inches(0.8), Inches(1.6), Inches(5.8), pricing_data,
          [Inches(1.5), Inches(1.5), Inches(2.8)])

# ROI box
add_rect(slide, Inches(7.0), Inches(1.6), Inches(5.5), Inches(4.2), RGBColor(0xEF, 0xF6, 0xFF), TEAL)
tf = add_textbox(slide, Inches(7.3), Inches(1.7), Inches(4.9), Inches(4.0))
add_para(tf, "10,000 Students \u2014 Enterprise Plan", size=14, color=NAVY, bold=True)
add_para(tf, "", size=6)
add_bullet(tf, "Annual Platform Cost:  \u20B959,90,000", size=13, color=DARK_GRAY, bold=True)
add_bullet(tf, "Manual Equivalent:", size=13, color=DARK_GRAY)
add_bullet(tf, "  \u2022 Career counsellors (10): \u20B960,00,000+", size=11, color=MID_GRAY)
add_bullet(tf, "  \u2022 Admin tracking tools: \u20B910,00,000+", size=11, color=MID_GRAY)
add_bullet(tf, "  \u2022 Wrong-stream dropouts cost: \u20B950,00,000+", size=11, color=MID_GRAY)
add_para(tf, "", size=4)
add_bullet(tf, "Net Savings:  \u20B960,00,000+ / year", size=13, color=GREEN_ACCENT, bold=True)
add_para(tf, "", size=6)
add_para(tf, "ROI: 2x+ in Year 1", size=22, color=TEAL, bold=True, alignment=PP_ALIGN.CENTER)

# Bottom note
add_rect(slide, Inches(0.8), Inches(4.3), Inches(5.8), Inches(1.0), LIGHT_GRAY, RGBColor(0xE5, 0xE7, 0xEB))
tf = add_textbox(slide, Inches(1.0), Inches(4.4), Inches(5.4), Inches(0.8))
add_para(tf, "Volume Discount Available", size=13, color=NAVY, bold=True)
add_para(tf, "Conglomerate-wide deployment of 10,000+ students qualifies for special group pricing. Ask us about multi-year agreements.", size=10, color=MID_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 17 \u2014 Implementation Plan
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Implementation Plan \u2014 Go Live in 6 Weeks")

weeks = [
    ("WEEK 1-2", "Setup &\nConfiguration", "Platform setup for all 9 institutions, Prince branding, campus mapping, admin accounts", NAVY),
    ("WEEK 3", "Pilot\nLaunch", "500 students from PCP + CBSE School take all 3 assessments. Faculty training.", BLUE_ACCENT),
    ("WEEK 4", "Feedback &\nOptimise", "Analyse pilot results, refine AI stream recommendations, train counsellors on reports", TEAL),
    ("WEEK 5-6", "Full\nRollout", "All 9 institutions live. Bulk student onboarding. Cross-institution dashboards active.", GREEN_ACCENT),
]

for i, (week, title, desc, color) in enumerate(weeks):
    left = Inches(0.8) + i * Inches(3.1)
    top = Inches(1.7)
    add_rect(slide, left, top, Inches(2.8), Inches(3.6), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, left, top, Inches(2.8), Inches(0.5), color)
    tf = add_textbox(slide, left + Inches(0.15), top + Inches(0.07), Inches(2.5), Inches(0.4))
    add_para(tf, week, size=13, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)
    tf2 = add_textbox(slide, left + Inches(0.15), top + Inches(0.6), Inches(2.5), Inches(0.6))
    add_para(tf2, title, size=14, color=NAVY, bold=True, alignment=PP_ALIGN.CENTER)
    tf3 = add_textbox(slide, left + Inches(0.15), top + Inches(1.3), Inches(2.5), Inches(2.0))
    add_para(tf3, desc, size=10, color=MID_GRAY, alignment=PP_ALIGN.CENTER)

for i in range(3):
    left = Inches(3.6) + i * Inches(3.1)
    add_rect(slide, left, Inches(2.85), Inches(0.3), Inches(0.04), TEAL)

add_rect(slide, Inches(0.8), Inches(5.7), Inches(11.7), Inches(0.9), LIGHT_GRAY, RGBColor(0xE5, 0xE7, 0xEB))
tf = add_textbox(slide, Inches(1.0), Inches(5.8), Inches(11.3), Inches(0.7))
add_para(tf, "Ongoing Support", size=14, color=NAVY, bold=True)
add_bullet(tf, "Dedicated account manager  \u2022  Monthly analytics review with Directors  \u2022  All updates included  \u2022  Integration with Prince Eduhub app possible", size=11, color=MID_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 18 \u2014 Security & Compliance
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Enterprise-Grade Security & Compliance")

security = [
    ("Data Encryption", "All data encrypted at rest & in transit (AES-256 / TLS 1.3)"),
    ("Role-Based Access", "Separate admin roles per institution, campus, faculty, student"),
    ("GDPR-Aligned Privacy", "Student data consent workflows, full data privacy compliance"),
    ("Multi-Tenant Architecture", "9 institutions, 13 campuses \u2014 data isolation guaranteed"),
    ("UGC / AICTE Aligned", "Assessment frameworks aligned with Indian education standards"),
    ("99.9% Uptime SLA", "Cloud-hosted on reliable, scalable infrastructure"),
    ("No Third-Party Sharing", "Prince\u2019s student data belongs to Prince \u2014 period."),
]

for i, (title, desc) in enumerate(security):
    left = Inches(0.8); top = Inches(1.6) + i * Inches(0.72)
    add_rect(slide, left, top, Inches(0.08), Inches(0.55), TEAL)
    tf = add_textbox(slide, left + Inches(0.25), top, Inches(4), Inches(0.35))
    add_para(tf, "\U0001f6e1  " + title, size=13, color=NAVY, bold=True)
    tf2 = add_textbox(slide, Inches(5.2), top + Inches(0.05), Inches(7.5), Inches(0.5))
    add_para(tf2, desc, size=12, color=MID_GRAY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 19 \u2014 Why Choose Us
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "What Sets MetrixsMate Apart")

compare_data = [
    ["Others", "MetrixsMate"],
    ["Generic LMS / ERP systems", "Purpose-built psychometric + AI career platform"],
    ["Manual career counselling", "AI-powered recommendations at 10,000+ scale"],
    ["Single-institution focus", "Multi-institution conglomerate architecture"],
    ["English-only platforms", "Hindi + English (critical for Rajasthan students)"],
    ["No topper analytics", "Topper DNA profiling to replicate success"],
    ["Months of implementation", "Live in 6 weeks across all campuses"],
    ["Generic reports", "Institution-branded PDF reports for parents"],
    ["Per-module pricing", "All-in-one transparent per-student pricing"],
]

add_table(slide, Inches(0.8), Inches(1.6), Inches(11.7), compare_data,
          [Inches(5.85), Inches(5.85)], header_color=NAVY)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 20 \u2014 Demo / Next Steps
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "See It In Action \u2014 Next Steps")

steps = [
    ("1", "Live Demo", "30-min walkthrough customized\nfor Prince Edu Hub", BLUE_ACCENT),
    ("2", "Pilot Program", "Free 3-week pilot: 500 students\nfrom PCP + CBSE School", TEAL),
    ("3", "Results Review", "Present pilot insights to\nDr. Piyush & Mr. Jogendra Sunda", PURPLE_ACCENT),
    ("4", "Full Rollout", "All 9 institutions, 13 campuses\nwith dedicated support", GREEN_ACCENT),
]

for i, (num, title, desc, color) in enumerate(steps):
    left = Inches(0.8) + i * Inches(3.1); top = Inches(2.3)
    add_rect(slide, left, top, Inches(2.8), Inches(2.2), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_rect(slide, left, top, Inches(2.8), Inches(0.06), color)
    circle = slide.shapes.add_shape(MSO_SHAPE.OVAL, left + Inches(1.1), top + Inches(0.2), Inches(0.6), Inches(0.6))
    circle.fill.solid(); circle.fill.fore_color.rgb = color; circle.line.fill.background()
    ctf = circle.text_frame; ctf.word_wrap = True
    ctf.paragraphs[0].text = num; ctf.paragraphs[0].font.size = Pt(18)
    ctf.paragraphs[0].font.color.rgb = WHITE; ctf.paragraphs[0].font.bold = True
    ctf.paragraphs[0].alignment = PP_ALIGN.CENTER
    tf2 = add_textbox(slide, left + Inches(0.15), top + Inches(0.9), Inches(2.5), Inches(0.4))
    add_para(tf2, title, size=14, color=NAVY, bold=True, alignment=PP_ALIGN.CENTER)
    tf3 = add_textbox(slide, left + Inches(0.15), top + Inches(1.35), Inches(2.5), Inches(0.7))
    add_para(tf3, desc, size=11, color=MID_GRAY, alignment=PP_ALIGN.CENTER)

add_rect(slide, Inches(3), Inches(5.2), Inches(7.3), Inches(1.3), NAVY)
tf = add_textbox(slide, Inches(3.3), Inches(5.3), Inches(6.7), Inches(1.1))
add_para(tf, "Schedule Your Demo Today", size=18, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)
add_para(tf, "Let\u2019s start with a free pilot for 500 students \u2014 zero commitment", size=13, color=RGBColor(0xA0, 0xD2, 0xDB), alignment=PP_ALIGN.CENTER)

add_confidential(slide); add_slide_number(slide, n)


# ============================================================
# SLIDE 21 \u2014 Closing
# ============================================================
n += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, NAVY)
add_rect(slide, Inches(0), Inches(2.9), SLIDE_W, Inches(0.06), TEAL)

tf = add_textbox(slide, Inches(1.5), Inches(1.0), Inches(10), Inches(1.2))
add_para(tf, "Prince Edu Hub Produces Champions.\nMetrixsMate Ensures Every Student\nGets the Chance to Become One.", size=32, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

tf2 = add_textbox(slide, Inches(2), Inches(3.2), Inches(9), Inches(1.4))
add_para(tf2, '"You\u2019ve built India\u2019s #1 education campus. You produce AIR-1 toppers and 854 MBBS selections.\nMetrixsMate adds the intelligence layer to ensure the NEXT 10,000 students\ndon\u2019t just enroll \u2014 they enroll in their perfect path."', size=14, color=RGBColor(0xA0, 0xD2, 0xDB), italic=True, alignment=PP_ALIGN.CENTER)

tf3 = add_textbox(slide, Inches(2), Inches(4.9), Inches(9), Inches(0.5))
add_para(tf3, "MetrixsMate", size=28, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

tf4 = add_textbox(slide, Inches(2), Inches(5.5), Inches(9), Inches(0.4))
add_para(tf4, "Assess.  Align.  Achieve.", size=16, color=TEAL, bold=True, italic=True, alignment=PP_ALIGN.CENTER)

tf5 = add_textbox(slide, Inches(3), Inches(6.3), Inches(7), Inches(0.4))
add_para(tf5, "CONFIDENTIAL \u2014 Prepared exclusively for Prince Education Hub, Sikar", size=10, color=RGBColor(0x99, 0x99, 0x99), italic=True, alignment=PP_ALIGN.CENTER)

add_slide_number(slide, n)


# --- Save ---
output_path = "/Users/mohammedsufiyanqureshi/Documents/Sufiyan Qureshi/CXS/Development/mm/metrixsmate/MetrixsMate_Prince_EduHub_Pitch_Deck.pptx"
prs.save(output_path)
print(f"Presentation saved to: {output_path}")
print(f"Total slides: {len(prs.slides)}")
