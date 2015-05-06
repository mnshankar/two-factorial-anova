<?php namespace mnshankar\anova;

/**
 * Copyright (c) 2015 Shankar Manamalkav <nshankar@ufl.edu>

 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR
 * A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
 * IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class ANOVA
{
    protected $factorialArray;
    protected $C;
    protected $R;
    protected $RC;
    protected $GrandTotal;
    protected $R1Total;
    protected $R2Total;
    protected $C1Total;
    protected $C2Total;
    protected $MSR;
    protected $MSE;
    protected $MSC;
    protected $MSRC;
    protected $Pr;
    protected $Pc;
    protected $Prc;

    protected $replications;
    protected $df1; //k-1;
    protected $df2; //r*k*(N-1);

    /**
     * Format the passed in as an array into a 2*2 factorial grid
     * NOTE: Order in which data is passed in is important
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
     * So, for forming the factorial array above, input data is array(1,2,3,4,5,6,7,8,9,10,11,12)
     * Replications = The default is to use 3 for each of four combinations (total 12 subjects)
     * @param $data
     * @param int $replications
     * @throws \Exception
     */
    public function __construct($data, $replications = 3)
    {
        $this->replications = $replications;
        //first partition the data into the number of replications
        $partition = array_chunk($data, $replications);
        //next partition the data into a 2*2 factorial matrix
        $this->factorialArray = array_chunk($partition, 2);
        //set the degrees of freedom
        $this->df1 = 1;
        $this->df2 = 4*($replications-1);
        //is the factorialArray valid?
        $this->checkArray();
        //run the base calculations. These are cached in fields for efficiency
        $this->computeTotals();
    }

    /**
     * Check if the factorial array is valid
     * @throws \Exception
     */
    public function checkArray()
    {
        if (count($this->factorialArray) !== 2) {
            throw new \Exception('Invalid data array. Unable to partition into 2*2 factorial array');
        }

        $rows = $this->factorialArray[0];
        $cols = $this->factorialArray[1];

        if (count($rows) !== 2 || count($cols) !== 2) {
            throw new \Exception('Invalid data array. Unable to partition into 2*2 factorial array');
        }
    }

    /**
     * Compute R/C totals.. Cache them in local vars for efficiency
     */
    private function computeTotals()
    {
        $this->computeR();
        $this->computeC();
        $this->computeRC();
        $this->computeGrandTotal();
    }

    /**
     * Return the 2 factorial array
     * @return array
     */

    public function getFactorialArray()
    {
        return $this->factorialArray;
    }

    /**
     * Compute Row sum R for factorial 2*2 matrix
     * @return array
     */
    public function computeR()
    {
        if (!$this->R) {
            $this->R = array(
                array_sum($this->factorialArray[0][0]) + array_sum($this->factorialArray[0][1]),
                array_sum($this->factorialArray[1][0]) + array_sum($this->factorialArray[1][1]),
            );
        }
        return $this->R;
    }

    /**
     * Compute Column sum C for factorial 2*2 matrix
     * @return array
     */
    public function computeC()
    {
        if (!$this->C) {
            $this->C = array(
                array_sum($this->factorialArray[0][0]) + array_sum($this->factorialArray[1][0]),
                array_sum($this->factorialArray[0][1]) + array_sum($this->factorialArray[1][1]),
            );
        }
        return $this->C;
    }

    /**
     * Compute all cell totals
     * @return array
     */
    public function computeRC()
    {
        if (!$this->RC) {
            $this->RC = array(
                array_sum($this->factorialArray[0][0]),
                array_sum($this->factorialArray[0][1]),
                array_sum($this->factorialArray[1][0]),
                array_sum($this->factorialArray[1][1]),
            );
        }
        return $this->RC;
    }

    public function computeGrandTotal()
    {
        if (!$this->GrandTotal) {
            $this->GrandTotal = array_sum($this->RC);
        }
        return $this->GrandTotal;
    }

    public function R1Mean()
    {
        return $this->R[0] / (2 * $this->replications);
    }

    public function R2Mean()
    {
        return $this->R[1] / (2 * $this->replications);
    }

    public function C1Mean()
    {
        return $this->C[0] / (2 * $this->replications);
    }

    public function C2Mean()
    {
        return $this->C[1] / (2 * $this->replications);
    }

    public function R1C1Mean()
    {
        return $this->RC[0] / $this->replications;
    }

    public function R1C2Mean()
    {
        return $this->RC[1] / $this->replications;
    }

    public function R2C1Mean()
    {
        return $this->RC[2] / $this->replications;
    }

    public function R2C2Mean()
    {
        return $this->RC[3] / $this->replications;
    }

    public function GrandMean()
    {
        return $this->computeGrandTotal() / (4 * $this->replications);
    }

    /**
     * Mean sum of Rows
     * @return float
     */
    public function MSR()
    {
        if (!$this->MSR) {
            $this->MSR = 2 * ($this->replications) * (pow($this->R1Mean() - $this->GrandMean(), 2) +
                    pow($this->R2Mean() - $this->GrandMean(), 2));
        }
        return $this->MSR;
    }

    /**
     * Mean sum of columns
     * @return float
     */
    public function MSC()
    {
        if (!$this->MSC) {
            $this->MSC = 2 * ($this->replications) * (pow($this->C1Mean() - $this->GrandMean(), 2) +
                    pow($this->C2Mean() - $this->GrandMean(), 2));
        }
        return $this->MSC;
    }

    /**
     * Mean sum of r*c interaction
     * @return float
     */
    public function MSRC()
    {
        //MAB - MA - MB + MT
        if (!$this->MSRC) {
            $this->MSRC = ($this->replications) * (pow($this->R1C1Mean() - $this->R1Mean() - $this->C1Mean() + $this->GrandMean(), 2) +
                    pow($this->R1C2Mean() - $this->R1Mean() - $this->C2Mean() + $this->GrandMean(), 2) +
                    pow($this->R2C1Mean() - $this->R2Mean() - $this->C1Mean() + $this->GrandMean(), 2) +
                    pow($this->R2C2Mean() - $this->R2Mean() - $this->C2Mean() + $this->GrandMean(), 2));

        }
        return $this->MSRC;
    }

    /**
     * Mean square error Xiab - MAB
     * @return float
     */
    public function MSE()
    {
        if (!$this->MSE) {
            $mse = 0;
            foreach ($this->factorialArray[0][0] as $val) {
                $mse += pow(($val - $this->R1C1Mean()), 2);
            }
            foreach ($this->factorialArray[0][1] as $val) {
                $mse += pow(($val - $this->R1C2Mean()), 2);
            }
            foreach ($this->factorialArray[1][0] as $val) {
                $mse += pow(($val - $this->R2C1Mean()), 2);
            }
            foreach ($this->factorialArray[1][1] as $val) {
                $mse += pow(($val - $this->R2C2Mean()), 2);
            }
            $this->MSE = $mse;
        }
        return $this->MSE / (4 * ($this->replications - 1));
    }

    public function Pr()
    {
        if (!$this->Pr) {
            $this->Pr = $this->FishF($this->Fr(), $this->df1, $this->df2);
        }
        return $this->Pr;
    }

    public function Pc()
    {
        if (!$this->Pc) {
            $this->Pc = $this->FishF($this->Fc(), $this->df1, $this->df2);
        }
        return $this->Pc;
    }

    public function Prc()
    {
        if (!$this->Prc) {
            $this->Prc = $this->FishF($this->Frc(), $this->df1, $this->df2);
        }
        return $this->Prc;
    }

    public function Fr()
    {
        return $this->MSR() / $this->MSE();
    }

    public function Fc()
    {
        return $this->MSC() / $this->MSE();
    }

    public function Frc()
    {
        return $this->MSRC() / $this->MSE();
    }

    /**
     * @link http://home.ubalt.edu/ntsbarsh/Business-stat/otherapplets/pvalues.htm#rtdist
     * @param float $f
     * @param int $n1
     * @param int $n2
     * @return float
     */
    public function FishF($f, $n1, $n2)
    {
        $Pi = M_PI;
        $PiD2 = $Pi / 2;

        $x = $n2 / ($n1 * $f + $n2);
        if (($n1 % 2) == 0) {
            return $this->statcom(1 - $x, $n2, $n1 + $n2 - 4, $n2 - 2) * pow($x, $n2 / 2);
        }
        if (($n2 % 2) == 0) {
            return 1 - ($this->statcom($x, $n1, $n1 + $n2 - 4, $n1 - 2) * pow(1 - $x, $n1 / 2));
        }
        $th = atan(sqrt($n1 * $f / $n2));
        $a = $th / $PiD2;
        $sth = sin($th);
        $cth = cos($th);
        if ($n2 > 1) {
            $a = $a + $sth * $cth * ($this->statcom($cth * $cth, 2, $n2 - 3, -1) / $PiD2);
        }
        if ($n1 == 1) {
            return 1 - $a;
        }
        $c = 4 * $this->statcom($sth * $sth, $n2 + 1, $n1 + $n2 - 4, $n2 - 2) * $sth * pow($cth, $n2) / $Pi;
        if ($n2 == 1) {
            return 1 - $a + ($c / 2);
        }
        $k = 2;
        while ($k <= ($n2 - 1) / 2) {
            $c = $c * $k / ($k - .5);
            $k = $k + 1;
        }
        return 1 - $a + $c;
    }

    /**
     * @link http://home.ubalt.edu/ntsbarsh/Business-stat/otherapplets/pvalues.htm#rtdist
     * @param float $q
     * @param float $i
     * @param float $j
     * @param float $b
     * @return float
     */
    private function statcom($q, $i, $j, $b)
    {
        $zz = 1;
        $z = $zz;
        $k = $i;
        while ($k <= $j) {
            $zz = $zz * $q * $k / ($k - $b);
            $z = $z + $zz;
            $k = $k + 2;
        }
        return $z;
    }
}