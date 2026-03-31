/* Example dataset for the DBH shop schema.
   Run this after dbh_setup.sql to populate the tables with test data. */

DELETE FROM `orderdetails`;
DELETE FROM `orders`;
DELETE FROM `promoCodes`;
DELETE FROM `products`;
DELETE FROM `users`;

INSERT INTO `users` (`userID`, `password`, `email`, `phonenumber`, `address`, `ordersdone`) VALUES
(1, 'hashed_pw_alice', 'alice.nguyen@example.com', '905-555-0101', '12 King St W, Hamilton, ON', 3),
(2, 'hashed_pw_ben', 'ben.patel@example.com', '905-555-0102', '44 Main St E, Hamilton, ON', 1),
(3, 'hashed_pw_chloe', 'chloe.martin@example.com', '905-555-0103', '88 Emerson St, Hamilton, ON', 0),
(4, 'hashed_pw_daniel', 'daniel.ross@example.com', '905-555-0104', '23 Dundurn St S, Hamilton, ON', 2),
(5, 'hashed_pw_ella', 'ella.kim@example.com', '905-555-0105', '301 James St N, Hamilton, ON', 4);

INSERT INTO `products` (`productID`, `productName`, `productDesc`, `price`, `productImg`, `productClass`) VALUES
(101, 'CK Chicken', 'Juicy chicken wings tossed in your choice of mild classic or spicy tangy sauce, cooked to perfection with a crispy exterior and tender, flavourful interior. A classic crowd-pleaser perfect for sharing. (one pound)', 14.00, 'placeholder.jpg', 'CK Favourites'),
(102, 'Cajun Wings', 'Tender, juicy chicken wings tossed in a bold Cajun spice blend, fried to crispy perfection, and bursting with smoky, zesty flavor. (one pound)', 14.00, 'wings.png', 'CK Favourites'),
(103, 'Classic Battered Fried Wings', 'Crispy, golden-battered chicken wings, fried to perfection for a crunchy exterior and juicy, tender interior. Served hot for a satisfying bite every time. (one pound)', 14.00, 'placeholder.jpg', 'CK Favourites'),
(104, 'CK Loaded Fries', 'Golden, crispy fries piled high with melted cheese, savory toppings like crispy bacon bites, diced onions, and jalapenos, and drizzled with your choice of sauces. A hearty, flavorful snack perfect for sharing or enjoying on your own. ', 12.00, 'placeholder.jpg', 'CK Favourites'),
(105, 'Classic Poutine', 'Golden, crispy fries topped with rich cheese curds and smothered in savory gravy, creating a warm, indulgent, and comforting Canadian favorite. Add spicy jerk chicken 4$.', 10.00, 'placeholder.jpg', 'Side'),
(106, 'Chocolate Milkshake', 'Vanilla ice cream blended with chocolate syrup.', 5.50, 'placeholder.jpg', 'Drink'),
(107, 'Iced Latte', 'Espresso with milk poured over ice.', 4.75, 'placeholder.jpg', 'Drink'),
(108, 'Caesar Salad', 'Romaine, croutons, parmesan, and Caesar dressing.', 6.99, 'placeholder.jpg', 'Salad');

INSERT INTO `promoCodes` (`promoID`, `promoCode`, `discountType`, `discountValue`, `active`, `expiryDate`) VALUES
(201, 'WELCOME10', 'percentage', 10.00, TRUE, '2026-12-31 00:00:00'),
(202, 'FREESHIP5', 'fixed', 5.00, TRUE, '2026-09-30 00:00:00'),
(203, 'SPRING15', 'percentage', 15.00, FALSE, '2026-04-15 00:00:00');

INSERT INTO `orders` (`orderID`, `accountID`, `orderDate`, `address`, `fullfilled`) VALUES
(301, 1, '2026-03-20 12:14:00', '12 King St W, Hamilton, ON', TRUE),
(302, 2, '2026-03-21 18:45:00', '44 Main St E, Hamilton, ON', TRUE),
(303, 4, '2026-03-22 13:05:00', '23 Dundurn St S, Hamilton, ON', FALSE),
(304, 5, '2026-03-23 09:32:00', '301 James St N, Hamilton, ON', TRUE),
(305, 1, '2026-03-24 19:11:00', '12 King St W, Hamilton, ON', FALSE),
(306, 5, '2026-03-25 11:28:00', '301 James St N, Hamilton, ON', FALSE);

INSERT INTO `orderdetails` (`orderDetailID`, `orderID`, `productID`, `quantity`) VALUES
(401, 301, 101, 2),
(402, 301, 104, 1),
(403, 301, 106, 2),
(404, 302, 103, 1),
(405, 302, 105, 1),
(406, 302, 107, 1),
(407, 303, 102, 2),
(408, 303, 104, 2),
(409, 304, 108, 1),
(410, 304, 103, 1),
(411, 304, 106, 1),
(412, 305, 101, 1),
(413, 305, 105, 2),
(414, 305, 107, 2),
(415, 306, 102, 1),
(416, 306, 104, 1),
(417, 306, 108, 1);
