<!-- @format -->

# 🎨 CSS Quick Examples

## Using Root Variables - Copy & Paste Examples

### 1️⃣ Colored Box

**Before (Hardcoded):**

```css
.my-box {
  background: rgb(225, 217, 188);
  color: rgb(48, 54, 79);
  border: 1px solid #ccc;
}
```

**After (Using Variables):**

```css
@import url("root.css");

.my-box {
  background: var(--accent-color);
  color: var(--primary-color);
  border: 1px solid var(--glass-border);
}
```

---

### 2️⃣ Typography

**Before:**

```css
h1 {
  font-family: "Playfair Display", Georgia, serif;
  font-size: 2.5rem;
  font-weight: 700;
  color: rgb(225, 217, 188);
}

p {
  font-family: "Inter", sans-serif;
  font-size: 1rem;
  color: rgb(40, 40, 40);
}
```

**After:**

```css
@import url("root.css");

h1 {
  font-family: var(--font-family-display);
  font-size: var(--font-size-5xl);
  font-weight: var(--font-weight-bold);
  color: var(--accent-color);
}

p {
  font-family: var(--font-family-primary);
  font-size: var(--font-size-base);
  color: var(--text-primary);
}
```

---

### 3️⃣ Button with Hover

**Before:**

```css
.btn {
  background: rgb(225, 217, 188);
  color: rgb(48, 54, 79);
  padding: 16px 24px;
  border-radius: 12px;
  border: none;
  font-weight: 600;
  transition: 0.3s ease;
}

.btn:hover {
  background: rgb(200, 190, 160);
  transform: translateY(-4px);
  box-shadow: 0 10px 15px -3px rgba(48, 54, 79, 0.1);
}
```

**After:**

```css
@import url("root.css");

.btn {
  background: var(--accent-color);
  color: var(--text-on-accent);
  padding: var(--space-lg) var(--space-2xl);
  border-radius: var(--border-radius-lg);
  border: none;
  font-weight: var(--font-weight-semibold);
  transition: var(--transition-normal);
}

.btn:hover {
  background: var(--accent-hover);
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}
```

---

### 4️⃣ Card Component

**Before:**

```css
.card {
  background: white;
  border-radius: 24px;
  box-shadow: 0 10px 15px -3px rgba(48, 54, 79, 0.1);
  overflow: hidden;
  transition: 0.3s ease;
}

.card:hover {
  transform: translateY(-12px);
  box-shadow: 0 25px 50px -12px rgba(48, 54, 79, 0.25);
}

.card-title {
  font-family: "Playfair Display", Georgia, serif;
  font-size: 1.5rem;
  color: rgb(40, 40, 40);
  margin-bottom: 16px;
}
```

**After:**

```css
@import url('root.css');
@import url('cards.css');

<!-- Use the consolidated class -->
<div class="card-base">
  <div class="card-image">
    <img src="image.jpg" alt="">
  </div>
  <div class="card-content">
    <h3 class="card-title">Title</h3>
    <p class="card-description">Description</p>
    <button class="card-button">Learn More</button>
  </div>
</div>
```

---

### 5️⃣ Form Input

**Before:**

```css
input {
  padding: 12px 16px;
  border: 1px solid rgba(172, 186, 196, 0.2);
  border-radius: 12px;
  font-family: "Inter", sans-serif;
  font-size: 1rem;
}

input:focus {
  border-color: rgb(225, 217, 188);
  box-shadow: 0 0 5px rgba(217, 131, 36, 0.4);
  outline: none;
}
```

**After:**

```css
@import url('root.css');
@import url('forms.css');

<!-- Use the standardized class -->
<input class="form-control-standard" type="text">
```

---

### 6️⃣ Alert Messages

**Before:**

```css
.alert {
  padding: 16px;
  border-radius: 12px;
  margin-bottom: 16px;
}

.alert-success {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  border-left: 4px solid #10b981;
}

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border-left: 4px solid #ef4444;
}
```

**After:**

```css
@import url('root.css');
@import url('forms.css');

<!-- Use the consolidated class -->
<div class="form-alert alert-success">Success message</div>
<div class="form-alert alert-danger">Error message</div>
```

---

### 7️⃣ Gradient Background

**Before:**

```css
.hero {
  background: linear-gradient(
    135deg,
    rgb(48, 54, 79) 0%,
    rgb(65, 73, 101) 50%,
    rgb(82, 92, 123) 100%
  );
  color: white;
  padding: 64px 32px;
}
```

**After:**

```css
@import url("root.css");

.hero {
  background: var(--gradient-primary);
  color: var(--text-on-dark);
  padding: var(--space-3xl) var(--space-lg);
}
```

---

### 8️⃣ Responsive Design

**Before:**

```css
.container {
  padding: 24px;
  max-width: 1400px;
}

@media (max-width: 768px) {
  .container {
    padding: 16px;
  }
}

@media (max-width: 576px) {
  .container {
    padding: 8px;
  }
}
```

**After:**

```css
@import url('root.css');

.container {
  padding: var(--space-lg);
  max-width: 1400px;
}

@media (max-width: 768px) {
  .container {
    padding: var(--space-md);
  }
}

@media (max-width: 576px) {
  .container {
    padding: var(--space-sm);
  }
}

/* Or use the pre-made container */
<div class="container-fluid">Your content</div>
```

---

### 9️⃣ Glass Effect

**Before:**

```css
.glass-card {
  background: rgba(240, 240, 219, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(225, 217, 188, 0.2);
  border-radius: 12px;
}
```

**After:**

```css
@import url('root.css');

.glass-card {
  background: var(--glass-bg);
  backdrop-filter: var(--glass-backdrop);
  border: 1px solid var(--glass-border);
  border-radius: var(--border-radius-lg);
}

<!-- Or use the utility class -->
<div class="glass-effect">Your content</div>
```

---

### 🔟 Complete Page Template

**HTML:**

```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Page</title>

    <!-- Import all CSS -->
    <link rel="stylesheet" href="assets/css/root.css" />
    <link rel="stylesheet" href="assets/css/cards.css" />
    <link rel="stylesheet" href="assets/css/forms.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/nav.css" />
    <link rel="stylesheet" href="assets/css/footer.css" />

    <style>
      @import url("root.css");

      /* Your custom styles */
      .my-section {
        background: var(--background-section);
        padding: var(--space-3xl) var(--space-lg);
      }

      .my-section h2 {
        color: var(--text-primary);
        font-family: var(--font-family-display);
        font-size: var(--font-size-4xl);
        margin-bottom: var(--space-lg);
      }
    </style>
  </head>
  <body>
    <nav class="navbar">
      <!-- Navigation -->
    </nav>

    <main>
      <section class="my-section">
        <h2>Welcome</h2>
        <p>Your content here</p>
      </section>
    </main>

    <footer>
      <!-- Footer -->
    </footer>
  </body>
</html>
```

---

## 📋 Quick Replacement Guide

| Need             | Use Variable                 |
| ---------------- | ---------------------------- |
| Primary color    | `var(--primary-color)`       |
| Accent color     | `var(--accent-color)`        |
| Text color       | `var(--text-primary)`        |
| Background       | `var(--background-card)`     |
| Display font     | `var(--font-family-display)` |
| Body font        | `var(--font-family-primary)` |
| Large spacing    | `var(--space-lg)`            |
| Big shadow       | `var(--shadow-lg)`           |
| Corner radius    | `var(--border-radius-lg)`    |
| Smooth animation | `var(--transition-normal)`   |

---

## 🎯 Pro Tips

1. **Always import root.css first**

   ```css
   @import url("root.css");
   ```

2. **Use semantic variables**

   ```css
   /* Good */
   color: var(--text-primary);

   /* Avoid */
   color: var(--color-charcoal);
   ```

3. **Leverage CSS cascade**
   ```css
   /* Instead of repeating properties */
   .card {
     box-shadow: var(--shadow-lg);
   }
   .card:hover {
     box-shadow: var(--card-shadow-hover);
   }
   ```

/_ Define relationships in root.css _/
--card-shadow-hover: var(--shadow-2xl);

```

4. **Test with different zoom levels**
- Browsers might zoom in/out
- Use `clamp()` for responsive sizing
- Test on mobile devices

5. **Check contrast ratios**
- Text must be readable
- Use contrast checking tools
- All text colors already optimized in root

---

## 🔗 Related Files

- [ROOT_VARIABLES_REFERENCE.md](ROOT_VARIABLES_REFERENCE.md) - Complete variable list
- [CSS_CONSOLIDATION_REPORT.md](CSS_CONSOLIDATION_REPORT.md) - Technical details
- [IMPLEMENTATION_GUIDE.md](IMPLEMENTATION_GUIDE.md) - How to use the new system

---

**Last Updated:** 2026-01-26
```
