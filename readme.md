[![Build Status](https://travis-ci.org/mnshankar/two-factorial-anova.svg)](https://travis-ci.org/mnshankar/two-factorial-anova)

Classic two-factorial ANOVA (With Replication)
==============================================

This package is used to output simple 2 factor ANOVA (Analysis of Variables) parameters using PHP.
(AKA: Two-Way ANOVA, Two-Way Crossed ANOVA)

Installation
------------

Add the LinearRegression package as a dependency to your composer.json file:

```javascript
{
    "require": {
        "mnshankar/anova": "1.0"
    }
}
```
Usage
-----
Initialize the factorial array for processing as follows:
NOTE: Order in which data is passed in is important

	*+-------------+------------------+-----------------------+
	*|             |             1st Factor                   |
	*|             |       1          |          2            |
	*|             |                  |                       |
	*+--------------------------------------------------------+
	*|             |                  |                       |
	*|             |       1          |          4            |
	*|  2  A       |       2          |          5            |
	*|  n          |       3          |          6            |
	*|  d          |                  |                       |
	*|             |                  |                       |
	*|--F----------+------------------------------------------+
	*|  a          |                  |                       |
	*|  c          |                  |                       |
	*|  t          |       7          |          10           |
	*|  o  B       |       8          |          11           |
	*|  r          |       9          |          12           |
	*|             |                  |                       |
	*+-------------+------------------+-----------------------+
```php
 $obj = new ANOVA(
            array(1,2,3,4,5,6,7,8,9,10,11,12),3
        );
```  
The above example is converting a 12 element array into a 2*2 factorial array containing 3 elements in each cell.
The first parameter to the constructor is the input array and the second parameter is the number of replications (defaults to 3)

The various ANOVA parameters can then be calculated as follows:
```php
$obj->computeR();   //array of row sums
$obj->computeC();   //array of column sums
$obj->MSC();        //Mean square of columns
$obj->MSR();        //Mean square of rows
$obj->MSRC();       //Mean square interaction rc
$obj->MSE();        //Mean square residuals
$obj->Fc();         //F stat Column
$obj->Fr();         //F stat row
$obj->Frc();        //F stat interaction rc
$obj->Pc();         //P value Column
$obj->Pr();         //P value row
$obj->Prc();        //P value interaction rc
```

Reference
---------
NIST/SEMATECH e-Handbook of Statistical Methods, 
http://www.itl.nist.gov/div898/handbook/ppc/section2/ppc232.htm, 5/4/2015.
