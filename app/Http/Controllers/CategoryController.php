<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        return view('livewire.category.index');
    }

    /**
     * Show the specified category.
     */
    public function show(Category $category)
    {
        return view('livewire.category.show', compact('category'));
    }

    /**
     * Get categories as JSON (for API endpoints if needed)
     */
    public function api(Request $request)
    {
        $query = Category::query();

        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null' || $request->parent_id === null) {
                $query->topLevel();
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        if ($request->has('active')) {
            $query->where('is_active', (bool) $request->active);
        }

        $categories = $query->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}
