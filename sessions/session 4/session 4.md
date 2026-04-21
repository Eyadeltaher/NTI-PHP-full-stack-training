
# CSS — Units, Combinators, Inheritance, Icons, Shadows & Gradients

---

## Units — rem, em, px and %

### px — Pixels (Fixed)

A fixed unit. `16px` is always `16px` regardless of anything else.

```css
.box {
  width: 300px;
  font-size: 16px;
  padding: 20px;
}
```

When to use:
- Borders: `border: 1px solid #ccc`
- Box shadows and precise decorative details
- When you absolutely need something to never change size

When not to use:
- Font sizes on body text — it overrides the user's browser font size preference, which is an accessibility problem. A visually impaired user who set their browser to large text will still get your fixed `px` size.

---

### em — Relative to the Parent's Font Size

`1em` equals the font size of the **parent element**. If the parent has `font-size: 20px`, then `1em` on the child equals `20px`.

```css
.parent {
  font-size: 20px;
}

.child {
  font-size: 1.5em;  /* 1.5 × 20px = 30px */
  padding: 1em;      /* 1 × 20px = 20px */
}
```

The problem with `em` is **compounding**. If you nest elements that each use `em`, the sizes multiply:

```css
body    { font-size: 16px; }
.level1 { font-size: 1.5em; }  /* 16 × 1.5 = 24px */
.level2 { font-size: 1.5em; }  /* 24 × 1.5 = 36px */
.level3 { font-size: 1.5em; }  /* 36 × 1.5 = 54px — unintended */
```

When to use `em`:
- `padding` and `margin` inside a component where you want spacing to scale with the component's own font size. For example, a button — if the button text gets larger, the padding should grow too.

```css
.button {
  font-size: 16px;
  padding: 0.75em 1.5em; /* Scales with the button's own font size */
}

.button.large {
  font-size: 20px;
  /* padding automatically becomes 15px 30px — no need to override */
}
```

---

### rem — Relative to the Root Font Size

`1rem` equals the font size of the **root `html` element** — always. It does not compound. No matter how deeply nested, `1rem` always refers to the same base.

```css
html {
  font-size: 16px; /* The base — 1rem = 16px everywhere */
}

h1 { font-size: 2.5rem; }    /* 40px */
h2 { font-size: 2rem; }      /* 32px */
p  { font-size: 1rem; }      /* 16px */
small { font-size: 0.875rem; } /* 14px */
```

The power of `rem` is that if you change `html { font-size }`, every `rem` value on the page scales proportionally — making the whole layout scale up or down from one place.

Real situation — responsive font scaling:

```css
html { font-size: 16px; }

@media (max-width: 768px) {
  html { font-size: 14px; } /* Everything using rem shrinks proportionally */
}
```

When to use `rem`:
- Font sizes — almost always
- Spacing (margin, padding) for global consistency
- Any value you want to scale with the user's browser settings

---

### % — Relative to the Parent's Size

`%` is relative to the **parent element's** corresponding property.

```css
.parent {
  width: 800px;
}

.child {
  width: 50%;    /* 50% of 800px = 400px */
  height: 100%;  /* 100% of parent's height */
}
```

When to use:
- Widths in responsive layouts: `width: 100%` to fill the parent
- Making an element take a fraction of its container
- `height: 100%` when you want to match the parent's height (the parent must have an explicit height set)

---

### The Practical Rule — When to Use Which

| Unit | Use For |
|---|---|
| `px` | Borders, box shadows, fine details that should never scale |
| `rem` | Font sizes, global spacing — scales with browser settings |
| `em` | Padding/margin inside a component that should scale with that component's font size |
| `%` | Widths in fluid layouts, filling a parent |
| `vh` / `vw` | Full-screen sections, hero areas, elements relative to the viewport |

---

## CSS Combinators

Combinators define the **relationship** between two selectors. They let you target elements based on where they sit in the HTML structure.

---

### Descendant Combinator — Space

Targets any matching element **anywhere inside** the parent, no matter how deeply nested.

```css
.card p {
  color: gray;
}
```

```html
<div class="card">
  <p>This is targeted</p>            <!-- targeted -->
  <div>
    <p>This is also targeted</p>     <!-- also targeted — deeply nested -->
  </div>
</div>
```

Real situation: Styling all paragraphs inside an article regardless of how many wrapper divs are between them.

---

### Child Combinator — `>`

Targets only **direct children** — one level deep, not grandchildren.

```css
.nav > li {
  display: inline-block;
}
```

```html
<ul class="nav">
  <li>Direct child — targeted</li>       <!-- targeted -->
  <li>
    <ul>
      <li>Grandchild — NOT targeted</li> <!-- not targeted -->
    </ul>
  </li>
</ul>
```

Real situation: A nested navigation menu where the outer `li` items should be horizontal but inner `li` items (dropdown items) should be vertical. Using `>` targets only the top level.

---

### Adjacent Sibling Combinator — `+`

Targets the element that comes **immediately after** a specified element, and they must share the same parent.

```css
h2 + p {
  font-size: 1.1rem;
  color: #555;
}
```

```html
<h2>Section Title</h2>
<p>This is targeted — immediately after h2</p>   <!-- targeted -->
<p>This is NOT targeted — not immediately after</p> <!-- not targeted -->
```

Real situation: The first paragraph after a heading often needs slightly different styling — larger, or a different color as an intro paragraph. The `+` combinator targets exactly that paragraph without adding a class.

---

### General Sibling Combinator — `~`

Targets **all matching siblings** that come after the specified element, not just the immediate one.

```css
h2 ~ p {
  color: #444;
}
```

```html
<h2>Title</h2>
<p>Targeted</p>        <!-- targeted -->
<div>Something</div>
<p>Also targeted</p>   <!-- also targeted — still a sibling after h2 -->
```

Real situation: Styling all paragraphs that follow an `h2` heading inside an article — all of them should have the same body text style.

---

### Combinator Summary

| Combinator | Symbol | Targets |
|---|---|---|
| Descendant | `A B` (space) | Any B inside A, any depth |
| Child | `A > B` | Only direct children B of A |
| Adjacent sibling | `A + B` | The single B immediately after A |
| General sibling | `A ~ B` | All B siblings after A |

---

## `inherit` — Pass the Parent's Value Down

By default, some CSS properties automatically pass down from parent to child (like `color` and `font-family`), and some do not (like `border`, `padding`, `background`).

Using `inherit` forces a property to take whatever value the parent has, even when it would not do so automatically.

```css
.parent {
  border: 2px solid red;
  color: navy;
}

.child {
  border: inherit;  /* Takes the parent's border — 2px solid red */
  color: inherit;   /* Takes the parent's color — navy (would happen anyway) */
}
```

Real situation: Links inside a colored div. By default, links ignore the parent's `color` and stay blue. Using `inherit` makes them match the surrounding text.

```css
.card a {
  color: inherit; /* Match the card's text color instead of staying browser-blue */
  text-decoration: none;
}
```

Another real situation: Form inputs do not inherit `font-family` by default — they use the browser's default system font. This is why inputs sometimes look different from the rest of your page.

```css
input,
textarea,
select {
  font-family: inherit; /* Match the rest of the page's font */
  font-size: inherit;
}
```

---

## `::before` and `::after` — Why Give Them `position`?

As covered in the pseudo-elements notes, `::before` and `::after` create invisible elements inside the selected element. A very common use is decorative shapes or overlays.

When you want a `::before` or `::after` to be positioned precisely — like an underline under a heading, an icon in the corner of a card, or a full overlay — you need the parent to have `position: relative` and the pseudo-element to have `position: absolute`.

```css
.heading {
  position: relative; /* Makes this the anchor for the pseudo-element */
  display: inline-block;
}

.heading::after {
  content: "";
  position: absolute;  /* Now it can be placed precisely */
  bottom: -4px;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: #2563eb;
}
```

Without `position: absolute` on the pseudo-element, it sits inline with the content and you cannot control its exact placement with `top`, `left`, `bottom`, `right`.

Real situation: A decorative colored line under a section heading. The line needs to be placed exactly 4px below the text — this requires `position: absolute` on `::after` with the parent as `position: relative`.

---

## Font Awesome Icons

Font Awesome is a library of thousands of icons that you can use as HTML elements and style with CSS like any other text.

---

### Method 1 — CDN (No Download Needed)

Add this link tag inside your HTML `<head>`:

```html
<link 
  rel="stylesheet" 
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
>
```

Then use icons directly in HTML:

```html
<i class="fa-solid fa-house"></i>
<i class="fa-solid fa-user"></i>
<i class="fa-brands fa-github"></i>
```

---

### Method 2 — Downloaded Files

When you download Font Awesome from fontawesome.com, you get a folder. Inside it, you only need two things — and you must keep them together in your project exactly as they are:

```
/fontawesome-free-6.7.2-web/
    css/
        all.min.css       <-- link this in your HTML
    webfonts/
        (many font files) <-- must stay here, do not move them
```

Link only the CSS file:

```html
<link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
```

> The CSS file references the webfonts folder using a relative path. If you move the CSS file without moving the webfonts folder alongside it, the icons will show as empty boxes. The folder structure must be preserved exactly as downloaded.

When you copy the Font Awesome folder into your project, keep both `css/` and `webfonts/` together inside the same parent folder — do not separate them.

---

### How to Find Icon Names and Properties

Go to fontawesome.com, search for the icon you want, click it, and it shows you the exact class names to use.

```html
<!-- Solid style -->
<i class="fa-solid fa-magnifying-glass"></i>

<!-- Regular style (outline) -->
<i class="fa-regular fa-heart"></i>

<!-- Brand icons -->
<i class="fa-brands fa-youtube"></i>
```

### Styling Icons with CSS

Since Font Awesome icons render as text, you style them with font properties:

```css
.fa-solid {
  font-size: 24px;       /* Controls icon size */
  color: #2563eb;        /* Controls icon color */
}

/* Or target a specific icon */
.fa-house {
  font-size: 32px;
  color: red;
}
```

You can also apply them inline:

```html
<i class="fa-solid fa-star" style="font-size: 20px; color: gold;"></i>
```

---

## CSS Nesting

CSS nesting lets you write child selectors inside their parent selector — the same way you write it in SCSS/Sass, but now natively in CSS without any tools.

```css
/* Without nesting — repetitive */
.card { background: white; padding: 20px; }
.card h2 { font-size: 1.5rem; }
.card p { color: #555; }
.card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

/* With nesting — clean and grouped */
.card {
  background: white;
  padding: 20px;

  h2 {
    font-size: 1.5rem;
  }

  p {
    color: #555;
  }

  &:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
}
```

The `&` symbol refers to the parent selector. `&:hover` becomes `.card:hover`.

```css
.button {
  background: blue;
  color: white;

  &:hover {
    background: darkblue;
  }

  &.active {
    background: green;
  }

  &.large {
    font-size: 1.25rem;
    padding: 14px 28px;
  }
}
```

Real situation: A component like a navigation bar or a card — all related styles stay grouped together in one block. When you need to find or change styles for that component, everything is in one place instead of scattered across the file.

> CSS nesting is supported in all modern browsers as of 2024. No build tools needed. Check `caniuse.com` if you need to support older browsers.

---

## Semantic HTML Elements

Semantic elements have meaningful names that describe their role in the page. They tell both the browser and developers what the content is for.

```html
<!-- Non-semantic — no meaning -->
<div id="header">...</div>
<div class="nav">...</div>
<div id="main">...</div>
<div class="article">...</div>
<div id="footer">...</div>

<!-- Semantic — clear meaning -->
<header>...</header>
<nav>...</nav>
<main>
  <article>...</article>
  <aside>...</aside>
  <section>...</section>
</main>
<footer>...</footer>
```

| Element | Meaning |
|---|---|
| `<header>` | Introductory content, logo, navigation |
| `<nav>` | Navigation links |
| `<main>` | The main content of the page (only one per page) |
| `<section>` | A thematic grouping of content |
| `<article>` | Self-contained content (blog post, news article) |
| `<aside>` | Secondary content, sidebar, related links |
| `<footer>` | Bottom section, copyright, contact info |

Why use semantic elements:
- Search engines understand your page structure better (SEO)
- Screen readers navigate by landmarks — `<nav>`, `<main>`, `<footer>` are recognized automatically
- Your code is easier to read and maintain
- They behave exactly like `<div>` but carry meaning

---

## Box Shadow

`box-shadow` adds a shadow behind an element.

```css
box-shadow: offset-x offset-y blur spread color;
```

```css
.card {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
/*            |  |   |       |_____________|
              x  y  blur         color        */
}
```

| Value | What it does |
|---|---|
| `offset-x` | Horizontal position of shadow. Positive = right, negative = left |
| `offset-y` | Vertical position. Positive = down, negative = up |
| `blur` | How blurry the shadow is. 0 = sharp, higher = softer |
| `spread` | How much larger than the element. Positive = bigger, negative = smaller |
| `color` | Shadow color — use `rgba` for transparency control |

```css
/* Subtle card shadow */
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);

/* Lifted card on hover */
box-shadow: 0 16px 40px rgba(0, 0, 0, 0.14);

/* Inner shadow (inset — appears inside the element) */
box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);

/* Multiple shadows — comma separated */
box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 8px 24px rgba(0,0,0,0.08);
```

> Use `rgba()` for shadows instead of named colors. A shadow with `rgba(0, 0, 0, 0.1)` blends naturally with any background color. A shadow with `color: gray` looks flat and artificial.

---

## Text Shadow

`text-shadow` adds a shadow behind text.

```css
text-shadow: offset-x offset-y blur color;
```

```css
h1 {
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}
```

There is no `spread` value in `text-shadow` — only offset-x, offset-y, blur, and color.

```css
/* Subtle depth */
text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);

/* Glow effect */
text-shadow: 0 0 10px rgba(37, 99, 235, 0.8);

/* Hard shadow, no blur */
text-shadow: 3px 3px 0 #000;

/* Multiple shadows for layered effect */
text-shadow: 
  1px 1px 0 #ccc,
  2px 2px 0 #bbb,
  3px 3px 0 #aaa;
```

---

### Shadow Generator Websites

Building shadows by hand is slow. These tools give you a visual editor where you drag sliders and copy the CSS:

- `shadows.brumm.af` — box shadow generator with live preview
- `css.glass` — frosted glass effect with shadows
- `cssgenerator.org` — box shadow, text shadow, and more
- Any search for "CSS box shadow generator" will find usable tools

---

## Centering a Div with Flex

The most common layout task in CSS. There are multiple scenarios:

### Center Horizontally and Vertically in the Viewport

```css
body {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
}
```

### Center a Child Inside a Parent

```css
.parent {
  display: flex;
  justify-content: center; /* Horizontal */
  align-items: center;     /* Vertical */
  height: 400px;           /* Parent needs a height for vertical centering to work */
}
```

### Center Text Inside a Button or Badge

```css
.button {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px; /* Space between icon and text */
}
```

> Flex centering requires the parent to have a defined height for vertical centering to have any effect. If the parent has no height (it just wraps content), `align-items: center` has nothing to center within.

---

## `caniuse.com` — Browser Compatibility Checker

Before using a newer CSS feature, check if it works in all the browsers your users might have.

Go to `caniuse.com`, search for the feature (e.g. "CSS nesting", "aspect-ratio", "gap in flexbox"), and it shows you a color-coded table of every browser and version — green means supported, red means not.

Real situation: You use `gap` in a flex layout and it looks broken for one user. You check `caniuse.com` and discover that older versions of Safari do not support `gap` in flexbox. You then either add a fallback or use `margin` instead.

---

## CSS Autoprefixer

Some CSS properties need **vendor prefixes** to work in older browsers. Instead of writing them by hand, Autoprefixer adds them automatically.

Without autoprefixer, you might need:

```css
.box {
  -webkit-transform: rotate(45deg);  /* Old Chrome, Safari */
  -moz-transform: rotate(45deg);     /* Old Firefox */
  -ms-transform: rotate(45deg);      /* Old IE */
  transform: rotate(45deg);          /* Standard */
}
```

With Autoprefixer, you write only the standard property and it generates the prefixed versions.

Online tool: `autoprefixer.github.io` — paste your CSS, it returns the prefixed version.

In real projects, Autoprefixer is usually part of a build tool like PostCSS, Vite, or Webpack — it runs automatically whenever you save.

---

## Linear Gradient

`linear-gradient()` creates a smooth color transition in a straight line.

```css
.element {
  background: linear-gradient(direction, color1, color2);
}
```

### Direction Options

```css
/* By angle */
background: linear-gradient(45deg, #2563eb, #7c3aed);

/* By keyword */
background: linear-gradient(to right, #2563eb, #7c3aed);
background: linear-gradient(to bottom, #2563eb, #7c3aed);
background: linear-gradient(to bottom right, #2563eb, #7c3aed);
```

### Multiple Color Stops

```css
background: linear-gradient(to right, #ff6b6b, #ffd93d, #6bcb77, #4d96ff);
```

### Controlling Stop Positions

```css
background: linear-gradient(to right, 
  #2563eb 0%,     /* Blue starts at left edge */
  #2563eb 30%,    /* Blue holds until 30% */
  #7c3aed 70%,    /* Purple starts at 70% */
  #7c3aed 100%    /* Purple holds to right edge */
);
```

### Hard Stop — Sharp Edge Between Colors

```css
/* No smooth blend — hard line between colors */
background: linear-gradient(to right, #2563eb 50%, #7c3aed 50%);
```

### Dark Overlay on Background Image

One of the most used patterns — a gradient overlay so text on top stays readable:

```css
.hero {
  background-image: 
    linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)),
    url("hero.jpg");
  background-size: cover;
  background-position: center;
}
```

---

## Radial Gradient

`radial-gradient()` creates a color transition that radiates outward from a center point — circular or elliptical.

```css
.element {
  background: radial-gradient(shape size at position, color1, color2);
}
```

```css
/* Simple radial */
background: radial-gradient(circle, #2563eb, #7c3aed);

/* Ellipse (default shape) */
background: radial-gradient(ellipse, #ff6b6b, #ffd93d);

/* Control center point */
background: radial-gradient(circle at top left, #2563eb, #7c3aed);
background: radial-gradient(circle at 30% 70%, #2563eb, #7c3aed);

/* Control size */
background: radial-gradient(circle closest-side, #2563eb, transparent);
```

Real situation: A soft background glow or spotlight effect behind a hero illustration. A radial gradient from the brand color to transparent creates a glowing atmosphere.

```css
.hero-section {
  background: radial-gradient(ellipse at center, 
    rgba(37, 99, 235, 0.15) 0%, 
    transparent 70%
  );
}
```

---

### Gradient Website — UI Gradient

`uigradients.com` — a collection of beautiful hand-picked two-color gradients. Click any gradient to copy the CSS immediately.

Other options:
- `cssgradient.io` — full gradient editor, supports radial and linear
- `gradienta.io` — complex multi-color mesh gradients
- `meshgradient.ibelick.com` — modern mesh gradient generator

---












