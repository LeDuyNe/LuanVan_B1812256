<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\CategoryRequests;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Exams;
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
            if (!empty($validated_request['note']) && empty($validated_request['is_published'])) {
                $category = Category::create([
                    'name' => $name_category,
                    'note' =>   $validated_request['note'],
                    'creatorId' => $userId
                ]);
                $this->setData($category);
            } elseif (empty($validated_request['note']) && !empty($validated_request['is_published'])) {
                $category = Category::create([
                    'name' => $name_category,
                    'is_published' =>   $validated_request['is_published'],
                    'creatorId' => $userId
                ]);
                $this->setData($category);
            } elseif (!empty($validated_request['note']) && !empty($validated_request['is_published'])) {
                $category = Category::create([
                    'name' => $name_category,
                    'note' =>   $validated_request['note'],
                    'is_published' =>   $validated_request['is_published'],
                    'creatorId' => $userId
                ]);
                $this->setData($category);
            } else {
                $category = Category::create([
                    'name' => $name_category,
                    'creatorId' => $userId
                ]);
                $this->setData($category);
            }

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

        $userId = auth()->id();

        if (!empty($validated_request['name'])) {
            $name_category = Str::lower($validated_request['name']);
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
        } else {
            $category = Category::where('id', $validated_request['id'])->update($request->all());
            $this->setStatus('200');
            $this->setMessage("Update category successfully.");

            return $this->respond();
        }
    }


    public function deleteCategory(CategoryRequests $request)
    {
        $validated_request = $request->validated();

        $categoryId = $validated_request['id'];
        $category = Category::FindOrFail($categoryId);
 
        $examId = Exams::where(['categoryId' =>  $categoryId])->pluck('id')->toArray();
        if($examId){
            $this->setStatus('400');
            $this->setMessage("Failed, you have to delete exams before deleting a category!");
            return $this->respond();
        }else{
            if ($category->delete()) {
                $this->setStatus('200');
                $this->setMessage("Delete successfully");
    
                return $this->respond();
            }
            $this->setMessage("Delete Failed");
    
            return $this->respond();
        }
    }

    public function activeCategory(CategoryRequests $request)
    {
        $validated_request = $request->validated();

        $category = Category::where('id', $validated_request['id'])->where('creatorId', auth()->id())->update([
            "is_published" => 1
        ]);

        if ($category) {
            $this->setStatus('200');
            $this->setMessage("Active category successfully!");

            return $this->respond();
        }
        $this->setStatus('400');
        $this->setMessage("Active category failed!");

        return $this->respond();
    }
}
