<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 上午9:39
 */

namespace Dilab\Cart\Rules;

class AgeRule
{
    const INFINITE = '~';

    const GREATER_AND_EQUAL = '>=';

    const LESS_AND_EQUAL = '<=';

    const BETWEEN_AND_EQUAL = '><=';

    const EQUALS = '=';

    private $allowedAge;

    private $comp = '';

    private $from = 0;

    private $to = self::INFINITE;

    public function __construct($allowedAge)
    {
        $this->allowedAge = $allowedAge;
        $this->parseAge();
    }

    private function parseAge()
    {
        if (is_array($this->allowedAge)) {
            $this->comp = $this->allowedAge['comp'];
            $this->from = $this->allowedAge['from'];
            $this->to = $this->allowedAge['to'];

            if ($this->allowedAge['comp'] == self::GREATER_AND_EQUAL) {
                $this->to = $this::INFINITE;
            } elseif ($this->allowedAge['comp'] == self::LESS_AND_EQUAL) {
                // regardless of compare's value it is 'from' while there is only one edge
                $this->to = $this->from;
                $this->from = 1;
            }

            return true;
        }

        if (1 != preg_match('/^(>|<|=)+(.)+/', $this->allowedAge)) {
            throw RuleException::invalidAgeRuleString($this->allowedAge);
        }

        $matches = array();
        preg_match('/^(>|<|=)+/', $this->allowedAge, $matches);
        $this->comp = $matches[0];
        $compareTo = str_replace($this->comp, '', $this->allowedAge);

        switch ($this->comp) {
            case self::GREATER_AND_EQUAL:
                $this->from = intval($compareTo);
                break;
            case self::LESS_AND_EQUAL:
                $this->to = intval($compareTo);
                break;
            case self::BETWEEN_AND_EQUAL:
                $compVal = explode(':', $compareTo);
                $this->from = $compVal[0];
                $this->to = $compVal[1];
                break;
            case self::EQUALS:
                $this->from = intval($compareTo);
                $this->to = intval($compareTo);
                break;
            default:
                throw RuleException::invalidAgeRuleString($this->allowedAge);
        }
        return true;
    }

    public function match($age)
    {
        if ($this::INFINITE == $this->to()) {
            return $age >= $this->from();
        }

        return ($age >= $this->from() && $age <= $this->to());
    }

    public function comp()
    {
        return $this->comp;
    }

    public function from()
    {
        return $this->from;
    }

    public function to()
    {
        return $this->to;
    }

    public function describe()
    {
        switch ($this->comp) {
            case self::GREATER_AND_EQUAL:
                if (1 == $this->from()) {
                    return 'Any age';
                }
                return sprintf('%s years old & above', $this->from());
                break;
            case self::LESS_AND_EQUAL:
                return sprintf('%s years old & below', $this->to());
                break;
            case self::BETWEEN_AND_EQUAL:
                return sprintf('From %s years old to %s years old', $this->from(), $this->to());
                break;
            case self::EQUALS:
                return sprintf('From %s years old to %s years old', $this->from(), $this->to());
                break;
            default:
                throw RuleException::invalidDescription('unable to describe age rule');
        }
    }

    public static function rules()
    {
        return [
            self::GREATER_AND_EQUAL => 'and above',
            self::LESS_AND_EQUAL => 'and below',
            self::BETWEEN_AND_EQUAL => 'to',
        ];
    }
}
