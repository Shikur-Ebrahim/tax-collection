-- ============================================================
-- Bure Town Online Tax Collection Management System
-- PostgreSQL Database Schema (Supabase)
-- ============================================================

-- ============================================================
-- Roles Table
-- ============================================================
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (role_name, description) VALUES
('admin', 'Full system administrator with all privileges'),
('staff', 'Staff worker who registers and manages taxpayers'),
('officer', 'Tax officer who calculates and generates reports'),
('taxpayer', 'Taxpayer who submits and pays taxes'),
('bank', 'Bank payment provider who verifies payments')
ON CONFLICT (role_name) DO NOTHING;

-- ============================================================
-- Users Table
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL REFERENCES roles(id) ON DELETE RESTRICT,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'suspended')),
    profile_image VARCHAR(255) DEFAULT NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin account (password: Admin@1234)
INSERT INTO users (full_name, username, email, phone, password_hash, role_id, status) VALUES
('System Administrator', 'admin', 'admin@buretcms.gov.et', '+251910000001', '$2y$12$XKW3kSXHn/A.Bi0RUkMoEu5H8GfGSaBs8mK9F/jniAeWJmEJlFmqW', 1, 'active'),
('Yeshiemebet Alemu', 'staffworker', 'staff@buretcms.gov.et', '+251910000002', '$2y$12$XKW3kSXHn/A.Bi0RUkMoEu5H8GfGSaBs8mK9F/jniAeWJmEJlFmqW', 2, 'active'),
('Almaz Tadesse', 'taxofficer', 'officer@buretcms.gov.et', '+251910000003', '$2y$12$XKW3kSXHn/A.Bi0RUkMoEu5H8GfGSaBs8mK9F/jniAeWJmEJlFmqW', 3, 'active'),
('Commercial Bank', 'bankprovider', 'bank@cbe.com.et', '+251910000004', '$2y$12$XKW3kSXHn/A.Bi0RUkMoEu5H8GfGSaBs8mK9F/jniAeWJmEJlFmqW', 5, 'active')
ON CONFLICT (username) DO NOTHING;

-- ============================================================
-- Taxpayers Table
-- ============================================================
CREATE TABLE IF NOT EXISTS taxpayers (
    id SERIAL PRIMARY KEY,
    user_id INT UNIQUE REFERENCES users(id) ON DELETE SET NULL,
    tin VARCHAR(20) NOT NULL UNIQUE, -- Taxpayer Identification Number
    full_name VARCHAR(150) NOT NULL,
    gender VARCHAR(10) NOT NULL CHECK (gender IN ('Male', 'Female', 'Other')),
    date_of_birth DATE,
    nationality VARCHAR(80) DEFAULT 'Ethiopian',
    kebele VARCHAR(80),
    woreda VARCHAR(80),
    region VARCHAR(80) DEFAULT 'Amhara',
    phone VARCHAR(20),
    email VARCHAR(150),
    taxpayer_type VARCHAR(20) NOT NULL CHECK (taxpayer_type IN ('Business', 'Employed', 'Investor', 'Importer', 'Exporter')),
    business_name VARCHAR(150),
    business_type VARCHAR(150),
    business_address TEXT,
    trade_license_no VARCHAR(80),
    trade_license_expiry DATE,
    annual_income DECIMAL(15,2) DEFAULT 0.00,
    registration_status VARCHAR(20) DEFAULT 'pending' CHECK (registration_status IN ('pending', 'approved', 'rejected', 'suspended')),
    registered_by INT REFERENCES users(id) ON DELETE SET NULL,
    approved_by INT REFERENCES users(id) ON DELETE SET NULL,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Tax Records Table
-- ============================================================
CREATE TABLE IF NOT EXISTS tax_records (
    id SERIAL PRIMARY KEY,
    taxpayer_id INT NOT NULL REFERENCES taxpayers(id) ON DELETE CASCADE,
    tin VARCHAR(20) NOT NULL,
    tax_year INT NOT NULL,
    tax_period VARCHAR(20) DEFAULT 'Annual' CHECK (tax_period IN ('Annual', 'Q1', 'Q2', 'Q3', 'Q4', 'Monthly')),
    income_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    taxable_income DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00, -- Percentage
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    penalty_amount DECIMAL(15,2) DEFAULT 0.00,
    total_due DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'assessed' CHECK (status IN ('assessed', 'pending_payment', 'paid', 'overdue', 'disputed')),
    assessment_date DATE,
    due_date DATE,
    notes TEXT,
    calculated_by INT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Payments Table
-- ============================================================
CREATE TABLE IF NOT EXISTS payments (
    id SERIAL PRIMARY KEY,
    tax_record_id INT NOT NULL REFERENCES tax_records(id) ON DELETE CASCADE,
    taxpayer_id INT NOT NULL REFERENCES taxpayers(id) ON DELETE CASCADE,
    payment_reference VARCHAR(50) NOT NULL UNIQUE,
    payment_method VARCHAR(20) NOT NULL CHECK (payment_method IN ('CBE', 'Telebirr', 'Cash', 'BankTransfer')),
    amount_paid DECIMAL(15,2) NOT NULL,
    payment_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    bank_transaction_ref VARCHAR(100),
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'verified', 'failed', 'refunded')),
    verified_by INT REFERENCES users(id) ON DELETE SET NULL,
    verified_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Receipts Table
-- ============================================================
CREATE TABLE IF NOT EXISTS receipts (
    id SERIAL PRIMARY KEY,
    payment_id INT NOT NULL UNIQUE REFERENCES payments(id) ON DELETE CASCADE,
    receipt_number VARCHAR(30) NOT NULL UNIQUE,
    taxpayer_id INT NOT NULL REFERENCES taxpayers(id) ON DELETE CASCADE,
    tax_record_id INT NOT NULL REFERENCES tax_records(id) ON DELETE CASCADE,
    amount_paid DECIMAL(15,2) NOT NULL,
    issued_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    issued_by INT REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- News / Announcements Table
-- ============================================================
CREATE TABLE IF NOT EXISTS news (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(20) DEFAULT 'general' CHECK (category IN ('announcement', 'deadline', 'notice', 'general')),
    is_published BOOLEAN DEFAULT true,
    published_by INT REFERENCES users(id) ON DELETE SET NULL,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO news (title, content, category, is_published, published_by) VALUES
('Welcome to Bure Town Online Tax System', 'The Bure Town Tax Collection Department has launched this online system to make tax payment easier for all taxpayers. You can now register, track payments, and download receipts online.', 'announcement', true, 1),
('Tax Collection Deadline - Q4 2026', 'All taxpayers are reminded that the Q4 2026 tax deadline is December 31, 2026. Please ensure you have submitted all required documents and paid your taxes on time to avoid penalties.', 'deadline', true, 1),
('Required Documents for Tax Registration', 'All new taxpayers must bring: (1) Trade License Certificate, (2) National ID, (3) TIN Certificate, (4) Business House Rent Contract or Property Map, (5) Accountant Documents.', 'notice', true, 1);

-- ============================================================
-- Reports Table
-- ============================================================
CREATE TABLE IF NOT EXISTS reports (
    id SERIAL PRIMARY KEY,
    report_title VARCHAR(255) NOT NULL,
    report_type VARCHAR(20) NOT NULL CHECK (report_type IN ('monthly', 'quarterly', 'annual', 'custom')),
    report_period_start DATE,
    report_period_end DATE,
    total_taxpayers INT DEFAULT 0,
    total_assessed DECIMAL(15,2) DEFAULT 0.00,
    total_collected DECIMAL(15,2) DEFAULT 0.00,
    total_pending DECIMAL(15,2) DEFAULT 0.00,
    report_data JSONB,
    generated_by INT REFERENCES users(id) ON DELETE SET NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Audit Logs Table
-- ============================================================
CREATE TABLE IF NOT EXISTS audit_logs (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(255) NOT NULL,
    module VARCHAR(100),
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    old_value JSONB,
    new_value JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- System Settings Table
-- ============================================================
CREATE TABLE IF NOT EXISTS settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    updated_by INT REFERENCES users(id) ON DELETE SET NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO settings (setting_key, setting_value, description) VALUES
('org_name', 'Bure Town Tax Collection Department', 'Organization name'),
('org_address', 'Bure Town, Amhara Region, Ethiopia', 'Organization address'),
('org_phone', '+251910000000', 'Contact phone number'),
('org_email', 'info@buretcms.gov.et', 'Contact email'),
('tax_year', '2026', 'Current tax year'),
('penalty_rate', '5', 'Late payment penalty rate (%)'),
('tin_prefix', 'BUR', 'TIN number prefix'),
('receipt_prefix', 'RCP', 'Receipt number prefix')
ON CONFLICT (setting_key) DO NOTHING;

-- ============================================================
-- Tax Brackets (Ethiopian Income Tax)
-- ============================================================
CREATE TABLE IF NOT EXISTS tax_brackets (
    id SERIAL PRIMARY KEY,
    min_income DECIMAL(15,2) NOT NULL,
    max_income DECIMAL(15,2),
    tax_rate DECIMAL(5,2) NOT NULL,
    fixed_amount DECIMAL(15,2) DEFAULT 0.00,
    description VARCHAR(255)
);

INSERT INTO tax_brackets (min_income, max_income, tax_rate, fixed_amount, description) VALUES
(0, 600, 0.00, 0.00, 'Tax-free band'),
(601, 1650, 10.00, 0.00, '10% bracket'),
(1651, 3200, 15.00, 105.00, '15% bracket'),
(3201, 5250, 20.00, 337.50, '20% bracket'),
(5251, 7800, 25.00, 747.50, '25% bracket'),
(7801, 10900, 30.00, 1382.50, '30% bracket'),
(10901, NULL, 35.00, 2312.50, '35% bracket - top rate');
