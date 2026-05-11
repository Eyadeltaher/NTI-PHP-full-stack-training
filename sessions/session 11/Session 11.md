# MySQL — Triggers, Views, Subqueries & PHP Database Connection

---

## Triggers

A trigger is a block of SQL code that **runs automatically** when a specific event happens on a table. You do not call it manually — MySQL fires it on its own when the event occurs.

### Trigger Events

| Event | When it fires |
|---|---|
| `INSERT` | When a new row is added |
| `UPDATE` | When an existing row is modified |
| `DELETE` | When a row is removed |

### Trigger Timing

| Timing | When it runs |
|---|---|
| `BEFORE` | Before the operation happens — can modify the data before it is saved |
| `AFTER` | After the operation completes — data is already saved |

### `NEW` and `OLD` — Accessing Row Data Inside a Trigger

| Keyword | Available in | Contains |
|---|---|---|
| `NEW` | INSERT and UPDATE triggers | The new values being inserted or the updated values |
| `OLD` | UPDATE and DELETE triggers | The original values before the change or deletion |

---

### Trigger 1 — AFTER INSERT: Auto-Create a Related Record

When a new user is inserted, automatically create a national ID record for them.

```sql
DELIMITER //
CREATE TRIGGER national_user
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO nationalids (number_id, user_id)
    VALUES (1236058, NEW.id);
    -- NEW.id = the id of the user that was just inserted
END //
DELIMITER ;
```

Real situation: Any time a new user registers, you want related data created automatically — a profile row, a settings row, a wallet row. Instead of remembering to do this in every PHP file that creates a user, the trigger handles it at the database level without fail.

---

### Trigger 2 — BEFORE UPDATE: Validate Data Before Saving

Cap the bonus value so it can never exceed 2000, no matter what was sent.

```sql
DELIMITER //
CREATE TRIGGER check_bonus
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.bonus > 2000 THEN
        SET NEW.bonus = 2000;
    END IF;
END //
DELIMITER ;
```

Real situation: Business rules that must always be enforced regardless of which application or developer writes the update. A `BEFORE` trigger can modify `NEW` values before they are written — acting as a last line of defense.

> You can only modify `NEW` values in a `BEFORE` trigger. In an `AFTER` trigger, the data is already written — `NEW` is read-only.

---

### Trigger 3 — AFTER DELETE: Log Deleted Records

When a user is deleted, save their name and the time of deletion to a log table.

```sql
DELIMITER //
CREATE TRIGGER users_logs_delete
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    INSERT INTO users_logs (name, created_at)
    VALUES (OLD.name, NOW());
    -- OLD.name = the name of the user that was just deleted
END //
DELIMITER ;
```

Real situation: Audit trails — knowing what was deleted, when, and keeping a record for compliance or debugging. The `OLD` keyword gives you access to the deleted row's values that no longer exist in the table.

---

### Trigger Summary

```
BEFORE INSERT  → Validate or modify data before it is saved
AFTER INSERT   → Create related records, send notifications
BEFORE UPDATE  → Enforce business rules, cap values
AFTER UPDATE   → Log changes, update related data
BEFORE DELETE  → Prevent deletion if conditions not met
AFTER DELETE   → Log deletions, clean up related data
```

### Managing Triggers

```sql
-- See all triggers in the current database
SHOW TRIGGERS;

-- Delete a trigger
DROP TRIGGER trigger_name;
DROP TRIGGER IF EXISTS trigger_name;
```

---

## Views

A view is a **saved SELECT query** that you can treat like a table. It does not store data itself — every time you query it, it runs the underlying SELECT query and returns the result.

### Creating a View

```sql
CREATE VIEW users_info AS
SELECT id, name, email, gender FROM users;
-- This view shows only these columns from users — hiding password and other sensitive fields
```

### Querying a View

```sql
SELECT * FROM users_info;
-- Executes the saved query and returns the result
```

### Dropping a View

```sql
DROP VIEW users_info;
```

### Why Use Views?

**Simplify complex queries** — instead of writing a long JOIN every time, save it as a view and query the view:

```sql
CREATE VIEW employee_details AS
SELECT emp.name AS employee_name, dep.name AS department_name, emp.salary
FROM employees AS emp
LEFT JOIN departments AS dep ON emp.department_id = dep.id;

-- Now any developer can just write:
SELECT * FROM employee_details;
-- Instead of the full JOIN every time
```

**Security** — expose only the columns a user should see:

```sql
CREATE VIEW public_users AS
SELECT id, name, email FROM users;
-- Password column is not included — safe to share access to this view
```

**Consistency** — the same logic in one place. If the query needs to change, update the view once instead of finding every place in PHP that uses that query.

> A view is a virtual table. It has no storage of its own. Querying a view is exactly the same as running the SELECT that defines it — there is no performance benefit from the view itself.

---

## Subqueries

A subquery is a `SELECT` statement nested inside another SQL statement. The inner query runs first, and its result is used by the outer query.

### Subquery in WHERE — Filter Based on a Calculation

Find employees whose salary is above the average:

```sql
-- Step 1: Get the average (inner query)
SELECT AVG(salary) FROM employees;   -- Returns: 5000

-- Step 2: Use it in the outer query
SELECT name, salary
FROM employees
WHERE salary > (SELECT AVG(salary) FROM employees);
```

> This is the most common use of subqueries. The alternative would be to run two separate queries in PHP — one to get the average, then construct the second query using that number. The subquery does it in one SQL statement.

---

### Subquery in SELECT — Compute a Value Per Row

Add the department name to each employee row without a JOIN:

```sql
SELECT name,
    (SELECT name FROM departments WHERE departments.id = employees.department_id) AS dept_name
FROM employees;
```

> This is called a **correlated subquery** — the inner query references the outer query (`employees.department_id`). It runs once for every row in the outer query. For large tables, a JOIN is usually faster.

---

### Subquery with IN — Filter Using a List

Find employees who work in the sales department:

```sql
SELECT name FROM employees
WHERE department_id IN (
    SELECT id FROM departments WHERE name = 'sales'
);
```

The inner query returns a list of department IDs where name is 'sales'. The outer query finds all employees whose `department_id` is in that list.

---

### EXISTS — Check if a Subquery Returns Any Rows

`EXISTS` returns true if the subquery finds at least one matching row. Faster than `IN` for large datasets.

```sql
-- Find employees who have a matching department
SELECT name FROM employees
WHERE EXISTS (
    SELECT id FROM departments
    WHERE departments.id = employees.department_id
);

-- Find employees with NO matching department
SELECT name FROM employees
WHERE NOT EXISTS (
    SELECT id FROM departments
    WHERE departments.id = employees.department_id
);
```

Real situation: Find all orders that have at least one item, or find all customers who have never placed an order.

### EXISTS vs IN

| | `IN` | `EXISTS` |
|---|---|---|
| Returns | Matches against a value list | True/false based on row existence |
| Performance | Better for small subqueries | Better for large subqueries |
| NULL handling | Can cause issues with NULLs | Handles NULLs safely |

---

## Self Join

A self join is when a table is joined to **itself**. This is used when rows in a table have a relationship with other rows in the same table.

### The Use Case — Employee and Manager

An employee table where each employee can have a manager — and the manager is also an employee in the same table:

```sql
-- employees table:
-- id | name    | manager_id
-- 1  | Ahmed   | NULL       ← CEO, no manager
-- 2  | Sara    | 1          ← reports to Ahmed
-- 3  | Omar    | 1          ← reports to Ahmed
-- 4  | Fatima  | 2          ← reports to Sara
```

```sql
SELECT
    emp.name     AS employee_name,
    manager.name AS manager_name
FROM employees AS emp
LEFT JOIN employees AS manager
ON emp.manager_id = manager.id;
```

```
Result:
Ahmed   | NULL     ← Ahmed has no manager
Sara    | Ahmed
Omar    | Ahmed
Fatima  | Sara
```

> The key is aliasing the same table twice — once as `emp` (the employee) and once as `manager`. The `LEFT JOIN` ensures that top-level employees with no manager (like the CEO) still appear in the result with `NULL` for manager name.

---

## PHP Database Connection with MySQLi

MySQLi (MySQL Improved) is the PHP extension for connecting to and working with MySQL databases. There are two styles — procedural and object-oriented. The training code uses the procedural style.

---

### Connection File

Create a separate file for the connection so every other file just includes it:

**connection.php**

```php
<?php
  $connection = mysqli_connect('localhost', 'root', '', 'nti11');
  // Parameters: host | username | password | database_name

  if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
  }
?>
```

> Use `die()` on connection failure. If the connection fails and you do not stop execution, every subsequent database call will throw confusing errors. `die()` stops the script and shows the real problem immediately.

---

### SELECT — Fetching Data

**index.php**

```php
<?php
  require('connection.php');

  $query  = "SELECT * FROM customers";
  $result = mysqli_query($connection, $query);

  $customers = mysqli_fetch_all($result, MYSQLI_ASSOC);
  // MYSQLI_ASSOC → each row is an associative array: $row['first_name'] not $row[0]
?>

<!DOCTYPE html>
<html>
<body>
  <table>
    <?php foreach ($customers as $customer): ?>
      <tr>
        <td><?php echo $customer['first_name']; ?></td>
        <td><?php echo $customer['last_name']; ?></td>
        <td><?php echo $customer['email']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
```

### Key Functions for Reading Data

```php
<?php
  $result = mysqli_query($connection, $query);

  // Fetch ALL rows at once as an array of arrays
  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

  // Fetch ONE row at a time (use in a while loop)
  while ($row = mysqli_fetch_assoc($result)) {
    echo $row['name'];
  }

  // Count how many rows were returned
  echo mysqli_num_rows($result);
?>
```

| Function | Returns |
|---|---|
| `mysqli_fetch_all($result, MYSQLI_ASSOC)` | All rows as array of associative arrays |
| `mysqli_fetch_assoc($result)` | Next single row as associative array |
| `mysqli_fetch_array($result)` | Next row as both indexed and associative |
| `mysqli_num_rows($result)` | Number of rows in the result |

---

### INSERT — Adding Data from a Form

**create.php — The Form**

```html
<form method="post" action="create.php">
  <input type="text" name="first_name" placeholder="First Name">
  <input type="text" name="last_name"  placeholder="Last Name">
  <input type="email" name="email"     placeholder="Email">
  <button type="submit" name="submit">Add Customer</button>
</form>
```

**create.php — Processing the Form**

```php
<?php
  require('connection.php');

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name   = $_POST['first_name'];
    $last_name    = $_POST['last_name'];
    $email        = $_POST['email'];
    $gender       = $_POST['gender'];
    $money        = $_POST['money'];
    $city         = $_POST['city'];
    $country      = $_POST['country'];
    $country_code = $_POST['country_code'];

    $query = "INSERT INTO customers (first_name, last_name, email, gender, money, city, country, country_code)
              VALUES ('$first_name', '$last_name', '$email', '$gender', '$money', '$city', '$country', '$country_code')";

    $result = mysqli_query($connection, $query);

    if ($result) {
      header('location: index.php');
      exit();
    }

    header('location: create.php');
    exit();
  }
?>
```

### After a Successful INSERT — Get the New Row's ID

```php
<?php
  $result = mysqli_query($connection, $query);

  if ($result) {
    $new_id = mysqli_insert_id($connection);
    // $new_id = the AUTO_INCREMENT id of the row just inserted
  }
?>
```

---

### DELETE — Removing a Record

The pattern: a delete link passes the row ID in the URL, the delete page reads it and runs the query.

**In the table (index.php) — The Delete Link**

```html
<a href="deleteCustomer.php?id=<?php echo $customer['id']; ?>" class="btn btn-danger">
  Delete
</a>
```

**deleteCustomer.php — Processing the Delete**

```php
<?php
  require('connection.php');

  $id = $_GET['id'];

  if ($id) {
    $query  = "DELETE FROM customers WHERE id = $id";
    $result = mysqli_query($connection, $query);

    if ($result) {
      header('location: index.php');
      exit();
    }
  }
?>
```

---

### UPDATE — Editing a Record

**edit.php — Load the Existing Data**

```php
<?php
  require('connection.php');

  $id     = $_GET['id'];
  $query  = "SELECT * FROM customers WHERE id = $id";
  $result = mysqli_query($connection, $query);
  $customer = mysqli_fetch_assoc($result);
?>

<form method="post" action="edit.php">
  <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
  <input type="text" name="first_name" value="<?php echo $customer['first_name']; ?>">
  <input type="text" name="last_name"  value="<?php echo $customer['last_name']; ?>">
  <button type="submit" name="submit">Update</button>
</form>
```

**edit.php — Processing the Update**

```php
<?php
  require('connection.php');

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id         = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];

    $query  = "UPDATE customers SET first_name = '$first_name', last_name = '$last_name' WHERE id = $id";
    $result = mysqli_query($connection, $query);

    if ($result) {
      header('location: index.php');
      exit();
    }
  }
?>
```

---

### SQL Injection — The Security Problem with This Code

The code above puts user input directly into SQL strings. This is vulnerable to **SQL injection** — a user can type SQL code into a form field and have it execute on your database.

```php
// If a user types this into the name field:
// '; DROP TABLE customers; --

$query = "INSERT INTO customers (name) VALUES ('$name')";
// Becomes:
// INSERT INTO customers (name) VALUES (''; DROP TABLE customers; --')
// The DROP TABLE executes — your data is gone
```

### The Fix — Prepared Statements

Prepared statements separate the SQL structure from the data. User input is never treated as SQL code.

```php
<?php
  require('connection.php');

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];

    // Prepare the query — ? is a placeholder
    $stmt = mysqli_prepare($connection,
      "INSERT INTO customers (first_name, last_name, email) VALUES (?, ?, ?)"
    );

    // Bind the actual values to the placeholders
    // "sss" = three strings. Use "i" for int, "d" for double, "s" for string
    mysqli_stmt_bind_param($stmt, "sss", $first_name, $last_name, $email);

    // Execute
    mysqli_stmt_execute($stmt);

    header('location: index.php');
    exit();
  }
?>
```

> Always use prepared statements when user input is involved in a query. The `?` placeholders are never interpreted as SQL — they are treated as pure data values regardless of what the user typed.

