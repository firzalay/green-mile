# GreenRun Design System

## Overview

GreenRun adalah platform lari berbasis dampak lingkungan yang menghubungkan aktivitas olahraga dengan aksi keberlanjutan. Desain aplikasi harus mencerminkan nilai:

* Sustainability
* Health & Wellness
* Simplicity
* Community
* Trust

Prinsip desain utama:

* Clean Interface
* Nature Inspired
* Mobile First
* Accessibility Friendly
* Consistent User Experience

---

# Design Principles

## 1. Eco-Centric

Gunakan warna hijau sebagai identitas utama untuk merepresentasikan lingkungan dan keberlanjutan.

## 2. Minimal Yet Informative

Tampilan sederhana namun tetap memberikan informasi penting dengan jelas.

## 3. Motivational

Setiap halaman harus memberikan kesan progres, pencapaian, dan dampak positif.

## 4. Consistent

Komponen, warna, spacing, dan typography harus konsisten di seluruh aplikasi.

---

# Color System

## Primary Colors

| Color         | Hex     |
| ------------- | ------- |
| Forest Green  | #003F2F |
| Emerald Green | #2ECF89 |
| Mint Green    | #7BE0B3 |

## Secondary Colors

| Color         | Hex     |
| ------------- | ------- |
| Earth Brown   | #8D6E63 |
| Sunset Orange | #FF7A45 |

## Neutral Colors

| Color      | Hex     |
| ---------- | ------- |
| Background | #F8F5F0 |
| White      | #FFFFFF |
| Gray 100   | #F3F4F6 |
| Gray 300   | #D1D5DB |
| Gray 500   | #6B7280 |
| Gray 700   | #374151 |
| Gray 900   | #111827 |

## Semantic Colors

### Success

#22C55E

### Warning

#F59E0B

### Error

#EF4444

### Info

#3B82F6

---

# Typography

## Primary Font

Inter

Fallback:

sans-serif

---

## Heading

### H1

* Size: 48px
* Weight: 700

### H2

* Size: 36px
* Weight: 700

### H3

* Size: 28px
* Weight: 600

### H4

* Size: 24px
* Weight: 600

---

## Body Text

### Large

18px

### Default

16px

### Small

14px

### Caption

12px

---

# Spacing System

Menggunakan skala 8px.

| Token | Value |
| ----- | ----- |
| xs    | 4px   |
| sm    | 8px   |
| md    | 16px  |
| lg    | 24px  |
| xl    | 32px  |
| 2xl   | 48px  |
| 3xl   | 64px  |

---

# Border Radius

| Token | Value  |
| ----- | ------ |
| sm    | 6px    |
| md    | 10px   |
| lg    | 14px   |
| xl    | 20px   |
| full  | 9999px |

---

# Shadow System

## Card

0px 2px 8px rgba(0,0,0,0.08)

## Floating Element

0px 8px 24px rgba(0,0,0,0.12)

## Modal

0px 16px 48px rgba(0,0,0,0.18)

---

# Buttons

## Primary Button

* Background: Forest Green
* Text: White
* Radius: 10px

States:

* Default
* Hover
* Active
* Disabled

## Secondary Button

* White background
* Green border

## Danger Button

* Red background

## Ghost Button

* Transparent

---

# Forms

## Input Field

Height: 48px

States:

* Default
* Focus
* Error
* Disabled

Features:

* Optional icon
* Helper text
* Validation message

---

# Card Component

Used For:

* Event Card
* Achievement Card
* Statistics Card
* Dashboard Widget

Properties:

* White background
* Radius 14px
* Soft shadow

---

# Navigation

## Top Navbar

Contains:

* Logo
* Navigation Menu
* User Menu
* Notification

Desktop Height:

72px

---

## Sidebar

Used in dashboard pages.

Contains:

* Dashboard
* Events
* Progress
* Leaderboard
* Profile
* Settings

Width:

280px

Collapsed:

80px

---

# Layout System

## Container

Max Width:

1440px

Padding:

24px

---

## Grid

Desktop:

12 Columns

Tablet:

8 Columns

Mobile:

4 Columns

---

# Responsive Breakpoints

## Mobile

0 - 767px

## Tablet

768 - 1023px

## Desktop

1024px+

## Large Desktop

1440px+

---

# Data Visualization

## Charts

Preferred:

* Bar Chart
* Line Chart
* Area Chart
* Progress Ring

Use colors:

* Green for positive metrics
* Orange for warnings
* Red for negative metrics

---

# Icons

Preferred Library:

Lucide React

Style:

* Outline
* Consistent Stroke Width

---

# Images

Photography Style:

* Nature
* Running
* Community Activities
* Reforestation

Image Treatment:

* Dark Overlay 30%–50%
* Green Gradient Overlay

---

# Accessibility

Minimum contrast ratio:

4.5:1

Requirements:

* Keyboard navigation
* Visible focus states
* Alt text for images
* Semantic HTML

---

# Motion & Animation

Animation Duration:

150ms – 300ms

Use For:

* Hover effects
* Modal transitions
* Page transitions

Avoid:

* Excessive motion
* Distracting animations

---

# Component Naming Convention

Buttons

* btn-primary
* btn-secondary
* btn-danger

Cards

* card-event
* card-stat
* card-achievement

Forms

* input-default
* input-error

---

# Design Consistency Rules

1. Semua halaman wajib menggunakan color palette yang telah ditentukan.
2. Semua spacing mengikuti 8px grid system.
3. Tidak diperbolehkan menggunakan font selain Inter tanpa persetujuan tim.
4. Komponen yang sudah ada harus digunakan kembali sebelum membuat komponen baru.
5. Seluruh halaman harus responsif untuk mobile, tablet, dan desktop.
