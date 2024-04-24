<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RestaurantController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $restaurant = new Restaurant();

        // Recupero le categorie da passare al form
        $categories = Category::select('label', 'id')->get();

        return view('admin.restaurants.create', compact('restaurant', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validazione e messaggi
        $request->validate([
            'restaurant_name' => 'required|string|min:5|max:50',
            'address' => 'required|string|min:5|max:50',
            'phone' => 'string|min:10|max:15|nullable',
            'vat' => 'required|unique:restaurants|string|min:13|max:13',
            'categories' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:png,jpg,jpeg'
        ], [
            'restaurant_name.required' => 'Il nome del ristorante è obbligatorio',
            'restaurant_name.min' => 'Il nome non può essere più corto di :min caratteri',
            'restaurant_name.max' => 'Il nome non può essere più corto di :max caratteri',
            'address.required' => 'L\'indirizzo del ristorante è obbligatorio',
            'address.min' => 'L\'indirizzo del ristorante non può contenere meno di :min caratteri',
            'address.max' => 'L\'indirizzo del ristorante non può contenere più di :max caratteri',
            'phone.min' => 'Il numero di telefono non può avere meno di :min cifre',
            'phone.max' => 'Il numero di telefono non può avere più di :max cifre',
            'vat.required' => 'La P.IVA è obbligatoria',
            'vat.unique' => 'P.IVA già usata',
            'vat.min' => 'La P.IVA non può contenere meno di :min cifre',
            'vat.max' => 'La P.IVA non può contenere più di :max cifre',
            'categories.required' => 'Categoria obbligatoria',
            'categories.exists' => 'Categoria non valida',
            'image.image' => 'Il file inserito non è un\'immagine',
        ]);

        $data = $request->all();

        $restaurant = new Restaurant();

        $restaurant->fill($data);

        $restaurant->slug = Str::slug($data['restaurant_name']);

        // Salvataggio dell'immagine nel database
        if (Arr::exists($data, 'image')) {
            $extension = $data['image']->extension();

            $img_url = Storage::putFileAs('restaurant_images', $data['image'], "$restaurant->slug.$extension");
            $restaurant->image = $img_url;
        }

        // Nel Ristorante collego lo user_id
        $restaurant->user_id = Auth::user()->id;

        $restaurant->save();

        if (Arr::exists($data, 'categories')) $restaurant->categories()->attach($data['categories']);

        return to_route('admin.restaurants.show', $restaurant->id)->with('type', 'success')->with('message', "Ristorante: $restaurant->restaurant_name aggiunto");
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant)
    {
        // Recupero le categorie del Ristorante e le trasforma in un array da mandare al form
        $prev_categories = $restaurant->categories->pluck('id')->toArray();

        // Recupero le categorie da passare al form
        $categories = Category::select('label', 'id')->get();

        return view('admin.restaurants.edit', compact('restaurant', 'categories', 'prev_categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant)
    {

        // Validazione e messaggi
        $request->validate([
            'restaurant_name' => 'required|string|min:5|max:50',
            'address' => 'required|string|min:5|max:50',
            'phone' => 'string|min:10|max:15|nullable',
            'email' => 'email|string|lowercase|nullable',
            'vat' => ['required', 'string', 'min:13', 'max:13', Rule::unique('restaurants')->ignore($restaurant->id)],
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'categories' => 'required|exists:categories,id'
        ], [
            'restaurant_name.required' => 'Il nome del ristorante è obbligatorio',
            'restaurant_name.min' => 'Il nome non può essere più corto di :min caratteri',
            'restaurant_name.max' => 'Il nome non può essere più corto di :max caratteri',
            'address.required' => 'L\'indirizzo del ristorante è obbligatorio',
            'address.min' => 'L\'indirizzo del ristorante non può contenere meno di :min caratteri',
            'address.max' => 'L\'indirizzo del ristorante non può contenere più di :max caratteri',
            'phone.min' => 'Il numero di telefono non può avere meno di :min cifre',
            'phone.max' => 'Il numero di telefono non può avere più di :max cifre',
            'email.email' => 'L\'email inserita non è valida',
            'email.lowercase' => 'L\'email non può contenere lettere maiuscole',
            'vat.required' => 'La P.IVA è obbligatoria',
            'vat.min' => 'La P.IVA non può contenere meno di :min cifre',
            'vat.max' => 'La P.IVA non può contenere più di :max cifre',
            'image.image' => 'Il file inserito non è un\'immagine',
            'categories.required' => 'Categoria obbligatoria',
            'categories.exists' => 'Categoria non valida'
        ]);

        $data = $request->all();

        $restaurant->slug = Str::slug($data['restaurant_name']);

        $restaurant->fill($data);

        // Modifica dell'immagine
        if (Arr::exists($data, 'image')) {

            if ($restaurant->image) Storage::delete($restaurant->image);

            $extension = $data['image']->extension();

            $img_url = Storage::putFileAs('restaurant_images', $data['image'], "$restaurant->slug.$extension");
            $restaurant->image = $img_url;
        }

        $restaurant->update();

        if (Arr::exists($data, 'categories')) $restaurant->categories()->sync($data['categories']);
        elseif (!Arr::exists($data, 'categories') && $restaurant->has('categories')) $restaurant->categories()->detach();

        return to_route('admin.restaurants.show', $restaurant->id)->with('type', 'success')->with('message', "Ristorante: $restaurant->restaurant_name modificato");
    }
}
