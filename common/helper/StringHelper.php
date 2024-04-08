<?php

namespace common\helper;

class StringHelper extends \yiqiang3344\yii2_lib\helper\StringHelper
{
    /**
     * 驼峰命名转下划线命名
     * @param $str
     * @return string
     */
    public static function toUnderScore($str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/', function ($matchs) {
            return '_' . strtolower($matchs[0]);
        }, $str);
        $dstr = preg_replace('/\\\_/', '/', $dstr);
        return trim(preg_replace('/_{2,}/', '_', $dstr), '_');
    }

    /**
     * 驼峰命名转横杠命名
     * @param $str
     * @return string
     */
    public static function toLineScore($str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/', function ($matchs) {
            return '-' . strtolower($matchs[0]);
        }, $str);
        $dstr = preg_replace('/\\\-/', '/', $dstr);
        return trim(preg_replace('/-{2,}/', '-', $dstr), '-');
    }


    /**
     * 匹配表名
     * @param string $sql
     * @return mixed|string
     * @since 1.0.73
     */
    public static function matchTableName(string $sql)
    {
        $table = '';
        try {
            $sqlParse = SQLParser::instance()->parse($sql);
            $table = $sqlParse['FROM'][0]['table'] ?? '';
            if ($table == 'DEPENDENT-SUBQUERY') {
                $table = '';
            }
        } catch (\Throwable $e) {
        }
        if ($table) {
            return $table;
        }
        $sql = strtolower($sql);
        $reg = [
            "/select\\s.+from\\s(.+)where\\s(.*)/",
            "/select\\s.+from\\s(.+)group\\s(.*)/",
            "/select\\s.+from\\s(.+)order\\s(.*)/",
            "/select\\s.+from\\s(.+)limit\\s(.*)/",
            "/select\\s.+from\\s(.+)/",
            "/((select.+?FROM)|(LEFT\\s+JOIN|JOIN|LEFT))[\\s`]+?(\\w+)[\\s`]+?/is",
            "/ \*\s+from\s+[\w\[\]]*\.?[\w\[\]]*\.?\[?(\b\w+)\]?[\r\n\s]*/",
            "/insert\\sinto\\s(.+)\\(.*\\)\\s.*/",
            "/update[\s`]+?(\w+[\.]?\w+)[\s`]+?/is",
            "/delete\\sfrom\\s(.+)where\\s(.*)/",
        ];
        foreach ($reg as $v) {
            $bool = preg_match($v, $sql, $matches);
            if ($bool) {
                return trim($matches[4] ?? ($matches[1] ?? ''));
            }
        }
        return '';
    }
}