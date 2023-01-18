<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $data['variants'] = Variant::with('product_variants')->select(['id', 'title'])->latest()->get();

        $data['products'] = Product::with('product_variant_prices.variant_one', 'product_variant_prices.variant_two', 'product_variant_prices.variant_three')->latest()->paginate(2);
        return view('products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
           $product = Product::create([
            'title'=>$request->title,
            'sku'=>$request->sku,
            'description'=>$request->description,
           ]);

           if($request->has('product_variant')){
                foreach($request->product_variant as $varant){

                    foreach ($varant['tags'] as $tag) {
                        ProductVariant::create([
                            'variant'=>$tag,
                            'product_id'=>$product->id,
                            'variant_id'=>$varant['option'],
                        ]);
                    }
                }
           }


           foreach($request->product_variant_prices as $productVariantPrice){
                $title = explode('/', $productVariantPrice['title']);
                 $productVariants = ProductVariant::whereIn('variant', $title)->where(['product_id'=>$product->id])->pluck('id');
                $variantOne = (sizeof($productVariants)>=1) ? $productVariants[0] : null;
                $variantTwo = (sizeof($productVariants)>=2) ? $productVariants[1] : null;
                $variantThree = (sizeof($productVariants)>=3) ? $productVariants[2] : null;
                ProductVariantPrice::create([
                    'product_variant_one'=>$variantOne,
                    'product_variant_two'=>$variantTwo,
                    'product_variant_three'=>$variantThree,
                    'price'=>$productVariantPrice['price'],
                    'stock'=>$productVariantPrice['stock'],
                    'product_id'=>$product->id
                ]);

           }


           DB::commit();

           return redirect()->route('product.index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::info($exception->getMessage());
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        $variantInfo = [];

        $product = Product::with(['variants'])->findOrFail($product->id);

        foreach ($variants as $variant) {
            $productVariants = ProductVariant::where(['product_id'=>$product->id, 'variant_id'=>$variant->id])->get();

            array_push($variantInfo, [
                'variant'=>$variant,
                'product_variants'=>$productVariants
            ]);
        }

        $productVariantPrices =  ProductVariantPrice::with(['variant_one', 'variant_two', 'variant_three'])->where(['product_id'=>$product->id])->get();
        
        return view('products.edit', compact('product', 'variantInfo', 'variants', 'productVariantPrices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function search(Request $request){
        if($request->ajax()){
            $data['products'] = Product::with('variants', 'product_variant_prices')->where(function($query) use($request) {
                if($request->title !== null && $request->variant === null && $request->price_from === null && $request->price_to === null && $request->date === null)
                {
                    $query->where('title','LIKE','%'.$request->title.'%');
                }
                if($request->variant !== null && $request->title === null && $request->price_from === null && $request->price_to === null && $request->date === null)
                {
                    $query->whereHas('variants', function($query) use($request){
                        $query->where('variant', 'LIKE','%'.$request->variant.'%');
                    });
                }
                if($request->price_from !== null && $request->title === null && $request->variant === null && $request->price_to === null && $request->date === null){
                    $query->whereHas('product_variant_prices', function($query) use($request){
                        $query->where('price', '>=', $request->price_from);
                    });
                }
                if($request->price_to !== null && $request->title === null && $request->variant === null && $request->price_from === null && $request->date === null){
                    $query->whereHas('product_variant_prices', function($query) use($request){
                        $query->where('price', '<=', $request->price_to);
                    });
                }
                if($request->date !== null && $request->title === null && $request->variant === null && $request->price_from === null && $request->price_from === null){
                    $query->whereDate('created_at', '=', date($request->date));
                }

                if($request->title !== null && $request->variant !== null && $request->price_from === null && $request->price_to === null && $request->date === null){
                    $query->where('title','LIKE','%'.$request->title.'%')
                        ->whereHas('variants', function($query) use($request){
                            $query->where('variant', 'LIKE','%'.$request->variant.'%');
                        });
                }
                if($request->title !== null && $request->variant === null && $request->price_from !== null && $request->price_to !== null && $request->date === null){
                    $query->where('title','LIKE','%'.$request->title.'%')->whereHas('product_variant_prices', function($query) use($request){
                        $query->whereBetween('price', [$request->price_from, $request->price_to]);
                    });
                }
                if($request->title !== null && $request->date !== null && $request->variant === null && $request->price_from === null && $request->price_to === null){
                    $query->where('title','LIKE','%'.$request->title.'%')->whereDate('created_at', '=', date($request->date));
                }
                if($request->title !== null && $request->date !== null &&  $request->price_from !== null && $request->price_to !== null && $request->variant === null){
                    $query->whereHas('product_variant_prices', function($query) use($request){
                        $query->whereBetween('price', [$request->price_from, $request->price_to]);
                    })
                    ->where('title','LIKE','%'.$request->title.'%')
                    ->whereDate('created_at', '=', date($request->date));
                }
                if($request->title !== null && $request->date !== null && $request->variant !== null &&  $request->price_from === null && $request->price_to === null){
                    $query->whereHas('variants', function($query) use($request){
                        $query->where('variant', 'LIKE','%'.$request->variant.'%');
                    })
                    ->where('title','LIKE','%'.$request->title.'%')
                    ->whereDate('created_at', '=', date($request->date));
                }
                if($request->title !== null && $request->date !== null &&  $request->price_from !== null && $request->price_to !== null && $request->variant !== null){
                    $query->whereHas('product_variant_prices', function($query) use($request){
                        $query->whereBetween('price', [$request->price_from, $request->price_to]);
                    })
                    ->whereHas('variants', function($query) use($request){
                        $query->where('variant', 'LIKE','%'.$request->variant.'%');
                    })
                    ->where('title','LIKE','%'.$request->title.'%')
                    ->whereDate('created_at', '=', date($request->date));
                }

            })->latest()->get();

            return response()->view('products.search-result', $data);

        }
    }

    public function fileEdit(Request $request){
        return $image = $request->file('file');
    }

    public function imageUpload(){
        return request()->file;
    }
}
