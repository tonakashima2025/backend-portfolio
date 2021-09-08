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

    public function update(Request $request, Category $category) {

        $category->update($request->all());

        return $category;
    }

}
