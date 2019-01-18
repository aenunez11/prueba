<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryBuyerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Category $category)
    {
        $compradores=$category->products()
            ->whereHas('transactions')
            ->with('transactions.buyer')
            ->get()->pluck('transactions')
            ->collapse()->pluck('buyer')
            ->unique()->values();

        return $this->showAll($compradores);
    }
}
