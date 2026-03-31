CREATE TABLE IF NOT EXISTS `users` (
    `userID` INT AUTO_INCREMENT,
    `password` TEXT NOT NULL,
    `email` TEXT UNIQUE,
    `phonenumber` varchar(25) UNIQUE,
    `address` TEXT,
    `ordersdone` INT DEFAULT(0),
    PRIMARY KEY (`userID`)
);

CREATE TABLE IF NOT EXISTS `products` (
    `productID` INT AUTO_INCREMENT,
    `productName` TEXT,
    `productDesc` TEXT,
    `price` DOUBLE,
    `productImg` TEXT,
    `productClass` TEXT,
    PRIMARY KEY (`productID`)
);

CREATE TABLE IF NOT EXISTS `orders` (
    `orderID` INT AUTO_INCREMENT,
    `accountID` INT,
    `orderDate` DATETIME,
    `address` TEXT NOT NULL,
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
    `promoCode` TEXT,
    `discountType` TEXT,
    `discountValue` DOUBLE,
    `active` BOOLEAN,
    `expiryDate` DATETIME,
    PRIMARY KEY (`promoID`)
);