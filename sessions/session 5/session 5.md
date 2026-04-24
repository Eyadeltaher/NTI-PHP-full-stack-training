# CSS — Grid Layout

---

## What is CSS Grid?

CSS Grid is a two-dimensional layout system. While Flexbox works along a single axis (either a row or a column), Grid works on both axes at the same time — rows and columns together.

```
Flexbox:  [item][item][item]  →  one direction at a time

Grid:     [item][item][item]
          [item][item][item]  →  rows AND columns simultaneously
          [item][item][item]
```

> Use Flexbox when you are arranging items in one direction — a navigation bar, a row of buttons, a vertical stack. Use Grid when you are building a full layout or placing items in both rows and columns at the same time — a page layout, a card grid, a dashboard.

They are not competing tools. In real projects you use both — Grid for the overall page structure, Flexbox for the content inside each grid area.

---

## The Core Concept — Container and Items

Just like Flexbox, Grid works on two levels:

```
┌─────────────────────────────────────────────┐
│              GRID CONTAINER (parent)        │  ← gets display: grid
│  ┌─────────┐  ┌─────────┐  ┌─────────┐      │
│  │  item   │  │  item   │  │  item   │      │
│  └─────────┘  └─────────┘  └─────────┘      │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐      │
│  │  item   │  │  item   │  │  item   │      │
│  └─────────┘  └─────────┘  └─────────┘      │
└─────────────────────────────────────────────┘
```

```html
<div class="container">     <!-- grid container -->
  <div class="item">1</div> <!-- grid item -->
  <div class="item">2</div>
  <div class="item">3</div>
  <div class="item">4</div>
  <div class="item">5</div>
  <div class="item">6</div>
</div>
```

```css
.container {
  display: grid;
}
```

---

## Grid Lines, Tracks, and Cells

Before touching properties, understand the vocabulary:

```
     col 1    col 2    col 3
      |         |        |        |
row 1 |  cell   | cell   | cell   |
      |         |        |        |
row 2 |  cell   | cell   | cell   |
      |         |        |        |
```

- **Grid line** — the dividing lines between rows and columns. They are numbered starting from 1.
- **Track** — the space between two grid lines. A column track or a row track.
- **Cell** — the intersection of one row track and one column track.
- **Area** — a rectangular group of cells spanning multiple rows or columns.

Grid lines are numbered from **1** and can also be counted from the end using negative numbers (-1 is the last line):

```
  1    2    3    4
  |    |    |    |
  | c1 | c2 | c3 |
  |    |    |    |
 -4   -3   -2   -1
```

---

## Container Properties

### `grid-template-columns` — Define the Columns

This is the most important grid property. It defines how many columns the grid has and how wide each one is.

```css
.container {
  display: grid;
  grid-template-columns: 200px 200px 200px; /* Three columns, each 200px wide */
}
```

```css
/* Different widths */
grid-template-columns: 200px 400px 200px;

/* Percentages */
grid-template-columns: 25% 50% 25%;

/* Auto — column takes as much space as its content needs */
grid-template-columns: auto auto auto;
```

---

### The `fr` Unit — Fractional Unit

`fr` is a unit specific to Grid. It means "one fraction of the available space." It works like `flex-grow` — it divides the remaining space proportionally.

```css
.container {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr; /* Three equal columns */
}
```

```css
/* Two columns: sidebar takes 1 part, main takes 3 parts */
grid-template-columns: 1fr 3fr;

/* Mixed units — fixed sidebar, fluid main */
grid-template-columns: 260px 1fr;

/* Three equal columns using fr */
grid-template-columns: 1fr 1fr 1fr;
```

> `fr` is almost always better than percentages for grid columns because it automatically accounts for gaps between columns. A `%` calculation breaks when you add `gap` — `fr` does not.

---

### `repeat()` — Avoid Repetition

When you have multiple identical columns, `repeat()` saves you from writing them out:

```css
/* Without repeat */
grid-template-columns: 1fr 1fr 1fr 1fr;

/* With repeat */
grid-template-columns: repeat(4, 1fr); /* Four equal columns */

/* Repeat with mixed values */
grid-template-columns: repeat(3, 1fr 2fr); /* Pattern: narrow wide narrow wide narrow wide */
```

---

### `grid-template-rows` — Define the Rows

Works exactly like `grid-template-columns` but for rows.

```css
.container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: 200px 400px 200px;
}
```

If you do not define rows explicitly, Grid creates them automatically based on content — called **implicit rows**. Their size is controlled by `grid-auto-rows`.

```css
.container {
  grid-auto-rows: 200px; /* All implicitly created rows will be 200px tall */
}
```

---

### `gap` — Space Between Cells

```css
.container {
  gap: 24px;            /* Same gap for rows and columns */
  gap: 16px 24px;       /* row-gap | column-gap */
  row-gap: 16px;
  column-gap: 24px;
}
```

> `gap` only adds space between cells, never on the outer edges. This is cleaner than using margins on items.

---

### `grid-template-areas` — Name Your Layout

One of the most powerful Grid features. You draw your layout visually using names, then assign elements to those named areas.

```css
.container {
  display: grid;
  grid-template-columns: 260px 1fr;
  grid-template-rows: 80px 1fr 60px;
  grid-template-areas:
    "header  header"
    "sidebar main  "
    "footer  footer";
}
```

Then assign each child to its area:

```css
header  { grid-area: header; }
.sidebar { grid-area: sidebar; }
main    { grid-area: main; }
footer  { grid-area: footer; }
```

```html
<div class="container">
  <header>Header</header>
  <aside class="sidebar">Sidebar</aside>
  <main>Main Content</main>
  <footer>Footer</footer>
</div>
```

The layout looks exactly like what you drew in the CSS. Each word in the quotes is one cell. A dot `.` means an empty cell.

```css
grid-template-areas:
  "header  header  header"
  "sidebar main    main  "
  ".       footer  footer"; /* dot = empty cell */
```

> This is the most readable way to write a page layout. You can see the entire structure at a glance without doing mental math about rows and columns.

---

### `justify-items` and `align-items` — Align Items Inside Their Cells

These control how items are aligned within their grid cell.

```css
.container {
  justify-items: stretch;  /* Default — item fills cell width */
  justify-items: start;    /* Item aligns to left of cell */
  justify-items: end;      /* Item aligns to right of cell */
  justify-items: center;   /* Item centers horizontally in cell */
}

.container {
  align-items: stretch;    /* Default — item fills cell height */
  align-items: start;      /* Item aligns to top of cell */
  align-items: end;        /* Item aligns to bottom of cell */
  align-items: center;     /* Item centers vertically in cell */
}
```

---

### `justify-content` and `align-content` — Align the Entire Grid

When the grid is smaller than its container, these control where the whole grid sits inside the container.

```css
.container {
  justify-content: center;        /* Grid centered horizontally */
  justify-content: space-between; /* Columns spread to edges */
  justify-content: space-evenly;

  align-content: center;          /* Grid centered vertically */
  align-content: space-between;
}
```

---

### `place-items` — Shorthand for align-items + justify-items

```css
.container {
  place-items: center;          /* Centers items in both directions */
  place-items: start end;       /* align-items: start | justify-items: end */
}
```

---

## Item Properties

### `grid-column` — Span Across Columns

Controls which column lines an item starts and ends at.

```css
.item {
  grid-column: 1 / 3; /* Start at line 1, end at line 3 — spans 2 columns */
  grid-column: 1 / -1; /* Start at line 1, end at last line — full width */
}
```

Using `span` keyword — more readable:

```css
.item {
  grid-column: span 2;    /* Span 2 columns from wherever the item currently sits */
  grid-column: span 3;    /* Span 3 columns */
}
```

---

### `grid-row` — Span Across Rows

```css
.item {
  grid-row: 1 / 3;    /* Start at row line 1, end at row line 3 — spans 2 rows */
  grid-row: span 2;   /* Span 2 rows */
}
```

---

### Using Both — Place an Item Precisely

```css
.featured-card {
  grid-column: 1 / 3;  /* Spans first two columns */
  grid-row: 1 / 3;     /* Spans first two rows */
}
```

Real situation: A magazine-style grid where one featured article is large and spans multiple cells while smaller articles fill the remaining cells.

---

### `grid-area` — Position with Four Lines

Shorthand for row-start, column-start, row-end, column-end:

```css
.item {
  grid-area: 1 / 1 / 3 / 3;
  /* row-start / col-start / row-end / col-end */
  /* Occupies rows 1-2 and columns 1-2 */
}
```

Or when using named areas (as shown in the container section):

```css
.item {
  grid-area: header;
}
```

---

### `justify-self` and `align-self` — Override Alignment for One Item

```css
.special-item {
  justify-self: center; /* This item centers horizontally in its cell */
  align-self: end;      /* This item aligns to the bottom of its cell */
}

/* Shorthand */
.special-item {
  place-self: center end;
}
```

---

## The `minmax()` Function

`minmax()` sets a minimum and maximum size for a track. The track will be at least the minimum and at most the maximum.

```css
.container {
  grid-template-columns: repeat(3, minmax(200px, 1fr));
  /* Each column: minimum 200px, grows to fill available space equally */
}
```

Real situation: A card grid where cards should never get narrower than 200px but should fill available space when there is more room.

---

## Auto-Fill and Auto-Fit — Truly Responsive Grid

This combination of `repeat()`, `auto-fill` / `auto-fit`, and `minmax()` creates a fully responsive grid with no media queries at all.

### `auto-fill`

Creates as many columns as can fit, even if some columns are empty:

```css
.container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 24px;
}
```

On a wide screen: 4 columns fit → 4 columns are created. On a tablet: 2 columns fit → 2 columns are created. On mobile: 1 column fits → 1 column is created.

No media queries needed. The grid responds automatically.

### `auto-fit`

Same as `auto-fill` but if there are fewer items than columns can fit, it collapses the empty columns and stretches the existing items to fill the space.

```css
.container {
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}
```

```
auto-fill with 2 items on a wide screen:
[ item ][ item ][      ][      ]  ← empty columns remain

auto-fit with 2 items on a wide screen:
[ item            ][ item            ]  ← items stretch to fill
```

> Use `auto-fill` when you want items to keep their minimum size and leave empty space. Use `auto-fit` when you want items to grow and fill all available space regardless of how many there are. In most real projects, `auto-fit` is what you want for card grids.

---

## Real-World Patterns

### Responsive Card Grid — No Media Queries

The most used pattern. Works at any screen size automatically.

```css
.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}
```

---

### Classic Page Layout

```css
.page {
  display: grid;
  grid-template-columns: 260px 1fr;
  grid-template-rows: 80px 1fr 60px;
  grid-template-areas:
    "header  header"
    "sidebar main  "
    "footer  footer";
  min-height: 100vh;
  gap: 0;
}

header  { grid-area: header; }
.sidebar { grid-area: sidebar; }
main    { grid-area: main; }
footer  { grid-area: footer; }
```

---


### Magazine / Masonry-Style Grid

```css
.magazine {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-template-rows: repeat(3, 200px);
  gap: 16px;
}

.featured {
  grid-column: span 2;
  grid-row: span 2;  /* Large featured article */
}

.wide {
  grid-column: span 2;  /* Article spans two columns */
}
```

---

### Centering a Single Item

```css
.container {
  display: grid;
  place-items: center;  /* Shorthand: align-items + justify-items */
  min-height: 100vh;
}
```

This is actually cleaner than the Flexbox centering approach for a single centered element.

---

## Grid vs Flexbox — When to Use Which

|Situation|Use|
|---|---|
|Navigation bar (items in a row)|Flexbox|
|Vertical stack of elements|Flexbox|
|Card grid (rows and columns)|Grid|
|Full page layout|Grid|
|Centering one item|Either (Grid with `place-items` is cleaner)|
|Items that need to align across rows|Grid|
|Component internal layout|Flexbox|
|Content that wraps unpredictably|Flexbox with `flex-wrap`|
|Explicit two-dimensional layout|Grid|

---

