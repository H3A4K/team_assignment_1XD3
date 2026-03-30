CREATE TABLE IF NOT EXISTS `users` (
    `userID` INT AUTO_INCREMENT,
    `password` varchar(255) NOT NULL,
    `email` varchar(255) UNIQUE,
    `phonenumber` varchar(25) UNIQUE,
    `address` varchar(255),
    `ordersdone` INT DEFAULT(0),
    PRIMARY KEY (`userID`)
);

CREATE TABLE IF NOT EXISTS `products` (
    `productID` INT AUTO_INCREMENT,
    `productName` varchar(255),
    `productDesc` varchar(255),
    `price` DECIMAL(7, 3),
    `productClass` varchar(255),
    PRIMARY KEY (`productID`)
);

CREATE TABLE IF NOT EXISTS `orders` (
    `orderID` INT AUTO_INCREMENT,
    `accountID` INT,
    `orderDate` varchar(255),
    `address` varchar(255) NOT NULL,
    `fullfilled` BOOLEAN DEFAULT(0),
    PRIMARY KEY (`orderID`)
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
    `promoCode` varchar(255),
    `discountType` varchar(255),
    `discountValue` DECIMAL(7, 3),
    `active` BOOLEAN,
    `expiryDate` DATE,
    PRIMARY KEY (`promoID`)
);