<?php


namespace Crud;


class CrudHelper
{

    public static function getUpdateFormUrl($table, $ric)
    {
        return url(CrudConfig::getCrudUri() . '?name=' . $table . '&action=edit&ric=' . $ric);
    }

    public static function getInsertFormUrl($table)
    {
        return url(CrudConfig::getCrudUri() . '?name=' . $table . '&action=insert');
    }

    public static function getListUrl($table)
    {
        return url(CrudConfig::getCrudUri() . '?name=' . $table);
    }


    public static function getWhereFragmentFromRic(array $ric, array &$markers)
    {
        $i = 0;
        $q = "(";
        foreach ($ric as $k => $v) {
            if (0 !== $i) {
                $q .= " and ";
            }
            $marker = 'm' . $i++;
            $q .= "$k=:" . $marker;
            $markers[$marker] = $v;
        }
        $q .= ")";
        return $q;
    }
}