-- Dummy Data

-- Insert sample users
INSERT INTO `users` (`email`, `username`, `phone_number`, `password_hash`) VALUES
('admin@example.com', 'janjansen', '+31612345678', '$2y$12$Lx8RaJ1e5yxTsOyl0Qj9nerUOQVtGWq8ttXhHSHYDHLu0jZQU3aFu'),
('maria.smit@example.com', 'mariasmit', '+31612345679', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('pieter.willems@example.com', 'pieterwillems', '+31612345680', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('anna.broek@example.com', 'annabroek', '+31612345681', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('charles.davids@example.com', 'charlesdavids', '+31612345682', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample images
INSERT INTO `images` (`id`, `path`, `name`, `extension`, `posted`) VALUES
('550e8400-e29b-41d4-a716-446655440000', '/uploads/2025/09/sunset.jpg', 'Mooie Zonsondergang', 'jpg', TRUE),
('550e8400-e29b-41d4-a716-446655440001', '/uploads/2025/09/mountain.png', 'Bergzicht', 'png', TRUE),
('550e8400-e29b-41d4-a716-446655440002', '/uploads/2025/09/ocean.webp', 'Oceaan Golven', 'webp', TRUE),
('550e8400-e29b-41d4-a716-446655440003', '/uploads/2025/09/forest.jpg', 'Bos Pad', 'jpg', FALSE),
('550e8400-e29b-41d4-a716-446655440004', '/uploads/2025/09/city.gif', 'Stads Silhouet', 'gif', TRUE);

-- Insert sample roles
INSERT INTO `roles` (`name`, `type`, `logo`) VALUES
('Beheerder', '', '/icons/admin.svg'),
('Moderator', '', '/icons/moderator.svg'),
('Premium Gebruiker', '', '/icons/premium.svg'),
('Gewone Gebruiker', '', '/icons/user.svg');

-- Insert sample groups
INSERT INTO `groups` (`user_id`, `name`, `discription`, `theme`) VALUES
(1, 'Fotografie Enthousiasten', 'Een groep voor mensen die van fotografie houden', 'nature'),
(2, 'Tech Discussies', 'Bespreek de nieuwste technologie', 'tech'),
(3, 'Reis Avonturen', 'Deel je reiservaringen', 'travel'),
(1, 'Lokale Gemeenschap', 'Verbind met mensen in je omgeving', 'community'),
(4, 'Boeken Club', 'Maandelijkse boekendiscussies en recensies', 'education');

-- Insert sample posts
INSERT INTO `posts` (`user_id`, `group_id`, `image_id`, `text_content`) VALUES
(1, 1, '550e8400-e29b-41d4-a716-446655440000', 'Net deze geweldige zonsondergang vastgelegd! De kleuren waren absoluut adembenemend. #fotografie #natuur'),
(2, 1, '550e8400-e29b-41d4-a716-446655440001', 'Wandelen in de bergen dit weekend. Het uitzicht vanaf de top was elke stap waard! #wandelen #avontuur'),
(3, 4, NULL, 'Heeft iemand aanbevelingen voor goede restaurants in het centrum? Op zoek naar iets nieuws om te proberen!'),
(4, 1, '550e8400-e29b-41d4-a716-446655440002', 'Stranddag! De golven waren perfect voor surfen vandaag. #strand #surfen #oceaan'),
(5, 5, NULL, 'Net een geweldig boek uitgelezen. Zou het graag bespreken met mede-boekenliefhebbers!'),
(1, 1, '550e8400-e29b-41d4-a716-446655440004', 'De stad ziet er zo anders uit s nachts. Houd van het stedelijke landschap! #stadleven #nachtfotografie'),
(2, 2, NULL, 'Werken aan een nieuw techproject. Spannend om de resultaten binnenkort te delen! #coderen #innovatie'),
(3, 3, '550e8400-e29b-41d4-a716-446655440003', 'Deze vredige bosweg gevonden tijdens mijn ochtendwandeling. Natuur is de beste therapie.');

-- Insert sample user_group relationships
INSERT INTO `user_group` (`user_id`, `group_id`) VALUES
(1, 1), -- Jan in Fotografie Enthousiasten
(1, 4), -- Jan in Lokale Gemeenschap
(2, 1), -- Maria in Fotografie Enthousiasten
(2, 2), -- Maria in Tech Discussies
(3, 3), -- Pieter in Reis Avonturen
(3, 4), -- Pieter in Lokale Gemeenschap
(4, 1), -- Anna in Fotografie Enthousiasten
(4, 5), -- Anna in Boeken Club
(5, 2), -- Charles in Tech Discussies
(5, 5); -- Charles in Boeken Club

-- Insert sample role_user relationships
INSERT INTO `role_user` (`role_id`, `user_id`) VALUES
(1, 1), -- Jan is Beheerder
(2, 2), -- Maria is Moderator
(3, 3), -- Pieter is Premium Gebruiker
(4, 4), -- Anna is Gewone Gebruiker
(4, 5); -- Charles is Gewone Gebruiker

-- Insert sample friendships
INSERT INTO `friendships` (`user1_id`, `user2_id`, `status`) VALUES
(1, 2, 'ACCEPTED'),
(1, 3, 'PENDING'),
(2, 4, 'REJECTED'),
(3, 4, 'ACCEPTED'),
(4, 5, 'PENDING');

-- Insert sample messages
INSERT INTO `messages` (`sender_id`, `receiver_id`, `message_text`) VALUES
(1, 2, 'Hoi Maria, hoe gaat het?'),
(2, 1, 'Hallo Jan, goed dank je!'),
(3, 4, 'Anna, leuke foto!'),
(4, 3, 'Dank je Pieter!');