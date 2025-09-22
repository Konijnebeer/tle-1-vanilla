-- Dummy Data

-- Insert sample users
INSERT INTO `users` (`email`, `username`, `phone_number`, `password_hash`) VALUES
('john.doe@example.com', 'johndoe', '+1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('jane.smith@example.com', 'janesmith', '+1234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('bob.wilson@example.com', 'bobwilson', '+1234567892', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('alice.brown@example.com', 'alicebrown', '+1234567893', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('charlie.davis@example.com', 'charliedavis', '+1234567894', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample images
INSERT INTO `images` (`id`, `path`, `name`, `extension`, `posted`) VALUES
('550e8400-e29b-41d4-a716-446655440000', '/uploads/2025/09/sunset.jpg', 'Beautiful Sunset', 'jpg', TRUE),
('550e8400-e29b-41d4-a716-446655440001', '/uploads/2025/09/mountain.png', 'Mountain View', 'png', TRUE),
('550e8400-e29b-41d4-a716-446655440002', '/uploads/2025/09/ocean.webp', 'Ocean Waves', 'webp', TRUE),
('550e8400-e29b-41d4-a716-446655440003', '/uploads/2025/09/forest.jpg', 'Forest Path', 'jpg', FALSE),
('550e8400-e29b-41d4-a716-446655440004', '/uploads/2025/09/city.gif', 'City Skyline', 'gif', TRUE);

-- Insert sample roles
INSERT INTO `roles` (`name`, `type`, `logo`) VALUES
('Administrator', '', '/icons/admin.svg'),
('Moderator', '', '/icons/moderator.svg'),
('Premium User', '', '/icons/premium.svg'),
('Regular User', '', '/icons/user.svg');

-- Insert sample groups
INSERT INTO `groups` (`user_id`, `name`, `discription`, `theme`) VALUES
(1, 'Photography Enthusiasts', 'A group for people who love photography', 'nature'),
(2, 'Tech Discussions', 'Discuss the latest in technology', 'tech'),
(3, 'Travel Adventures', 'Share your travel experiences', 'travel'),
(1, 'Local Community', 'Connect with people in your area', 'community'),
(4, 'Book Club', 'Monthly book discussions and reviews', 'education');

-- Insert sample posts
INSERT INTO `posts` (`user_id`, `group_id`, `image_id`, `text_content`) VALUES
(1, 1, '550e8400-e29b-41d4-a716-446655440000', 'Just captured this amazing sunset! The colors were absolutely breathtaking. #photography #nature'),
(2, 1, '550e8400-e29b-41d4-a716-446655440001', 'Hiking in the mountains this weekend. The view from the top was worth every step! #hiking #adventure'),
(3, 4, NULL, 'Does anyone have recommendations for good restaurants in downtown? Looking for something new to try!'),
(4, 1, '550e8400-e29b-41d4-a716-446655440002', 'Beach day! The waves were perfect for surfing today. #beach #surfing #ocean'),
(5, 5, NULL, 'Just finished reading an amazing book. Would love to discuss it with fellow book lovers!'),
(1, 1, '550e8400-e29b-41d4-a716-446655440004', 'The city looks so different at night. Love the urban landscape! #citylife #nightphotography'),
(2, 2, NULL, 'Working on a new tech project. Excited to share the results soon! #coding #innovation'),
(3, 3, '550e8400-e29b-41d4-a716-446655440003', 'Found this peaceful forest trail during my morning walk. Nature is the best therapy.');

-- Insert sample user_group relationships
INSERT INTO `user_group` (`user_id`, `group_id`) VALUES
(1, 1), -- John in Photography Enthusiasts
(1, 4), -- John in Local Community
(2, 1), -- Jane in Photography Enthusiasts
(2, 2), -- Jane in Tech Discussions
(3, 3), -- Bob in Travel Adventures
(3, 4), -- Bob in Local Community
(4, 1), -- Alice in Photography Enthusiasts
(4, 5), -- Alice in Book Club
(5, 2), -- Charlie in Tech Discussions
(5, 5); -- Charlie in Book Club

-- Insert sample role_user relationships
INSERT INTO `role_user` (`role_id`, `user_id`) VALUES
(1, 1), -- John is Administrator
(2, 2), -- Jane is Moderator
(3, 3), -- Bob is Premium User
(4, 4), -- Alice is Regular User
(4, 5); -- Charlie is Regular User