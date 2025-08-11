CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL DEFAULT 'Cafe & Netic',
  `contact_email` varchar(255) NOT NULL DEFAULT 'contact@cafenetic.com',
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  `primary_color` varchar(7) NOT NULL DEFAULT '#92400e',
  `secondary_color` varchar(7) NOT NULL DEFAULT '#f59e0b',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `site_settings` (`id`, `site_name`, `contact_email`, `timezone`, `dark_mode`, `primary_color`, `secondary_color`) 
VALUES (1, 'Cafe & Netic', 'contact@cafenetic.com', 'UTC', 0, '#92400e', '#f59e0b');