# Database & MySQL — Full Study Notes

---

## What is a Database?

A database is an organized system for storing, managing, and retrieving data persistently. Without a database, all data disappears when the script ends.

### Two Main Types

#### Relational Database (SQL)

Stores data in **tables** with rows and columns — like a spreadsheet. Tables can be linked to each other through relationships. Uses SQL (Structured Query Language) to communicate.

Examples: MySQL, PostgreSQL, SQLite, Microsoft SQL Server

#### Non-Relational Database (NoSQL)

Stores data in other formats — documents (JSON-like), key-value pairs, graphs. More flexible structure, better for certain use cases like real-time apps or unstructured data.

Examples: MongoDB, Redis, Firebase, Cassandra

> In this course we use **MySQL** — relational. It is the most common database used with PHP and the standard for web backends.

---

## ERD — Entity Relationship Diagram

Before writing a single line of SQL, you design your database on paper using an ERD. It maps out:

- What **entities** (tables) you need
- What **attributes** (columns) each table has
- What **relationships** exist between tables

```
[users] ----< [orders] >---- [products]
  one            many            one
```

> Drawing the ERD first prevents major structural mistakes that are expensive to fix later. 

---

## Starting MySQL — Command Line

```bash
mysql -u root          # Connect as root with no password
mysql -u root -p       # Connect as root and prompt for password
```

Once connected, you type SQL commands directly:

```sql
SHOW DATABASES;        -- List all databases
USE nti;               -- Switch to a specific database
SHOW TABLES;           -- List all tables in current database
```

---

## Database Commands

### Create a Database

```sql
CREATE DATABASE nti;
```

### Drop (Delete) a Database

```sql
DROP DATABASE nti;
-- This deletes everything inside — irreversible
```

> `DROP DATABASE` permanently deletes the database and all its tables and data. There is no undo.

---

## Data Types

You must declare the type of each column when creating a table. Choosing the right type affects storage size, performance, and what data is allowed.

### Numeric

| Type           | Range                           | Use                               |
| -------------- | ------------------------------- | --------------------------------- |
| `INT`          | -2,147,483,648 to 2,147,483,647 | IDs, counts, ages                 |
| `TINYINT`      | -128 to 127                     | Small numbers, flags (0/1)        |
| `BIGINT`       | Very large integers             | Large IDs, counters               |
| `FLOAT`        | Decimal, approximate            | Measurements                      |
| `DECIMAL(p,s)` | Exact decimal                   | Money — never use FLOAT for money |

### String

| Type         | Use                                                     |
| ------------ | ------------------------------------------------------- |
| `VARCHAR(n)` | Variable-length string up to n characters — most common |
| `CHAR(n)`    | Fixed-length string — always exactly n characters       |
| `TEXT`       | Long text — articles, descriptions                      |
| `ENUM(...)`  | One value from a predefined list                        |

### Date and Time

| Type        | Format                | Use                      |
| ----------- | --------------------- | ------------------------ |
| `DATE`      | `YYYY-MM-DD`          | Birthdates, event dates  |
| `DATETIME`  | `YYYY-MM-DD HH:MM:SS` | Timestamps               |
| `TIMESTAMP` | `YYYY-MM-DD HH:MM:SS` | Auto-updates, created_at |

---

## Column Constraints

Constraints are rules that enforce data integrity at the database level.

| Constraint        | Meaning                                                               |
| ----------------- | --------------------------------------------------------------------- |
| `PRIMARY KEY`     | Unique identifier for each row — cannot be null, cannot repeat        |
| `AUTO_INCREMENT`  | Automatically increases by 1 for each new row — used with PRIMARY KEY |
| `NOT NULL`        | This column cannot be left empty                                      |
| `UNIQUE`          | Every value in this column must be different across all rows          |
| `DEFAULT 'value'` | If no value is provided, use this default                             |
| `FOREIGN KEY`     | Links this column to a primary key in another table                   |

---

## CREATE TABLE

### Full Syntax

```sql
CREATE TABLE customers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(50) UNIQUE,
    birth_date DATE,
    gender     ENUM('male', 'female') DEFAULT 'male',
    INDEX id_name_email (name, email)
);
```

### Breaking Down Each Line

```sql
id INT AUTO_INCREMENT PRIMARY KEY
-- id is an integer, auto-numbered, and uniquely identifies each row

name VARCHAR(100) NOT NULL
-- name is text up to 100 chars, cannot be left empty

email VARCHAR(50) UNIQUE
-- email is text up to 50 chars, no two rows can have the same email

birth_date DATE
-- stores dates in YYYY-MM-DD format

gender ENUM('male', 'female') DEFAULT 'male'
-- can only be 'male' or 'female', defaults to 'male' if not specified

INDEX id_name_email (name, email)
-- creates an index on these two columns to speed up searches by name or email
```

### What is an INDEX?

An index is a data structure that MySQL builds alongside your table to make searches on specific columns much faster. Without an index, MySQL reads every row to find a match. With an index, it jumps directly to the matching rows.

```sql
INDEX my_index (column1, column2)
```

> Add indexes to columns you frequently search or filter by — typically `name`, `email`, `foreign key` columns. The primary key is automatically indexed. Do not index every column — indexes take up storage and slow down writes.

---

## INSERT — Adding Data

### Single Row

```sql
INSERT INTO users (name, email, password, gender, birth_date)
VALUES ('Ahmed', 'ahmed@gmail.com', 'pass123', 'male', '2000-07-13');
```

### Multiple Rows at Once

```sql
INSERT INTO users (name, email, password, gender, birth_date)
VALUES
  ('Ahmed',   'ahmed@gmail.com',   'pass123', 'male',   '2000-07-13'),
  ('Sara',    'sara@gmail.com',    'pass456', 'female', '1998-03-22'),
  ('Omar',    'omar@gmail.com',    'pass789', 'male',   '2001-11-05'),
  ('Fatima',  'fatima@gmail.com',  'pass000', 'female', '1999-08-14');
```

> Inserting multiple rows in one `INSERT` statement is much faster than running multiple separate `INSERT` statements. Always prefer bulk inserts when loading initial data.

---

## UPDATE — Modifying Data

```sql
-- Update one column
UPDATE users SET name = 'Zain' WHERE id = 1;

-- Update multiple columns
UPDATE users SET name = 'Mohamed', gender = 'male' WHERE id = 1;
```

> **Always include `WHERE` in an UPDATE.** Without it, every row in the table gets updated:
> `UPDATE users SET name = 'Zain'` — this changes every single user's name to Zain.

---

## DELETE — Removing Data

```sql
DELETE FROM users WHERE id = 2;
```

> **Always include `WHERE` in a DELETE.** Without it, every row is deleted:
> `DELETE FROM users` — this empties the entire table. The table structure remains but all data is gone.

---

## SELECT — Reading Data

### Select All Columns

```sql
SELECT * FROM customers;
```

### Select Specific Columns

```sql
SELECT first_name, last_name FROM customers;
```

### Column Aliases — `AS`

Rename a column in the output without changing the table:

```sql
SELECT first_name AS firstName, last_name FROM customers;
```

---

## WHERE — Filtering Rows

### Comparison Operators

```sql
SELECT * FROM customers WHERE id > 500;
SELECT * FROM customers WHERE id < 100;
SELECT * FROM customers WHERE id >= 500;
SELECT * FROM customers WHERE id <= 100;
SELECT * FROM customers WHERE id = 250;
SELECT * FROM customers WHERE id != 250;

SELECT * FROM customers WHERE first_name = 'Eleonora';
SELECT * FROM customers WHERE country = 'China';
```

### AND, OR, NOT

```sql
-- AND: both conditions must be true
SELECT first_name FROM customers
WHERE country = 'China' AND city = 'Jiabei';

-- OR: at least one condition must be true
SELECT first_name FROM customers
WHERE country = 'China' OR country = 'Egypt';

-- NOT: inverts the condition
SELECT * FROM customers WHERE NOT country = 'China';
```

---

## BETWEEN — Range Filtering

```sql
-- Without BETWEEN — verbose
SELECT * FROM customers WHERE id > 250 AND id < 500;

-- With BETWEEN — cleaner (inclusive on both ends)
SELECT * FROM customers WHERE id BETWEEN 250 AND 500;

-- NOT BETWEEN
SELECT * FROM customers WHERE id NOT BETWEEN 10 AND 60;
```

> `BETWEEN` is inclusive — `BETWEEN 250 AND 500` includes both 250 and 500.

---

## IN — Match a List of Values

```sql
-- Without IN — verbose
SELECT * FROM customers WHERE id = 200 OR id = 250 OR id = 300;

-- With IN — clean
SELECT * FROM customers WHERE id IN (200, 250, 300);

-- NOT IN
SELECT * FROM customers WHERE country NOT IN ('China', 'Russia', 'Brazil');
```

---

## LIKE — Pattern Matching

`LIKE` searches for patterns in text. Two wildcards:

| Wildcard | Meaning |
|---|---|
| `%` | Any number of characters (zero or more) |
| `_` | Exactly one character |

```sql
-- Starts with "jo"
SELECT * FROM customers WHERE first_name LIKE 'jo%';

-- Starts with "jo" followed by exactly one character
SELECT * FROM customers WHERE first_name LIKE 'jo_';

-- Starts with "jo" followed by exactly two characters
SELECT * FROM customers WHERE first_name LIKE 'jo__';

-- One character before "jo" and exactly two after
SELECT * FROM customers WHERE first_name LIKE '_jo__';

-- Ends with "j"
SELECT * FROM customers WHERE first_name LIKE '%j';

-- Contains "a" anywhere
SELECT * FROM customers WHERE first_name LIKE '%a%';

-- Contains "j" anywhere
SELECT * FROM customers WHERE first_name LIKE '%j%';
```

> `LIKE 'jo%'` is much faster than `LIKE '%jo%'` because a pattern starting with `%` cannot use an index — MySQL must scan every row.

---

## DISTINCT — Remove Duplicates

Returns only unique values — removes repeated rows.

```sql
-- Without DISTINCT — may return many "China" entries
SELECT country FROM customers WHERE country = 'China';

-- With DISTINCT — each country appears once
SELECT DISTINCT country FROM customers;

-- Distinct combination of two columns
SELECT DISTINCT first_name, last_name FROM customers;
```

---

## ORDER BY — Sorting Results

```sql
-- Default is ASC (ascending)
SELECT * FROM customers ORDER BY id;
SELECT * FROM customers ORDER BY id ASC;
SELECT * FROM customers ORDER BY id DESC;

-- Sort alphabetically
SELECT * FROM customers ORDER BY country DESC;

-- Sort by multiple columns: first by name A-Z, then by money high-low within same name
SELECT first_name, money FROM customers ORDER BY first_name ASC, money DESC;
```

### ORDER BY Must Come After WHERE

```sql
-- Wrong — syntax error
SELECT first_name FROM customers ORDER BY first_name WHERE id > 5;

-- Correct — WHERE before ORDER BY
SELECT first_name FROM customers WHERE id > 5 ORDER BY first_name ASC;
```

> The order of SQL clauses matters:
> `SELECT` → `FROM` → `WHERE` → `GROUP BY` → `HAVING` → `ORDER BY` → `LIMIT`

---

## LIMIT and OFFSET — Pagination

Used to retrieve a specific slice of rows. Essential for pagination in any web application.

```sql
-- First 10 rows
SELECT * FROM customers LIMIT 10;

-- First 10 rows, starting from position 0
SELECT * FROM customers LIMIT 10 OFFSET 0;   -- Page 1

-- 10 rows, skip the first 10 (rows 11-20)
SELECT * FROM customers LIMIT 10 OFFSET 10;  -- Page 2

-- 10 rows, skip the first 20 (rows 21-30)
SELECT * FROM customers LIMIT 10 OFFSET 20;  -- Page 3
```

### Pagination Formula

```
OFFSET = (current_page - 1) × rows_per_page

Page 1: OFFSET = (1-1) × 10 = 0
Page 2: OFFSET = (2-1) × 10 = 10
Page 3: OFFSET = (3-1) × 10 = 20
```

---

## Aggregate Functions — Calculations on Groups of Rows

These functions compute a single result from multiple rows.

```sql
-- MIN: lowest value
SELECT MIN(money) FROM customers;

-- MAX: highest value
SELECT MAX(money) FROM customers;

-- COUNT: number of rows
SELECT COUNT(*) FROM customers;                     -- Total rows
SELECT COUNT(*) AS data_number FROM customers;      -- With alias
SELECT COUNT(id) AS data_number FROM customers;     -- Count non-null id values

-- SUM: total of all values
SELECT SUM(money) FROM customers;

-- AVG: average value
SELECT AVG(money) FROM customers;
```

> `COUNT(*)` counts all rows including nulls. `COUNT(column)` counts only rows where that column is not null. They give different results if a column has null values.

---

## GROUP BY — Aggregate Per Category

`GROUP BY` splits rows into groups and applies aggregate functions to each group separately.

```sql
-- Without GROUP BY: count all rows in China
SELECT COUNT(*) FROM customers WHERE country = 'China';

-- With GROUP BY: count rows for each country
SELECT COUNT(*) AS count, country
FROM customers
GROUP BY country;

-- With ORDER BY on the result
SELECT COUNT(*) AS count_num, country
FROM customers
GROUP BY country
ORDER BY count_num DESC;

-- SUM per group
SELECT SUM(money) AS total, country
FROM customers
GROUP BY country;
```

> When you use `GROUP BY`, every column in your `SELECT` must either be in the `GROUP BY` clause or be inside an aggregate function. Otherwise the query is invalid.

---

## HAVING — Filter After Grouping

`WHERE` filters rows **before** grouping. `HAVING` filters groups **after** `GROUP BY` is applied.

```sql
-- Filter individual rows before grouping (WHERE)
-- Filter grouped results (HAVING)

-- All groups where count is greater than 5
SELECT COUNT(*) AS count, country
FROM customers
GROUP BY country
HAVING count > 5;

-- WHERE + HAVING together
-- First: keep only customers with money > 1000 (WHERE)
-- Then: group by country
-- Then: keep only groups with count > 5 (HAVING)
SELECT COUNT(*) AS count, country
FROM customers
WHERE money > 1000
GROUP BY country
HAVING count > 5;
```

> You cannot use `WHERE count > 5` because `count` is an aggregate — it does not exist yet when `WHERE` runs. `HAVING` runs after `GROUP BY` so it can filter on aggregates.

---

## Relationships — Connecting Tables

Real databases have multiple tables that are connected. This avoids repeating data and keeps the structure clean. These connections are called relationships.

---

### Foreign Key

A foreign key is a column in one table that **references the primary key** of another table. It enforces that you cannot insert a value in the foreign key column unless that value exists in the referenced table.

```sql
FOREIGN KEY (column_in_this_table) REFERENCES other_table(id)
```

---

### One-to-One Relationship

Each row in Table A links to exactly one row in Table B, and vice versa.

Real example: a user has exactly one national ID, and each national ID belongs to exactly one user.

```sql
CREATE TABLE users (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100)
);

CREATE TABLE national_ids (
    id        INT PRIMARY KEY AUTO_INCREMENT,
    number_id INT UNIQUE,
    user_id   INT UNIQUE,             -- UNIQUE enforces one-to-one
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

> The `UNIQUE` constraint on `user_id` is what makes this one-to-one. Without it, multiple national IDs could reference the same user — that would be one-to-many.

---

### One-to-Many Relationship

One row in Table A can link to many rows in Table B. But each row in Table B links to only one row in Table A.

Real example: one department has many employees, but each employee belongs to only one department.

```sql
CREATE TABLE departments (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50)
);

CREATE TABLE employees (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    name          VARCHAR(100),
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);
```

```
departments          employees
-----------          ---------
id=1 (Sales)  ←───  department_id=1  (Ahmed)
              ←───  department_id=1  (Sara)
              ←───  department_id=1  (Omar)

id=2 (IT)     ←───  department_id=2  (Fatima)
```

The foreign key `department_id` in `employees` has no `UNIQUE` constraint — multiple employees can share the same department. This is what makes it one-to-many.

---

### Many-to-Many Relationship

A many-to-many relationship requires a **junction table** — a third table that holds the connection between the two main tables. Each row in the junction table represents one relationship between one row from each side.

```sql
CREATE TABLE students (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE TABLE subjects (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

-- Junction table — one row = one student enrolled in one subject
CREATE TABLE student_subject (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    subject_id INT,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);
```

```
students          student_subject        subjects
--------          ---------------        --------
id=1 Ahmed   ←── student_id=1, subject_id=1 ──→ id=1 Math
             ←── student_id=1, subject_id=2 ──→ id=2 Science
id=2 Sara    ←── student_id=2, subject_id=1 ──→ id=1 Math
```

Ahmed is enrolled in Math and Science. Sara is enrolled in Math. Math has two students. This is many-to-many.


---

## SQL Clause Order (Must Follow This)

```sql
SELECT columns
FROM table
WHERE conditions
GROUP BY columns
HAVING aggregate_conditions
ORDER BY columns
LIMIT n OFFSET n;
```

> This is not just a convention — SQL requires this order. Writing `ORDER BY` before `WHERE` is a syntax error.


