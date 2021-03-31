<?php

namespace App\Common\Factory;

class DocumentFactory
{
    private $mg;
    private $c;
    public function __construct($c, $database)
    {
        $this->c  = $c;
        $this->mg = $c['mongodb']->{$database};
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getDb()
    {
        return $this->mg;
    }

    public function get($collection)
    {
        $class_name = $this->_getClassName($collection);
        if (count(explode('\\', $class_name)) > 1 && class_exists($class_name)) {
            $class = new $class_name($this->c);
            return $class;
        } else {
            /**
             * @TODO put model path in config file
             */
            if (class_exists($cl = '\App\Model\Custom\Document\\' . $class_name)) {
                $class = new $cl($this->c);
                //->setDebug();
                return $class;
            }
            if (class_exists($cl = '\App\Model\Core\Document\\' . $class_name)) {
                $class = new $cl($this->c);
                //->setDebug();
                return $class;
            } else {
                return $this->mg->{$collection};
            }
        }
        throw new \Exception($class_name . " Not Found");
    }

    protected function _getClassName($_str)
    {
        $str_tab = $_str;
        if (!is_array($_str)) {
            $str_tab = [$_str];
        }
        foreach ($str_tab as &$str) {
            $temp = '';
            // // Remove common prefixes
            // foreach ($this->_tablePrefixes as $prefix) {
            //     if (preg_match("/^$prefix/i", $str)) {
            //         // Only replace a single prefix
            //         $str = preg_replace("/^$prefix/i", '', $str);
            //         break;
            //     }
            // }

            // // Remove common suffixes
            // foreach ($this->_columnSuffixes as $suffix) {
            //     if (preg_match("/$suffix$/i", $str)) {
            //         // Only replace a single prefix
            //         $str = preg_replace("/$suffix$/i", '', $str);
            //         break;
            //     }
            // }

            foreach (explode("_", $str) as $part) {
                $temp .= ucfirst($part);
            }
            $str = $temp;
        }
        return count($str_tab) > 1 ? $str_tab : $str_tab[0];
    }
}
