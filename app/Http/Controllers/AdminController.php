<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;


class AdminController extends Controller
{
    public function index() {
        return view('admin.index');
    }

    // ************** Brand Controller *******************
    public function brands() {
        $brands = Brand::orderBy('id' , 'desc')->paginate(10);
        return view('admin.brands' , compact('brands'));
    }

    public function add_brand(){
        return view('admin.brand-add');
    }

    public function brand_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateBrandThumbailsImage($image , $file_name , 'brands');
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status' , 'Brand has been added successfully');
    }

    public function brand_edit($id) {
        $brand = Brand::findOrFail($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
                File::delete(public_path('uploads/brands').'/'.$brand->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateBrandThumbailsImage($image , $file_name , 'brands');
            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('status' , 'Brand has been updated successfully');
    }

    public function brand_delete($id){
        $brand = Brand::findOrFail($id);
        if(File::exists(public_path("uploads/brands").'/'.$brand->image)){
            File::delete(public_path("upload/brands").'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status' , 'Brand has been deleted successfuly!');
    }
    // ************** End Brand Controller ****************

    // ************** Category Controller ****************
    public function categories() {
        $categories = Category::orderBy('id' , 'desc')->paginate(10);
        return view('admin.categories' , compact('categories'));
    }

    public function category_add(){
        return view('admin.category_add');
    }

    public function category_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateBrandThumbailsImage($image , $file_name , 'categories');
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status' , 'Category has been added successfully');
    }

    public function category_edit($id){
        $category = Category::findOrFail($id);
        return view('admin.category_edit' , compact('category'));
    }

    public function category_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories/'.$request->image))){
                File::delete(public_path('uploads/categories/'.$request->image));
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateBrandThumbailsImage($image, $file_name, 'categories');
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('status' , 'Category has been updated successfully');
    }

    public function category_delete($id){
        $category = Category::findOrFail($id);
        if(File::exists(public_path('uploads/categories/'.$category->image))){
            File::delete(public_path('uploads/categories/'.$category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status' , 'Category has been deleted successfuly!');
    }

    public function GenerateBrandThumbailsImage($image, $imageName , $containPhoto)
    {

        $destinationPath = public_path('uploads/'. $containPhoto);
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            // đảm bảo img không bị méo
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }
}

