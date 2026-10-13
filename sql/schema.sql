-- PharmaDEM work tracker: tables + demo data
-- Import with:  mysql -u root -p < sql/schema.sql

CREATE DATABASE IF NOT EXISTS pharmadem
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pharmadem;

-- safe to re-run, this wipes the existing tables
DROP TABLE IF EXISTS daily_updates;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100)        NOT NULL,
  email         VARCHAR(150)        NOT NULL UNIQUE,
  password_hash VARCHAR(255)        NOT NULL,
  role          ENUM('manager','employee') NOT NULL DEFAULT 'employee',
  created_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) NOT NULL,
  description TEXT,
  assigned_to INT NOT NULL,
  assigned_by INT NOT NULL,
  priority    ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  status      ENUM('todo','in_progress','pending','completed') NOT NULL DEFAULT 'todo',
  due_date    DATE,
  completed_at DATETIME DEFAULT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_task_employee FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_task_manager  FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_assigned_to (assigned_to),
  INDEX idx_status (status),
  INDEX idx_due_date (due_date)
);

-- one update per employee per day
CREATE TABLE daily_updates (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT  NOT NULL,
  update_date  DATE NOT NULL,
  work_summary TEXT NOT NULL,
  plan         TEXT,
  blockers     TEXT,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_update_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uq_user_day (user_id, update_date)
);

-- demo data, every user's password is password123
INSERT INTO users (name, email, password_hash, role) VALUES
  ('Ramya Ramala',  'manager@pharmadem.in', '$2y$12$wCOU3tpvKNLV7Zk4TRE1peVtUpF82ErL8hNEUIPYiZmD06mHMB1CK', 'manager'),
  ('Arjun Mehta',   'arjun@pharmadem.in',   '$2y$12$wCOU3tpvKNLV7Zk4TRE1peVtUpF82ErL8hNEUIPYiZmD06mHMB1CK', 'employee'),
  ('Sneha Reddy',   'sneha@pharmadem.in',   '$2y$12$wCOU3tpvKNLV7Zk4TRE1peVtUpF82ErL8hNEUIPYiZmD06mHMB1CK', 'employee');

INSERT INTO tasks (title, description, assigned_to, assigned_by, priority, status, due_date, completed_at) VALUES
  ('Prepare batch QC report',  'Compile QC results for batch #A-204',        2, 1, 'high',   'completed',  DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY)),
  ('Update stability data',    'Enter 6-month stability readings',            2, 1, 'medium', 'in_progress',DATE_ADD(CURDATE(), INTERVAL 2 DAY), NULL),
  ('Vendor follow-up',         'Chase pending CoA from supplier',             2, 1, 'low',    'pending',    DATE_SUB(CURDATE(), INTERVAL 1 DAY), NULL),
  ('Draft SOP revision',       'Revise SOP-17 cleaning procedure',           3, 1, 'high',   'todo',       DATE_ADD(CURDATE(), INTERVAL 5 DAY), NULL),
  ('Calibrate HPLC',           'Monthly calibration + log',                  3, 1, 'medium', 'completed',  DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY));

INSERT INTO daily_updates (user_id, update_date, work_summary, plan, blockers) VALUES
  (2, CURDATE(), 'Finished QC report for batch A-204, started stability data entry.', 'Finish stability data, call vendor about CoA.', 'Waiting on CoA from vendor.'),
  (3, CURDATE(), 'Completed HPLC calibration and updated the log.', 'Start SOP-17 revision.', NULL);
