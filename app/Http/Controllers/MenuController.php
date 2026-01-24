<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::with('menuItems')->orderBy('sort_order')->get();
        return view('pos.menu.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'name_my' => 'nullable',
        ]);

        Category::create($request->only(['name', 'name_my']));
        return back();
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
            'name_my' => 'nullable',
        ]);

        $category->update($request->only(['name', 'name_my']));

        return back();
    }
    
    public function destroyCategory(Category $category)
    {
        $category->delete(); // cascade deletes items
        return back();
    }

    public function createItem()
    {
        $categories = Category::all();
        return view('pos.menu.create_item', compact('categories'));
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'name_my' => 'nullable',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'description_my' => 'nullable',
            'image' => 'nullable|image',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu', 'public');
            $data['image_path'] = $path;
        }

        MenuItem::create($data);

        return redirect()->route('pos.menu.index');
    }

    public function editItem(MenuItem $menuItem)
    {
        $categories = Category::all();
        return view('pos.menu.edit_item', compact('menuItem', 'categories'));
    }

    public function updateItem(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'name_my' => 'nullable',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'description_my' => 'nullable',
            'image' => 'nullable|image',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu', 'public');
            $data['image_path'] = $path;
        }

        $menuItem->update($data);

        return redirect()->route('pos.menu.index');
    }
    
    public function toggleItem(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);
        return back();
    }
    
    public function destroyItem(MenuItem $menuItem)
    {
        $menuItem->delete();
        return back();
    }
}
