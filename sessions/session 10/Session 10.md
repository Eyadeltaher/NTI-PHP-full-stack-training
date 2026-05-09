# MySQL — Advanced: Joins, DDL, Functions & Procedures

---

## JOINs — Combining Data from Multiple Tables

A `JOIN` lets you query data from two or more tables at the same time by matching rows based on a related column. Without joins, you would need multiple separate queries and combine the results yourself.

---

### INNER JOIN (default JOIN)

Returns only rows where there is a **match in both tables**. Rows with no match on either side are excluded.

```sql
SELECT * FROM employees
JOIN departments
ON employees.department_id = departments.id;
```

With aliases — shorter table names:

```sql
SELECT emp.name, dep.name
FROM employees AS emp
JOIN departments AS dep
ON emp.department_id = dep.id;
```

With column aliases — cleaner output:

```sql
SELECT emp.name AS employee_name, dep.name AS department_name
FROM employees AS emp
JOIN departments AS dep
ON emp.department_id = dep.id;
```

Real situation: Show every employee alongside their department name. Employees with no department assigned are excluded from the result.

---

### LEFT JOIN

Returns **all rows from the left table**, and the matching rows from the right table. If there is no match on the right side, the right-side columns come back as `NULL`.

```sql
SELECT emp.name AS employee_name, dep.name AS department_name
FROM employees AS emp
LEFT JOIN departments AS dep
ON emp.department_id = dep.id;
```

```
Result:
Ahmed   | Sales
Sara    | NULL    ← Sara has no department — still appears
Omar    | IT
```

Real situation: Show all employees, including ones who have not been assigned to a department yet. With a regular JOIN, Sara would disappear from the results — with LEFT JOIN she appears with `NULL` for department.

---

### RIGHT JOIN

Returns **all rows from the right table**, and the matching rows from the left table. If there is no match on the left side, left-side columns come back as `NULL`.

```sql
SELECT emp.name AS employee_name, dep.name AS department_name
FROM employees AS emp
RIGHT JOIN departments AS dep
ON emp.department_id = dep.id;
```

```
Result:
Ahmed   | Sales
Omar    | IT
NULL    | Marketing   ← Marketing exists but has no employees yet
```

Real situation: Show all departments, including ones that have no employees yet.

---

### JOIN Comparison

| JOIN Type | Returns |
|---|---|
| `JOIN` / `INNER JOIN` | Only rows with a match in BOTH tables |
| `LEFT JOIN` | All rows from left table + matching rows from right (NULL if no match) |
| `RIGHT JOIN` | All rows from right table + matching rows from left (NULL if no match) |

> In practice, `LEFT JOIN` is used far more often than `RIGHT JOIN`. You can always rewrite a `RIGHT JOIN` as a `LEFT JOIN` by swapping the table order. Most developers stick to `LEFT JOIN` for consistency.

---

## UNION — Combining Results from Multiple Queries

`UNION` stacks the results of two `SELECT` queries vertically — one result set on top of the other. Both queries must return the same number of columns.

### UNION — Removes Duplicates

```sql
SELECT name FROM employees
UNION
SELECT first_name FROM users;
-- If the same name appears in both tables, it appears only once
```

### UNION ALL — Keeps Duplicates

```sql
SELECT name FROM employees
UNION ALL
SELECT first_name FROM users;
-- Every row from both queries appears — including duplicates
```

Real situation: You have two tables of users from two different systems and want to display a combined list. Use `UNION` if you want to deduplicate, `UNION ALL` if you want every row regardless.

---

## Copying Table Structure and Data

### Copy Structure Only — No Data

```sql
CREATE TABLE customer_test LIKE customers;
-- Creates customer_test with the exact same columns and constraints as customers
-- The new table is empty
```

### Copy Data into an Existing Table

```sql
INSERT INTO customer_test SELECT * FROM customers;
-- Copies all rows from customers into customer_test
```

### Copy Structure AND Data in One Step

```sql
CREATE TABLE customer_test AS SELECT * FROM customers;
-- Creates the table and fills it with data in one command
-- Note: constraints like PRIMARY KEY and FOREIGN KEY are NOT copied — only columns and data
```

> `CREATE TABLE ... LIKE` copies the full structure including indexes and constraints. `CREATE TABLE ... AS SELECT` copies columns and data but not constraints. Know which you need.

---

## SQL Language Categories

SQL is divided into sub-languages based on what the commands do:

| Category | Full Name | Commands |
|---|---|---|
| DDL | Data Definition Language | `CREATE`, `ALTER`, `DROP`, `TRUNCATE`, `RENAME` |
| DML | Data Manipulation Language | `INSERT`, `UPDATE`, `DELETE` |
| DQL | Data Query Language | `SELECT` |
| DCL | Data Control Language | `GRANT`, `REVOKE` |
| TCL | Transaction Control Language | `START TRANSACTION`, `COMMIT`, `ROLLBACK` |

---

## DDL Commands — Defining and Modifying Structure

### TRUNCATE — Delete All Data, Keep Structure

```sql
TRUNCATE TABLE customer_test;
-- Deletes every row — faster than DELETE FROM table
-- The table itself remains — empty and ready to use
```

### TRUNCATE vs DELETE

| | `TRUNCATE` | `DELETE` |
|---|---|---|
| Removes | All rows — no WHERE clause | Specific rows with WHERE, or all rows |
| Speed | Very fast — does not log each row | Slower — logs each deleted row |
| Auto increment reset | Yes — counter resets to 1 | No — counter continues from last value |
| Can be rolled back | No (in most engines) | Yes |
| Triggers | Does not fire row-level triggers | Fires triggers |

> Use `TRUNCATE` when you want to completely empty a table and start fresh — like clearing test data. Use `DELETE` when you need to remove specific rows or need the operation to be reversable.

---

### RENAME — Rename a Table

```sql
RENAME TABLE customer2 TO customer_test;
```

---

### ALTER — Modify an Existing Table

`ALTER TABLE` changes the structure of a table that already exists and has data in it.

#### Add a Column

```sql
ALTER TABLE customer_test ADD age INT;
ALTER TABLE customer_test ADD age INT AFTER name;      -- Place after specific column
ALTER TABLE customer_test ADD age INT FIRST;           -- Place as first column
```

#### Change Column Type — MODIFY

```sql
ALTER TABLE customer_test MODIFY age VARCHAR(50);
-- Changes the data type of the column — column name stays the same
```

#### Rename a Column — Two Ways

```sql
-- MySQL 8.0+ only
ALTER TABLE customer_test RENAME COLUMN first_name TO name;

-- Works in older MySQL versions too
ALTER TABLE customer_test CHANGE COLUMN first_name name VARCHAR(100);
-- CHANGE requires you to specify the new data type too
```

> `RENAME COLUMN` is cleaner but only available in MySQL 8.0+. Use `CHANGE COLUMN` for compatibility with older versions.

#### Drop a Column

```sql
ALTER TABLE customer_test DROP COLUMN age;
```

#### Add Primary Key

```sql
ALTER TABLE customer_test ADD PRIMARY KEY (id);
```

#### Drop Primary Key

```sql
ALTER TABLE customer_test DROP PRIMARY KEY;
```

#### Add Unique Constraint

```sql
ALTER TABLE customer_test ADD CONSTRAINT unique_email UNIQUE (email);
```

#### Drop Unique Constraint (Drop Index)

```sql
ALTER TABLE customer_test DROP INDEX unique_email;
```

> A `UNIQUE` constraint in MySQL is implemented as an index internally. That is why you drop it with `DROP INDEX` not `DROP CONSTRAINT`.

---

## Transactions — All or Nothing

A transaction groups multiple SQL statements so they either **all succeed together** or **all fail together**. This prevents data from being left in a broken half-updated state.

### The Problem Without Transactions

```sql
-- Transfer money from account 2 to account 1
UPDATE customer_test SET money = money + 1000 WHERE id = 1;  -- Step 1: Success
-- Server crashes here
UPDATE customer_test SET money = money - 1000 WHERE id = 2;  -- Step 2: Never runs
-- Result: Account 1 gained 1000 but Account 2 was never debited — money was created
```

### The Solution — Transaction

```sql
START TRANSACTION;

UPDATE customer_test SET money = money + 1000 WHERE id = 1;
UPDATE customer_test SET money = money - 1000 WHERE id = 2;

COMMIT;  -- Only now are the changes permanently saved
```

If something goes wrong between `START TRANSACTION` and `COMMIT`:

```sql
START TRANSACTION;

UPDATE customer_test SET money = money + 1000 WHERE id = 1;
-- Something fails here

ROLLBACK;  -- Undo everything back to the state before START TRANSACTION
```

> `COMMIT` makes changes permanent. `ROLLBACK` undoes all changes since `START TRANSACTION`. Transactions are essential for any operation involving money, inventory, or multiple related updates.

---

## CASE — Conditional Logic in SQL

`CASE` works like an if-else statement inside a SQL query. It evaluates conditions and returns different values based on which condition is true.

### In SELECT — Add a Computed Column

```sql
SELECT first_name, money,
CASE
    WHEN money < 50000  THEN 'poor'
    WHEN money < 100000 THEN 'mid poor'
    WHEN money > 100000 THEN 'rich'
    ELSE 'invalid'
END AS result
FROM customers;
```

Output adds a `result` column to every row based on their `money` value. No table data is modified.

### In UPDATE — Conditional Updates

```sql
UPDATE customers
SET money =
CASE
    WHEN money < 1000 THEN money + 1000
    WHEN money < 5000 THEN money + 5000
END;
-- Different rows get different increases based on their current money value
```

> `CASE` in an `UPDATE` lets you apply different logic to different rows in a single query instead of running multiple separate `UPDATE` statements.

---

## Built-in MySQL Functions

### String Functions

```sql
-- CONCAT — join strings together
SELECT CONCAT(first_name, last_name) FROM customers;
SELECT CONCAT(first_name, ' ', last_name) FROM customers;  -- with space

-- CONCAT_WS — join with a separator (WS = With Separator)
SELECT CONCAT_WS(' ', first_name, last_name) FROM customers;
-- Cleaner than CONCAT when you have a consistent separator

-- Case
SELECT LOWER(first_name) FROM customers;   -- lowercase
SELECT UPPER(first_name) FROM customers;   -- UPPERCASE
SELECT LCASE(first_name) FROM customers;   -- same as LOWER
SELECT UCASE(first_name) FROM customers;   -- same as UPPER

-- Trim whitespace
SELECT TRIM(first_name) FROM customers;    -- remove from both ends
SELECT LTRIM(first_name) FROM customers;   -- remove from left
SELECT RTRIM(first_name) FROM customers;   -- remove from right

-- Pad a string
SELECT LPAD('5', 3, '0') FROM customers;   -- '005' — pad left to reach length 3

-- Replace
SELECT REPLACE(first_name, 'a', 'A') FROM customers;  -- Replace 'a' with 'A'

-- Reverse
SELECT REVERSE(first_name) FROM customers;

-- Repeat
SELECT REPEAT('ab', 3);    -- 'ababab'
```

### Numeric Functions

```sql
SELECT ABS(-42);        -- 42 — absolute value
SELECT CEIL(25.1);      -- 26 — round up
SELECT FLOOR(25.9);     -- 25 — round down
SELECT ROUND(25.567, 2); -- 25.57 — round to 2 decimal places
SELECT MOD(10, 3);      -- 1 — remainder (same as %)
SELECT POWER(2, 8);     -- 256
SELECT SQRT(16);        -- 4
```

### Date Functions

```sql
SELECT NOW();                          -- 2024-03-15 14:30:00 — current date and time
SELECT CURRENT_DATE();                 -- 2024-03-15 — date only
SELECT CURRENT_TIME();                 -- 14:30:00 — time only
SELECT YEAR(birth_date) FROM users;    -- Extract year
SELECT MONTH(birth_date) FROM users;   -- Extract month
SELECT DAY(birth_date) FROM users;     -- Extract day

-- Difference between two dates
SELECT TIMESTAMPDIFF(YEAR, birth_date, CURRENT_DATE()) FROM users;
-- Returns how many full years between birth_date and today — used for age calculation
```

### Conditional Functions

```sql
-- IF: simple if-else
SELECT IF(gender = 'male', 'yes', 'no') FROM customers;
-- Returns 'yes' if gender is male, 'no' otherwise

-- IFNULL: replace NULL with a default
SELECT IFNULL(first_name, 'Unknown') FROM customers;
-- If first_name is NULL → show 'Unknown', otherwise show the name

-- NULLIF: returns NULL if two values are equal
SELECT NULLIF(first_name, last_name) FROM customers;
-- If first_name equals last_name → returns NULL, otherwise returns first_name

-- ISNULL: check if a value is NULL (returns 1 if null, 0 if not)
SELECT ISNULL(first_name) FROM customers;

-- COALESCE: return the first non-NULL value from a list
SELECT COALESCE(nickname, first_name, 'Anonymous') FROM customers;
-- Tries nickname first — if NULL, tries first_name — if NULL, uses 'Anonymous'
```

### Information Functions

```sql
SELECT DATABASE();          -- Name of the currently selected database
SELECT CURRENT_USER();      -- Current logged-in MySQL user
SELECT CONNECTION_ID();     -- ID of the current connection
SELECT LAST_INSERT_ID();    -- ID of the last row inserted — used after INSERT
```

> `LAST_INSERT_ID()` is very useful in PHP. After inserting a row, call `LAST_INSERT_ID()` to get its auto-generated ID so you can use it in related inserts immediately.

### CAST — Convert Data Type

```sql
SELECT CAST('2024-03-15' AS DATE);    -- String to DATE
SELECT CAST(price AS DECIMAL(10,2));  -- Convert to decimal with precision
SELECT CAST(id AS CHAR);              -- Number to string
```

---

## Creating Custom Functions

A stored function is a reusable piece of SQL logic that takes input, does a calculation, and returns a single value. It can be called anywhere you can use an expression.

### Syntax

```sql
DELIMITER //
CREATE FUNCTION function_name(param_name type)
RETURNS return_type
BEGIN
    DECLARE local_var type;
    -- logic here
    RETURN value;
END //
DELIMITER ;
```

> `DELIMITER //` changes the statement delimiter from `;` to `//`. This is necessary because the function body contains `;` characters, and without changing the delimiter, MySQL would think the function ends at the first `;` inside the body.

### Example 1 — Calculate Tax

```sql
DELIMITER //
CREATE FUNCTION calc_taxes(salary INT)
RETURNS INT
BEGIN
    DECLARE taxes INT;
    SET taxes = salary * 0.14;
    RETURN taxes;
END //
DELIMITER ;

-- Using the function
SELECT name, salary, calc_taxes(salary) AS taxes
FROM employees;
```

### Example 2 — Calculate Age from Birth Date

```sql
DELIMITER //
CREATE FUNCTION calc_age(birth_date DATE)
RETURNS INT
BEGIN
    RETURN TIMESTAMPDIFF(YEAR, birth_date, CURRENT_DATE());
END //
DELIMITER ;

-- Using the function
SELECT birth_date, calc_age(birth_date) AS age FROM users;
```

> `TIMESTAMPDIFF(YEAR, start, end)` calculates the number of complete years between two dates — it is the correct way to calculate someone's age from a birth date.

---

## Stored Procedures

A stored procedure is a saved block of SQL code that you can call by name. Unlike functions, procedures do not return a value directly — they use `IN`, `OUT`, and `INOUT` parameters to communicate.

| Parameter Mode | Direction | Purpose |
|---|---|---|
| `IN` | Input only | Pass a value into the procedure |
| `OUT` | Output only | Procedure writes a result back to a variable |
| `INOUT` | Both | Pass a value in, procedure modifies it and sends it back |

---

### IN — Input Parameter

```sql
DELIMITER //
CREATE PROCEDURE insert_user(
    IN user_name VARCHAR(100),
    IN email     VARCHAR(100)
)
BEGIN
    INSERT INTO users (name, email)
    VALUES (user_name, email);
END //
DELIMITER ;

-- Calling the procedure
CALL insert_user('Asmaa', 'asmaa@gmail.com');
```

Real situation: Wrap a complex INSERT with business logic, validation, or multiple steps into a single callable procedure.

---

### OUT — Output Parameter

The procedure calculates or retrieves something and stores it in a variable you provide.

```sql
DELIMITER //
CREATE PROCEDURE get_user_email(
    IN  user_id INT,
    OUT my_email VARCHAR(100)
)
BEGIN
    SELECT email INTO my_email
    FROM users
    WHERE id = user_id;
END //
DELIMITER ;

-- Calling the procedure
CALL get_user_email(1, @email);
SELECT @email;   -- Read the output variable
```

> Variables starting with `@` are **user-defined session variables** in MySQL. They exist for the duration of your connection and are how you receive `OUT` parameters from procedures.

---

### INOUT — Input and Output Combined

The caller passes a value in. The procedure uses it, modifies it, and sends the modified value back.

```sql
DELIMITER //
CREATE PROCEDURE full_user(
    IN    user_id    INT,
    OUT   my_email   VARCHAR(100),
    INOUT user_bonus INT
)
BEGIN
    SELECT email INTO my_email
    FROM users
    WHERE id = user_id;

    UPDATE users
    SET bonus = user_bonus + bonus
    WHERE id = user_id;
END //
DELIMITER ;

-- Set a starting value for the INOUT variable
SET @user_bonus = 200;

-- Call the procedure
CALL full_user(3, @email, @user_bonus);

-- Read both output values
SELECT @email, @user_bonus;
```

Real situation: A procedure that both retrieves user info and applies a bonus calculation — getting data out while also using input data to update the database.

---

### Function vs Procedure — Key Differences

|                 | Function                           | Procedure                              |
| --------------- | ---------------------------------- | -------------------------------------- |
| Returns         | Single value with `RETURN`         | Nothing directly — uses OUT parameters |
| Called in       | `SELECT`, `WHERE`, expressions     | `CALL` statement only                  |
| Use for         | Calculations, transformations      | Complex operations, multiple steps     |
| Can modify data | Limited (depends on configuration) | Yes — full DML allowed                 |



