<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        // authorize
        Gate::authorize('viewAny', Category::class);
        // fetch all categories
        $categories = Category::paginate(10);
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // authorize
        Gate::authorize('create', Category::class);
        // validate request
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        // create new category
        $category = Category::create($data);
        return $this->json(CategoryResource::make($category), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): CategoryResource
    {
        // authorize
        Gate::authorize('view', $category);
        // fetch category
        return CategoryResource::make($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // authorize
        Gate::authorize('update', $category);
        // validate
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        // update category
        $category->update($data);
        return $this->json(CategoryResource::make($category));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        // authorize
        Gate::authorize('delete', $category);
        // delete category
        $category->delete();
        return $this->json(null, 204);
    }
}
