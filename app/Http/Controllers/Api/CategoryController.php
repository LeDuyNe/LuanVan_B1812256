<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\CategoryRequests;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends AbstractApiController
{
    public function getCategories()
    {
        $categories = CategoryResource::collection(Category::where('creatorId', auth()->id())->get());
        $this->setData($categories);
        $this->setStatus('200');
        $this->setMessage("List all categories");

        return $this->respond();
    }

    public function getCategorie(CategoryRequests $request)
    {
        $validated_request = $request->validated();

        $categorie = new CategoryResource(Category::findOrFail($validated_request['id']));

        $this->setData($categorie);
        $this->setStatus('200');

        return $this->respond();
    }

    public function createCategory(CategoryRequests $request)
    {
        $validated_request = $request->validated();

        $name_category = Str::lower($validated_request['name']);
        $userId = auth()->id();

        $checkCategory = Category::where(['creatorId' => $userId, 'name' => $name_category])->first();
        if (!$checkCategory) {
            $category = Category::create([
                'name' => $name_category,
                'creatorId' => $userId
            ]);

            $this->setData($category);
            $this->setStatus('200');
            $this->setMessage("Create category successfully.");

            return $this->respond();
        }
        $this->setStatus('400');
        $this->setMessage("Category is existed");

        return $this->respond();
    }

    public function updateCategory(CategoryRequests $request)
    {
        $validated_request = $request->validated();
        $name_category = Str::lower($validated_request['name']);
        $userId = auth()->id();

        $checkCategory = Category::where(['creatorId' => $userId, 'name' => $name_category])->first();

        if (!$checkCategory) {
            $category = Category::where('id', $validated_request['id'])->update($request->all());

            $this->setStatus('200');
            $this->setMessage("Update category successfully.");

            return $this->respond();
        }
        $this->setStatus('400');
        $this->setMessage("Category is existed");

        return $this->respond();
    }


    public function deleteCategory(CategoryRequests $request)
    {
        $validated_request = $request->validated();

        $category = Category::FindOrFail($validated_request['id']);
        if ($category->delete()) {
            $this->setStatus('200');
            $this->setMessage("Delete successfully");

            return $this->respond();
        }
        $this->setMessage("Delete Failed");

        return $this->respond();
    }
}
