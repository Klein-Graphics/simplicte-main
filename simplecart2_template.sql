-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 29, 2012 at 02:48 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `simplecart2_template`
--

-- --------------------------------------------------------

--
-- Table structure for table `authstamps`
--

CREATE TABLE IF NOT EXISTS `authstamps` (
  `userID` varchar(25) NOT NULL,
  `Stamp` int(11) NOT NULL,
  `Expires` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `custid` varchar(48) NOT NULL,
  `ship_firstname` varchar(255) NOT NULL,
  `ship_initial` varchar(10) NOT NULL,
  `ship_lastname` varchar(255) NOT NULL,
  `ship_streetaddress` text NOT NULL,
  `ship_apt` varchar(255) NOT NULL,
  `ship_city` varchar(255) NOT NULL,
  `ship_state` varchar(255) NOT NULL,
  `ship_postalcode` varchar(32) NOT NULL,
  `ship_country` varchar(48) NOT NULL,
  `ship_phone` varchar(255) NOT NULL,
  `bill_firstname` varchar(255) NOT NULL,
  `bill_initial` varchar(255) NOT NULL,
  `bill_lastname` varchar(255) NOT NULL,
  `bill_streetaddress` text NOT NULL,
  `bill_apt` varchar(255) NOT NULL,
  `bill_city` varchar(255) NOT NULL,
  `bill_state` varchar(255) NOT NULL,
  `bill_postalcode` varchar(32) NOT NULL,
  `bill_country` varchar(48) NOT NULL,
  `bill_phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwordmd5` varchar(33) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custid` (`custid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `details`
--

CREATE TABLE IF NOT EXISTS `details` (
  `detail` varchar(255) DEFAULT NULL,
  `readable` varchar(255) NOT NULL,
  `detail_value` text,
  `encoded` tinyint(1) NOT NULL,
  `category` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  UNIQUE KEY `detail` (`detail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `details`
--

INSERT INTO `details` (`detail`, `readable`, `detail_value`, `encoded`, `category`, `type`) VALUES
('storename', 'Store name', 'Simplecart Store', 0, 'basic', 'text'),
('buttons_folder', 'Button style', 'orange', 0, 'display', 'special'),
('salestax', 'Sales tax', '.0', 0, 'payment', 'percent'),
('upsAccessKey', 'Access Key', '', 0, 'shipping-driver-UPS', 'text'),
('upsUsername', 'Username', '', 0, 'shipping-driver-UPS', 'text'),
('upsPassword', 'Password', '', 1, 'shipping-driver-UPS', 'password'),
('upsAccountNum', 'Account Number', '', 0, 'shipping-driver-UPS', 'text'),
('storeZipcode', 'Distribution Zip Code', '', 0, 'basic', 'text'),
('paypaluser', 'Username', '', 0, 'payment-driver-Paypal_EC', 'text'),
('paypalsignature', 'API Signature', '', 0, 'payment-driver-Paypal_EC', 'text'),
('taxstates', 'States to tax (Separated by a comma. ''*'' for all)', '*', 0, 'payment', 'text'),
('timezone', 'Store Timezone', 'America/Los_Angeles', 0, 'basic', 'special'),
('cleanupRate', 'Order cleanup rate', '30', 0, 'basic', 'number'),
('orderEmail', 'Order alert email', '', 0, 'basic', 'email'),
('sendEmail', 'Outgoing email', '', 0, 'basic', 'email'),
('baseshipping', 'Base shipping amount', '', 0, 'shipping', 'currency'),
('paymentmethods', 'Payment Methods', '', 0, 'payment', 'special'),
('authorizedotnetid', 'API ID', '', 0, 'payment-driver-Authorizedotnet_SIM', 'text'),
('authorizedotnetkey', 'API Key', '', 0, 'payment-driver-Authorizedotnet_SIM', 'text'),
('paypalpwd', 'API Password', '', 1, 'payment-driver-Paypal_EC', 'password'),
('setupfinished', '', 'true', 0, 'internal', 'internal'),
('shipping_drivers', 'Shipping Methods', '', 0, 'shipping', 'special'),
('cart_driver', 'Cart style', 'Folding', 0, 'display', 'special'),
('checkout_driver', 'Checkout style', 'Folding', 0, 'display', 'special'),
('account_driver', 'User account management style', 'Folding', 0, 'display', 'special'),
('storelive', 'Store live', '0', 0, 'basic', 'checkbox'),
('item_template', 'Item template', 'default', 0, 'display', 'special');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE IF NOT EXISTS `discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `action` enum('percentoff','fixedoff','bxgx','itempercentoff','itemfixedoff') NOT NULL,
  `value` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `itemoptions`
--

CREATE TABLE IF NOT EXISTS `itemoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) DEFAULT NULL,
  `optorder` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `image` text,
  `cat` varchar(255) DEFAULT NULL,
  `flags` text NOT NULL,
  `stock` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `number` varchar(255) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `image` text,
  `flags` text NOT NULL,
  `stock` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordernumber` varchar(16) NOT NULL,
  `items` text NOT NULL,
  `custid` varchar(24) NOT NULL,
  `transtype` varchar(255) NOT NULL,
  `status` varchar(16) NOT NULL,
  `lastupdate` int(11) NOT NULL,
  `shipping` float NOT NULL,
  `taxrate` float NOT NULL,
  `discount` int(11) NOT NULL,
  `ship_firstname` varchar(255) NOT NULL,
  `ship_initial` varchar(10) NOT NULL,
  `ship_lastname` varchar(255) NOT NULL,
  `ship_streetaddress` text NOT NULL,
  `ship_apt` varchar(255) NOT NULL,
  `ship_city` varchar(255) NOT NULL,
  `ship_state` varchar(255) NOT NULL,
  `ship_postalcode` varchar(255) NOT NULL,
  `ship_country` varchar(48) NOT NULL,
  `ship_phone` varchar(255) NOT NULL,
  `bill_firstname` varchar(255) NOT NULL,
  `bill_initial` varchar(10) NOT NULL,
  `bill_lastname` varchar(255) NOT NULL,
  `bill_streetaddress` text NOT NULL,
  `bill_apt` varchar(255) NOT NULL,
  `bill_city` varchar(255) NOT NULL,
  `bill_state` varchar(255) NOT NULL,
  `bill_postalcode` varchar(32) NOT NULL,
  `bill_country` varchar(48) NOT NULL,
  `bill_phone` varchar(255) NOT NULL,
  `paytype` varchar(255) NOT NULL,
  `shipping_method` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=56 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `passwordmd5` varchar(33) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `master` tinyint(1) NOT NULL,
  `lastlogin` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `username`, `passwordmd5`, `realname`, `email`, `master`, `lastlogin`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Simplecart Admin', '', 1, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
