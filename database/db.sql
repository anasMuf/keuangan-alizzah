-- ===============================================
-- School Finance System — ERD SQL (MySQL 8.x)
-- Compatible with dbdiagram.io (Import > MySQL)
-- Generated: 2025-08-16
-- ===============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if exist (order matters due to FKs)
DROP TABLE IF EXISTS savings_transactions;
DROP TABLE IF EXISTS savings_accounts;
DROP TABLE IF EXISTS payment_lines;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS invoice_lines;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS fee_item_assignments;
DROP TABLE IF EXISTS fee_item_rules;
DROP TABLE IF EXISTS fee_items;
DROP TABLE IF EXISTS student_daycare;
DROP TABLE IF EXISTS student_extracurr;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS academic_calendar_days;
DROP TABLE IF EXISTS daycare_plans;
DROP TABLE IF EXISTS extracurriculars;
DROP TABLE IF EXISTS classes;
DROP TABLE IF EXISTS academic_years;

SET FOREIGN_KEY_CHECKS = 1;

-- 1) Academic references
CREATE TABLE academic_years (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(20) NOT NULL,        -- e.g., 2025/2026
  start_date    DATE NOT NULL,
  end_date      DATE NOT NULL,
  is_active     TINYINT(1) NOT NULL DEFAULT 0,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE classes (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(50) NOT NULL,        -- Mutiara / Intan / Berlian
  level         VARCHAR(10) NOT NULL,        -- KB / TK-A / TK-B
  code          VARCHAR(20) NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE extracurriculars (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(60) NOT NULL,        -- Aslin, Calisan, Robotik, ...
  is_mandatory  TINYINT(1) NOT NULL DEFAULT 0, -- true for Aslin TK-B
  monthly_fee   DECIMAL(15,2) NULL,
  notes         TEXT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE daycare_plans (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name                  VARCHAR(60) NOT NULL,      -- Rutin KB, Rutin TK, Harian Lepas, Paket Konsumsi
  periodicity           ENUM('monthly','daily','one_time') NOT NULL,
  fee_spd               DECIMAL(15,2) NULL,
  fee_package           DECIMAL(15,2) NULL,
  consumption_daily_fee DECIMAL(15,2) NULL,
  created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE academic_calendar_days (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  date             DATE NOT NULL,
  is_effective_day TINYINT(1) NOT NULL DEFAULT 1,
  notes            VARCHAR(120) NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_acd_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Students & participation
CREATE TABLE students (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nis        VARCHAR(30) NULL,
  full_name  VARCHAR(120) NOT NULL,
  nickname   VARCHAR(60) NULL,
  gender     ENUM('L','P') NOT NULL,
  dob        DATE NULL,
  notes      TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE enrollments (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id       BIGINT UNSIGNED NOT NULL,
  class_id         BIGINT UNSIGNED NOT NULL,
  academic_year_id BIGINT UNSIGNED NOT NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_enroll_student FOREIGN KEY (student_id) REFERENCES students(id),
  CONSTRAINT fk_enroll_class FOREIGN KEY (class_id) REFERENCES classes(id),
  CONSTRAINT fk_enroll_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
  UNIQUE KEY uniq_enroll (student_id, class_id, academic_year_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE student_extracurr (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id          BIGINT UNSIGNED NOT NULL,
  extracurricular_id  BIGINT UNSIGNED NOT NULL,
  academic_year_id    BIGINT UNSIGNED NOT NULL,
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_se_student FOREIGN KEY (student_id) REFERENCES students(id),
  CONSTRAINT fk_se_extra FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id),
  CONSTRAINT fk_se_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
  UNIQUE KEY uniq_student_extra (student_id, extracurricular_id, academic_year_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE student_daycare (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id       BIGINT UNSIGNED NOT NULL,
  daycare_plan_id  BIGINT UNSIGNED NOT NULL,
  academic_year_id BIGINT UNSIGNED NOT NULL,
  start_date       DATE NULL,
  end_date         DATE NULL,
  is_package       TINYINT(1) NOT NULL DEFAULT 0, -- paket SPD + konsumsi
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_sd_student FOREIGN KEY (student_id) REFERENCES students(id),
  CONSTRAINT fk_sd_plan FOREIGN KEY (daycare_plan_id) REFERENCES daycare_plans(id),
  CONSTRAINT fk_sd_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Fee master & rules
CREATE TABLE fee_items (
  id                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name                   VARCHAR(80) NOT NULL, -- SPP, Infaq Harian, Biaya Awal, dll
  category               ENUM('once','daily','monthly','yearly') NOT NULL,
  is_mandatory           TINYINT(1) NOT NULL DEFAULT 0,
  is_selective_mandatory TINYINT(1) NOT NULL DEFAULT 0, -- wajib jika dipilih
  notes                  TEXT NULL,
  created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE fee_item_rules (
  id                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fee_item_id            BIGINT UNSIGNED NOT NULL,
  level                  VARCHAR(10) NULL,          -- KB/TK-A/TK-B (NULL = all)
  class_id               BIGINT UNSIGNED NULL,
  gender                 ENUM('L','P') NULL,
  amount                 DECIMAL(15,2) NULL,        -- bisa NULL (tabungan umum)
  unit                   ENUM('IDR','percent','per_day','per_month') NOT NULL DEFAULT 'IDR',
  is_mandatory_override  TINYINT(1) NULL,
  effective_from         DATE NULL,
  effective_to           DATE NULL,
  notes                  TEXT NULL,
  created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_rule_target (fee_item_id, level, class_id, gender),
  CONSTRAINT fk_rule_item FOREIGN KEY (fee_item_id) REFERENCES fee_items(id),
  CONSTRAINT fk_rule_class FOREIGN KEY (class_id) REFERENCES classes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Assignment → Invoice → Payment
CREATE TABLE fee_item_assignments (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id       BIGINT UNSIGNED NOT NULL,
  academic_year_id BIGINT UNSIGNED NOT NULL,
  fee_item_id      BIGINT UNSIGNED NOT NULL,
  rule_id          BIGINT UNSIGNED NULL,
  period           VARCHAR(20) NULL,            -- YYYY-MM / YYYY / date range token
  qty              INT NOT NULL DEFAULT 1,
  amount           DECIMAL(15,2) NOT NULL DEFAULT 0,
  subtotal         DECIMAL(15,2) NOT NULL DEFAULT 0,
  status           ENUM('pending','invoiced','paid','partial') NOT NULL DEFAULT 'pending',
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at       DATETIME NULL,
  KEY idx_assign_student_year_status (student_id, academic_year_id, status),
  CONSTRAINT fk_assign_student FOREIGN KEY (student_id) REFERENCES students(id),
  CONSTRAINT fk_assign_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
  CONSTRAINT fk_assign_item FOREIGN KEY (fee_item_id) REFERENCES fee_items(id),
  CONSTRAINT fk_assign_rule FOREIGN KEY (rule_id) REFERENCES fee_item_rules(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoices (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id       BIGINT UNSIGNED NOT NULL,
  academic_year_id BIGINT UNSIGNED NOT NULL,
  invoice_no       VARCHAR(50) NOT NULL,
  invoice_date     DATE NOT NULL,
  due_date         DATE NULL,
  status           ENUM('draft','open','partial','paid','void') NOT NULL DEFAULT 'open',
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at       DATETIME NULL,
  UNIQUE KEY uq_invoice_no (invoice_no),
  CONSTRAINT fk_invoice_student FOREIGN KEY (student_id) REFERENCES students(id),
  CONSTRAINT fk_invoice_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoice_lines (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  invoice_id     BIGINT UNSIGNED NOT NULL,
  assignment_id  BIGINT UNSIGNED NULL,
  fee_item_id    BIGINT UNSIGNED NOT NULL,
  description    VARCHAR(180) NULL,
  qty            INT NOT NULL DEFAULT 1,
  amount         DECIMAL(15,2) NOT NULL DEFAULT 0,
  subtotal       DECIMAL(15,2) NOT NULL DEFAULT 0,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_line_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
  CONSTRAINT fk_line_assignment FOREIGN KEY (assignment_id) REFERENCES fee_item_assignments(id),
  CONSTRAINT fk_line_item FOREIGN KEY (fee_item_id) REFERENCES fee_items(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payments (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id   BIGINT UNSIGNED NOT NULL,
  receipt_no   VARCHAR(50) NOT NULL,
  paid_at      DATETIME NOT NULL,
  method       VARCHAR(20) NULL,                -- cash/transfer
  total_paid   DECIMAL(15,2) NOT NULL DEFAULT 0,
  operator_id  BIGINT UNSIGNED NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at   DATETIME NULL,
  UNIQUE KEY uq_receipt_no (receipt_no),
  CONSTRAINT fk_payment_student FOREIGN KEY (student_id) REFERENCES students(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payment_lines (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  payment_id      BIGINT UNSIGNED NOT NULL,
  invoice_line_id BIGINT UNSIGNED NOT NULL,
  paid_amount     DECIMAL(15,2) NOT NULL DEFAULT 0,
  notes           VARCHAR(180) NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pl_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
  CONSTRAINT fk_pl_invoice_line FOREIGN KEY (invoice_line_id) REFERENCES invoice_lines(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) Savings (general & graduation TK-B)
CREATE TABLE savings_accounts (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id       BIGINT UNSIGNED NOT NULL,
  academic_year_id BIGINT UNSIGNED NOT NULL,
  type             ENUM('general','graduation_tkb') NOT NULL,
  opening_balance  DECIMAL(15,2) NOT NULL DEFAULT 0,
  notes            VARCHAR(180) NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_sav_acc (student_id, academic_year_id, type),
  CONSTRAINT fk_sav_student FOREIGN KEY (student_id) REFERENCES students(id),
  CONSTRAINT fk_sav_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE savings_transactions (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  account_id   BIGINT UNSIGNED NOT NULL,
  date         DATE NOT NULL,
  description  VARCHAR(180) NULL,
  debit        DECIMAL(15,2) NOT NULL DEFAULT 0,  -- deposit
  credit       DECIMAL(15,2) NOT NULL DEFAULT 0,  -- withdrawal
  ref_type     VARCHAR(20) NULL,       -- invoice/manual/withdrawal
  ref_id       BIGINT UNSIGNED NULL,
  created_by   BIGINT UNSIGNED NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at   DATETIME NULL,
  KEY idx_sav_tx_account_date (account_id, date),
  CONSTRAINT fk_sav_tx_account FOREIGN KEY (account_id) REFERENCES savings_accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

