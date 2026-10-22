CREATE DATABASE IF NOT EXISTS creditready CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE creditready;

CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('business_owner','lender','admin') NOT NULL DEFAULT 'business_owner',
 preferred_language ENUM('en','fr') NOT NULL DEFAULT 'en',
 business_name VARCHAR(180) NULL,
 sector VARCHAR(40) NULL,
 phone VARCHAR(40) NULL,
 years_operating SMALLINT UNSIGNED NOT NULL DEFAULT 0,
 monthly_revenue DECIMAL(15,2) NOT NULL DEFAULT 0,
 monthly_expenses DECIMAL(15,2) NOT NULL DEFAULT 0,
 requested_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
 registration_status ENUM('unknown','registered','informal','in_process') NOT NULL DEFAULT 'unknown',
 has_bank_account TINYINT(1) NULL DEFAULT NULL,
 collateral_available TINYINT(1) NULL DEFAULT NULL,
 business_description TEXT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS business_activity (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 activity_date DATE NOT NULL,
 activity_type ENUM('sale','expense','purchase','invoice','other') NOT NULL,
 description VARCHAR(255) NOT NULL,
 amount DECIMAL(15,2) NOT NULL DEFAULT 0,
 evidence_note TEXT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_activity_user_date (user_id, activity_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS documents (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 original_name VARCHAR(255) NOT NULL,
 stored_name VARCHAR(255) NOT NULL,
 mime_type VARCHAR(120) NOT NULL,
 file_size INT UNSIGNED NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_documents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lender_criteria (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 lender_user_id INT UNSIGNED NOT NULL,
 institution_name VARCHAR(180) NOT NULL,
 min_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
 max_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
 sector VARCHAR(40) NOT NULL DEFAULT 'all',
 min_years_operating SMALLINT UNSIGNED NOT NULL DEFAULT 0,
 requires_registration TINYINT(1) NOT NULL DEFAULT 0,
 requires_bank_account TINYINT(1) NOT NULL DEFAULT 0,
 other_requirements TEXT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_criteria_lender FOREIGN KEY (lender_user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_criteria_active (is_active, sector, min_amount, max_amount)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS consents (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 business_user_id INT UNSIGNED NOT NULL,
 lender_user_id INT UNSIGNED NOT NULL,
 criteria_id INT UNSIGNED NOT NULL,
 consent_purpose VARCHAR(255) NOT NULL,
 consented_at DATETIME NOT NULL,
 revoked_at DATETIME NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_consent_business FOREIGN KEY (business_user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_consent_lender FOREIGN KEY (lender_user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_consent_criteria FOREIGN KEY (criteria_id) REFERENCES lender_criteria(id) ON DELETE CASCADE,
 INDEX idx_consent_access (lender_user_id, business_user_id, revoked_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS consent_events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 business_user_id INT UNSIGNED NOT NULL,
 lender_user_id INT UNSIGNED NOT NULL,
 criteria_id INT UNSIGNED NOT NULL,
 event_type ENUM('granted','revoked') NOT NULL,
 details VARCHAR(255) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_consent_event_business FOREIGN KEY (business_user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_consent_event_lender FOREIGN KEY (lender_user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_consent_event_criteria FOREIGN KEY (criteria_id) REFERENCES lender_criteria(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lender_decisions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 business_user_id INT UNSIGNED NOT NULL,
 lender_user_id INT UNSIGNED NOT NULL,
 decision ENUM('under_review','more_information','approved','declined') NOT NULL,
 reason TEXT NOT NULL,
 decided_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_decision_business FOREIGN KEY (business_user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_decision_lender FOREIGN KEY (lender_user_id) REFERENCES users(id) ON DELETE CASCADE,
 UNIQUE KEY uq_decision_business_lender (business_user_id,lender_user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS readiness_history (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 readiness_score TINYINT UNSIGNED NOT NULL,
 recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_readiness_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_readiness_history (user_id, recorded_at)
) ENGINE=InnoDB;
