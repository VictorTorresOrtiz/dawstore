<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Image;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class AdminpanelController extends Controller
{
    public function products()
    {
        $products = Product::all(); // Nos saca todos los productos de la BBDD
        return view('admin/admin-panel', @compact('products'));
    }

    public function products_detail()
    {
        $brands = Brand::all();
        $categories = Category::all(); // Nos saca todos las categorias de la BBDD
        return view('admin/product-manager', @compact('categories','brands'));
    }


    public function insert(Request $request)
    {

        $productInsert = new Product;
        $productInsert->sku = $request->sku;
        $productInsert->name = $request->name;
        $productInsert->price = $request->price;
        $productInsert->description = $request->desc;
        $productInsert->stock = $request->stock;
        $productInsert->genre = $request->genre;
        $productInsert->brand_id = $request->brand;
        $productInsert->save();

        $i = 1;
        foreach ($request->images as $image)
        {
            $imagen = new Image();
            $imagen->product_id = $productInsert->id;
            $imagen->image_name = $request->sku."-".$i.".".$image->extension();
            $ruta = public_path("img/products/".$request->sku."-files");
            $image->move($ruta,$imagen->image_name);
            $imagen->save();
            $i++;
        }
        notify()->success('Product created successfully!');
        return back();
    }



    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin/edit-product', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $file_path = Product::findOrFail($id);

        if (File::exists(public_path("img/products/" . $file_path->images))) {
            File::delete(public_path("img/products/" . $file_path->images));
        }

        $productUpdate = Product::findOrFail($id);
        $productUpdate->sku = $request->sku;
        $productUpdate->name = $request->name;
        $productUpdate->price = $request->price;
        $productUpdate->description = $request->description;
        $productUpdate->stock = $request->stock;
        $productUpdate->genre = $request->genre;
        if($request->hasFile("images")){
            $imagen = $request->images;
            $nombreimagen = Str::slug($request->name).".".$imagen->guessExtension();
            $ruta = public_path("img/products/");
            $imagen->move($ruta,$nombreimagen);
            $productUpdate->images = $nombreimagen;
        }
        $productUpdate->update();
        return back()->with('mensaje', 'Producto editado exitosamente');
    }        


    public function delete($id) {
        $deleteProduct = Product::findOrFail($id);
        $deleteImg = Image::where('product_id', $id);
        $deleteImg->delete();
        $deleteProduct->delete();

        notify()->success('Product removed successfully!');
        return back();

    }


/* CATEGORIAS */


    public function category()
    {
        $categories = Category::all(); // Nos saca todas las categorias de la BBDD
        return view('admin/category-manager', @compact('categories'));
    }

    public function insert_category(Request $request)
    {
        $categoryInsert = new Category;
        $categoryInsert->name = $request->name;
        $categoryInsert->description = $request->desc;
        $categoryInsert->save();
        notify()->success('Product Category created Succefully!');
        return back();
    }
    public function edit_category($id)
    {
        $category = Category::findOrFail($id);
        return view('admin/edit-category', compact('category'));
    }
    public function update_category(Request $request, $id)
    {
        $categoryUpdate = Category::findOrFail($id);
        $categoryUpdate->name = $request->name;
        $categoryUpdate->description = $request->description;
        $categoryUpdate->update();
        notify()->success('Product Edited successfully!');
        return back();
    }

    public function delete_category($id) {
        $deleteCategory = Category::findOrFail($id);
        $deleteCategory->delete();

        return notify()->success('Category deleted successfully!');

    }
    
    /* MARCAS */


    public function brand()
    {
        $brands = Brand::all(); // Nos saca todas las marcas de la BBDD
        return view('admin/brand-manager', @compact('brands'));
    }

    public function insert_brand(Request $request)
    {
        $drandInsert = new Brand;
        $drandInsert->name = $request->name;
        $drandInsert->save();
        return back() -> with('mensaje', '');
    }
    public function edit_brand($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin/edit-brand', compact('brand'));
    }
    public function update_brand(Request $request, $id)
    {
        $drandUpdate = Brand::findOrFail($id);
        $drandUpdate->name = $request->name;
        $drandUpdate->update();
        return back()->with('mensaje', '');
    }        

    public function delete_brand($id) {
        $deleteBrand = Brand::findOrFail($id);
        $deleteBrand->delete();
        return back()->with('message', '');
}
}






