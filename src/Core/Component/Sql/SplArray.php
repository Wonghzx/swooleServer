<?php

namespace Core\Component\Sql;

class SplArray extends \ArrayObject
{


    /**
     * __get  [description]
     * @param $name
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     * @return mixed|null
     */
    public function __get($name)
    {
        // TODO: Implement __get() method.
        if (!isset($this[$name])) {
            return null;
        }
        return $this[$name];
    }

    /**
     * getArrayCopy  [复制的数组对象]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     * @return array
     */
    public function getArrayCopy()
    {
        // TODO: Implement getArrayCopy() method.
        $all = parent::getArrayCopy();
        foreach ($all as $key => $item) {
            if ($item instanceof SplArray) {
                $all[$key] = $item->getArrayCopy();
            }
        }
        return $all;
    }

    /**
     * __set  [description]
     * @param $name
     * @param $value
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this[$name] = $value;
    }

    /**
     * set  [description]
     * @param $path
     * @param $value
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     */
    public function set($path, $value)
    {
        $path = explode(".", $path);
        $temp = $this;
        while ($key = array_shift($path)) {
            $temp = &$temp[$key];
        }
        $temp = $value;
    }

    /**
     * get  [description]
     * @param $path
     * @param bool $security
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     * @return null
     */
    public function get($path, $security = false)
    {
        $paths = explode(".", $path);
        $func = function ($data, $pathArr, $security = false) use (&$func) {
            $path = array_shift($pathArr);
            if ($path == "*") {
                if ($security) {
                    if (isset($data['*'])) {
                        return $data["*"];
                    }
                }
                if (!empty($pathArr)) {
                    $temp = [];
                    foreach ($data as $key => $item) {
                        if (is_array($item) && !empty($item)) {
                            $temp[$key] = $func($item, $pathArr, $security);
                        }
                        //对于非数组无下级则不再搜索
                    }
                    return $temp;
                } else {
                    return $data;
                }
            } else {
                if (isset($data[$path])) {
                    if (!empty($pathArr)) {
                        //继续搜索。
                        return $func($data[$path], $pathArr, $security);
                    } else {
                        return $data[$path];
                    }
                } else {
                    return null;
                }
            }
        };
        return $func($this->getArrayCopy(), $paths, $security);
    }


    /**
     * __toString  [转换成一个字符串]
     * @copyright Copyright (c)
     * @author Wongzx <842687571@qq.com>
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return json_encode($this, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);
    }
}