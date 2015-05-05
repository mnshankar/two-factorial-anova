<?php
use mnshankar\anova\ANOVA;

class AnovaTest extends PHPUnit_Framework_TestCase{

    public function testSplitIntoTwoByTwoFactorialArray(){
        $sut = new ANOVA(
            array(1,2,3,4,5,6,7,8,9,10,11,12),3
        );
        $actual = ($sut->getFactorialArray());
        $expected = array(
            array(
                array(1,2,3),
                array(4,5,6),
            ),
            array(
                array(7,8,9),
                array(10,11,12),
            ),
        );
        $this->assertEquals($expected, $actual);
    }
    /**
     * @expectedException Exception
     */
    public function testEmptyArrayException()
    {
        $sut = new ANOVA(
            array(),1
        );
    }
    /**
     * @expectedException Exception
     */
    public function testInvalidFactorialArrayException()
    {
        $sut = new ANOVA(
            array(1,2,3),3
        );
    }

    /**
     * Actual numbers were obtained using MS Excel,
     * refer to "test data.xlsx" file in the tests folder
     */
    public function testAnovaDefaultReplications()
    {
        $sut = new ANOVA(
            array(32,45,67,67,56,89,56,23,57,67,56,79)
        );
        $this->assertEquals(1496.33, $sut->MSC(), '', .1);
        $this->assertEquals(27, $sut->MSR(), '', .1);
        $this->assertEquals(.33, $sut->MSRC(), '', .1);
        $this->assertEquals(5.43, $sut->Fc(), '', .1);
        $this->assertEquals(.09, $sut->Fr(), '', .1);
        $this->assertEquals(.0021, $sut->Frc(), '', .1);
        $this->assertEquals(.048, $sut->Pc(), '', .1);
        $this->assertEquals(.7622, $sut->Pr(), '', .1);
        $this->assertEquals(.9731, $sut->Prc(), '', .1);
    }
    /**
     * Actual numbers were obtained using MS Excel,
     * refer to "test data.xlsx" file in the tests folder
     */
    public function testAnovaWithFourReplications()
    {
        $sut = new ANOVA(
            array(32,45,67,67,56,89,56,23,57,67,56,79,45,78,56,89),4
        );
        $this->assertEquals(30.25, $sut->MSC(), '', .1);
        $this->assertEquals(529, $sut->MSR(), '', .1);
        $this->assertEquals(1, $sut->MSRC(), '', .1);
        $this->assertEquals(.0784, $sut->Fc(), '', .1);
        $this->assertEquals(1.371, $sut->Fr(), '', .1);
        $this->assertEquals(.0025, $sut->Frc(), '', .1);
        $this->assertEquals(.7842, $sut->Pc(), '', .1);
        $this->assertEquals(.2643, $sut->Pr(), '', .1);
        $this->assertEquals(.9602, $sut->Prc(), '', .1);
    }
} 