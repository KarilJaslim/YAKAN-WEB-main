-- Fix MySQL max_allowed_packet error
-- Run this in phpMyAdmin SQL tab or MySQL command line

-- Set for current session (temporary)
SET GLOBAL max_allowed_packet=67108864; -- 64MB

-- Verify the change
SHOW VARIABLES LIKE 'max_allowed_packet';
