CREATE TABLE IF NOT EXISTS `users` (
    `userID` INT AUTO_INCREMENT,
    `password` TEXT NOT NULL,
    `email` VARCHAR(255) UNIQUE,
    `phonenumber` varchar(25) UNIQUE,
    `address` TEXT,
    `ordersdone` INT DEFAULT(0),
    `admin` BOOLEAN DEFAULT(0),
    PRIMARY KEY (`userID`)
);

CREATE TABLE IF NOT EXISTS `productClasses` (
    `classID` INT AUTO_INCREMENT,
    `name` varchar(255) UNIQUE,
    `quantity` INT,
    `time` DECIMAL(5, 2),
    PRIMARY KEY (`classID`)
);

CREATE TABLE IF NOT EXISTS `products` (
    `productID` INT AUTO_INCREMENT,
    `productName` TEXT,
    `productDesc` TEXT,
    `price` DOUBLE,
    `productImg` TEXT,
    `productClass` varchar(255),
    PRIMARY KEY (`productID`),
    FOREIGN KEY (`productClass`) REFERENCES `productClasses`(`name`)
);

CREATE TABLE IF NOT EXISTS `orders` (
    `orderID` INT AUTO_INCREMENT,
    `accountID` INT,
    `orderDate` DATETIME,
    `address` TEXT NOT NULL,
    `fulfillmentMethod` VARCHAR(20) NOT NULL DEFAULT('pickup'),
    `discountTotal` DECIMAL(10, 2) NOT NULL DEFAULT(0.00),
    `fullfilled` BOOLEAN DEFAULT(0),
    PRIMARY KEY (`orderID`),
    FOREIGN KEY (`accountID`) REFERENCES `users`(`userID`)
);

CREATE TABLE IF NOT EXISTS `orderdetails` (
    `orderDetailID` INT AUTO_INCREMENT,
    `orderID` INT,
    `productID` INT NOT NULL,
    `quantity` INT,
    PRIMARY KEY (`orderDetailID`),
    FOREIGN KEY (`orderID`) REFERENCES `orders`(`orderID`),
    FOREIGN KEY (`productID`) REFERENCES `products`(`productID`)
);

CREATE TABLE IF NOT EXISTS `promoCodes` (
    `promoID` INT AUTO_INCREMENT,
    `promoCode` TEXT,
    `discountType` TEXT,
    `discountValue` DOUBLE,
    `active` BOOLEAN,
    `expiryDate` DATETIME,
    `requiredProductIDs` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`promoID`)
);

CREATE TABLE IF NOT EXISTS `promotions` (
    `promotionID` INT AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `eyebrow` VARCHAR(100),
    `price` VARCHAR(50),
    `badge` VARCHAR(100),
    `description` TEXT,
    `finePrint` TEXT,
    `image` VARCHAR(255),
    `theme` VARCHAR(20) DEFAULT 'orange',
    `ctaLabel` VARCHAR(50) DEFAULT 'Order Now',
    `active` BOOLEAN DEFAULT(1),
    `sortOrder` INT DEFAULT(0),
    `promoCode` VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (`promotionID`)
);

-- test data 

DELETE FROM `orderdetails`;
DELETE FROM `orders`;
DELETE FROM `promoCodes`;
DELETE FROM `promotions`;
DELETE FROM `products`;
DELETE FROM `productClasses`;
DELETE FROM `users`;

INSERT INTO `users` (`userID`, `password`, `email`, `phonenumber`, `address`, `ordersdone`, `admin`) VALUES
(1, '$2y$10$XomCcc9tE4Ay8x.h.exJ4ubTjCVSLvJlppAgqfMJRtU9kk2YskOjW' /* hashed_pw_alice */, 'alice.nguyen@example.com', '905-555-0101', '12 King St W, Hamilton, ON', 3, 0),
(2, '$2y$10$3OjAgP8bCBLM/iTLB9McsedmH3t7rMsXP/JuCOffvEF7b0vwJEzdO' /* hashed_pw_ben */, 'ben.patel@example.com', '905-555-0102', '44 Main St E, Hamilton, ON', 1, 0),
(3, '$2y$10$MN90tat/8X3b8IwY/G8ADuij.F/OaMACUArIBkyb5sL1SaOyctmLC' /* hashed_pw_chloe */, 'chloe.martin@example.com', '905-555-0103', '88 Emerson St, Hamilton, ON', 0, 0),
(4, '$2y$10$ep1kN8ahskXxShxc6uA2Su5074nlCie57wILgX2r.EpWeCijtbk1e' /* hashed_pw_daniel */, 'daniel.ross@example.com', '905-555-0104', '23 Dundurn St S, Hamilton, ON', 2, 0),
(5, '$2y$10$o3loxrP55ogWQMws8QZ11.RLeN3QO5Ty.RsVZKdPjxPvpqIXI3W.i' /* hashed_pw_ella */, 'ella.kim@example.com', '905-555-0105', '301 James St N, Hamilton, ON', 4, 0),
(6, '$2y$10$XomCcc9tE4Ay8x.h.exJ4ubTjCVSLvJlppAgqfMJRtU9kk2YskOjW' /* hashed_pw_alice */, 'admin@example.com', '905-555-0106', '302 James St N, Hamilton, ON', 4, 1);

INSERT INTO `productClasses` (`name`, `quantity`, `time`) VALUES
('CK Favourites', 31, 10),
('Side', 8, 5),
('Drink', 8, 1),
('Salad', 2, 2.5);

INSERT INTO `products` (`productID`, `productName`, `productDesc`, `price`, `productImg`, `productClass`) VALUES
(101, 'Ck Chicken Wings (1 lb)', '*Fan favourite!* Juicy wings tossed in your choice of mild classic or spicy tangy sauce. Crispy outside and tender inside.', 16.99, 'ck-chicken-wings-1-lb.jpg', 'CK Favourites'),
(102, 'Cajun Wings (1 lb)', 'Boldly seasoned wings with spices and fried to crispy perfection. Crispy on the outside, juicy on the inside, and tossed in a bold blend of smoky Cajun seasoning.', 16.99, 'cajun-wings-1-lb.jpg', 'CK Favourites'),
(103, 'CK Loaded Fries', 'Golden fries stacked high and covered in melted cheese, crispy bacon, red onions, and spicy jalapenos. Finished with a drizzle of creamy garlic sauce for the perfect mix of cheesy, smoky, and spicy flavor in every bite.', 14.99, 'ck-loaded-fries.jpg', 'CK Favourites'),
(104, 'Classic Poutine', 'Crispy golden fries piled high and smothered in rich, savory brown gravy, topped with squeaky, melt in your mouth cheese curds. Every bite is a perfect mix of crunch, creaminess, and pure Canadian comfort.', 11.99, 'classic-poutine.jpg', 'Side'),
(105, 'Large French Fries', 'Crispy on the outside and fluffy on the inside. Golden fries seasoned to perfection and served hot for the perfect salty, satisfying snack or side. Simple, classic, and always delicious.', 8.99, 'large-french-fries.jpg', 'Side'),
(106, 'Small French Fries', 'Crispy on the outside and fluffy on the inside. Golden fries seasoned to perfection and served hot for the perfect salty, satisfying snack or side. Simple, classic, and always delicious.', 4.99, 'small-french-fries.jpg', 'Side'),
(107, 'Fried Pita & House Hummus', 'Crispy, Creamy, Fresh.
Golden fried pita served with our smooth, house made hummus and topped with cool tzatziki, bell peppers and onions. Garnish with a drizzle of extra virgin olive oil for the perfect balance of crunch, creaminess, and Mediterranean flavor.', 9.99, 'fried-pita-and-house-hummus.jpg', 'Side'),
(108, 'Onion Rings', 'Golden, crunchy onion rings made fresh and served with our creamy, house-made dipping sauce. Simple, crispy, and packed with flavor.', 8.99, 'onion-rings.jpg', 'Side'),
(109, 'Calamari Rings', 'Crispy, tender, and full of flavor. Lightly breaded calamari rings fried to golden perfection and served with our signature house made dipping sauce. A perfect blend of crunch and tenderness in every bite.', 18.99, 'calamari-rings.jpg', 'Side'),
(110, 'Caesar Salad', 'Fresh, smoky, and full of flavour. Crisp iceberg lettuce tossed in our creamy Caesar dressing, topped with crunchy croutons,  Parmesan and crispy bacon bites for that perfect smoky finish. A classic favourite with a savoury twist.', 8.99, 'caesar-salad.jpg', 'Salad'),
(111, 'Greek Salad', 'Fresh, vibrant, and full of Mediterranean flavor.
A colorful mix of crisp cucumbers, red onions, juicy tomatoes, and black olives, all tossed in our signature house made dressing. Light, refreshing, and perfectly balanced.', 8.99, 'greek-salad.jpg', 'Salad'),
(112, 'Cream of Chicken Soup', 'Comfort in a bowl
Tender chicken simmered in a rich, velvety cream broth with onions, celery, carrots, and herbs. Smooth, hearty, and full of cozy flavour.', 7.99, 'cream-of-chicken-soup.jpg', 'Side'),
(113, 'Garlic Bread', 'Golden, toasty bread with a savoury garlic spread. Crisp outside, soft inside.', 3.00, 'garlic-bread.jpg', 'Side'),
(114, 'Chicken Fried Rice', 'Fragrant fried rice tossed with tender chicken and fresh vegetables including carrots, leeks, and cabbage. Cooked to perfection and packed with rich, savory flavor in every bite. a delicious, satisfying classic made fresh and full of taste.', 17.99, 'chicken-fried-rice.jpg', 'CK Favourites'),
(115, 'Nasi Goreng', 'A flavorful blend of rice stir-fried with tender chicken, juicy shrimp, and fresh crunchy vegetables. Topped with a perfectly cooked sunny-side-up egg for a rich and satisfying finish. A delicious balance of savory, and spicy flavors in every bite.', 19.99, 'nasi-goreng.jpg', 'CK Favourites'),
(116, 'Portuguese Rice with chicken', 'Fragrant flavour-packed Portuguese rice cooked with tender chicken, juicy tomatoes, colourful bell peppers, and savoury olives loaded with rich, authentic flavour.', 18.99, 'portuguese-rice-with-chicken.jpg', 'CK Favourites'),
(117, 'Souvlaki Rice Platter with Greek salad & Roast potatoes', 'A complete Mediterranean feast.
Tender, marinated 2 souvlaki skewers served over fluffy, seasoned rice, accompanied by golden Greek roast potatoes and a side of fresh Greek salad with cucumbers, tomatoes, red onions, and olives tossed in our signature house dressing and a tzatziki sauce. A colourful, flavour-packed, large meal that''s hearty, satisfying, and authentically Mediterranean.', 19.99, 'souvlaki-rice-platter-with-greek-salad-and-roast-potatoes.jpg', 'CK Favourites'),
(118, 'Chicken Shawarma Platter', 'Juicy, flavorful, and loaded with goodness.
Tender, chicken shawarma served with golden fries and a fresh salad , all drizzled with our creamy house made garlic sauce. a satisfying, flavor packed platter that''s crispy, savory, and perfectly balanced in every bite.', 18.99, 'chicken-shawarma-platter.jpg', 'CK Favourites'),
(119, 'CK Butter Chicken and rice', 'Rich, creamy and full of flavour. Tender pieces of marinated chicken are simmered in a smooth tomato and butter sauce with a blend of aromatic spices. Each bite has the perfect balance of savoury, tangy, and mild heat. Served with fluffy basmati rice and warm naan for the ultimate comfort meal.', 18.99, 'ck-butter-chicken-and-rice.jpg', 'CK Favourites'),
(120, 'Baked Fish with seasoned rice', 'Tender baked fish served with seasoned rice and a side of steamed vegetables, finished with a rich lemon butter sauce.', 21.99, 'baked-fish-with-seasoned-rice.jpg', 'CK Favourites'),
(121, 'Egg Fried Rice', 'Steamed rice stir-fried with farm-fresh eggs, crunchy carrots, tender cabbage, and leeks, seasoned with savoury seasoning.', 19.99, 'egg-fried-rice.jpg', 'CK Favourites'),
(122, 'Creamy Pesto Pasta', 'Pasta tossed in a velvety, aromatic pesto cream sauce, topped with tender grilled chicken and finished with a generous sprinkle of parmesan cheese A comforting, flavorful dish that hits every note of creamy, cheesy, and savory perfection.', 18.99, 'creamy-pesto-pasta.jpg', 'CK Favourites'),
(123, 'Chicken Alfredo', 'Creamy flavor. savoury, and full of flavour.
Tender grilled chicken served over pasta, smothered in a rich, creamy Alfredo sauce and finished with fresh herbs. Simple, rich and deliciously satisfying.', 18.99, 'chicken-alfredo.jpg', 'CK Favourites'),
(124, 'Spaghetti Bolognese', 'Classic, hearty, and full of flavor.
Spaghetti topped with a rich, slow simmered Bolognese sauce made with seasoned ground meat, tomatoes, and herbs. Finished with a sprinkle of Parmesan  cheese for a comforting, timeless Italian favorite', 18.99, 'spaghetti-bolognese.jpg', 'CK Favourites'),
(125, 'Classic Rose Veggie', 'A creamy blend of tomato and cream sauce tossed with tender pasta and fresh vegetables. Loaded with broccoli, carrots, and colorful peppers for a perfect mix of flavor and crunch. Comforting, hearty, and made for veggie lovers.', 18.99, 'classic-rose-veggie.jpg', 'CK Favourites'),
(126, 'Spaghetti & Meatballs', 'Al dente spaghetti served with tender, savory meatballs simmered in a rich tomato sauce, finished with a sprinkle of Parmesan cheese. A classic Italian favourite that''s warm, satisfying, and utterly delicious.', 18.99, 'spaghetti-and-meatballs.jpg', 'CK Favourites'),
(127, 'Classic Rose Chicken and Veggies', 'Tender chicken and fresh vegetables tossed in a creamy tomato and herb rose sauce made with broccoli, carrots, colorful peppers for the perfect mix of flavor and texture. A hearty, satisfying pasta dish that''s full of comfort and freshness.', 20.99, 'classic-rose-chicken-and-veggies.jpg', 'CK Favourites'),
(128, 'Creamy Seafood Pasta', 'Dive into a rich, velvety cream sauce tossed with perfectly cooked pasta and loaded with tender shrimp, scallops, and calamari, finished with herbs, parmesan, and a hint of garlic.', 24.99, 'creamy-seafood-pasta.jpg', 'CK Favourites'),
(129, 'Bacon Fettuccine Pasta', 'Tender fettuccine tossed in a rich. creamy sauce with smoky bacon, sauteed onions, and a hint of garlic. finished with parmesan and herbs. This dish brings bold flavour and smooth, irresistible creaminess in every bite.', 18.99, 'bacon-fettuccine-pasta.jpg', 'CK Favourites'),
(130, 'CK Smash Burger', 'A juicy, freshly made beef patty smashed to perfection and topped with melted cheese, crisp lettuce, ripe tomato, tangy pickles, and sweet caramelized onions. Served on a toasted bun for the perfect bite every time. Classic bold, and full of flavor.', 12.99, 'ck-smash-burger.jpg', 'CK Favourites'),
(131, 'Crispy Chicken Burger', 'Crispy chicken patty with lettuce, tomato, onion, and pickle on a toasted bun.', 11.99, 'crispy-chicken-burger.jpg', 'CK Favourites'),
(132, 'Swiss Mushroom Burger', 'A crispy-edged Swiss cheese smashburger with beef smashed thin so it gets those deep, crackly browned edges, topped with melty Swiss that drapes over every ridge. Piled with buttery sautéed mushrooms and sweet caramelized onions that practically melt into the patty, then finished with cool, crunchy lettuce, juicy tomato, and sharp pickles for that perfect tangy snap.', 13.99, 'swiss-mushroom-burger.jpg', 'CK Favourites'),
(133, 'Clarence''s Signature Burger', 'Our signature, grilled beef patty (not smashed) is nestled beneath a blanket of crisp leaf lettuce, vine-ripened tomato slices, and onions. Finished with tangy brined pickles for a bright, acidic crunch, all served on a toasted bun. Let us know in the notes if you would prefer not to have any of these toppings.', 12.99, 'clarences-signature-burger.jpg', 'CK Favourites'),
(134, 'Clarence''s Signature with Spice', 'Our signature, grilled beef patty topped with fresh lettuce, ripe tomatoes, onions, and tangy pickles, all tucked into a toasted bun and drizzled with our homemade spicy sauce.', 12.99, 'clarences-signature-with-spice.jpg', 'CK Favourites'),
(135, 'Crispy Chicken Coleslaw Sandwich', 'Fresh fried crispy chicken breast topped with creamy coleslaw on a toasted bun.', 12.99, 'crispy-chicken-coleslaw-sandwich.jpg', 'CK Favourites'),
(136, 'Loaded Crispy Chicken Wrap', 'Crispy chicken, lettuce, fries, tomato, ketchup and ranch in a grilled tortilla.', 13.99, 'loaded-crispy-chicken-wrap.jpg', 'CK Favourites'),
(137, 'Chicken Strips and Fries', 'Golden chicken strips with seasoned fries.', 11.99, 'chicken-strips-and-fries.jpg', 'CK Favourites'),
(138, 'Fish and Chips', 'Crispy, golden-fried fish served in two hearty pieces, paired with hot, crunchy fries and a side of classic, creamy tartar sauce, offering a perfect balance of crunch and flavour.', 21.99, 'fish-and-chips.jpg', 'CK Favourites'),
(139, 'Souvlaki Wrap', 'Lettuce, chicken, tomato, onion and tzatziki wrapped in a warm pita bread.', 9.99, 'souvlaki-wrap.jpg', 'CK Favourites'),
(140, 'Fried Chicken and Fries', 'Golden, crispy, and packed with flavour. Our fried chicken is perfectly seasoned and fried to a juicy crunch. Served with a generous side of crispy fries. Simple, satisfying, and always delicious.', 10.99, 'fried-chicken-and-fries.jpg', 'CK Favourites'),
(141, 'CK Smash Burger with Bacon', 'A juicy, freshly made beef patty smashed to perfection and topped with melted cheese, crisp lettuce, ripe tomato, savoury bacon, tangy pickles, and sweet caramelized onions. Served on a toasted bun for the perfect bite every time. Classic bold, and full of flavor.  (Our original Smash Burger with bacon on top)', 13.99, 'ck-smash-burger.jpg', 'CK Favourites'),
(142, 'Coca Cola', '', 2.99, 'coca-cola.jpg', 'Drink'),
(143, 'Diet Coke', '', 2.99, 'coca-cola.jpg', 'Drink'),
(144, 'Pepsi', '', 2.99, 'coca-cola.jpg', 'Drink'),
(145, 'Orange Crush', '', 2.99, 'coca-cola.jpg', 'Drink'),
(146, 'Root Beer', 'Classic, fizzy soda with the familiar root beer flavour. Refreshing and crisp.', 2.99, 'coca-cola.jpg', 'Drink'),
(147, 'Fuze Iced Tea', '', 2.99, 'coca-cola.jpg', 'Drink'),
(148, 'Red Bull', '', 4.00, 'coca-cola.jpg', 'Drink'),
(149, 'Alani', '', 4.00, 'coca-cola.jpg', 'Drink');

INSERT INTO `promoCodes` (`promoID`, `promoCode`, `discountType`, `discountValue`, `active`, `expiryDate`, `requiredProductIDs`) VALUES
(201, 'WELCOME10', 'percentage', 10.00, TRUE, '2026-12-31 00:00:00', NULL),
(202, 'PICK3', 'fixed', 5.00, TRUE, '2026-09-30 00:00:00', NULL),
(203, 'SPRING15', 'percentage', 15.00, FALSE, '2026-04-15 00:00:00', NULL),
(204, 'WINGSFRIES', 'fixed', 3.00, TRUE, '2026-12-31 00:00:00', '101,103');

INSERT INTO `promotions` (`title`, `eyebrow`, `price`, `badge`, `description`, `finePrint`, `image`, `theme`, `ctaLabel`, `active`, `sortOrder`, `promoCode`) VALUES
('Wings & Fries', 'Combo Deal', '$22.99', 'Only for a Limited Time',
 'A full pound of our signature wings paired with a large order of CK Loaded Fries. Classic comfort, bigger portions.',
 '*Available for both pickup & delivery orders.\n*Not applicable with any other offer or deal.',
 'ck-chicken-wings-1-lb.jpg', 'orange', 'Order Now', TRUE, 1, 'WINGSFRIES'),
('Pick 3 Favourites', 'Family Feast', '$39.99', 'New!',
 'Any 3 items from our CK Favourites menu plus 2 drinks. Comfortably feeds 3 to 4 people.',
 '*Available for Pickup Only.\n*Selection of included drinks may vary.',
 'creamy-seafood-pasta.jpg', 'brown', 'Order Now', TRUE, 2, 'PICK3');

INSERT INTO `orders` (`orderID`, `accountID`, `orderDate`, `address`, `fulfillmentMethod`, `discountTotal`, `fullfilled`) VALUES
(301, 1, '2026-03-20 12:14:00', '12 King St W, Hamilton, ON', 'delivery', 0.00, TRUE),
(302, 2, '2026-03-21 18:45:00', '44 Main St E, Hamilton, ON', 'delivery', 0.00, TRUE),
(303, 4, '2026-03-22 13:05:00', 'Pickup at Clarence''s Kitchen', 'pickup', 0.00, FALSE),
(304, 5, '2026-03-23 09:32:00', '301 James St N, Hamilton, ON', 'delivery', 0.00, TRUE),
(305, 1, '2026-03-24 19:11:00', 'Pickup at Clarence''s Kitchen', 'pickup', 0.00, FALSE),
(306, 5, '2026-03-25 11:28:00', 'Pickup at Clarence''s Kitchen', 'pickup', 0.00, FALSE);

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
