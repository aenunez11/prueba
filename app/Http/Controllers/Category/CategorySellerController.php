<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategorySellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Category $category)
    {
        $seller = $category->products()
            ->with('seller')->orderBy('seller_id')->get()->pluck('seller');

        return $this->showAll($seller);
    }


}
