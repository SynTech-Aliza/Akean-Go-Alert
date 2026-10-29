-- Run once in the akean_go-alert database before using the registration API.
-- Resolve existing duplicate user_EmailAddress rows before adding this index.
ALTER TABLE users
    MODIFY user_Password VARCHAR(255) NOT NULL,
    MODIFY user_OTP VARCHAR(255) NULL,
    ADD UNIQUE KEY uq_users_email_address (user_EmailAddress);

CREATE TABLE IF NOT EXISTS user_signup_otp_meta (
    user_EmailAddress VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (user_EmailAddress)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
