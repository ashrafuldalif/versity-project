<!-- @format -->

# CSS ROOT VARIABLES QUICK REFERENCE

## 🎨 Color Palette

### Primary Colors (Blues & Dark Tones)

```css
--color-primary-dark: rgb(48, 54, 79); /* Deep blue-gray */
--color-primary-base: rgb(65, 73, 101); /* Medium blue-gray */
--color-primary-light: rgb(82, 92, 123); /* Light blue-gray */
--primary-color: var(--color-primary-base);
```

### Secondary Colors (Cool Grays)

```css
--color-secondary-dark: rgb(140, 155, 171); /* Darker cool gray */
--color-secondary-base: rgb(172, 186, 196); /* Cool gray */
--color-secondary-light: rgb(200, 210, 218); /* Light cool gray */
--secondary-color: var(--color-secondary-base);
```

### Accent Colors (Warm Cream)

```css
--color-accent-dark: rgb(200, 190, 160); /* Darker cream */
--color-accent-base: rgb(225, 217, 188); /* Warm cream */
--color-accent-light: rgb(240, 240, 219); /* Light cream */
--accent-color: var(--color-accent-base);
```

### Neutral Colors

```css
--color-white: rgb(250, 250, 250); /* Soft white */
--color-off-white: rgb(245, 245, 245); /* Off white */
--color-light-gray: rgb(235, 235, 235); /* Light gray */
--color-medium-gray: rgb(120, 120, 120); /* Medium gray */
--color-dark-gray: rgb(60, 60, 60); /* Dark gray */
--color-charcoal: rgb(40, 40, 40); /* Charcoal */
```

### Semantic Colors

```css
--color-success: #10b981; /* Green */
--color-warning: #f59e0b; /* Amber */
--color-danger: #ef4444; /* Red */
--color-info: #3b82f6; /* Blue */
```

---

## 📝 Typography

### Font Families

```css
--font-family-primary:
  "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
--font-family-display: "Playfair Display", Georgia, serif;
--font-family-fallback: "Poppins", sans-serif;
```

### Font Sizes

```css
--font-size-xs: 0.75rem; /* 12px */
--font-size-sm: 0.875rem; /* 14px */
--font-size-base: 1rem; /* 16px */
--font-size-lg: 1.125rem; /* 18px */
--font-size-xl: 1.25rem; /* 20px */
--font-size-2xl: 1.5rem; /* 24px */
--font-size-3xl: 1.875rem; /* 30px */
--font-size-4xl: 2.25rem; /* 36px */
--font-size-5xl: 3rem; /* 48px */
--font-size-6xl: 3.75rem; /* 60px */
```

### Font Weights

```css
--font-weight-light: 300;
--font-weight-normal: 400;
--font-weight-medium: 500;
--font-weight-semibold: 600;
--font-weight-bold: 700;
--font-weight-black: 900;
```

### Line Heights

```css
--line-height-tight: 1.25;
--line-height-normal: 1.5;
--line-height-relaxed: 1.75;
```

---

## 🎯 Backgrounds

```css
--background-primary: var(--color-primary-dark); /* For main bg */
--background-secondary: var(--color-primary-base); /* Secondary bg */
--background-light: var(--color-off-white); /* Light bg */
--background-card: var(--color-white); /* Cards */
--background-section: var(--color-light-gray); /* Sections */
```

---

## 📄 Text Colors

```css
--text-primary: var(--color-charcoal); /* Main text */
--text-secondary: var(--color-dark-gray); /* Secondary text */
--text-muted: var(--color-medium-gray); /* Muted text */
--text-light: var(--color-white); /* Light text */
--text-on-dark: var(--color-white); /* Text on dark bg */
--text-on-accent: var(--color-primary-dark); /* Text on accent bg */
```

---

## 🎨 Gradients

```css
--gradient-primary: linear-gradient(
  135deg,
  rgb(48, 54, 79) 0%,
  rgb(65, 73, 101) 50%,
  rgb(82, 92, 123) 100%
);
--gradient-secondary: linear-gradient(
  135deg,
  rgb(172, 186, 196) 0%,
  rgb(200, 210, 218) 100%
);
--gradient-accent: linear-gradient(
  135deg,
  rgb(225, 217, 188) 0%,
  rgb(240, 240, 219) 100%
);
--gradient-hero: linear-gradient(
  135deg,
  rgba(48, 54, 79, 0.9) 0%,
  rgba(65, 73, 101, 0.8) 100%
);
```

---

## ✨ Effects

### Shadows

```css
--shadow-sm: 0 1px 2px 0 rgba(48, 54, 79, 0.05);
--shadow-md:
  0 4px 6px -1px rgba(48, 54, 79, 0.1), 0 2px 4px -1px rgba(48, 54, 79, 0.06);
--shadow-lg:
  0 10px 15px -3px rgba(48, 54, 79, 0.1), 0 4px 6px -2px rgba(48, 54, 79, 0.05);
--shadow-xl:
  0 20px 25px -5px rgba(48, 54, 79, 0.1),
  0 10px 10px -5px rgba(48, 54, 79, 0.04);
--shadow-2xl: 0 25px 50px -12px rgba(48, 54, 79, 0.25);
```

### Glass Effect

```css
--glass-bg: rgba(240, 240, 219, 0.1);
--glass-border: rgba(225, 217, 188, 0.2);
--glass-backdrop: blur(10px);
```

### Overlays

```css
--overlay-dark: rgba(48, 54, 79, 0.8);
--overlay-light: rgba(240, 240, 219, 0.9);
--overlay-accent: rgba(225, 217, 188, 0.9);
```

---

## 🔄 Transitions

```css
--transition-fast: all 0.15s ease;
--transition-normal: all 0.3s ease;
--transition-slow: all 0.5s ease;
```

---

## 📦 Spacing

```css
--space-xs: 0.25rem; /* 4px */
--space-sm: 0.5rem; /* 8px */
--space-md: 1rem; /* 16px */
--space-lg: 1.5rem; /* 24px */
--space-xl: 2rem; /* 32px */
--space-2xl: 3rem; /* 48px */
--space-3xl: 4rem; /* 64px */
```

---

## 🔲 Border Radius

```css
--border-radius-sm: 0.375rem; /* 6px */
--border-radius-md: 0.5rem; /* 8px */
--border-radius-lg: 0.75rem; /* 12px */
--border-radius-xl: 1rem; /* 16px */
--border-radius-2xl: 1.5rem; /* 24px */
--border-radius-full: 9999px; /* Full circle */
```

---

## 📑 Z-Index Scale

```css
--z-dropdown: 1000;
--z-sticky: 1020;
--z-fixed: 1030;
--z-modal-backdrop: 1040;
--z-modal: 1050;
--z-popover: 1060;
--z-tooltip: 1070;
--z-toast: 1080;
```

---

## 🎬 Interactive States

```css
--primary-hover: rgb(82, 92, 123);
--accent-hover: rgb(200, 190, 160);
--secondary-hover: rgb(140, 155, 171);
--accent-active: rgb(180, 170, 140);
```

---

## 📝 Form States

```css
--form-border-focus: var(--accent-color);
--form-shadow-focus: 0 0 5px rgba(225, 217, 188, 0.5);
```

---

## 🃏 Card Styling

```css
--card-border: 1px solid var(--glass-border);
--card-shadow: var(--shadow-lg);
--card-shadow-hover: var(--shadow-2xl);
```

---

## 💡 Usage Examples

### Colors

```css
/* Background */
background: var(--background-card);

/* Text */
color: var(--text-primary);

/* Gradients */
background: var(--gradient-accent);
```

### Typography

```css
font-family: var(--font-family-display);
font-size: var(--font-size-2xl);
font-weight: var(--font-weight-semibold);
```

### Spacing

```css
padding: var(--space-lg);
margin-bottom: var(--space-md);
gap: var(--space-sm);
```

### Effects

```css
box-shadow: var(--shadow-lg);
border-radius: var(--border-radius-lg);
transition: var(--transition-normal);
```

### States

```css
&:hover {
  background: var(--accent-hover);
  box-shadow: var(--card-shadow-hover);
}

&:focus {
  border-color: var(--form-border-focus);
  box-shadow: var(--form-shadow-focus);
}
```

---

## 🌙 Dark Mode

All variables support dark mode. Set `[data-theme="dark"]` on root element to use dark mode colors:

```html
<html data-theme="dark">
  <!-- Your content -->
</html>
```

Or use `@media (prefers-color-scheme: dark)` for automatic dark mode switching.

---

## ✅ Best Practices

1. **Always use variables** - Never hardcode colors, fonts, or sizes
2. **Use semantic names** - `--accent-color` instead of `--orange-500`
3. **Group related variables** - Colors together, typography together
4. **Use cascading** - `--primary-color` references `--color-primary-base`
5. **Document changes** - Update this reference when adding new variables
6. **Test in dark mode** - Ensure all variables work in both themes

---

Last Updated: 2026-01-26
