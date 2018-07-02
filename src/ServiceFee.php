<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/6/19
 * Time: 下午3:49
 */

namespace Dilab\Cart;

class ServiceFee
{
    private $percentage;

    private $fixed;

    /**
     * ServiceFee constructor.
     *
     * @param $percentage
     * @param $fixed
     */
    public function __construct($percentage, Money $fixed)
    {
        $this->percentage = $percentage;
        $this->fixed = $fixed;
    }

    /**
     * @return mixed
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @return Money
     */
    public function getFixed()
    {
        return $this->fixed;
    }
}
