
# HTML 

---

##  What is an HTML Tag?

An **HTML tag** is the building block of any webpage. Tags tell the browser *what to display and how*.

Most tags come in pairs — an **opening tag** and a **closing tag**:

```html
<p>This is a paragraph.</p>
```

Some tags are **self-closing** (empty tags) — they don't wrap content, they just insert something:

```html
<br>
<hr>
<img src="photo.jpg" alt="a photo">
```

> **Nested tag** = a tag placed *inside* another tag.
> **Empty tag** = a tag with no closing counterpart (no content inside).

---

##  Rule: No Duplicate Attributes in a Single Tag

You **cannot repeat the same attribute** inside one HTML tag. If you do, the browser will only read the **first one** and ignore the rest.

```html
<!--  Wrong — two src attributes -->
<img src="cat.jpg" src="dog.jpg" alt="animal">

<!--  Only the first src is used: cat.jpg will appear, dog.jpg is ignored -->
```

---

##  Essential HTML Structure Tags

### `<html>`, `<head>`, `<body>`

Every HTML page must have this skeleton:

```html
<!DOCTYPE html>
<html>
  <head>
    <title>Page Title</title>
  </head>
  <body>
    <!-- Everything visible goes here -->
  </body>
</html>
```

| Tag | Role |
|-----|------|
| `<html>` | The root — wraps everything |
| `<head>` | Metadata (title, styles, scripts) — not visible |
| `<body>` | Everything the user *sees* |

---

##  Text & Content Tags

### Headings — `<h1>` to `<h6>`

```html
<h1>Main Title</h1>
<h2>Section Title</h2>
<h3>Sub-section</h3>
```

>  **SEO Tip:** Use **only one `<h1>`** per page. Search engines (like Google) treat `<h1>` as the main topic of the page. Using multiple `<h1>` tags confuses the crawler and weakens your ranking.

---

### Paragraph — `<p>`

```html
<p>This is a paragraph of text. It creates spacing above and below automatically.</p>
```


---

##  Lorem Ipsum — Placeholder Text

**Lorem ipsum** is fake Latin text used as placeholder content when you're building a layout but don't have real text yet.

### In VS Code / Any Editor

Just type `lorem` and press **Tab** — it auto-generates a paragraph:

```
lorem
```

↓ becomes ↓

```
Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam voluptatem...
```

You can also control how many words:

```
lorem10   → generates 10 words
lorem50   → generates 50 words
lorem100  → generates 100 words
```


---

### Line Break — `<br>` & Horizontal Rule — `<hr>`

```html
<p>Line one<br>Line two on a new line</p>

<hr> <!-- Draws a horizontal dividing line -->
```

> `<br>` and `<hr>` are **empty tags** — no closing tag needed.

---

### Text Formatting Tags

| Tag      | Effect            | Example                 |
| -------- | ----------------- | ----------------------- |
| `<b>`    | **Bold**          | `<b>Important</b>`      |
| `<i>`    | *Italic*          | `<i>emphasized</i>`     |
| `<u>`    | Underline         | `<u>underlined</u>`     |
| `<mark>` | ==Highlight==     | `<mark>key term</mark>` |
| `<del>`  | ~~Strikethrough~~ | `<del>old price</del>`  |
| `<sup>`  | Superscript (x²)  | `x<sup>2</sup>`         |
| `<sub>`  | Subscript (H₂O)   | `H<sub>2</sub>O`        |

```html
<p>Water formula: H<sub>2</sub>O</p>
<p>Area = r<sup>2</sup></p>
<p>Old price: <del>$100</del> New price: <mark>$70</mark></p>
```

---

### `<font>` Tag

Used to change text color, size, and face (old-style, avoid in modern HTML — use CSS instead):

```html
<font color="red" size="4">This text is red</font>
```

---

##  Anchor Tag — `<a>`

Used to create **hyperlinks** — clickable text or images that take the user somewhere.

```html
<a href="https://www.google.com">Visit Google</a>
```

### `target` Attribute

Controls where the link opens:

| Value | Behavior |
|-------|----------|
| `_self` | Opens in the **same tab** (default) |
| `_blank` | Opens in a **new tab** |

```html
<a href="https://github.com" target="_blank">Open GitHub in new tab</a>
```

---

###  Making an Image a Clickable Link

Wrap `<img>` inside `<a>`:

```html
<a href="https://github.com/Eyadeltaher/Cybersecurity-notes" target="_blank">
  <img src="/images/my-image.png" alt="cybersecurity notes" width="200">
</a>
```

>  The `<a>` tag is the **parent**, the `<img>` is the **child** (nested tag). Clicking the image acts like clicking a link.

---

## Image Tag — `<img>`

An **empty tag** that embeds an image.

```html
<img src="profile.jpg" alt="profile photo" width="300">
```

### Key Attributes

| Attribute | Purpose |
|-----------|---------|
| `src` | Path to the image file |
| `alt` | Alternative text if image fails to load |
| `width` | Sets the display width |

>  **SEO + Accessibility Tip:** The `alt` attribute does two important things:
> 1. Helps **search engines** understand what the image is about
> 2. Helps **screen readers** describe the image to visually impaired users

>  Best practice: keep `alt` text to **3 meaningful words**
> `alt="red sports car"` OK
> `alt="image1"` NOOOP

---

### HTML Comments

```html
<!-- This is an HTML comment -->

<!-- Navigation section -->
<nav>
  <a href="#">Home</a>
</nav>

<!-- TODO: add dropdown menu here -->
```

---

##  Layout / Container Tags

### `<div>` — Block Container

Used to group elements and create layout sections. Takes up the **full width** of its parent.

```html
<div style="background-color: lightblue;">
  <h2>Section Title</h2>
  <p>Content goes here.</p>
</div>
```

---

### `<span>` — Inline Container

Used to style or target a **small part** of text inside a line.

```html
<p>My favorite color is <span style="color: red;">red</span>.</p>
```

>  Rule of thumb:
> - `<div>` = a **box** (block-level, starts on a new line)
> - `<span>` = a **highlighter** (inline, stays in the flow of text)

---

##  Media Tags

### Video — `<video>`

```html
<video src="movie.mp4" width="600" controls muted autoplay loop>
  Your browser does not support video.
</video>
```

| Attribute | Effect |
|-----------|--------|
| `controls` | Shows play/pause/volume buttons |
| `muted` | Starts with no sound |
| `autoplay` | Plays automatically on load |
| `loop` | Replays when it ends |

>  Browsers often **block autoplay with sound** — use `muted` + `autoplay` together to make it work reliably.

---

### Audio — `<audio>`

Same attributes as video:

```html
<audio src="song.mp3" controls autoplay loop muted></audio>
```

---

### Iframe — `<iframe>`

Embeds another **webpage or external content** (YouTube, Google Maps, etc.):

```html
<iframe 
  src="https://www.youtube.com/embed/dQw4w9WgXcQ" 
  width="560" 
  height="315">
</iframe>
```

>  Think of `<iframe>` as a **window cut into your page** showing another website inside it.

---

##  `<textarea>` — Multiline Text Input

```html
<textarea rows="5" cols="40" placeholder="Write your message here..."></textarea>
```

| Attribute | Effect |
|-----------|--------|
| `rows` | Number of visible text lines |
| `cols` | Width in characters |

---

##  Common Attributes

### `style` — Inline CSS

Applies styling directly to an element:

```html
<p style="color: blue; font-size: 20px;">Styled paragraph</p>
```

### `width`

Sets the width of an element (images, tables, videos):

```html
<img src="photo.jpg" width="400">
```

---

##  Lists

### 1️- Ordered List — `<ol>`

Items with **numbers or letters**.

```html
<ol type="A" start="3">
  <li>Step C</li>
  <li>Step D</li>
</ol>
```

| Attribute | Effect |
|-----------|--------|
| `type` | `1` (numbers), `A` (uppercase), `a` (lowercase), `I` (Roman numerals) |
| `start` | Starting number/letter |

---

### 2- Unordered List — `<ul>`

Items with **bullets**.

```html
<ul type="square">
  <li>HTML</li>
  <li>CSS</li>
  <li>JavaScript</li>
</ul>
```

| `type` value | Bullet style |
| ------------ | ------------ |
| `disc`       | ● (default)  |
| `circle`     | ○            |
| `square`     | ■            |

---

### 3️- Definition List — `<dl>`

Used for **term + definition** pairs (like a glossary):

```html
<dl>
  <dt>HTML</dt>
  <dd>The structure language of the web</dd>

  <dt>CSS</dt>
  <dd>The styling language for web pages</dd>
</dl>
```

| Tag | Meaning |
|-----|---------|
| `<dl>` | Definition List (the wrapper) |
| `<dt>` | Definition Term (the word) |
| `<dd>` | Definition Description (the explanation) |

---

##  Tables

```html
<table border="1">
  <thead>
    <tr>
      <th>Name</th>
      <th>Age</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Ahmed</td>
      <td>25</td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">End of Table</td>
    </tr>
  </tfoot>
</table>
```

### Table Structure

| Tag | Role |
|-----|------|
| `<table>` | The table wrapper |
| `<thead>` | Header section |
| `<tbody>` | Main content section |
| `<tfoot>` | Footer section |
| `<tr>` | Table Row |
| `<th>` | Table Header cell (bold + centered by default) |
| `<td>` | Table Data cell |

### Important Table Attributes

| Attribute | Effect | Example |
|-----------|--------|---------|
| `border` | Adds a border | `<table border="1">` |
| `colspan` | Merges **columns** horizontally | `<td colspan="2">` |
| `rowspan` | Merges **rows** vertically | `<td rowspan="3">` |

```html
<!-- colspan example: cell spans across 2 columns -->
<td colspan="2">Full Name</td>

<!-- rowspan example: cell spans across 2 rows -->
<td rowspan="2">Ahmed</td>
```

>  Imagine `colspan` like merging cells horizontally in Excel. `rowspan` merges them vertically.

---

##  Forms

Forms collect **input from the user** and send it somewhere (a server, email, etc.).

```html
<form action="https://example.com/submit" method="POST">
  <!-- inputs go here -->
  <button type="submit">Send</button>
</form>
```

>  Full reference: [MDN Form Docs](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/form) | [GeeksforGeeks HTML Forms](https://www.geeksforgeeks.org/html/html-forms/)

---

### Common Input Types

```html
<!-- Text -->
<input type="text" placeholder="Enter your name" required>

<!-- Email -->
<input type="email" placeholder="example@mail.com">

<!-- Password -->
<input type="password">

<!-- Phone Number  Use tel — NOT text or number -->
<input type="tel" placeholder="+20 100 000 0000">

<!-- Number -->
<input type="number" min="1" max="100">

<!-- Checkbox -->
<input type="checkbox" name="hobby" value="reading"> Reading
<input type="checkbox" name="hobby" value="coding"> Coding

<!-- Radio buttons -->
<input type="radio" name="gender" value="male"> Male
<input type="radio" name="gender" value="female"> Female

<!-- Submit button -->
<input type="submit" value="Send Form">
```

>  **Phone number tip:** Always use `type="tel"` for phone numbers — NOT `type="number"`.
> Why? `type="number"` strips leading zeros and doesn't allow `+` signs or spaces. `type="tel"` keeps the number exactly as typed and opens the **phone keypad on mobile**.

---

### `placeholder` and `required`

```html
<input type="text" placeholder="Your full name" required>
```

| Attribute | Effect |
|-----------|--------|
| `placeholder` | Shows hint text inside the input (disappears when user types) |
| `required` | Form **cannot be submitted** if this field is empty |

---

###  Radio & Checkbox — Same `name`, Different `value`

This is one of the most important form rules:

```html
<!--  Correct: same name, different values — they act as ONE group -->
<input type="radio" name="color" value="red"> Red
<input type="radio" name="color" value="blue"> Blue
<input type="radio" name="color" value="green"> Green

<!--  Wrong: different names — they become INDEPENDENT inputs -->
<input type="radio" name="color1" value="red"> Red
<input type="radio" name="color2" value="blue"> Blue
```

>  The `name` attribute is what **groups** radio buttons together. If they share the same `name`, selecting one **deselects** the others. If they have different names, they are independent and you can select all of them — which breaks the logic of a "choose one" question.
>
> The same rule applies to **checkboxes** when you want to group related options (e.g., "select your hobbies").


# CSS 

---

##  What is CSS?

**CSS (Cascading Style Sheets)** is the language that controls how HTML elements *look* — colors, sizes, spacing, layout, fonts, and more.

>  Think of HTML as the **skeleton** of a house (structure), and CSS as the **interior design** (paint, furniture, layout).

---

##  How to Use CSS — 3 Methods

### 1️- Inline CSS

Written directly inside the HTML tag using the `style` attribute.

```html
<p style="color: red; font-size: 18px;">This is red text</p>
```

* Quick for one-off changes
 * Hard to maintain — messy for large projects

---

### 2️- Internal CSS

Written inside a `<style>` tag in the `<head>` section of the HTML file.

```html
<head>
  <style>
    p {
      color: blue;
      font-size: 16px;
    }
  </style>
</head>
```

 * Good for single-page styling
 * Doesn't scale well across multiple pages

---

### 3️- External CSS

Written in a **separate `.css` file** and linked to the HTML.

```html
<!-- In the HTML <head> -->
<link rel="stylesheet" href="styles.css">
```

```css
/* styles.css */
p {
  color: green;
  font-size: 16px;
}
```

* Best practice — one file styles your entire website
* Clean separation between structure and design

>  **Important:** The `<link>` tag must be placed as the **last line inside `<head>`** to ensure styles load correctly and in the right order.

```html
<head>
  <title>My Page</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles.css">  <!--  Last line in head -->
</head>
```

---

##  CSS is Sequential — The Cascade Rule

CSS reads **top to bottom**. If two rules target the same element, **the last one wins**.

```css
p {
  color: red;
}

p {
  color: blue; /*  This wins — it comes last */
}
```

### This Also Applies Across Internal & External CSS

Whichever comes **last in the `<head>`** takes priority:

```html
<head>
  <link rel="stylesheet" href="styles.css">  <!-- External: sets color to red -->
  <style>
    p { color: blue; }  <!-- Internal: comes last → blue wins  -->
  </style>
</head>
```

```html
<head>
  <style>
    p { color: blue; }
  </style>
  <link rel="stylesheet" href="styles.css">  <!-- External: comes last → red wins  -->
</head>
```

>  It's like painting a wall twice — the second coat is what you actually see.

---

##  Colors in CSS

###  1- Named Colors

The simplest way — just write the color name:

```css
p {
  color: red;
  background-color: lightblue;
}
```

> Works fine for basic colors, but limited. For precise control, use RGB or Hex.

---

### 2- RGB — `rgb(red, green, blue)`

Every color on a screen is made of **three light channels**: Red, Green, Blue. Each channel goes from **0** (none) to **255** (full).

```css
p {
  color: rgb(255, 0, 0);       /* Pure red */
  color: rgb(0, 0, 255);       /* Pure blue */
  color: rgb(0, 0, 0);         /* Black — all zero */
  color: rgb(255, 255, 255);   /* White — all full */
  color: rgb(34, 139, 34);     /* Forest green */
}
```


### 3- RGBA — With Transparency

Add a 4th value (alpha) from `0` (invisible) to `1` (fully visible):

```css
.overlay {
  background-color: rgba(0, 0, 0, 0.5); /* Black at 50% opacity */
}
```

>  **Best case:** Dark overlays on images, semi-transparent cards, modal backgrounds.

---

### 4- Hexadecimal — `#RRGGBB`

Hex is just RGB written in **base-16** (hexadecimal). Instead of 0–255, each channel is written as two characters from `0–9` and `a–f`.

```
Base 10 →  0  1  2  3  4  5  6  7  8  9  10  11  12  13  14  15
Base 16 →  0  1  2  3  4  5  6  7  8  9   a   b   c   d   e   f
```

So `16` values per character → two characters = 16 × 16 = **256 possible values** (0–255). Same range as RGB, different notation.

```css
p {
  color: #ff0000;   /* Red   — ff=255, 00=0,   00=0   */
  color: #0000ff;   /* Blue  — 00=0,   00=0,   ff=255 */
  color: #000000;   /* Black — all zero */
  color: #ffffff;   /* White — all full */
  color: #228b22;   /* Forest green */
}
```



> **Shorthand Hex**: If each pair is the same digit repeated, you can shorten it to 3 characters

```css
color: #ff0000  →  color: #f00;   /* Red */
color: #ffffff  →  color: #fff;   /* White */
color: #000000  →  color: #000;   /* Black */
```



---

### CSS Comments

```css
/* This is a CSS comment */

/* === RESET === */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Typography */
h1 {
  font-size: 32px; /* Main page title */
}
```


---

##  CSS Selectors

### Styling by Tag Name

Targets **every element** of that type on the page.

```css
p {
  color: gray;
}

h1 {
  font-size: 32px;
}
```

>  This is **not** using an ID or class — it selects by the raw tag name. Affects all matching elements globally.

---

### Class Selector — `.`

A class can be used on **multiple elements**. Reusable.

```css
.highlight {
  background-color: yellow;
  font-weight: bold;
}
```

```html
<p class="highlight">This is highlighted</p>
<span class="highlight">This too</span>
```

---

### ID Selector — `#`

An ID should be used on **only one element** per page. Unique.

```css
#main-title {
  font-size: 40px;
  color: navy;
}
```

```html
<h1 id="main-title">Welcome</h1>
```

|           | Class         | ID             |
| --------- | ------------- | -------------- |
| Symbol    | `.`           | `#`            |
| Reusable? | Yes           |  One per page  |
| Use case  | Shared styles | Unique element |

---

### Using 2 Classes on a Single Element

You can apply **multiple classes** to one element by separating them with a space:

```html
<p class="highlight bold-text">This has two classes applied</p>
```

```css
.highlight {
  background-color: yellow;
}

.bold-text {
  font-weight: bold;
}
```

>  Think of classes like **stickers** — you can put as many stickers on one item as you want, and each one adds its own style.

---

##  Block vs Inline Elements

This is one of the most fundamental concepts in CSS layout.

### Block Elements

- Take up the **full width** of their parent
- Always start on a **new line**
- You can set `width`, `height`, `margin`, `padding` freely

**Examples:** `<div>`, `<p>`, `<h1>`–`<h6>`, `<ul>`, `<li>`, `<table>`

```html
<div style="background: lightblue;">I am block — I take full width</div>
<div style="background: lightcoral;">I start on a new line</div>
```

---

### Inline Elements

- Take up **only as much width as their content**
- Stay **on the same line** as surrounding content
- You **cannot** set `width` or `height` on them

**Examples:** `<span>`, `<a>`, `<b>`, `<i>`, `<img>`

```html
<p>This is <span style="color:red;">inline</span> and stays in the line.</p>
```

---

##  The `display` Property

You can **override** an element's default block/inline behavior using CSS.

### `display: block`

Forces an inline element to behave like a block element.

```css
a {
  display: block;
  width: 200px;
  background-color: navy;
  color: white;
}
```

> Useful when you want links to look like buttons that fill a row.

---

### `display: inline`

Forces a block element to behave like an inline element.

```css
div {
  display: inline;
}
```

> The element will now sit next to other elements on the same line.

---

### `display: inline-block` —  Avoid This

Tries to combine both: inline flow + ability to set width/height.

**The problem:** it creates **unwanted white space** between elements that you cannot easily control or remove.

```html
<!-- These will have a mysterious gap between them -->
<div style="display: inline-block; width: 100px;">Box 1</div>
<div style="display: inline-block; width: 100px;">Box 2</div>
```

>  **Do not use `display: inline-block`** for layout. The white space gap between elements is caused by the newline characters in your HTML and is very difficult to control.
>
>  Use `display: flex` or `display: grid` instead for proper layouts.

---

##  Targeting an Image Inside a Specific Container

Use a **descendant selector** to style an image only when it's inside a certain class or element:

```css
.item img {
  width: 100%;
}
```

```html
<div class="item">
  <img src="photo.jpg" alt="product photo">  <!--  This image gets the style -->
</div>

<img src="other.jpg" alt="other photo">  <!--  This one is NOT affected -->
```

###  Important Rule: Match Image Width to Its Parent

When you put an image inside a container (`div`, etc.), you must set the image's `width` relative to the parent — otherwise it will **overflow** or look wrong.

```css
.item {
  width: 300px;
}

.item img {
  width: 100%; /*  Image fills exactly its parent div */
}
```

>  If the box (parent `div`) is 300px wide but the image is 500px, the image will burst out of the box. Setting `width: 100%` tells the image: *"be as wide as your parent, nothing more."*

---

##  Default Values

Every browser applies its own **default CSS** to HTML elements before you write a single line of CSS. These are called **user-agent styles**.

Common defaults:
- `<body>` has a small default `margin`
- `<h1>`–`<h6>` have default `font-size` and `margin`
- `<p>` has default top and bottom `margin`
- `<a>` is blue and underlined by default
- `<ul>` / `<ol>` have default `padding-left`

### Resetting Defaults

It's common practice to reset these at the top of your CSS file:

```css
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
```

>  The `*` selector means **"apply this to every single element."** This gives you a clean, predictable starting point before you add your own styles.


# Task 1 note

The **`vertical-align`** property sets the vertical alignment of an inline, inline-block, or table-cell box and 

```css
/* Keyword values */
vertical-align: baseline;
vertical-align: sub;
vertical-align: super;
vertical-align: text-top;
vertical-align: text-bottom;
vertical-align: middle;
vertical-align: top;
vertical-align: bottom;
```