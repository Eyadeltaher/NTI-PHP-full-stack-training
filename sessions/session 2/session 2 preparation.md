# CSS — Pseudo-Elements, Pseudo-Classes, Animation & Display Flex

---
  
## Pseudo-Elements

### What is a Pseudo-Element?

A pseudo-element lets you **style a specific part** of an element, or **insert content before/after** it — without adding any extra HTML tags.

Pseudo-elements use **double colon** `::` syntax:

```css
selector::pseudo-element {
  /* styles */
}
```

---

### `::before` — Insert Content Before

Creates a fake element **before** the content of the selected element.

```css
h1::before {
  content: "🔥 ";  /* ← content property is REQUIRED */
}
```

```html
<h1>Welcome</h1>
<!-- Renders as: 🔥 Welcome -->
```

---

### `::after` — Insert Content After

Creates a fake element **after** the content of the selected element.

```css
h1::after {
  content: " ✅";
}
```

```html
<h1>Done</h1>
<!-- Renders as: Done ✅ -->
```

>  **The `content` property is mandatory** for `::before` and `::after`. Even if you don't want text, you must write `content: ""` (empty string) — otherwise the element won't appear at all.

```css
/*  Decorative line with no text */
h2::after {
  content: "";           /* Empty but required */
  display: block;
  width: 60px;
  height: 3px;
  background-color: red;
  margin-top: 6px;
}
```

>  **Best cases for `::before` / `::after`:**
> 
> - Decorative underlines or lines under headings
> - Adding icons or symbols without touching HTML
> - Overlay effects on images
> - Quotation marks around blockquotes
> - Custom list bullet styling
> - Clearing floats (the clearfix technique)

---

### `::first-letter` — Style the First Letter

```css
p::first-letter {
  font-size: 3em;
  font-weight: bold;
  float: left;
  margin-right: 6px;
  color: darkred;
}
```

>  **Best case:** Drop cap effect — like in newspaper or magazine articles where the first letter of a paragraph is large and decorative.

---

### `::first-line` — Style the First Line

```css
p::first-line {
  font-weight: bold;
  color: navy;
}
```

>  The "first line" is dynamic — it adjusts automatically if the user resizes the window.

---

### `::selection` — Style Highlighted Text

Controls what selected/highlighted text looks like:

```css
::selection {
  background-color: gold;
  color: black;
}
```

>  **Best case:** Brand customization — matching the highlight color to your website's theme.

---

### `::placeholder` — Style Input Placeholder Text

```css
input::placeholder {
  color: #aaa;
  font-style: italic;
}
```

---

### Pseudo-Elements Summary

|Pseudo-Element|What it does|
|---|---|
|`::before`|Inserts content before element's content|
|`::after`|Inserts content after element's content|
|`::first-letter`|Targets the very first letter|
|`::first-line`|Targets the first rendered line|
|`::selection`|Targets text the user highlights|
|`::placeholder`|Targets placeholder text in inputs|

---

## Pseudo-Classes

### What is a Pseudo-Class?

A pseudo-class targets an element based on its **state** or **position** — things that aren't fixed in the HTML but depend on user interaction or document structure.

Pseudo-classes use **single colon** `:` syntax:

```css
selector:pseudo-class {
  /* styles */
}
```

>  If pseudo-elements are about _parts of_ an element, pseudo-classes are about _the condition_ of an element.

---

### Interaction / State Pseudo-Classes

#### `:hover` — Mouse Over

```css
button:hover {
  background-color: darkblue;
  cursor: pointer;
}

a:hover {
  color: red;
  text-decoration: underline;
}
```

>  **Most used pseudo-class.** Use it for any visual feedback when the user moves their mouse over something.

---

#### `:focus` — Element is Active/Selected

Triggered when an element receives keyboard focus (clicked or tabbed into):

```css
input:focus {
  border-color: blue;
  outline: 2px solid lightblue;
}
```

>  **Best case:** Highlighting form fields when the user clicks into them — essential for accessibility.

---

#### `:active` — Being Clicked

Triggered at the **exact moment** of a mouse click (held down):

```css
button:active {
  transform: scale(0.97);  /* Slight shrink — feels like a real button press */
  background-color: darkblue;
}
```

---

#### `:visited` — Link Already Clicked

```css
a:visited {
  color: purple;  /* Classic visited link color */
}
```

---

#### `:checked` — Checkbox or Radio is Checked

```css
input[type="checkbox"]:checked {
  accent-color: green;
}
```

---

#### `:disabled` / `:enabled`

```css
input:disabled {
  background-color: #eee;
  cursor: not-allowed;
}
```

---

### Structural Pseudo-Classes — Position in the DOM

#### `:first-child` — First Sibling

Targets the element only if it is the **first child** of its parent:

```css
li:first-child {
  font-weight: bold;
  color: red;
}
```

---

#### `:last-child` — Last Sibling

```css
li:last-child {
  border-bottom: none;  /* Remove border from last item in a list */
}
```

---

#### `:nth-child(n)` — Specific Position

Targets elements based on their position number:

```css
li:nth-child(2)    { color: red; }     /* Exactly the 2nd item */
li:nth-child(odd)  { background: #f5f5f5; } /* 1st, 3rd, 5th... */
li:nth-child(even) { background: #fff; }    /* 2nd, 4th, 6th... */
li:nth-child(3n)   { color: blue; }    /* Every 3rd item */
```

>  **Best case for `odd`/`even`:** Zebra-stripe table rows — alternating row colors for readability.

```css
tr:nth-child(even) {
  background-color: #f9f9f9;
}
```

---

#### `:not()` — Exclude an Element

Targets everything **except** what's inside the parentheses:

```css
/* Style all li items EXCEPT the last one */
li:not(:last-child) {
  border-bottom: 1px solid #ddd;
}

/* Style all inputs EXCEPT submit buttons */
input:not([type="submit"]) {
  border: 1px solid gray;
}
```

>  Very practical — instead of styling everything and then overriding the exception, just exclude it from the start.

---

#### `:is()` — Group Selectors Cleanly

Shorthand to apply the same style to multiple selectors:

```css
/* Verbose */
h1:hover, h2:hover, h3:hover {
  color: blue;
}

/* Clean */
:is(h1, h2, h3):hover {
  color: blue;
}
```

---

### Link Pseudo-Classes — The LVHA Order

>  When styling links, always write the pseudo-classes in this exact order — otherwise they override each other:

```css
a:link    { color: blue; }     /* 1. Unvisited link */
a:visited { color: purple; }   /* 2. Visited link */
a:hover   { color: red; }      /* 3. Mouse over */
a:active  { color: orange; }   /* 4. Being clicked */
```

>  Remember with: **L**o**V**e **HA**te → `link`, `visited`, `hover`, `active`

---

### Pseudo-Classes Summary

|Pseudo-Class|Triggers When...|
|---|---|
|`:hover`|Mouse is over the element|
|`:focus`|Element is focused (clicked or tabbed)|
|`:active`|Element is being clicked|
|`:visited`|Link has been visited|
|`:checked`|Checkbox/radio is checked|
|`:disabled`|Input is disabled|
|`:first-child`|Element is the first child of its parent|
|`:last-child`|Element is the last child of its parent|
|`:nth-child(n)`|Element is at position n|
|`:not(x)`|Element does NOT match x|
|`:is(x, y)`|Element matches any in the list|

---

##  CSS Animation

There are **two tools** for movement in CSS — know when to use each:

|Tool|How it works|Use when|
|---|---|---|
|`transition`|Animates between two states (A → B)|Hover effects, toggling classes|
|`animation`|Plays a defined keyframe sequence|Looping, auto-playing, complex sequences|

---

##  `transition` — Smooth State Changes

`transition` makes a change happen **smoothly over time** instead of instantly.

```css
button {
  background-color: blue;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: darkblue;
}
```

### Syntax

```css
transition: property duration timing-function delay;
```

|Part|What it means|Example|
|---|---|---|
|`property`|Which CSS property to animate|`background-color`, `transform`, `all`|
|`duration`|How long the transition takes|`0.3s`, `500ms`|
|`timing-function`|The speed curve|`ease`, `linear`, `ease-in`, `ease-out`|
|`delay`|Wait before starting|`0.2s`|

```css
/* Transition multiple properties */
.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}
```

### Timing Functions

|Value|Behavior|
|---|---|
|`ease`|Slow start, fast middle, slow end (default)|
|`linear`|Same speed throughout|
|`ease-in`|Slow start, fast end|
|`ease-out`|Fast start, slow end|
|`ease-in-out`|Slow start and slow end|

---

##  `animation` — Keyframe Animations

For more complex animations that play automatically or loop.

### Step 1 — Define the Keyframes

```css
@keyframes animation-name {
  from {
    /* starting state */
  }
  to {
    /* ending state */
  }
}
```

Or with percentage control:

```css
@keyframes bounce {
  0%   { transform: translateY(0); }
  50%  { transform: translateY(-30px); }
  100% { transform: translateY(0); }
}
```

### Step 2 — Apply the Animation

```css
.ball {
  animation: bounce 1s ease infinite;
}
```

### Full Syntax

```css
animation: name duration timing-function delay iteration-count direction;
```

|Property|What it controls|Common values|
|---|---|---|
|`animation-name`|Which `@keyframes` to use|`bounce`, `fadein`|
|`animation-duration`|How long one cycle takes|`1s`, `500ms`|
|`animation-timing-function`|Speed curve|`ease`, `linear`|
|`animation-delay`|Wait before starting|`0.5s`|
|`animation-iteration-count`|How many times to play|`1`, `3`, `infinite`|
|`animation-direction`|Which direction to play|`normal`, `reverse`, `alternate`|
|`animation-fill-mode`|State before/after animation|`forwards`, `backwards`, `both`|
|`animation-play-state`|Pause or play|`running`, `paused`|

---

### `animation-fill-mode` — What Happens After?

```css
.box {
  animation: slideIn 0.5s ease forwards;
  /* "forwards" = stay at the final keyframe state after animation ends */
}
```

|Value|Behavior|
|---|---|
|`none`|Returns to original state after animation (default)|
|`forwards`|Stays at the last keyframe state|
|`backwards`|Applies first keyframe state during delay|
|`both`|Combines forwards + backwards|

>  `forwards` is the most useful — without it, your element snaps back to its original state the moment the animation ends.

---

### `animation-direction: alternate`

Makes the animation play forward then **reverse** on the next cycle — great for loops that look natural:

```css
.pulse {
  animation: scale-up 1s ease-in-out infinite alternate;
}

@keyframes scale-up {
  from { transform: scale(1); }
  to   { transform: scale(1.1); }
}
/* Grows → shrinks → grows → shrinks... smoothly */
```

---

### Practical Examples

#### Fade In on Load

```css
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

.hero-text {
  animation: fadeIn 0.8s ease forwards;
}
```

#### Spinning Loader

```css
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.loader {
  width: 40px;
  height: 40px;
  border: 4px solid #eee;
  border-top-color: blue;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
```

#### Pulsing Button

```css
@keyframes pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4); }
  50%       { box-shadow: 0 0 0 12px rgba(0, 123, 255, 0); }
}

.cta-button {
  animation: pulse 2s ease-out infinite;
}
```

---

### Pausing an Animation on Hover

```css
.animated-element {
  animation: spin 2s linear infinite;
}

.animated-element:hover {
  animation-play-state: paused;  /* Freeze on hover */
}
```

---

### Multiple Animations on One Element

```css
.element {
  animation: 
    fadeIn 0.5s ease forwards,
    float 3s ease-in-out infinite 0.5s;
    /* name duration timing fill-mode , name duration timing count delay */
}
```

---

# Display Flex (Flexbox)

---

## What is Flexbox?

**Flexbox** (Flexible Box Layout) is a CSS layout system that makes it easy to **arrange, align, and distribute** elements inside a container — in a single row or column — even when their sizes are unknown or dynamic.

>  Before Flexbox, centering something vertically was a painful hack. With Flexbox, it's one line. It replaced `float` for almost all layout needs.

---

##  The Core Concept — Parent & Children

Flexbox works on **two levels**:

```
┌─────────────────────────────────────────┐
│           FLEX CONTAINER (parent)       │  ← gets display: flex
│  ┌───────┐  ┌───────┐  ┌───────┐        │
│  │ item  │  │ item  │  │ item  │        │  ← FLEX ITEMS (children)
│  └───────┘  └───────┘  └───────┘        │
└─────────────────────────────────────────┘
```

- You apply `display: flex` to the **parent**
- The **direct children** automatically become flex items
- You control layout from the **parent**, and fine-tune individual items on the **children**

```html
<div class="container">       <!-- flex container -->
  <div class="item">A</div>   <!-- flex item -->
  <div class="item">B</div>   <!-- flex item -->
  <div class="item">C</div>   <!-- flex item -->
</div>
```

```css
.container {
  display: flex;
}
```

>  Just adding `display: flex` to the parent immediately lines up all children **in a row** — that's the default behavior.

---

##  The Two Axes

Everything in Flexbox is about two axes:

```
Main Axis  →→→→→→→→→→→→→→→→→→→→→
           ┌──────┬──────┬──────┐
           │  A   │  B   │  C   │
           └──────┴──────┴──────┘
Cross Axis ↓ (perpendicular to main)
```

- **Main Axis** — the direction items are placed (horizontal by default)
- **Cross Axis** — perpendicular to the main axis (vertical by default)

>  When `flex-direction` changes, the axes flip. This is the most important concept to internalize.

---

##  Container Properties (Applied to the Parent)

---

### `flex-direction` — Which Way Do Items Go?

```css
.container {
  flex-direction: row;            /* → Default: left to right */
  flex-direction: row-reverse;    /* ← Right to left */
  flex-direction: column;         /* ↓ Top to bottom */
  flex-direction: column-reverse; /* ↑ Bottom to top */
}
```

>  **`column`** is how you stack elements vertically with flex — cleaner than old margin hacks.

---

### `justify-content` — Align Along the MAIN Axis

Controls how items are distributed along the main axis (horizontal when `row`, vertical when `column`):

```css
.container {
  justify-content: flex-start;    /* ← Items packed at the start (default) */
  justify-content: flex-end;      /* → Items packed at the end */
  justify-content: center;        /* → Items centered */
  justify-content: space-between; /* Items spread: first at start, last at end */
  justify-content: space-around;  /* Equal space around each item */
  justify-content: space-evenly;  /* Perfectly equal space between all items */
}
```

#### Visual Reference

```
flex-start:    [A][B][C]          →
center:             [A][B][C]
flex-end:                [A][B][C]
space-between: [A]    [B]    [C]
space-around:   [A]   [B]   [C]
space-evenly:  [A]  [B]  [C]
```

>  **`space-between`** is the most used in real projects — navigation bars, card grids, toolbars.

---

### `align-items` — Align Along the CROSS Axis

Controls how items align on the cross axis (vertical when `row`, horizontal when `column`):

```css
.container {
  align-items: stretch;     /* Default — items stretch to fill container height */
  align-items: flex-start;  /* Items align at the top */
  align-items: flex-end;    /* Items align at the bottom */
  align-items: center;      /* Items centered vertically */
  align-items: baseline;    /* Items aligned by their text baseline */
}
```

>  `align-items: center` on the cross axis + `justify-content: center` on the main axis = **perfectly centered element**. This is the most famous Flexbox combo.

```css
/*  The holy grail of CSS centering */
.container {
  display: flex;
  justify-content: center;  /* horizontal center */
  align-items: center;      /* vertical center */
  height: 100vh;
}
```

---

### `flex-wrap` — What if Items Don't Fit?

By default, flex items try to squeeze into one line. `flex-wrap` controls what happens when they overflow:

```css
.container {
  flex-wrap: nowrap;   /* Default — all on one line, items may shrink */
  flex-wrap: wrap;     /* Items wrap to next line when they don't fit */
  flex-wrap: wrap-reverse; /* Wraps in reverse direction */
}
```

>  **Always use `flex-wrap: wrap`** when building responsive grids. Without it, items just shrink and squish instead of wrapping to the next row.

---

### `gap` — Space Between Items

The cleanest way to add spacing between flex items:

```css
.container {
  display: flex;
  gap: 20px;           /* Same gap in all directions */
  gap: 10px 20px;      /* row-gap | column-gap */
  row-gap: 10px;
  column-gap: 20px;
}
```

>  Much better than using `margin` on each item. `gap` only adds space **between** items — never on the outer edges.

---

### `align-content` — When There Are Multiple Rows

`align-content` works like `justify-content` but for **multiple rows** (only has effect when `flex-wrap: wrap` is set and items span more than one line):

```css
.container {
  align-content: flex-start;
  align-content: flex-end;
  align-content: center;
  align-content: space-between;
  align-content: space-around;
  align-content: stretch;    /* Default */
}
```

>  `align-items` = how items align within their row
> `align-content` = how the rows align within the container

---

### `flex-direction` + Axes — The Flip

>  This is where people get confused. When you change `flex-direction` to `column`, the axes **swap**:

| `flex-direction` | `justify-content` controls | `align-items` controls |
|---|---|---|
| `row` (default) | Horizontal (←→) | Vertical (↑↓) |
| `column` | Vertical (↑↓) | Horizontal (←→) |

```css
/* Center items in a vertical stack */
.container {
  display: flex;
  flex-direction: column;
  align-items: center;      /* NOW controls horizontal centering */
  justify-content: center;  /* NOW controls vertical centering */
}
```

---

## Item Properties (Applied to the Children)

---

### `flex-grow` — How Much Can an Item Grow?

When there is **extra space** in the container, `flex-grow` controls how much of it each item takes:

```css
.item-a { flex-grow: 1; }  /* Takes 1 share of free space */
.item-b { flex-grow: 2; }  /* Takes 2 shares — twice as wide as item-a */
.item-c { flex-grow: 1; }  /* Takes 1 share */
```


```css
/* Common pattern: one item fills all remaining space */
.sidebar { width: 250px; }
.main-content { flex-grow: 1; } /* Takes all leftover space */
```

---

### `flex-shrink` — How Much Can an Item Shrink?

When there is **not enough space**, `flex-shrink` controls how much each item shrinks:

```css
.item { flex-shrink: 1; }  /* Default — can shrink */
.item { flex-shrink: 0; }  /* Never shrink — keep original size */
```

>  Use `flex-shrink: 0` on items that must never be squeezed — like logos, icons, or fixed-size images.

---

### `flex-basis` — What is the Item's Starting Size?

Sets the **initial size** of an item before growing or shrinking:

```css
.item { flex-basis: 200px; }   /* Start at 200px, then grow/shrink from there */
.item { flex-basis: 33.33%; }  /* Start at 1/3 of container */
.item { flex-basis: auto; }    /* Use the item's own width/height (default) */
```

---

### `flex` — Shorthand for grow / shrink / basis

```css
.item {
  flex: grow shrink basis;
}

/* Common patterns */
.item { flex: 1; }         /* grow:1, shrink:1, basis:0 — equal width columns */
.item { flex: 0 0 200px; } /* Don't grow, don't shrink, stay exactly 200px */
.item { flex: 1 0 auto; }  /* Grow freely, never shrink */
```

> `flex: 1` on all children = **equal width columns**. One of the most used patterns.

```css
/* 3 equal columns */
.container { display: flex; }
.item { flex: 1; }
```

---

### `align-self` — Override Alignment for ONE Item

While `align-items` on the parent sets alignment for all children, `align-self` overrides it for a **single item**:

```css
.container {
  display: flex;
  align-items: center;   /* All items centered by default */
}

.special-item {
  align-self: flex-end;  /* This one item goes to the bottom */
}
```

---

### `order` — Change Visual Order Without Changing HTML

```css
.item-a { order: 2; }
.item-b { order: 1; }  /* Appears first visually */
.item-c { order: 3; }
```

>  **Best case:** Reordering elements for mobile layouts without duplicating HTML.
>  Screen readers follow the HTML order, not the visual order — use carefully for accessibility.

---

## Common Real-World Patterns

### Perfect Center (Horizontally + Vertically)

```css
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
```

---

### Navigation Bar

```css
.navbar {
  display: flex;
  justify-content: space-between;  /* Logo left, links right */
  align-items: center;
  padding: 0 24px;
}
```

---

### Card Grid (Responsive)

```css
.grid {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
}

.card {
  flex: 1 1 300px;
  /* Grow and shrink freely, but start at 300px minimum */
  /* Cards wrap to next row when they can't fit */
}
```

---

### Sidebar + Main Layout

```css
.layout {
  display: flex;
}

.sidebar {
  flex: 0 0 260px;  /* Fixed width — never grow or shrink */
}

.main {
  flex: 1;  /* Take all remaining space */
}
```

---

### Sticky Footer

```css
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

main {
  flex: 1;  /* Pushes footer to the bottom */
}

footer {
  /* Stays at the bottom no matter how little content is on the page */
}
```

---

## 🗒️ Quick Reference

| Property | Applies To | What it controls |
|---|---|---|
| `display: flex` | Parent | Activates flexbox |
| `flex-direction` | Parent | Main axis direction |
| `justify-content` | Parent | Alignment on main axis |
| `align-items` | Parent | Alignment on cross axis (single row) |
| `align-content` | Parent | Alignment on cross axis (multiple rows) |
| `flex-wrap` | Parent | Whether items wrap to new lines |
| `gap` | Parent | Space between items |
| `flex-grow` | Child | How much the item grows into free space |
| `flex-shrink` | Child | How much the item shrinks when tight |
| `flex-basis` | Child | Item's starting size before grow/shrink |
| `flex` | Child | Shorthand for grow + shrink + basis |
| `align-self` | Child | Override `align-items` for one item |
| `order` | Child | Visual order without changing HTML |

---

### The Axes Cheat Sheet

| `flex-direction` | `justify-content` | `align-items` |
|---|---|---|
| `row` | ←→ Horizontal | ↑↓ Vertical |
| `column` | ↑↓ Vertical | ←→ Horizontal |
