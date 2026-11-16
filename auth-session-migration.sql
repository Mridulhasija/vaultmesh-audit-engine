-- Apply once to an existing database before deploying the updated account flow.
-- The browser receives PHP's HttpOnly session cookie; only its SHA-256 hash is stored.
ALTER TABLE `tbl_user`
  MODIFY `last_login` timestamp NOT NULL DEFAULT current_timestamp();

CREATE TABLE IF NOT EXISTS `tbl_user_session` (
  `session_hash` char(64) NOT NULL,
  `user_id` char(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL,
  PRIMARY KEY (`session_hash`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `fk_tbl_user_session_user`
    FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
