<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductImage;

class ProductImageController extends Controller
{
    public function removeImage(Request $request)
    {
        ProductImage::where('id',$request->id)->delete();
        // Set success message in session flash data
        $request->session()->flash('success','Image removed successfully');
        // Return response (optional)
        return response()->json(['success' => true]);
    }
}
