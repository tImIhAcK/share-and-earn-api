<?php

require_once PROJECT_ROOT_PATH . "/Model/QueryModel.php";

class CategoryModel extends QueryModel
{
    public static function getCategory(){
        return QueryModel::query('SELECT * FROM category where status=:status ORDER BY id ASC',
        array(':status'=>1));
    }   
}


class ProductModel extends QueryModel
{
    public static function getProduct()
    {
        return QueryModel::query('SELECT * FROM product where status=:status ORDER BY rand()',
        array(':status'=>1)
        );
    }
}