<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index(Category $category)
    {
        $transaction=$category->products()
            ->with('transactions')
            ->get()->pluck('transactions')
            ->collapse()->unique('id')->values();

        return $this->showAll($transaction);
    }


}
