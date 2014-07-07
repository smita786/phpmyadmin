/*Tables Having composite key and partial dependencies: for testing 2NF */
CREATE TABLE IF NOT EXISTS employees_department (
    emp_no      INT             NOT NULL,
    dept_no     CHAR(4)         NOT NULL,
    birth_date  DATE            NOT NULL,   /*depends on emp_no*/
    first_name  VARCHAR(14)     NOT NULL,   /*depends on emp_no*/
    last_name   VARCHAR(16)     NOT NULL,   /*depends on emp_no*/
    gender      ENUM ('M','F')  NOT NULL,   /*depends on emp_no*/
    hire_date   DATE            NOT NULL,   /*depends on emp_no and dept_no*/
    dept_name   VARCHAR(40)     NOT NULL,   /*depends on dept_no*/
    from_date   DATE        NOT NULL,       /*depends on emp_no and dept_no*/
    to_date     DATE        NOT NULL,       /*depends on emp_no and dept_no*/
    PRIMARY KEY (emp_no, dept_no)
);
INSERT INTO `employees_department` (`emp_no`, `dept_no`, `birth_date`, `first_name`, `last_name`, `gender`, `hire_date`, `dept_name`, `from_date`, `to_date`) VALUES
(1, '1', '2014-07-01', 'Smita', 'Kumari', 'F', '2014-07-09', 'information technology', '2014-07-01', '2014-07-12'),
(1, '2', '2014-07-01', 'Smita', 'Kumari', 'F', '2014-07-10', 'information security', '2014-07-02', '2014-07-13'),
(2, '1', '2014-07-10', 'Sachin', 'Tendulakar', 'M', '2014-07-04', 'information technology', '2014-07-02', '2014-07-12'),
(2, '2', '2014-07-10', 'Sachin', 'Tendulakar', 'M', '2014-07-05', 'information security', '2014-07-09', '2014-07-15');

CREATE TABLE IF NOT EXISTS `orderdetails` (
  `orderNumber` int(11) NOT NULL, 
  `productCode` varchar(15) NOT NULL,
  `customerId` int(11) NOT NULL,
  `customerName` varchar(128) NOT NULL,     /*depends on customerId*/
  `customerPhone` int(11) NOT NULL,         /*depends on customerId*/
  `productName` varchar(70) NOT NULL,       /*depends on productCode*/
  `productVendor` varchar(50) NOT NULL,     /*depends on productCode*/
  `productDescription` text NOT NULL,       /*depends on productCode*/
  `MSRP` double NOT NULL,                   /*depends on productCode*/
  `quantityOrdered` int(11) NOT NULL,       /*depends on oredrNumber and productCode*/
  `orderDate` date NOT NULL,                /*depends on orderNumber*/
  `requiredDate` date NOT NULL,             /*depends on orderNumber*/
  `shippedDate` date DEFAULT NULL,          /*depends on orderNumber*/
  `orderStatus` varchar(15) NOT NULL,       /*depends on orderNumber*/
  PRIMARY KEY (`orderNumber`,`productCode`, `customerId`)
);