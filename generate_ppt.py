#!/usr/bin/env python3
"""Generate MetrixsMate pitch deck for SGI Sikar."""

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
SLIDE_W = Inches(13.333)
SLIDE_H = Inches(7.5)

prs = Presentation()
prs.slide_width = SLIDE_W
prs.slide_height = SLIDE_H


# --- Helpers ---
def add_confidential(slide):
    """Add CONFIDENTIAL footer to every slide."""
    txBox = slide.shapes.add_textbox(Inches(0.5), Inches(7.0), Inches(12.3), Inches(0.4))
    tf = txBox.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "CONFIDENTIAL — Prepared exclusively for Shekhawati Group of Institutions, Sikar"
    p.font.size = Pt(8)
    p.font.color.rgb = RGBColor(0x99, 0x99, 0x99)
    p.font.italic = True
    p.alignment = PP_ALIGN.CENTER


def add_slide_number(slide, num):
    txBox = slide.shapes.add_textbox(Inches(12.5), Inches(7.0), Inches(0.7), Inches(0.4))
    tf = txBox.text_frame
    p = tf.paragraphs[0]
    p.text = str(num)
    p.font.size = Pt(8)
    p.font.color.rgb = RGBColor(0x99, 0x99, 0x99)
    p.alignment = PP_ALIGN.RIGHT


def fill_slide_bg(slide, color):
    bg = slide.background
    fill = bg.fill
    fill.solid()
    fill.fore_color.rgb = color


def add_shape_rect(slide, left, top, width, height, fill_color, border_color=None):
    shape = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, left, top, width, height)
    shape.fill.solid()
    shape.fill.fore_color.rgb = fill_color
    if border_color:
        shape.line.color.rgb = border_color
        shape.line.width = Pt(1)
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
    p.text = text
    p.font.size = Pt(size)
    p.font.color.rgb = color
    p.font.bold = bold
    p.font.italic = italic
    p.alignment = alignment
    p.space_after = space_after
    p.space_before = space_before
    return p


def add_bullet(tf, text, size=13, color=DARK_GRAY, bold=False, level=0):
    p = tf.add_paragraph()
    p.text = text
    p.font.size = Pt(size)
    p.font.color.rgb = color
    p.font.bold = bold
    p.level = level
    p.space_after = Pt(4)
    p.space_before = Pt(2)
    return p


def add_card(slide, left, top, width, height, title, value, value_color=NAVY, subtitle=None):
    add_shape_rect(slide, left, top, width, height, WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    tf = add_textbox(slide, left + Inches(0.2), top + Inches(0.15), width - Inches(0.4), Inches(0.3))
    add_para(tf, title, size=9, color=MID_GRAY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.2), top + Inches(0.45), width - Inches(0.4), Inches(0.5))
    add_para(tf2, value, size=22, color=value_color, bold=True)
    if subtitle:
        tf3 = add_textbox(slide, left + Inches(0.2), top + Inches(0.85), width - Inches(0.4), Inches(0.3))
        add_para(tf3, subtitle, size=9, color=MID_GRAY)


def section_heading(slide, text, subtitle=None):
    add_shape_rect(slide, Inches(0), Inches(0), SLIDE_W, Inches(1.3), NAVY)
    tf = add_textbox(slide, Inches(0.8), Inches(0.25), Inches(11), Inches(0.6))
    add_para(tf, text, size=28, color=WHITE, bold=True)
    if subtitle:
        tf2 = add_textbox(slide, Inches(0.8), Inches(0.8), Inches(11), Inches(0.4))
        add_para(tf2, subtitle, size=13, color=RGBColor(0xA0, 0xD2, 0xDB), italic=True)


def add_table(slide, left, top, width, rows_data, col_widths, header_color=NAVY):
    rows = len(rows_data)
    cols = len(rows_data[0])
    table_shape = slide.shapes.add_table(rows, cols, left, top, width, Inches(0.4 * rows))
    table = table_shape.table

    for i, w in enumerate(col_widths):
        table.columns[i].width = w

    for r, row in enumerate(rows_data):
        for c, cell_text in enumerate(row):
            cell = table.cell(r, c)
            cell.text = ""
            p = cell.text_frame.paragraphs[0]
            p.text = str(cell_text)
            p.font.size = Pt(11)
            p.space_after = Pt(2)
            p.space_before = Pt(2)

            if r == 0:
                p.font.color.rgb = WHITE
                p.font.bold = True
                cell.fill.solid()
                cell.fill.fore_color.rgb = header_color
            else:
                p.font.color.rgb = DARK_GRAY
                cell.fill.solid()
                cell.fill.fore_color.rgb = WHITE if r % 2 == 1 else LIGHT_GRAY

    return table


# ============================================================
# SLIDE 1 — Cover
# ============================================================
slide_num = 1
slide = prs.slides.add_slide(prs.slide_layouts[6])  # blank
fill_slide_bg(slide, NAVY)

# Accent bar
add_shape_rect(slide, Inches(0), Inches(3.2), SLIDE_W, Inches(0.06), TEAL)

# Title
tf = add_textbox(slide, Inches(1.5), Inches(1.5), Inches(10), Inches(1.2))
add_para(tf, "MetrixsMate", size=52, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

# Tagline
tf2 = add_textbox(slide, Inches(2), Inches(2.7), Inches(9), Inches(0.5))
add_para(tf2, "AI-Powered Psychometric Assessment & Career Intelligence Platform", size=18, color=TEAL, alignment=PP_ALIGN.CENTER)

# Subtitle
tf3 = add_textbox(slide, Inches(2), Inches(3.6), Inches(9), Inches(0.5))
add_para(tf3, 'Assess.  Align.  Achieve.', size=20, color=RGBColor(0xA0, 0xD2, 0xDB), bold=True, italic=True, alignment=PP_ALIGN.CENTER)

# Custom line
tf4 = add_textbox(slide, Inches(2), Inches(4.5), Inches(9), Inches(0.5))
add_para(tf4, "A Tailored Proposal for Shekhawati Group of Institutions, Sikar", size=15, color=WHITE, alignment=PP_ALIGN.CENTER)

# Confidential badge
tf5 = add_textbox(slide, Inches(3), Inches(5.5), Inches(7), Inches(0.4))
add_para(tf5, "CONFIDENTIAL", size=12, color=RGBColor(0xFF, 0x99, 0x99), bold=True, alignment=PP_ALIGN.CENTER)

add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 2 — About SGI
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "About Shekhawati Group of Institutions", "A Legacy of Multi-Disciplinary Excellence")

tf = add_textbox(slide, Inches(0.8), Inches(1.6), Inches(7), Inches(4.5))
add_bullet(tf, "Established 2002 — Over 20 years of academic excellence in Sikar, Rajasthan", bold=True)
add_bullet(tf, "Multi-faculty campus: Engineering, Management, Law, Pharmacy, Agriculture, Science, Arts & Education")
add_bullet(tf, "Accreditations: AICTE, NCTE, PCI, BCI — Affiliated with leading Rajasthan universities")
add_bullet(tf, "Strong placement ecosystem: TCS, Infosys, Wipro, Accenture, HCL & 50+ recruiters")
add_bullet(tf, "Thousands of students across multiple departments and programs")

# Stats cards
add_card(slide, Inches(8.5), Inches(1.7), Inches(1.8), Inches(1.1), "FACULTIES", "8+", TEAL)
add_card(slide, Inches(10.6), Inches(1.7), Inches(1.8), Inches(1.1), "YEARS", "20+", NAVY)
add_card(slide, Inches(8.5), Inches(3.1), Inches(1.8), Inches(1.1), "RECRUITERS", "50+", GREEN_ACCENT)
add_card(slide, Inches(10.6), Inches(3.1), Inches(1.8), Inches(1.1), "PROGRAMS", "15+", AMBER_ACCENT)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 3 — Current Challenges
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Current Challenges Facing Institutions Like SGI", "What's holding great institutions back?")

challenges = [
    ("Manual Student Tracking", "Faculty & admin spend 40%+ time on paperwork"),
    ("Fragmented Systems", "Admissions, exams, placements use disconnected tools"),
    ("No Scientific Career Guidance", "Students choose courses based on peer pressure, not aptitude"),
    ("Placement Cell Blind Spots", "No data to match student strengths to recruiter needs"),
    ("Performance Analytics Gap", "Decisions made on intuition, not intelligence"),
    ("Multi-Department Complexity", "8+ faculties = 8 different workflows, zero unified visibility"),
]

for i, (title, desc) in enumerate(challenges):
    row = i // 2
    col = i % 2
    left = Inches(0.8) + col * Inches(6.2)
    top = Inches(1.6) + row * Inches(1.6)
    add_shape_rect(slide, left, top, Inches(5.8), Inches(1.3), LIGHT_GRAY, RGBColor(0xE5, 0xE7, 0xEB))
    # Red indicator
    add_shape_rect(slide, left, top, Inches(0.08), Inches(1.3), RED_ACCENT)
    tf = add_textbox(slide, left + Inches(0.25), top + Inches(0.15), Inches(5.3), Inches(0.35))
    add_para(tf, title, size=14, color=NAVY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.25), top + Inches(0.55), Inches(5.3), Inches(0.6))
    add_para(tf2, desc, size=12, color=MID_GRAY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 4 — Problem Statement
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "The Core Problem")

# Quote box
add_shape_rect(slide, Inches(0.8), Inches(1.6), Inches(11.7), Inches(1.2), RGBColor(0xEF, 0xF6, 0xFF), TEAL)
tf = add_textbox(slide, Inches(1.1), Inches(1.7), Inches(11.1), Inches(1.0))
add_para(tf, '"Large multi-course institutions invest crores in infrastructure but lack a single intelligent platform to assess student potential, guide career paths, and optimize placement outcomes."', size=14, color=NAVY, italic=True, alignment=PP_ALIGN.CENTER)

# 3 pain points
pains = [
    ("1. No Scientific Student Profiling", "Which Engineering student is suited for TCS vs. a startup? Which MBA student thrives in marketing vs. finance? No data exists."),
    ("2. Placement Mismatch", "Recruiters get generic resumes. Students get generic prep. Result: missed placements and wasted potential."),
    ("3. Zero Predictive Intelligence", "No system to identify at-risk students, high-performers, or hidden talent early in the academic cycle."),
]

for i, (title, desc) in enumerate(pains):
    top = Inches(3.2) + i * Inches(1.3)
    add_shape_rect(slide, Inches(0.8), top, Inches(11.7), Inches(1.1), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_shape_rect(slide, Inches(0.8), top, Inches(0.08), Inches(1.1), AMBER_ACCENT)
    tf = add_textbox(slide, Inches(1.1), top + Inches(0.1), Inches(11.1), Inches(0.35))
    add_para(tf, title, size=14, color=NAVY, bold=True)
    tf2 = add_textbox(slide, Inches(1.1), top + Inches(0.45), Inches(11.1), Inches(0.55))
    add_para(tf2, desc, size=12, color=MID_GRAY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 5 — Solution Overview
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Introducing MetrixsMate", "One Platform. Three Assessments. Unlimited Insight.")

tf = add_textbox(slide, Inches(0.8), Inches(1.6), Inches(11.5), Inches(0.6))
add_para(tf, "MetrixsMate is an AI-powered psychometric assessment & career intelligence platform that scientifically profiles every student across three dimensions:", size=14, color=DARK_GRAY)

pillars = [
    ("OCEAN", "Big Five Personality", "Who they are", RGBColor(0x3B, 0x82, 0xF6)),
    ("RIASEC", "Career Interest Mapping", "What they should do", RGBColor(0x10, 0xB9, 0x81)),
    ("Cognitive", "Aptitude & Reasoning", "How they think", RGBColor(0x8B, 0x5C, 0xF6)),
]

for i, (name, sub, desc, color) in enumerate(pillars):
    left = Inches(0.8) + i * Inches(4.1)
    add_shape_rect(slide, left, Inches(2.5), Inches(3.8), Inches(2.0), color)
    tf = add_textbox(slide, left + Inches(0.3), Inches(2.65), Inches(3.2), Inches(0.5))
    add_para(tf, name, size=26, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)
    tf2 = add_textbox(slide, left + Inches(0.3), Inches(3.2), Inches(3.2), Inches(0.4))
    add_para(tf2, sub, size=13, color=WHITE, alignment=PP_ALIGN.CENTER)
    tf3 = add_textbox(slide, left + Inches(0.3), Inches(3.65), Inches(3.2), Inches(0.4))
    add_para(tf3, '"' + desc + '"', size=12, color=RGBColor(0xE0, 0xE0, 0xE0), italic=True, alignment=PP_ALIGN.CENTER)

# Arrow → AI Engine → Outputs
add_shape_rect(slide, Inches(4), Inches(4.9), Inches(5.3), Inches(0.06), TEAL)
tf = add_textbox(slide, Inches(3.5), Inches(5.1), Inches(6.3), Inches(0.5))
add_para(tf, "AI Engine  →  Career Paths  |  Placement Match  |  Student Profiles  |  PDF Reports", size=14, color=TEAL, bold=True, alignment=PP_ALIGN.CENTER)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 6 — Key Features
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "What MetrixsMate Delivers")

features = [
    ("OCEAN Personality Assessment", "Scientifically validated Big Five personality profiling"),
    ("RIASEC Career Mapping", "Holland-code based career interest inventory"),
    ("Cognitive Aptitude Test", "IQ, reasoning & problem-solving evaluation"),
    ("AI School / Career Finder", "Intelligent matching of results to career paths"),
    ("Detailed PDF Reports", "Downloadable profiles for placement cells & parents"),
    ("Admin Analytics Dashboard", "Institution-wide insights, trends & comparisons"),
    ("Certificate Generation", "Verified digital certificates for assessments"),
    ("Multi-Department Support", "One platform for all faculties — unified data"),
]

for i, (title, desc) in enumerate(features):
    row = i // 2
    col = i % 2
    left = Inches(0.8) + col * Inches(6.2)
    top = Inches(1.6) + row * Inches(1.3)
    add_shape_rect(slide, left, top, Inches(5.8), Inches(1.1), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_shape_rect(slide, left, top, Inches(0.08), Inches(1.1), TEAL)
    tf = add_textbox(slide, left + Inches(0.25), top + Inches(0.12), Inches(5.3), Inches(0.35))
    add_para(tf, title, size=13, color=NAVY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.25), top + Inches(0.5), Inches(5.3), Inches(0.5))
    add_para(tf2, desc, size=11, color=MID_GRAY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 7 — How It Fits SGI
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "MetrixsMate × SGI Sikar — A Perfect Fit")

fit_data = [
    ["SGI Faculty", "MetrixsMate Application"],
    ["Engineering", "Identify Tech vs. Research vs. Management aptitudes"],
    ["Management (MBA)", "Map Marketing vs. Finance vs. HR personality fits"],
    ["Law", "Assess analytical reasoning & communication strengths"],
    ["Pharmacy", "Evaluate detail-orientation & scientific aptitude"],
    ["Agriculture", "Career path mapping: agri-business vs. research"],
    ["Education (B.Ed)", "Teacher personality profiling for school placements"],
    ["Science & Arts", "Early career guidance before specialization"],
]

add_table(slide, Inches(0.8), Inches(1.6), Inches(11.7), fit_data,
          [Inches(3), Inches(8.7)])

tf = add_textbox(slide, Inches(0.8), Inches(5.8), Inches(11.5), Inches(0.5))
add_para(tf, "Multi-Campus Scale: One dashboard, all departments, unified data.", size=14, color=TEAL, bold=True, alignment=PP_ALIGN.CENTER)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 8 — Use Cases
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Who Benefits? Everyone.")

use_cases = [
    ("Administration", NAVY, [
        "Unified student intelligence across all departments",
        "Automated report generation (no manual Excel)",
        "Data-backed academic planning decisions",
    ]),
    ("Faculty", TEAL, [
        "Understand student learning styles via OCEAN data",
        "Identify at-risk students early via cognitive scores",
        "Personalized mentoring based on scientific data",
    ]),
    ("Students", RGBColor(0x8B, 0x5C, 0xF6), [
        "Self-awareness through personality & aptitude results",
        "AI-recommended career paths aligned to strengths",
        "Verified certificates to add to resumes",
    ]),
    ("Placement Cell", GREEN_ACCENT, [
        "Talent pool sorted by recruiter requirements",
        "Share student profiles, not just resumes",
        "Data to prove student-recruiter fit",
    ]),
]

for i, (title, color, bullets) in enumerate(use_cases):
    col = i % 2
    row = i // 2
    left = Inches(0.8) + col * Inches(6.2)
    top = Inches(1.6) + row * Inches(2.7)
    add_shape_rect(slide, left, top, Inches(5.8), Inches(2.4), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_shape_rect(slide, left, top, Inches(5.8), Inches(0.5), color)
    tf = add_textbox(slide, left + Inches(0.3), top + Inches(0.07), Inches(5.2), Inches(0.4))
    add_para(tf, title, size=15, color=WHITE, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.3), top + Inches(0.6), Inches(5.2), Inches(1.7))
    for b in bullets:
        add_bullet(tf2, "•  " + b, size=12, color=DARK_GRAY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 9 — Automation & Efficiency
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Save Time. Reduce Errors. Scale Effortlessly.")

eff_data = [
    ["Manual Process", "With MetrixsMate", "Time Saved"],
    ["Paper-based personality tests", "Online OCEAN assessment (auto-scored)", "95%"],
    ["Excel-based student tracking", "Real-time admin dashboard", "80%"],
    ["Manual report preparation", "One-click PDF report generation", "90%"],
    ["Individual career counselling (1:1)", "AI-powered batch recommendations", "70%"],
    ["Placement data compilation", "Filterable talent analytics", "85%"],
]

add_table(slide, Inches(0.8), Inches(1.6), Inches(11.7), eff_data,
          [Inches(4.5), Inches(5.2), Inches(2)])

# Bottom callout
add_shape_rect(slide, Inches(3), Inches(5.4), Inches(7.3), Inches(0.8), TEAL)
tf = add_textbox(slide, Inches(3.3), Inches(5.5), Inches(6.7), Inches(0.6))
add_para(tf, "For 1,000 students, MetrixsMate saves an estimated 2,000+ staff-hours per year", size=16, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 10 — Analytics & Decision Making
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Intelligence That Drives Results")

analytics_sections = [
    ("Institution-Level Insights", NAVY, [
        "Department-wise personality distribution analysis",
        "Cognitive score trends across batches & years",
        "Career interest heat maps by faculty",
    ]),
    ("Placement Intelligence", TEAL, [
        "Auto-generated talent profiles matching recruiter JDs",
        "Identify top 10% candidates per skill dimension",
        "Track which personality types get placed fastest",
    ]),
    ("Student-Level Insights", RGBColor(0x8B, 0x5C, 0xF6), [
        "Individual strength/weakness radar charts",
        "Career fit scores ranked by compatibility",
        "Progress tracking across assessment completions",
    ]),
]

for i, (title, color, bullets) in enumerate(analytics_sections):
    left = Inches(0.8) + i * Inches(4.1)
    top = Inches(1.6)
    add_shape_rect(slide, left, top, Inches(3.8), Inches(3.5), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_shape_rect(slide, left, top, Inches(3.8), Inches(0.5), color)
    tf = add_textbox(slide, left + Inches(0.2), top + Inches(0.07), Inches(3.4), Inches(0.4))
    add_para(tf, title, size=13, color=WHITE, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.2), top + Inches(0.65), Inches(3.4), Inches(2.5))
    for b in bullets:
        add_bullet(tf2, "•  " + b, size=11, color=DARK_GRAY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 11 — Competitive Advantage
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "How MetrixsMate Positions SGI Ahead")

advantages = [
    ("NAAC & Ranking Boost", "Data-driven student outcomes = stronger accreditation narratives"),
    ("Recruiter Magnet", "Share scientific talent profiles, not just CGPAs"),
    ("Parent Confidence", '"We don\'t just teach — we scientifically guide careers"'),
    ("Student Retention", "Right course + right career path = fewer dropouts"),
    ("Digital-First Reputation", "Position SGI as Rajasthan's most tech-forward institution"),
    ("Marketing Edge", '"First institution in Sikar with AI-powered psychometric assessments"'),
]

for i, (title, desc) in enumerate(advantages):
    row = i // 2
    col = i % 2
    left = Inches(0.8) + col * Inches(6.2)
    top = Inches(1.6) + row * Inches(1.6)
    add_shape_rect(slide, left, top, Inches(5.8), Inches(1.3), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_shape_rect(slide, left, top, Inches(0.08), Inches(1.3), GREEN_ACCENT)
    tf = add_textbox(slide, left + Inches(0.25), top + Inches(0.15), Inches(5.3), Inches(0.35))
    add_para(tf, "✓  " + title, size=14, color=NAVY, bold=True)
    tf2 = add_textbox(slide, left + Inches(0.25), top + Inches(0.55), Inches(5.3), Inches(0.6))
    add_para(tf2, desc, size=12, color=MID_GRAY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 12 — Case Study
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Case Study: Regional Engineering College", "Institution of similar scale — hypothetical, based on projected outcomes")

# Challenge
add_shape_rect(slide, Inches(0.8), Inches(1.6), Inches(5.5), Inches(1.5), RGBColor(0xFF, 0xF7, 0xED), AMBER_ACCENT)
tf = add_textbox(slide, Inches(1.0), Inches(1.7), Inches(5.1), Inches(1.3))
add_para(tf, "CHALLENGE", size=11, color=AMBER_ACCENT, bold=True)
add_bullet(tf, "1,200 students across 5 branches", size=12, color=DARK_GRAY)
add_bullet(tf, "Placement rate stuck at 45%", size=12, color=DARK_GRAY)
add_bullet(tf, "No career guidance system in place", size=12, color=DARK_GRAY)

# Solution
add_shape_rect(slide, Inches(6.9), Inches(1.6), Inches(5.5), Inches(1.5), RGBColor(0xEF, 0xF6, 0xFF), RGBColor(0x3B, 0x82, 0xF6))
tf = add_textbox(slide, Inches(7.1), Inches(1.7), Inches(5.1), Inches(1.3))
add_para(tf, "METRIXSMATE DEPLOYED", size=11, color=RGBColor(0x3B, 0x82, 0xF6), bold=True)
add_bullet(tf, "All students completed 3 assessments in Week 1", size=12, color=DARK_GRAY)
add_bullet(tf, "AI generated personalized career maps", size=12, color=DARK_GRAY)
add_bullet(tf, "Placement cell received filterable talent dashboards", size=12, color=DARK_GRAY)

# Results
add_shape_rect(slide, Inches(0.8), Inches(3.5), Inches(11.6), Inches(0.5), GREEN_ACCENT)
tf = add_textbox(slide, Inches(1.0), Inches(3.55), Inches(11.2), Inches(0.4))
add_para(tf, "RESULTS AFTER 1 YEAR", size=14, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

metrics = [
    ("Placement Rate", "45% → 68%", "+23 pts"),
    ("Recruiter Satisfaction", "4.1 → 4.7 / 5.0", "+15%"),
    ("Career Clarity Score", "3.2 → 4.5 / 5.0", "+41%"),
    ("Admin Time Saved", "—", "80% reduction"),
]

for i, (label, value, delta) in enumerate(metrics):
    left = Inches(0.8) + i * Inches(3.05)
    add_card(slide, left, Inches(4.3), Inches(2.8), Inches(1.3), label, value, NAVY, delta)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 13 — ROI & Cost Justification
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "ROI & Cost Justification", "The numbers that matter")

pricing_data = [
    ["Plan", "Per Student/Year", "Includes"],
    ["Starter", "₹199", "OCEAN + RIASEC assessments"],
    ["Professional", "₹499", "All 3 assessments + AI Career Finder"],
    ["Enterprise", "₹799", "Everything + Admin Dashboard + Reports + Support"],
]

add_table(slide, Inches(0.8), Inches(1.6), Inches(5.5), pricing_data,
          [Inches(1.8), Inches(1.5), Inches(2.2)])

# ROI box
add_shape_rect(slide, Inches(6.9), Inches(1.6), Inches(5.5), Inches(3.5), RGBColor(0xEF, 0xF6, 0xFF), TEAL)
tf = add_textbox(slide, Inches(7.2), Inches(1.7), Inches(4.9), Inches(3.3))
add_para(tf, "1,000 Students — Enterprise Plan", size=14, color=NAVY, bold=True)
add_para(tf, "", size=6)
add_bullet(tf, "Annual Platform Cost:  ₹7,99,000", size=13, color=DARK_GRAY, bold=True)
add_bullet(tf, "Manual Equivalent (counsellors, staff, tools):  ₹18,00,000+", size=13, color=DARK_GRAY)
add_bullet(tf, "Net Savings:  ₹10,00,000+ / year", size=13, color=GREEN_ACCENT, bold=True)
add_para(tf, "", size=6)
add_para(tf, "ROI: 2.25x in Year 1", size=22, color=TEAL, bold=True, alignment=PP_ALIGN.CENTER)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 14 — Implementation Plan
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Go Live in 4 Weeks")

weeks = [
    ("WEEK 1", "Setup & Configuration", "Platform setup, SGI branding,\ndepartment mapping, admin training", NAVY),
    ("WEEK 2", "Pilot Launch", "100 students from 2 departments\ntake all 3 assessments", RGBColor(0x3B, 0x82, 0xF6)),
    ("WEEK 3", "Feedback & Optimization", "Analyze pilot results, refine\nrecommendations, train placement cell", TEAL),
    ("WEEK 4", "Full Rollout", "All departments live, bulk student\nonboarding, staff dashboards active", GREEN_ACCENT),
]

for i, (week, title, desc, color) in enumerate(weeks):
    left = Inches(0.8) + i * Inches(3.1)
    top = Inches(1.8)
    add_shape_rect(slide, left, top, Inches(2.8), Inches(3.2), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_shape_rect(slide, left, top, Inches(2.8), Inches(0.5), color)
    tf = add_textbox(slide, left + Inches(0.2), top + Inches(0.07), Inches(2.4), Inches(0.4))
    add_para(tf, week, size=13, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)
    tf2 = add_textbox(slide, left + Inches(0.2), top + Inches(0.65), Inches(2.4), Inches(0.5))
    add_para(tf2, title, size=14, color=NAVY, bold=True, alignment=PP_ALIGN.CENTER)
    tf3 = add_textbox(slide, left + Inches(0.2), top + Inches(1.25), Inches(2.4), Inches(1.5))
    add_para(tf3, desc, size=11, color=MID_GRAY, alignment=PP_ALIGN.CENTER)

# Connectors between weeks
for i in range(3):
    left = Inches(3.6) + i * Inches(3.1)
    add_shape_rect(slide, left, Inches(2.95), Inches(0.3), Inches(0.04), TEAL)

# Ongoing support
add_shape_rect(slide, Inches(0.8), Inches(5.4), Inches(11.7), Inches(1.0), LIGHT_GRAY, RGBColor(0xE5, 0xE7, 0xEB))
tf = add_textbox(slide, Inches(1.0), Inches(5.5), Inches(11.3), Inches(0.8))
add_para(tf, "Ongoing Support", size=14, color=NAVY, bold=True)
add_bullet(tf, "Dedicated account manager  •  Monthly analytics review calls  •  Platform updates & new features included", size=12, color=MID_GRAY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 15 — Security & Compliance
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "Enterprise-Grade Security")

security = [
    ("Data Encryption", "All data encrypted at rest and in transit (AES-256 / TLS 1.3)"),
    ("Role-Based Access", "Admin, faculty, student roles with granular permissions"),
    ("GDPR-Aligned", "Data privacy best practices, student consent workflows"),
    ("UGC/AICTE Friendly", "Assessment frameworks aligned with Indian education standards"),
    ("99.9% Uptime SLA", "Cloud-hosted on reliable, scalable infrastructure"),
    ("Regular Backups", "Automated daily backups with disaster recovery"),
    ("No Third-Party Sharing", "Student data belongs to SGI — period."),
]

for i, (title, desc) in enumerate(security):
    left = Inches(0.8)
    top = Inches(1.6) + i * Inches(0.75)
    # Shield indicator
    add_shape_rect(slide, left, top, Inches(0.08), Inches(0.55), TEAL)
    tf = add_textbox(slide, left + Inches(0.25), top, Inches(4), Inches(0.35))
    add_para(tf, "🛡  " + title, size=13, color=NAVY, bold=True)
    tf2 = add_textbox(slide, Inches(5.2), top + Inches(0.05), Inches(7.5), Inches(0.5))
    add_para(tf2, desc, size=12, color=MID_GRAY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 16 — Why Choose Us
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "What Sets Us Apart")

compare_data = [
    ["Others", "MetrixsMate"],
    ["Generic LMS with no assessments", "3 scientifically validated assessments built-in"],
    ["Manual career counselling", "AI-powered career & school recommendations"],
    ["One-size-fits-all", "Multi-department, multi-faculty architecture"],
    ["English only", "Hindi + English support (key for Rajasthan)"],
    ["PDF-only reports", "Interactive dashboards + downloadable reports"],
    ["Per-module pricing", "All-in-one transparent pricing"],
    ["Complex onboarding", "Live in 4 weeks, not 4 months"],
]

add_table(slide, Inches(0.8), Inches(1.6), Inches(11.7), compare_data,
          [Inches(5.85), Inches(5.85)], header_color=NAVY)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 17 — Demo / Next Steps
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, WHITE)
section_heading(slide, "See It In Action")

tf = add_textbox(slide, Inches(0.8), Inches(1.6), Inches(11.5), Inches(0.6))
add_para(tf, "Proposed Next Steps", size=18, color=NAVY, bold=True)

steps = [
    ("1", "Live Demo", "30-minute walkthrough customized for SGI's departments", RGBColor(0x3B, 0x82, 0xF6)),
    ("2", "Pilot Program", "Free 2-week pilot with 100 students — no commitment", TEAL),
    ("3", "Results Review", "Present pilot insights to SGI leadership team", RGBColor(0x8B, 0x5C, 0xF6)),
    ("4", "Decision & Rollout", "Full deployment with dedicated onboarding support", GREEN_ACCENT),
]

for i, (num, title, desc, color) in enumerate(steps):
    left = Inches(0.8) + i * Inches(3.1)
    top = Inches(2.5)
    # Number circle
    add_shape_rect(slide, left, top, Inches(2.8), Inches(2.0), WHITE, RGBColor(0xE5, 0xE7, 0xEB))
    add_shape_rect(slide, left, top, Inches(2.8), Inches(0.06), color)
    circle = slide.shapes.add_shape(MSO_SHAPE.OVAL, left + Inches(1.1), top + Inches(0.2), Inches(0.6), Inches(0.6))
    circle.fill.solid()
    circle.fill.fore_color.rgb = color
    circle.line.fill.background()
    ctf = circle.text_frame
    ctf.paragraphs[0].text = num
    ctf.paragraphs[0].font.size = Pt(18)
    ctf.paragraphs[0].font.color.rgb = WHITE
    ctf.paragraphs[0].font.bold = True
    ctf.paragraphs[0].alignment = PP_ALIGN.CENTER
    ctf.word_wrap = True

    tf2 = add_textbox(slide, left + Inches(0.2), top + Inches(0.9), Inches(2.4), Inches(0.4))
    add_para(tf2, title, size=14, color=NAVY, bold=True, alignment=PP_ALIGN.CENTER)
    tf3 = add_textbox(slide, left + Inches(0.2), top + Inches(1.3), Inches(2.4), Inches(0.6))
    add_para(tf3, desc, size=11, color=MID_GRAY, alignment=PP_ALIGN.CENTER)

# Contact box
add_shape_rect(slide, Inches(3), Inches(5.2), Inches(7.3), Inches(1.3), NAVY)
tf = add_textbox(slide, Inches(3.3), Inches(5.3), Inches(6.7), Inches(1.1))
add_para(tf, "Schedule Your Demo Today", size=18, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)
add_para(tf, "Contact us to get started with a free pilot program", size=13, color=RGBColor(0xA0, 0xD2, 0xDB), alignment=PP_ALIGN.CENTER)

add_confidential(slide)
add_slide_number(slide, slide_num)

# ============================================================
# SLIDE 18 — Closing
# ============================================================
slide_num += 1
slide = prs.slides.add_slide(prs.slide_layouts[6])
fill_slide_bg(slide, NAVY)

# Accent bar
add_shape_rect(slide, Inches(0), Inches(3.0), SLIDE_W, Inches(0.06), TEAL)

tf = add_textbox(slide, Inches(1.5), Inches(1.2), Inches(10), Inches(1.0))
add_para(tf, "Let's Build the Future of\nStudent Success — Together.", size=36, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

tf2 = add_textbox(slide, Inches(2), Inches(3.4), Inches(9), Inches(1.2))
add_para(tf2, '"SGI has built 20+ years of academic excellence.\nMetrixsMate adds the intelligence layer to turn that excellence\ninto measurable, recruiter-ready outcomes."', size=15, color=RGBColor(0xA0, 0xD2, 0xDB), italic=True, alignment=PP_ALIGN.CENTER)

tf3 = add_textbox(slide, Inches(2), Inches(4.9), Inches(9), Inches(0.5))
add_para(tf3, "MetrixsMate", size=28, color=WHITE, bold=True, alignment=PP_ALIGN.CENTER)

tf4 = add_textbox(slide, Inches(2), Inches(5.5), Inches(9), Inches(0.4))
add_para(tf4, "Assess.  Align.  Achieve.", size=16, color=TEAL, bold=True, italic=True, alignment=PP_ALIGN.CENTER)

# Confidential
tf5 = add_textbox(slide, Inches(3), Inches(6.3), Inches(7), Inches(0.4))
add_para(tf5, "CONFIDENTIAL — Prepared exclusively for Shekhawati Group of Institutions, Sikar", size=10, color=RGBColor(0x99, 0x99, 0x99), italic=True, alignment=PP_ALIGN.CENTER)

add_slide_number(slide, slide_num)


# --- Save ---
output_path = "/Users/mohammedsufiyanqureshi/Documents/Sufiyan Qureshi/CXS/Development/mm/metrixsmate/MetrixsMate_SGI_Sikar_Pitch_Deck.pptx"
prs.save(output_path)
print(f"Presentation saved to: {output_path}")
print(f"Total slides: {len(prs.slides)}")
