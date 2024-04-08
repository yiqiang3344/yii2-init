<?php

namespace common\helper;

class ArrayHelper extends \yiqiang3344\yii2_lib\helper\ArrayHelper
{
    /**
     * 无限极分类生成树
     * @param $items
     * @param $idKey
     * @param $parentIdKey
     * @param string $nodeName
     * @return array
     */
    public static function generateTree($items, $idKey, $parentIdKey, $nodeName = "child")
    {
        $tree = array();
        foreach ($items as $item) {
            if (isset($items[$item[$parentIdKey]])) {
                $items[$item[$parentIdKey]][$nodeName][] = &$items[$item[$idKey]];
            } else {
                $tree[] = &$items[$item[$idKey]];
            }
        }
        return $tree;
    }

    public static function handleSelectTree(&$tree, $map)
    {
        foreach ($tree as &$item) {
            if (empty($item['select']) && (isset($map[$item['id']]) || !empty($item['is_public']))) {
                $item['select'] = 1;
            } else {
                $item['select'] = 0;
            }
            if (!empty($item['child'])) {
                self::handleSelectTree($item['child'], $map);
            }
        }
    }

    public static function removeUnSelectTree(&$tree)
    {
        foreach ($tree as $k => &$item) {
            if (empty($item['select'])) {
                unset($tree[$k]);
            }
            unset($item['select']);
            if (!empty($item['child'])) {
                self::removeUnSelectTree($item['child']);
            }
        }
    }
}