<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 28.01.14
 * Time: 10:07
 * To change this template use File | Settings | File Templates.
 */

function generateDropDown($dataList,$valueKey = false,$textKey = false,$defaultValue = false,$currentValue = false,$name = false,$id = false,$class = false, $optionClass = false)
{
    $html = '';

    $html .= '<select name="'.$name.'" ';
    if($id)
    {
        $html .= ' id="'.$id.'" ';
    }

    if($class)
    {

        if(is_array($class))
        {

            $html .= ' class="';

             foreach($class as $c)
             {
                 $html .= $c.' ' ;
             }

            $html .= '"';

        }
        else
        {
            $html .= ' class="'.$class.'" ';
        }
    }

    $html .= ' >';

    foreach($dataList as $key => $value)
    {

        if(!$valueKey)
        {



            $html .= '<option value="'.$key.'" ';

            if($optionClass)
            {
                $html .= ' class="'.$optionClass.'" ';
            }

            if($defaultValue AND $key==$defaultValue)
            {
                $html .= ' selected="selected" ';
            }

            if($currentValue AND ((!is_array($currentValue) AND $key==$defaultValue) OR (is_array($currentValue) AND in_array($key,$currentValue))) )
            {
                $html .= ' selected="selected" ';
            }
        }
        elseif(isset($value[$valueKey]))
        {
            $html .= '<option value="'.$value[$valueKey].'" ';

            if($optionClass)
            {
                $html .= ' class="'.$optionClass.'" ';
            }

            if($defaultValue AND $value[$valueKey]==$defaultValue)
            {
                $html .= ' selected="selected" ';
            }

            if($currentValue AND ((!is_array($currentValue) AND $value[$valueKey]==$defaultValue) OR (is_array($currentValue) AND in_array($value[$valueKey],$currentValue))) )
            {
                $html .= ' selected="selected" ';
            }
        }

        if(!$textKey)
        {
            $html .= ' >'.$key.'</option>';
        }
        elseif(isset($dataList[$textKey]))
        {
            $html .= ' >'.$dataList[$textKey].'</option>';
        }

        $html .= '</select>';

        return $html;

    }
}
