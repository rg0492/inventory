<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|unique:products|max:255',
                'description' => 'nullable',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id'
            ]);
            DB::beginTransaction();

            // Create a new product using the validated data
            $product = new Product;
            $product->name = $validatedData['name'];
            $product->description = $validatedData['description'];
            $product->price = $validatedData['price'];
            $product->category_id = $validatedData['category_id'];
            if($product->save()){
                if ($request->hasFile('images')) {
                    $file_path =config('constants.IMAGE_PATH');
    
                    if (!file_exists($file_path)) {
                        mkdir($file_path, 0777, true);
                    }
                    $product_photos = $request->images;
                    $insert_photo = array();
                    foreach ($product_photos as $photos) {
                        if (empty($photos)) {
                            continue;
                        }
                        $image_name = $photos->getClientOriginalName();
                        $image_name = str_replace(' ', '_', $image_name); 
                        $image_name = preg_replace('/[^A-Za-z0-9.\-]/', '', $image_name);
                        $ann_photo = time() . '_' . $image_name;
    
                        $photos->move($file_path, $ann_photo);
    
                        $temp_photo = array();
                        $temp_photo['product_id'] = $product->id;
                        $temp_photo['image'] = $ann_photo;
                        $temp_photo['created_at'] = date('Y-m-d H:i:s');
                        $temp_photo['updated_at'] = date('Y-m-d H:i:s');
                        $insert_photo[] = $temp_photo;
                    }
    
                    if (!empty($insert_photo)) {
                        ProductImage::insert($insert_photo);
                    }
                }
            }
            DB::commit();
            // Redirect the user back with a success message
                return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       $product = Product::with('images')->find($id);
       $categories = Category::all();
       return view('products.edit',compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);
            $product->update($validatedData);


            if ($request->hasFile('images')) {
                $file_path =config('constants.IMAGE_PATH');

                if (!file_exists($file_path)) {
                    mkdir($file_path, 0777, true);
                }
                $product_photos = $request->images;
                $insert_photo = array();
                foreach ($product_photos as $photos) {
                    if (empty($photos)) {
                        continue;
                    }
                    $image_name = $photos->getClientOriginalName();
                    $image_name = str_replace(' ', '_', $image_name); 
                    $image_name = preg_replace('/[^A-Za-z0-9.\-]/', '', $image_name);
                    $ann_photo = time() . '_' . $image_name;

                    $photos->move($file_path, $ann_photo);

                    $temp_photo = array();
                    $temp_photo['product_id'] = $product->id;
                    $temp_photo['image'] = $ann_photo;
                    $temp_photo['created_at'] = date('Y-m-d H:i:s');
                    $temp_photo['updated_at'] = date('Y-m-d H:i:s');
                    $insert_photo[] = $temp_photo;
                }

                if (!empty($insert_photo)) {
                    ProductImage::insert($insert_photo);
                }
            }

            DB::commit();

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    /** product list**/
    public function productList(Request $request)
    {
        try {
            $columns = array(
                0 => 'id',
                1 => 'name',
                2 => 'description',
                3 => 'price',
                4 => 'category',
                5 => 'action'
            );
    
            $totalData = Product::count();
            $totalFiltered = $totalData;
    
            $limit = $request->input('length');
            $start = $request->input('start');
            $orderColumnIndex = $request->input('order.0.column');
            $orderColumnName = $columns[$orderColumnIndex];
            $orderDirection = $request->input('order.0.dir');
    
            $postsQuery = Product::select('products.*', 'categories.name AS category_name')
                 ->join('categories', 'products.category_id', '=', 'categories.id');

            
            // Apply search filter
            $search = $request->input('search.value');
            if ($search) {
                $postsQuery->where(function ($query) use ($search) {
                    $query->where('products.name', 'LIKE', "%{$search}%")
                        ->orWhere('products.description', 'LIKE', "%{$search}%")
                        ->orWhere('products.price', 'LIKE', "%{$search}%")
                        ->orWhere('categories.name', 'LIKE', "%{$search}%");
                });
            }
    
            // Get filtered count
            $totalFiltered = $postsQuery->count();
    
            // Order and paginate
            $posts = $postsQuery->offset($start)
                ->limit($limit)
                ->orderBy($orderColumnName, $orderDirection)
                ->get();
    
            $data = array();
            foreach ($posts as $post) {
                $nestedData['id'] = $post->id;
                $nestedData['name'] = $post->name;
                $nestedData['description'] = $post->description;
                $nestedData['price'] = $post->price;
                $nestedData['category'] = $post->category_name;
                $nestedData['action'] = '<a href="'.route('products.edit',['product'=>$post->id]).'"><i class="fa-solid fa-pen-to-square"></i></a>';
                $data[] = $nestedData;
            }
    
            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );
    
            return response()->json($json_data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}
