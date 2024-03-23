<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Helpers\ResponseHelper;

class ProductController extends Controller
{
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;


    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\ResponseHelper $responseHelper
     */
    public function __construct(
        ResponseHelper $responseHelper

    ) {
        $this->responseHelper = $responseHelper;
    }

    /**list of products**/
    public function index(Request $request)
    {
        // Start building the query
        $query = Product::with('category');

        // Filtering by category
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtering by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        $limit = config('constants.PAGINATION_LIMIT');
        
        if(isset($request->limit)){
            $limit = $request->limit;
        }
        // Retrieve the filtered products
        $products = $query->paginate($limit);

        $apiCode    = 200;
        $apiStatus  = true;
        $apiMessage = 'Product list';
        $apiData    = $products;

        return $this->responseHelper->successWithPagination($apiCode, $apiStatus, $apiMessage, $apiData);
    }
}
