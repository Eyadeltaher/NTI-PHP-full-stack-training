# CSS — Advanced Concepts & Properties

---

## Overflow & Its Properties

### What is Overflow?

When content inside an element is **larger than the element itself**, the browser has to decide what to do with the extra content. That is what `overflow` controls.

```css
.box {
  width: 200px;
  height: 100px;
  overflow: visible; /* default */
}
```

### The Four Values

#### `overflow: visible` — Default

The content spills **outside** the box. The element stays the same size but the content bleeds out.

```css
.card {
  width: 200px;
  height: 80px;
  overflow: visible;
}
```

Real situation: You have a card with a fixed height and a long description — the text flows outside the card and overlaps whatever is below it. This is often a bug, not intentional.

---

#### `overflow: hidden` — Clip Everything Outside

Anything outside the element's boundaries is **cut off and invisible**.

```css
.card {
  width: 200px;
  height: 80px;
  overflow: hidden;
}
```

Real situations:

- Cropping an image inside a circular avatar: `border-radius: 50%; overflow: hidden`
- Hiding the float collapse problem (as covered in previous notes)
- Preventing a dropdown from breaking a layout
- Image zoom effects on hover — the image scales with `transform: scale()` but `overflow: hidden` keeps it clipped inside the container

```css
/* Image zoom on hover — overflow: hidden is the key */
.image-wrapper {
  overflow: hidden;
  border-radius: 8px;
}

.image-wrapper img {
  transition: transform 0.4s ease;
}

.image-wrapper:hover img {
  transform: scale(1.1);
}
```

---

#### `overflow: scroll` — Always Show Scrollbars

Forces scrollbars to appear **always**, even if the content fits.

```css
.sidebar {
  overflow: scroll;
}
```

Not commonly used because scrollbars appear even when unnecessary, which looks odd.

---

#### `overflow: auto` — Scrollbars Only When Needed

The browser adds scrollbars **only if the content overflows**. If everything fits, no scrollbars appear.

```css
.sidebar {
  height: 400px;
  overflow: auto;
}
```

Real situations:

- A chat window with a fixed height where messages scroll
- A sidebar with too many links
- A terms-and-conditions box with a fixed size

> `overflow: auto` is the one you will use most in real projects. It is the smart version of `scroll`.

---

### `overflow-x` and `overflow-y` — Control Each Axis Separately

```css
.container {
  overflow-x: hidden;  /* No horizontal scroll */
  overflow-y: auto;    /* Vertical scroll when needed */
}
```

Real situation: A full-width page layout where content sometimes accidentally creates a horizontal scrollbar. Setting `overflow-x: hidden` on the `body` or a wrapper is a common fix.

---

### Overflow Summary

| Value     | What happens to content outside the box |     |
| --------- | --------------------------------------- | --- |
| `visible` | Spills out — still visible (default)    |     |
| `hidden`  | Clipped — invisible                     |     |
| `scroll`  | Always shows scrollbars                 |     |
| `auto`    | Scrollbars only when needed             |     |

---

## Margin Collapse — The Silent Layout Bug

### What is Margin Collapse?

When two **vertical margins** from different elements touch each other, they do not add up — instead, they **merge into one**, and only the larger margin survives.

This only happens with **vertical margins** (top and bottom). Horizontal margins never collapse.

```css
.box-1 { margin-bottom: 40px; }
.box-2 { margin-top: 20px; }

/* You expect: 40 + 20 = 60px gap */
/* You get: 40px gap — the larger one wins */
```

---

### Where it Happens

**Case 1 — Between two sibling elements**

```html
<p style="margin-bottom: 30px;">First paragraph</p>
<p style="margin-top: 20px;">Second paragraph</p>
<!-- Gap = 30px, not 50px -->
```

**Case 2 — Parent and first/last child**

The most confusing case. When a child has a `margin-top` and there is no border or padding on the parent between them, the child's margin "escapes" through the parent and pushes the parent down instead.

```html
<div class="parent">           <!-- You expect this to stay in place -->
  <div class="child" style="margin-top: 40px;">Content</div>
</div>
<!-- The parent moves down 40px instead of the child moving inside the parent -->
```

---

### How to Solve Margin Collapse

**Solution 1 — Add padding to the parent**

```css
.parent {
  padding-top: 1px; /* Even 1px stops the collapse */
}
```

Or give the parent the spacing instead of the child:

```css
.parent { padding-top: 40px; }
.child  { margin-top: 0; }
```

**Solution 2 — Add a border to the parent**

```css
.parent {
  border-top: 1px solid transparent; /* Invisible but stops collapse */
}
```

**Solution 3 — Use `overflow: hidden` on the parent**

```css
.parent {
  overflow: hidden; /* Creates a Block Formatting Context — stops collapse */
}
```

**Solution 4 — Use `display: flex` on the parent**

Flex containers never collapse margins with their children:

```css
.parent {
  display: flex;
  flex-direction: column;
}
```

> The root cause is always the same: there is no **barrier** between the parent's edge and the child's margin. Any of these solutions create that barrier.

---

## The Four Position Properties — top, right, bottom, left

These properties only work when an element has `position` set to anything other than `static`.

```css
.box {
  position: absolute;
  top: 20px;
  left: 40px;
}
```

### The Stretch Trick — All Four Set to Zero

Setting all four to `0` makes the element **stretch to completely fill its parent**:

```css
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  /* Element now covers its entire parent */
}
```

This works because you are pulling all four edges to the parent's edges at the same time. It is equivalent to `width: 100%; height: 100%` but more reliable in many cases.

> The parent must have `position: relative` for this to work. Without it, the element stretches to the nearest positioned ancestor — usually the whole page.

Real situations:

- Dark overlay on top of an image before text
- A loading spinner that covers a card while data loads
- A modal backdrop that covers the entire page

```css
/* Image with dark overlay for text readability */
.image-container {
  position: relative;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
}
```

---

### You Cannot Use left + right if width is Set (and vice versa)

When you give an element a `width`, setting both `left` and `right` at the same time creates a conflict — the browser cannot honor all three.

```css
/* Conflict — browser will ignore right */
.box {
  position: absolute;
  width: 200px;
  left: 0;
  right: 0; /* Ignored — width already controls the horizontal size */
}
```

You can use either:

- `left` + `right` with **no width** (the element stretches between them)
- `left` or `right` alone **with** a width

```css
/* Stretch between two points — no width needed */
.box {
  position: absolute;
  left: 20px;
  right: 20px;
  /* Width is automatically: container width minus 40px */
}
```

---

### When You Give an Element `position`, Width Becomes `auto`

By default, block elements take full width. But as soon as you set `position: absolute` or `position: fixed`, the element **shrinks to fit its content** — width becomes `auto` (content-width).

```css
/* A div normally takes full width */
div { background: red; } /* Full width */

/* Now it only wraps its content */
div {
  position: absolute;
  background: red; /* Only as wide as the text inside */
}
```

Real situation: You position a dropdown menu absolutely, and it suddenly becomes very narrow because it only wraps the text. Fix it by explicitly setting `width` or using `min-width`.

```css
.dropdown {
  position: absolute;
  min-width: 200px; /* Prevents it from being too narrow */
}
```

---

## Hiding Elements — Three Different Ways

There are three ways to make an element invisible, and they behave very differently.

### `display: none` — Completely Removed

The element disappears **and its space is released**. Other elements fill the gap as if it never existed.

```css
.modal {
  display: none;
}

.modal.active {
  display: block;
}
```

Real situation: Hiding a mobile menu, hiding a modal before it is triggered, toggling sections on/off.

- Not visible: yes
- Takes up space: no
- Children affected: yes — all children disappear too
- Transitions work on it: no — you cannot animate display

---

### `visibility: hidden` — Invisible but Space Remains

The element becomes invisible but **its space is preserved**. Other elements do not move.

```css
.tooltip {
  visibility: hidden;
}

.tooltip.active {
  visibility: visible;
}
```

Real situation: Holding a reserved space in a layout that you do not want to collapse — like a placeholder in a grid while content loads.

- Not visible: yes
- Takes up space: yes
- Children affected: yes — unless you set `visibility: visible` on a specific child

---

### `opacity: 0` — Transparent but Interactive

The element is fully transparent but **still exists, takes space, and responds to clicks**.

```css
.button {
  opacity: 0;
  transition: opacity 0.3s ease;
}

.button:hover {
  opacity: 1;
}
```

Real situation: Fade-in animations. A button or image that fades in on hover or on page load.

- Not visible: yes
- Takes up space: yes
- Responds to clicks: yes — a user can still click an invisible element
- Can be animated: yes — transitions and animations work perfectly

> This is an important trap: `opacity: 0` does not disable click events. If an invisible element sits on top of another element, it can block clicks on the one behind it.

---

### important --> The Effect of `opacity` on Children

When you set `opacity` on a parent element, **all children inherit it** and you cannot override it on a child.

```css
.card {
  opacity: 0.5;
}

.card .title {
  opacity: 1; /* Does nothing — still shows at 0.5 because the parent controls it */
}
```

If you need a semi-transparent background without affecting the children, use `rgba()` on the background instead:

```css
/* Wrong approach — children fade too */
.card { opacity: 0.5; }

/* Correct approach — only background is transparent */
.card { background-color: rgba(0, 0, 0, 0.5); }
```

---


## `!important` — The Override Keyword

`!important` forces a CSS rule to win over all other rules, regardless of specificity or order.

```css
p {
  color: blue !important;
}

/* This will NOT override it, even though it comes later */
p {
  color: red;
}
/* Result: blue */
```

Real situation: Overriding styles from a third-party CSS library you cannot edit — like Bootstrap or a plugin.

> Use `!important` as a **last resort only**. It breaks the natural cascade and makes debugging very painful. When two rules both have `!important`, the one with higher specificity wins — and you are back to the same problem.

---

## CSS Variables

### What Are CSS Variables?

CSS variables (also called custom properties) let you **store a value once and reuse it everywhere**. When you change the variable in one place, every element using it updates automatically.

### How to Write Them

Variables must start with `--` (double dash):

```css
--variable-name: value;
```

To use them, you call `var()`:

```css
color: var(--variable-name);
```

---

### Define Variables in `:root` or `html`

`:root` targets the top-level element of the page. Defining variables there makes them **globally available** to every element on the page.

```css
:root {
  --primary-color: #2563eb;
  --secondary-color: #64748b;
  --font-size-base: 16px;
  --border-radius: 8px;
  --spacing-md: 24px;
}
```

Then use them anywhere:

```css
.button {
  background-color: var(--primary-color);
  border-radius: var(--border-radius);
  padding: var(--spacing-md);
}

.link {
  color: var(--primary-color); /* Same value, no repetition */
}
```

Real situation: You have a brand color used in 50 places across your CSS. Without variables, changing the brand color means finding and replacing 50 lines. With variables, you change one line in `:root` and everything updates.

---

### Fallback Values

You can provide a fallback in case the variable is not defined:

```css
color: var(--primary-color, #000); /* Uses black if variable is missing */
```

---

### Local Variables

You can also define variables on a specific element — they will only apply to that element and its children:

```css
.card {
  --card-bg: #f8fafc;
  --card-padding: 20px;

  background-color: var(--card-bg);
  padding: var(--card-padding);
}
```

---

## Specificity — Which Rule Wins?

When two CSS rules target the same element, the browser uses **specificity** to decide which one to apply. Higher specificity always wins, regardless of order.

### The Specificity Score

Each selector type has a weight:

|Selector|Weight|
|---|---|
|Inline style (`style=""`)|1000|
|ID selector (`#id`)|100|
|Class, attribute, pseudo-class (`.class`, `:hover`)|10|
|Element selector (`p`, `div`, `h1`)|1|

You calculate the score by counting each type:

```css
p              { color: black; }   /* score: 1 */
.text          { color: blue; }    /* score: 10 */
#title         { color: green; }   /* score: 100 */
#title.text    { color: red; }     /* score: 110 */
```

Real situation: You write `.card p { color: gray; }` but the paragraph stays black. You check and find `#main p { color: black; }` elsewhere — the ID selector wins because 101 beats 11.

---

### The Specificity Hierarchy (Most to Least)

```
inline style > #id > .class / :pseudo-class > element tag
```

> The more specific your selector is, the harder it is to override. This is why using IDs for styling can cause problems — they are very hard to override without `!important` or another ID.

---

## `calc()` — Math Inside CSS

`calc()` lets you perform **mathematical calculations directly in CSS values**, including mixing different units.

```css
.sidebar {
  width: calc(100% - 260px);
  /* Full width minus a fixed sidebar */
}
```

You can use `+`, `-`, `*`, `/` inside `calc()`:

```css
.element {
  width: calc(50% + 20px);
  height: calc(100vh - 80px);    /* Full viewport minus navbar height */
  padding: calc(var(--spacing) * 2);
  font-size: calc(1rem + 0.5vw); /* Fluid typography */
}
```

> Always put **spaces around** `+` and `-` inside `calc()`. Without spaces they will not work:

```css
width: calc(100% - 20px);  /* Correct */
width: calc(100%-20px);    /* Broken */
```

Real situations:

- A layout where you want to take full width minus a known sidebar width
- A section that is full viewport height minus the navbar height
- Fluid font sizes that scale between mobile and desktop

---

## Smooth Scroll

Makes the page scroll smoothly instead of jumping instantly when navigating to anchor links.

```css
html {
  scroll-behavior: smooth;
}
```

---

## Linking to an ID with Anchor Tags

You can link to a specific section on the same page by pointing the `href` to an element's ID:

```html
<!-- The link -->
<a href="#contact">Go to Contact</a>

<!-- The target section -->
<section id="contact">
  <h2>Contact Us</h2>
</section>
```

When the user clicks the link, the page jumps (or scrolls smoothly if `scroll-behavior: smooth` is set) to the element with that ID.

Real situation: Every navigation bar with anchor links uses this pattern — "About", "Services", "Contact" links on a landing page.

---

## The `for` Attribute in Labels

The `for` attribute connects a `<label>` to its `<input>` by matching the input's `id`. When a user clicks the label, the input gets focused automatically.

```html
<label for="email">Email Address</label>
<input type="email" id="email" name="email">
```

Real situation: Clicking the word "Email Address" focuses the input field. This is important for accessibility — screen readers read the label aloud, and the click area for the input becomes much larger (the entire label text, not just the tiny input box).

> Without `for`, the label and input have no connection — clicking the label text does nothing.

---

## Why Only Headers Should Use `vh` for Height

Block elements like `<div>` do not have an intrinsic height — their height is determined by their content. If you set `height: 50vh` on a `<div>` and there is not enough content to fill it, the content just floats at the top with empty space below.

Headers (hero sections) are the exception because they are **intentionally designed as full-screen or large visual blocks** — their height is a deliberate design choice, not determined by content.

```css
/* Hero header — intentional full screen */
header.hero {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
```

For other sections, use `padding` instead of `height` — it grows naturally with the content:

```css
/* Section — let content determine height */
.about-section {
  padding: 80px 0; /* Space above and below content */
  /* No height set — expands naturally */
}
```

> Setting fixed heights in `px` or `vh` on content sections is a common beginner mistake. Content almost always varies — especially on different screen sizes — and a fixed height will either clip content or leave awkward empty space.

---

## cubic-bezier — Custom Animation Timing

`cubic-bezier()` is a function that lets you define a **completely custom speed curve** for transitions and animations, beyond the built-in options like `ease` and `linear`.

```css
.button {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  /* This creates a bouncy overshoot effect */
}
```

The four numbers represent two control points on a curve graph. You do not need to understand the math — the website `cubic-bezier.com` gives you a visual editor where you drag the curve and copy the values.

> Go to `cubic-bezier.com` to visually design your own timing curve. You drag handles on a graph and it generates the `cubic-bezier()` values for you. The preview shows you exactly how the animation will feel before you use it.

Common custom curves:

- Bouncy/overshoot feel: `cubic-bezier(0.34, 1.56, 0.64, 1)`
- Fast then snap: `cubic-bezier(0.87, 0, 0.13, 1)`
- Smooth deceleration: `cubic-bezier(0.22, 1, 0.36, 1)`

---

# CSS — Transition & Animation

## The Difference Between Transition and Animation

Before going into either one, you need to understand when to use which:

| | Transition | Animation |
|---|---|---|
| Trigger | Needs a state change (hover, focus, class added) | Plays on its own — no trigger needed |
| Direction | A to B only | Can go A to B to C to D... any sequence |
| Loop | Cannot loop on its own | Can loop infinitely |
| Control | Simple | Full control over every step |
| Best for | Hover effects, UI feedback, toggling | Loaders, entrance effects, attention-grabbing motion |


---

## Transition

### What it Does

`transition` watches a CSS property and whenever that property changes — for any reason — it makes the change happen gradually over time instead of instantly.

```css
.button {
  background-color: blue;
  transition: background-color 0.3s ease;
}

.button:hover {
  background-color: darkblue;
}
```

Without the transition, the color snaps immediately. With it, the color fades smoothly over 0.3 seconds.

---

### Syntax

```css
transition: property duration timing-function delay;
```

```css
.box {
  transition: background-color 0.3s ease 0s;
/*            |_____________| |___| |__| |_|
                  property    time  curve delay  */
}
```

---

### The Four Parts

#### Property — What to Watch

Which CSS property should animate when it changes.

```css
transition: background-color 0.3s ease;
transition: transform 0.4s ease;
transition: opacity 0.2s linear;
transition: all 0.3s ease; /* Watch every property — use carefully */
```

> Using `all` is convenient but expensive. If any property on that element changes — even ones you do not intend — it will animate. Be specific when you can.

---

#### Duration — How Long

How many seconds or milliseconds the transition takes.

```css
transition: opacity 0.3s ease;   /* 300ms — fast, snappy */
transition: opacity 0.6s ease;   /* 600ms — moderate */
transition: opacity 1.2s ease;   /* 1.2s — slow, dramatic */
```

Real guideline:
- UI feedback (button hover, color change): 150ms — 300ms
- Element entering the screen (dropdown, modal): 300ms — 500ms
- Page-level transitions: 400ms — 700ms

Anything above 700ms usually feels too slow and makes the interface feel unresponsive.

---

#### Timing Function — The Speed Curve

Controls whether the animation starts fast or slow, accelerates or decelerates.

```css
transition: transform 0.3s ease;
transition: transform 0.3s linear;
transition: transform 0.3s ease-in;
transition: transform 0.3s ease-out;
transition: transform 0.3s ease-in-out;
transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
```

| Value | Behavior | Best for |
|---|---|---|
| `ease` | Slow start, fast middle, slow end | General UI — feels natural |
| `linear` | Constant speed throughout | Spinners, continuous rotation |
| `ease-in` | Starts slow, ends fast | Elements leaving the screen |
| `ease-out` | Starts fast, slows at end | Elements entering the screen |
| `ease-in-out` | Slow start and slow end | Elements moving across the screen |
| `cubic-bezier()` | Fully custom curve | Bouncy, springy, unique feels |

> `ease-out` is the most used for UI elements entering view. Things in real life decelerate before stopping — a drawer sliding open, a dropdown appearing. `ease-out` mimics that physical feel.

---

#### Delay — Wait Before Starting

```css
transition: opacity 0.3s ease 0.1s;
/* Wait 0.1s, then animate over 0.3s */
```

Real situation: Staggering a group of items so they appear one after another. Each item gets a slightly longer delay than the previous one.

```css
.item:nth-child(1) { transition-delay: 0s; }
.item:nth-child(2) { transition-delay: 0.1s; }
.item:nth-child(3) { transition-delay: 0.2s; }
.item:nth-child(4) { transition-delay: 0.3s; }
```

---

### Transitioning Multiple Properties

Separate each transition with a comma:

```css
.card {
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease,
    background-color 0.2s ease;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
  background-color: #f0f9ff;
}
```

---

### Real Situations — Transition

#### Button Hover

The most common transition on any website. Every button should have one.

```css
.button {
  background-color: #2563eb;
  color: white;
  padding: 12px 24px;
  border-radius: 6px;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.button:hover {
  background-color: #1d4ed8;
  transform: translateY(-2px); /* Subtle lift */
}

.button:active {
  transform: translateY(0); /* Snaps back when clicked */
}
```

---

#### Card Hover Lift

A card that rises slightly and casts a shadow when hovered — extremely common in product listings, blog grids, portfolio pages.

```css
.card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease-out, box-shadow 0.3s ease-out;
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.14);
}
```

---

#### Navigation Link Underline Slide

A stylish alternative to the default underline — a line that slides in from the left.

```css
.nav-link {
  position: relative;
  text-decoration: none;
  color: #333;
}

.nav-link::after {
  content: "";
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;           
  height: 2px;
  background-color: #2563eb;
  transition: width 0.3s ease-out;
}

.nav-link:hover::after {
  width: 100%;        /* Expands to full width on hover */
}
```

---

#### Image Overlay Fade

A dark overlay that fades in over an image when hovered — used in galleries, team pages, portfolio grids.

```css
.image-wrapper {
  position: relative;
  overflow: hidden;
}

.overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.image-wrapper:hover .overlay {
  opacity: 1;
}
```

---

#### Sidebar / Drawer Slide In

A sidebar that slides in from the left — used in mobile menus.

```css
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 280px;
  height: 100vh;
  background: white;
  transform: translateX(-100%);  /* Hidden off-screen to the left */
  transition: transform 0.35s ease-out;
}

.sidebar.open {
  transform: translateX(0);      /* Slides into view */
}
```

---

### What Cannot Be Transitioned

Not all CSS properties can be animated. The property must have a calculable midpoint — the browser needs to know what the value looks like halfway through.

Cannot transition:
- `display` (none to block has no middle value)
- `visibility` (only snaps between hidden and visible, no smooth fade)
- `font-family`
- `background-image` (two different images have no midpoint)

Can transition:
- Numbers: `width`, `height`, `font-size`, `opacity`, `margin`, `padding`
- Colors: `color`, `background-color`, `border-color`
- Transforms: `translate`, `rotate`, `scale`

> This is why hiding elements with `opacity: 0` and `visibility: hidden` together is the professional way to fade something out — `display: none` cannot be transitioned but `opacity` can.

```css
/* Correct way to animate something in and out */
.tooltip {
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s ease, visibility 0.2s ease;
}

.tooltip.visible {
  opacity: 1;
  visibility: visible;
}
```

---

## Animation

### What it Does

`animation` plays a sequence you define using `@keyframes`. It runs automatically — no user interaction needed — and can loop, reverse, pause, and run through multiple steps.

---

### Step 1 — Define Keyframes

`@keyframes` is where you write the sequence of states.

```css
@keyframes your-animation-name {
  from {
    /* starting state */
  }
  to {
    /* ending state */
  }
}
```

Or with percentages for full control over every step:

```css
@keyframes your-animation-name {
  0%   { /* state at start */ }
  50%  { /* state at halfway */ }
  100% { /* state at end */ }
}
```

---

### Step 2 — Apply to an Element

```css
.element {
  animation: name duration timing-function delay iteration-count direction fill-mode;
}
```

```css
.element {
  animation: fadeIn 0.6s ease-out 0s 1 normal forwards;
/*           |____| |___| |______| |_| |_| |______| |______|
             name  time   curve  delay reps  dir   fill-mode */
}
```

---

### Each Property Explained

#### `animation-name`

The name you gave your `@keyframes` block.

```css
animation-name: fadeIn;
animation-name: slideUp;
animation-name: spin;
```

---

#### `animation-duration`

How long one full cycle of the animation takes.

```css
animation-duration: 0.5s;
animation-duration: 2s;
```

---

#### `animation-timing-function`

Same values as transition — `ease`, `linear`, `ease-out`, `cubic-bezier()`, etc.

```css
animation-timing-function: ease-out;
animation-timing-function: linear; /* Good for spinners — constant speed */
```

---

#### `animation-delay`

Wait before the animation starts.

```css
animation-delay: 0.3s;
animation-delay: -0.5s; /* Negative delay — starts the animation partway through */
```

> Negative delay is a useful trick. If an animation takes 2s and you set delay to `-1s`, it starts already halfway through — useful for making multiple looping animations feel offset.

---

#### `animation-iteration-count`

How many times the animation plays.

```css
animation-iteration-count: 1;        /* Once and done */
animation-iteration-count: 3;        /* Plays three times */
animation-iteration-count: infinite; /* Loops forever */
```

---

#### `animation-direction`

Which direction the animation plays each cycle.

```css
animation-direction: normal;           /* Always forward: 0% → 100% */
animation-direction: reverse;          /* Always backward: 100% → 0% */
animation-direction: alternate;        /* Forward then backward: 0%→100%→0%→100% */
animation-direction: alternate-reverse;/* Backward then forward */
```

> `alternate` is the most natural-looking for looping animations. Instead of jumping back to the start at the end of each cycle, it smoothly reverses — like breathing in and out.

---

#### `animation-fill-mode` — What State Does the Element Stay In?

This is one of the most important properties and the one most beginners forget.

```css
animation-fill-mode: none;       /* Default — snaps back to original state when done */
animation-fill-mode: forwards;   /* Stays at the last keyframe state */
animation-fill-mode: backwards;  /* Applies first keyframe during the delay period */
animation-fill-mode: both;       /* Applies both forwards and backwards rules */
```

Real situation: You animate an element fading in with `opacity: 0` to `opacity: 1`. Without `forwards`, the moment the animation ends it snaps back to `opacity: 0` and disappears. With `forwards`, it stays visible.

```css
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

.hero-text {
  animation: fadeIn 0.7s ease-out forwards; /* "forwards" keeps it visible */
}
```

---

#### `animation-play-state`

Pause or resume the animation.

```css
.spinner {
  animation: spin 1s linear infinite;
}

.spinner:hover {
  animation-play-state: paused; /* Freeze on hover */
}
```

---

### Shorthand

```css
/* name | duration | timing | delay | count | direction | fill-mode */
animation: fadeIn 0.6s ease-out 0s 1 normal forwards;

/* Most common shorthand — just the essentials */
animation: fadeIn 0.6s ease-out forwards;
```

---

### Multiple Animations on One Element

```css
.element {
  animation:
    fadeIn 0.5s ease-out forwards,
    float 3s ease-in-out infinite 0.5s;
}
```

---

### Real Situations — Animation

#### Page Load Fade In

Content that fades up into view when the page loads — used on hero sections, landing pages, and portfolios.

```css
@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-title {
  animation: fadeUp 0.7s ease-out forwards;
}

.hero-subtitle {
  animation: fadeUp 0.7s ease-out 0.15s forwards; /* Slight delay — appears after title */
}

.hero-button {
  animation: fadeUp 0.7s ease-out 0.3s forwards;  /* Appears last */
}
```

This staggered entrance is used on almost every modern landing page. Each element appears slightly after the previous one, guiding the user's eye down the page.

---

#### CSS Loading Spinner

A circular spinner used while data is loading — used in every web application.

```css
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;       /* Light full circle */
  border-top-color: #2563eb;       /* One colored segment */
  border-radius: 50%;
  animation: spin 0.8s linear infinite; /* linear = constant speed, no acceleration */
}
```

> `linear` is essential here. Any other timing function makes the spinner accelerate and decelerate each rotation, which looks broken.

---

#### Skeleton Loading Screen

Gray pulsing placeholder blocks shown while content loads — used by Facebook, LinkedIn, YouTube.

```css
@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.skeleton {
  background: linear-gradient(
    90deg,
    #e5e7eb 25%,
    #f3f4f6 50%,
    #e5e7eb 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite linear;
  border-radius: 4px;
}

.skeleton-title {
  height: 24px;
  width: 60%;
  margin-bottom: 12px;
}

.skeleton-text {
  height: 14px;
  width: 100%;
  margin-bottom: 8px;
}
```

---

#### Floating / Breathing Effect

An element that gently floats up and down — used for hero illustrations, icons, call-to-action elements.

```css
@keyframes float {
  from { transform: translateY(0); }
  to   { transform: translateY(-12px); }
}

.illustration {
  animation: float 3s ease-in-out infinite alternate;
  /* "alternate" makes it go up then come back down smoothly */
}
```

> `alternate` is key here. Without it, the element jumps back to the bottom at the end of each cycle. `alternate` reverses the animation each time, creating the smooth up-down-up motion.

---

#### Attention Pulse

A button or notification badge that pulses to draw attention — used for call-to-action buttons, unread notifications, live indicators.

```css
@keyframes pulse {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.5);
  }
  50% {
    box-shadow: 0 0 0 12px rgba(37, 99, 235, 0);
  }
}

.cta-button {
  animation: pulse 2s ease-out infinite;
}
```

---

#### Typing Cursor

A blinking cursor effect — used on portfolio hero text, terminal-style UIs.

```css
@keyframes blink {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0; }
}

.cursor {
  display: inline-block;
  width: 2px;
  height: 1.2em;
  background-color: currentColor;
  animation: blink 1s step-end infinite;
  /* "step-end" makes it snap between visible and invisible — no fade */
  vertical-align: text-bottom;
  margin-left: 2px;
}
```

> `step-end` is a timing function that snaps instantly rather than fading — this is what makes a cursor blink rather than fade in and out. Real cursors do not fade; they appear and disappear.

---

#### Notification Shake

An element that shakes to indicate an error or alert — used on login forms when the password is wrong, on inputs with validation errors.

```css
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20%       { transform: translateX(-8px); }
  40%       { transform: translateX(8px); }
  60%       { transform: translateX(-6px); }
  80%       { transform: translateX(6px); }
}

.input.error {
  animation: shake 0.4s ease-out;
  border-color: #ef4444;
}
```

---

#### Entrance from Side (Slide In)

Used for modals, notifications, sidebars, toast messages — anything that enters the screen from an edge.

```css
@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.toast-notification {
  animation: slideInRight 0.35s ease-out forwards;
}
```

---

### The `will-change` Property — Performance Hint

When you know an element is going to animate, you can tell the browser to prepare in advance:

```css
.card {
  will-change: transform, opacity;
  transition: transform 0.3s ease;
}
```

This tells the browser to put the element on its own GPU layer, which makes animations smoother — especially on mobile.

> Only use `will-change` on elements that actually animate. Applying it to everything wastes memory.

---

### Properties That Perform Well vs Poorly

The browser has to do a lot of work to animate certain properties. Some are cheap and some are expensive:

| Performance | Properties | Why |
|---|---|---|
| Fast (GPU) | `transform`, `opacity` | Handled by the graphics card — no layout recalculation |
| Slow (CPU) | `width`, `height`, `margin`, `padding`, `top`, `left` | Forces the browser to recalculate the entire layout |

> Always prefer `transform: translateX()` over `left`, and `transform: scale()` over `width`/`height` when animating. The visual result is the same but the performance difference is significant, especially on mobile.

```css
/* Slow — triggers layout recalculation every frame */
.box { transition: left 0.3s ease; }
.box:hover { left: 20px; }

/* Fast — handled by GPU, no layout recalculation */
.box { transition: transform 0.3s ease; }
.box:hover { transform: translateX(20px); }
```



