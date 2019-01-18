<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\Transformers\ProductTransformer;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:'. ProductTransformer::class)->only(['store','update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $productos = $seller->products;

        return $this->showAll($productos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image'
        ];


        $this->validate($request,$rules);

        $data = $request->all();

        $data['status'] = Product::PRODUCT_NO_DISPONIBLE;
        $data['image'] = $request->image->store('');
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product,201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in: '. Product::PRODUCT_NO_DISPONIBLE.','. Product::PRODUCT_DISPONIBLE,
            'image' => 'image'
        ];

        $this->validate($request,$rules);

        $this->verificarVendedor($seller,$product);

        $product->fill($request->only([
            'quantity',
            'name',
            'description'
        ]));

        if ($request->has('status')){

            $product->status = $request->status;

            if($product->estaDisponible() && $product->categories()->count()==0){
                return $this->errorResponse('Para adiccionar un producto tienes que asiganrle una categoria',409);
            }
        }

        if($request->hasFile('image')){
            Storage::delete($product->image);

            $product->image = $request->image->store('');
        }
        if($product->isClean()){
            return $this->errorResponse('se debe esecificar un valor diferente apra actualizar',422);
        }

        $product->save();

        return $this->showOne($product);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {

        $this->verificarVendedor($seller,$product);

        Storage::delete($product->image);
        $product->delete();

        return$this->showOne($product);
    }

    protected  function verificarVendedor(Seller $seller, Product $product){
        if( $seller->id != $product->seller_id){
            throw new HttpException(422,'No tiene permiso para editar este producto');
        }
    }
}
