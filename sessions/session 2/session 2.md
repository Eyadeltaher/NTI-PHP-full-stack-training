
# CSS

---

##  Border & Its Properties

### Basic Syntax

```css
div {
  border: width style color;
}

/* Example */
div {
  border: 2px solid black;
}
```

The three parts are:

| Part | Options | Example |
|---|---|---|
| Width | Any size unit | `2px`, `4px` |
| Style | `solid`, `dashed`, `dotted`, `double`, `none` | `solid` |
| Color | Any color format | `black`, `#333`, `rgb(0,0,0)` |

### Target Individual Sides

```css
div {
  border-top: 2px solid red;
  border-right: 1px dashed blue;
  border-bottom: 3px dotted green;
  border-left: 2px solid black;
}
```

---

### `border-radius` — Rounded Corners

Rounds the corners of an element.

```css
div {
  border-radius: 10px;    /* All 4 corners */
  border-radius: 50%;     /* Perfect circle (on a square element) */
}
```

> **Note:** `border-radius` does **NOT require a border to exist**. It works on the element's background too — you can round a `div`'s background without any visible border line.

```css
/*  Rounded card with no border — border-radius still works */
.card {
  background-color: lightblue;
  border-radius: 16px;
  /* No border property needed */
}
```

### Target Individual Corners

```css
div {
  border-top-left-radius: 20px;
  border-top-right-radius: 5px;
  border-bottom-right-radius: 20px;
  border-bottom-left-radius: 5px;
}

/* Shorthand: top-left | top-right | bottom-right | bottom-left */
border-radius: 20px 5px 20px 5px;
```

>  Order goes **clockwise** starting from top-left — same logic as margin/padding shorthand.

---

##  Font & Its Properties

### `font-family`

Sets the typeface of the text.

```css
p {
  font-family: Arial, Helvetica, sans-serif;
}
```

###  Why More Than One Font Family?

You always list **multiple fonts** separated by commas — this is called a **font stack**. The browser tries each one in order:

1. Try the first font — if it's installed on the user's device, use it
2. If not found, try the second font
3. Keep going until one works
4. The last one should always be a **generic family** as the final fallback

```css
font-family: "Roboto",   "Helvetica Neue",   Arial   , sans-serif;
/*             ↑ preferred    ↑ fallback      ↑ system     ↑ last resort */
```

>  You don't control what fonts users have installed on their computers. The font stack is your safety net — if your preferred font isn't available, the text still looks reasonable with the fallback.

### Generic Font Families (Last Resort Fallbacks)

| Generic | Style |
|---|---|
| `serif` | Has small strokes at ends of letters (Times New Roman style) |
| `sans-serif` | Clean, no strokes (Arial style) |
| `monospace` | Every character same width (code style) |
| `cursive` | Handwriting style |
| `fantasy` | Decorative style |

---

### `font-size`

```css
p {
  font-size: 16px;   /* Fixed size */
  font-size: 1rem;   /* Relative to root element (recommended) */
  font-size: 1.2em;  /* Relative to parent element */
}
```

---

### `font-weight`

Controls how bold the text is.

```css
p {
  font-weight: normal;   /* = 400 */
  font-weight: bold;     /* = 700 */
  font-weight: 100;      /* Thin */
  font-weight: 900;      /* Extra black */
}
```

>  **Note:** Not all font families support all weight values. If you set `font-weight: 300` and nothing changes, it means that font only has `normal` (400) and `bold` (700) built in — the intermediate weights don't exist for it.

> Variable-weight fonts (like Google Fonts with multiple weights loaded) will respond to any value. Always check which weights a font actually supports.

---

### `font-style`

```css
p {
  font-style: normal;
  font-style: italic;
  font-style: oblique;
}
```

---

### `line-height`

Controls the vertical space between lines of text.

```css
p {
  line-height: 1.6;   /* 1.6× the font size — recommended for readability */
}
```

---

### `text-align`

```css
p {
  text-align: left;
  text-align: center;
  text-align: right;
  text-align: justify;
}
```

---

### `text-decoration`

```css
a {
  text-decoration: none;        /* Remove underline from links */
  text-decoration: underline;
  text-decoration: line-through;
}
```

---

### `text-transform`

```css
p {
  text-transform: uppercase;   /* ALL CAPS */
  text-transform: lowercase;   /* all lowercase */
  text-transform: capitalize;  /* First Letter Of Each Word */
}
```

---

###  How to Use Google Fonts

Google Fonts gives you free, high-quality fonts that load from Google's servers — no installation needed.

**Step 1:** Go to [fonts.google.com](https://fonts.google.com)

**Step 2:** Choose a font → select the weights you need → copy the `<link>` tag

**Step 3:** Paste the link in your HTML `<head>` **before** your CSS file:

```html
<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">  <!-- Your CSS comes AFTER -->
</head>
```

**Step 4:** Use it in your CSS:

```css
body {
  font-family: "Roboto", sans-serif;
}
```

>  Always load Google Fonts **before** your own CSS file in `<head>`, so your CSS can reference the font immediately.

>  Only load the weights you actually use. Loading all weights slows down your page.

---

###  WhatFont — Browser Extension

**WhatFont** is a browser extension that lets you **click on any text on any website** and instantly see:
- The font family name
- Font size
- Font weight
- Line height
- Color

---

##  Spacing — Margin & Padding

### The Difference (Quick Reminder)

```
[ MARGIN ] [ BORDER ] [ PADDING ] [ CONTENT ] [ PADDING ] [ BORDER ] [ MARGIN ]
   outside                inside                inside               outside
```

- **Margin** = space outside the element (between it and neighbors)
- **Padding** = space inside the element (between border and content)

---

### Shorthand — Top Right Bottom Left (Clockwise)

```css
/* 4 values: top | right | bottom | left */
margin: 10px 20px 30px 40px;

/* 3 values: top | right&left | bottom */
margin: 10px 20px 30px;
/* left gets the SAME value as right → left = 20px */

/* 2 values: top&bottom | right&left */
margin: 10px 20px;

/* 1 value: all sides */
margin: 10px;
```

>  **Note:** When you provide **3 values** (top, right, bottom) — the **left side automatically takes the same value as right**. You don't need to write it.

---

### `margin: auto` — Horizontal Centering Only

`auto` tells the browser: *"figure out the margin yourself"* — it splits the available space equally on both sides.

```css
.container {
  width: 800px;
  margin: 150px auto;
  /* top & bottom = 150px | left & right = auto (centered horizontally) */
}
```

>  `margin: auto` only works on the **horizontal axis (left & right)**. It has **no effect vertically** — you cannot center an element vertically this way.

>  There is **no `padding: auto`** — padding does not accept `auto` as a value.

---

### Padding & Border Affect Element Size — Margin Does NOT

```css
.box {
  width: 200px;
  padding: 20px;   /* ← Adds to size (unless border-box is set) */
  border: 5px solid black; /* ← Adds to size */
  margin: 30px;    /* ← Does NOT affect the element's own size */
}
```

>  Margin just pushes the element away from its neighbors — it's external. Padding and border are part of the element's physical body.

>  This is exactly why `box-sizing: border-box` matters — it keeps padding and border from silently enlarging your element.

---

### Solving Margin with a Parent Container

>  `box-sizing: border-box` fixes **padding and border** — but it does **NOT solve margin**.

If a child element has a `margin-top` and you expect it to push down inside the parent, it can "collapse" through the parent instead. The fix:

```css
/* Give the PARENT padding equal to what the CHILD needs as margin */

/*  Problem */
.parent { background: lightgray; }
.child  { margin-top: 30px; } /* This collapses — bleeds through parent */

/*  Solution */
.parent { padding-top: 30px; }  /* Use parent's padding instead */
.child  { margin-top: 0; }
```

>  This is called **margin collapse**. When a child's margin has no border or padding to "push against" on the parent, it escapes outside the parent. Giving the parent padding creates a barrier that stops this.

---

##  Background

### `background-color`

```css
div {
  background-color: #f0f0f0;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent */
}
```

---

### `background-image`

```css
div {
  background-image: url("images/hero.jpg");
}
```

---

### `background-size`

Controls how the background image is sized inside the element.

```css
div {
  background-size: 100% 100%; /* Stretches image to fill — distorts resolution */
  background-size: cover;     /* Scales image to fill without distortion */
  background-size: contain;   /* Scales image to fit entirely inside (may leave gaps) */
  background-size: 300px;     /* Fixed size */
}
```

>  **`100% 100%`** forces the image to match the element's exact width AND height. If proportions don't match, the image gets **stretched and distorted** — it looks bad at any size.>
> **`cover`** is the professional solution. It scales the image proportionally until it covers the entire element — no distortion, no gaps. Parts of the image may be cropped, but it always looks clean.

---

### `background-position`

Controls where the image is positioned inside the element. Most useful with `cover` to control which part stays visible when cropped.

```css
div {
  background-position: center center;  /*  Most common — center the image */
  background-position: top left;
  background-position: 50% 20%;        /* x% from left | y% from top */
}
```

---

### `background-repeat`

By default, background images tile (repeat) to fill the element.

```css
div {
  background-repeat: no-repeat;   /* Usually what you want for photos */
  background-repeat: repeat;      /* Default — tiles in both directions */
  background-repeat: repeat-x;    /* Tiles horizontally only */
  background-repeat: repeat-y;    /* Tiles vertically only */
}
```

---

### Multiple Background Images

You can layer **more than one** background image on a single element — separate them with commas. The **first one listed is on top**:

```css
div {
  background-image: url("overlay.png"), url("hero.jpg");
  /* overlay.png sits on top of hero.jpg */
}
```

>  **Best case:** Adding a dark gradient over a photo so text on top stays readable:

```css
.hero {
  background-image: 
    linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
    url("hero.jpg");
  background-size: cover;
  background-position: center;
}
```

---

### `background-attachment`

Controls whether the background scrolls with the page or stays fixed.

```css
div {
  background-attachment: scroll;  /* Default — moves with the page */
  background-attachment: fixed;   /* Stays fixed as you scroll */
}
```

>  **Best case for `fixed`:** Hero sections with a parallax scrolling effect — the background image stays still while the content scrolls over it.

---

### Full Background Shorthand

```css
.hero {
  background: url("hero.jpg") center/cover no-repeat fixed;
  /* url | position / size | repeat | attachment */
}
```

---

##  Float

### What is Float?

`float` was originally designed to wrap **text around images** (like in a magazine). It pulls an element to the left or right and lets content flow around it.

```css
img {
  float: left;   /* Pull image to the left, text wraps around the right */
  float: right;  /* Pull image to the right, text wraps around the left */
}
```

---

###  Float Problems

Float has two major side effects that you must know:

**Problem 1 — The background disappears**
When all children inside a parent are floated, the parent collapses to zero height — its background vanishes.

**Problem 2 — It disrespects elements after it**
Elements that come after a floated element will wrap around it whether you want them to or not.

```html
<!-- The parent div collapses — no visible background -->
<div class="parent">
  <div style="float: left;">Box 1</div>
  <div style="float: left;">Box 2</div>
</div>
<p>This paragraph wraps around the floated boxes unexpectedly</p>
```

---

###  Fix 1 — `clear: both`

Add an empty element **after the last floated child** inside the parent, with `clear: both`:

```html
<div class="parent">
  <div style="float: left;">Box 1</div>
  <div style="float: left;">Box 2</div>
  <div style="clear: both;"></div>  <!-- ← Forces parent to expand -->
</div>
```

Or in CSS on the element that comes after:

```css
.after-float {
  clear: both; /* This element refuses to wrap — it starts below all floats */
}
```

>  `clear: both` says: *"I don't care about floated elements — I start fresh below all of them."*

---

### Fix 2 — `overflow: hidden` on the Parent

A cleaner solution — apply it to the parent container:

```css
.parent {
  overflow: hidden; /* Forces parent to contain its floated children */
}
```

>  This works because `overflow: hidden` creates a **Block Formatting Context** — it forces the parent to calculate its height including floated children.

---

### When to Use Float Today

>  Float was the main layout tool before CSS Flexbox and Grid existed. Today it's mostly **replaced by Flexbox** for layouts.

>  The main modern use case for `float` is still its original purpose: **wrapping text around an image**, like in a blog article or newspaper-style layout.

```css
/*  Still valid today */
article img {
  float: left;
  margin-right: 16px;
  margin-bottom: 8px;
}
```










