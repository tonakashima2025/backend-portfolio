<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index() {

        return Category::all();
    
    }

    public function store(Request $request){

        return Category::create($request->all());
    
    }

    public function update(Request $request, $update_category) {

        $category = Category::find($update_category);
        $category->name = $request->name;
        $category->collapsed = 1;
        $category->save();
        // update($request->all());

        return $category;
    }

    public function delete($category_id) {
        
        $category = Category::find($category_id);
        $category->delete();
    
        return $category;
    }

}
