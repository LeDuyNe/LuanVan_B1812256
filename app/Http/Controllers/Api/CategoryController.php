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

        $categorieId = Category::where('uuid', $validated_request['id'])->pluck('id');
        $categorie = new CategoryResource(Category::findOrFail($categorieId[0]));

        $this->setData($categorie);
        $this->setStatus('200');

        return $this->respond();
    }

    public function createCategory(CategoryRequests $request)
    {
        $validated_request = $request->validated();

        $nameCategory = Str::lower($validated_request['name']);
        $userId = auth()->id(); 

        $checkCategory = Category::where(['creatorId' => $userId, 'name' => $nameCategory])->first();
        if (!$checkCategory) {
            if (!empty($validated_request['note']) && empty($validated_request['isPublished'])) {
                $category = Category::create([
                    'name' => $nameCategory,
                    'note' =>   $validated_request['note'],
                    'creatorId' => $userId,
                    'uuid' => Str::uuid()->toString()
                ]);
                $this->setData(new CategoryResource($category));
            } elseif (empty($validated_request['note']) && !empty($validated_request['isPublished'])) {
                $category = Category::create([
                    'name' => $nameCategory,
                    'isPublished' =>   $validated_request['isPublished'],
                    'creatorId' => $userId,
                    'uuid' => Str::uuid()->toString()
                ]);
                $this->setData(new CategoryResource($category));
            } elseif (!empty($validated_request['note']) && !empty($validated_request['isPublished'])) {
                $category = Category::create([
                    'name' => $nameCategory,
                    'note' =>   $validated_request['note'],
                    'isPublished' =>   $validated_request['isPublished'],
                    'creatorId' => $userId,
                    'uuid' => Str::uuid()->toString()
                ]);
                // dd($category);
                $this->setData(new CategoryResource($category));
            } else {
                $category = Category::create([
                    'name' => $nameCategory,
                    'creatorId' => $userId,
                    'uuid' => Str::uuid()->toString()
                ]);
                $this->setData(new CategoryResource($category));
            }

            $this->setStatus('200');
            $this->setMessage("Create category successfully.");

            return $this->respond();
        }else{
            $this->setStatus('400');
            $this->setMessage("Category is existed");
        }
        return $this->respond();
    }

    public function updateCategory(CategoryRequests $request)
    {
        $validated_request = $request->validated();

        $userId = auth()->id();
        $categorieId = Category::where('uuid', $validated_request['id'])->pluck('id');
        
        if (!empty($validated_request['name'])) {
            $nameCategory = Str::lower($validated_request['name']);
            $checkCategory = Category::where(['creatorId' => $userId, 'name' => $nameCategory])->first();

            if (!$checkCategory) {
                Category::where('id', $categorieId[0])->update($validated_request);
                // dd($Category[0]);
                $this->setStatus('200');
                $this->setMessage("Update category successfully.");

                return $this->respond();
            }
            $this->setStatus('400');
            $this->setMessage("Category is existed");

            return $this->respond();
        } else {
            Category::where('id', $categorieId[0])->update($validated_request);
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
            "isPublished" => 1
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
