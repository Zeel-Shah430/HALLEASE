-- =====================================================
-- HallEase Database Upgrade Script
-- Purpose: Add Razorpay fields, security improvements
-- Author: System Administrator  
-- Date: 2026-02-15
-- =====================================================

USE hallease;

-- Step 1: Upgrade bookings table structure
ALTER TABLE `bookings`
ADD COLUMN `total_days` INT DEFAULT 1 AFTER `booking_end_date`,
ADD COLUMN `price_per_day` DECIMAL(10,2) DEFAULT 0.00 AFTER `total_days`,
ADD COLUMN `razorpay_order_id` VARCHAR(100) DEFAULT NULL AFTER `payment_status`,
ADD COLUMN `razorpay_payment_id` VARCHAR(100) DEFAULT NULL AFTER `razorpay_order_id`,
ADD COLUMN `razorpay_signature` VARCHAR(255) DEFAULT NULL AFTER `razorpay_payment_id`,
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `razorpay_signature`,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Step 2: Update booking_status enum to include payment_failed
ALTER TABLE `bookings` 
MODIFY COLUMN `booking_status` ENUM('pending_payment','confirmed','cancelled','payment_failed','completed') DEFAULT 'pending_payment';

-- Step 3: Update payment_status enum
ALTER TABLE `bookings`
MODIFY COLUMN `payment_status` ENUM('pending','paid','failed','refunded') DEFAULT 'pending';

-- Step 4: Add indexes for performance optimization
ALTER TABLE `bookings`
ADD INDEX `idx_hall_dates` (`hall_id`, `booking_start_date`, `booking_end_date`),
ADD INDEX `idx_status` (`booking_status`),
ADD INDEX `idx_created_at` (`created_at`);

-- Step 5: Add index on halls table
ALTER TABLE `halls`
ADD INDEX `idx_status_owner` (`status`, `owner_id`);

-- Step 6: Update existing bookings to have proper total_days calculation
UPDATE `bookings` 
SET `total_days` = DATEDIFF(`booking_end_date`, `booking_start_date`) + 1
WHERE `total_days` IS NULL OR `total_days` = 1;

-- Step 7: Update existing bookings to fetch price_per_day from halls
UPDATE `bookings` b
INNER JOIN `halls` h ON b.hall_id = h.hall_id
SET b.price_per_day = h.price_per_day
WHERE b.price_per_day = 0;

-- Step 8: Recalculate total_amount for existing bookings
UPDATE `bookings`
SET `total_amount` = `total_days` * `price_per_day`
WHERE `total_amount` = 0 OR `total_amount` IS NULL;

-- Step 9: Create a session_tokens table for CSRF protection
CREATE TABLE IF NOT EXISTS `session_tokens` (
  `token_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `user_type` ENUM('user','owner','admin') NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 10: Add audit log table for tracking changes
CREATE TABLE IF NOT EXISTS `audit_log` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `user_type` ENUM('user','owner','admin','guest') NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `table_name` VARCHAR(50) DEFAULT NULL,
  `record_id` INT(11) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 11: Verify data integrity
SELECT 
    'Bookings Updated' AS status,
    COUNT(*) AS count 
FROM bookings 
WHERE total_days > 0;

SELECT 
    'Indexes Created' AS status,
    COUNT(*) AS count
FROM information_schema.statistics 
WHERE table_schema = 'hallease' 
AND table_name = 'bookings';

-- End of upgrade script
