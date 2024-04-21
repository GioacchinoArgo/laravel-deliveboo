@extends('layouts.app')
 @section('content')
    
    <h1 class="text-center py-3">
        @if (Route::is('admin.dishes.create')) Aggiungi nuovo piatto
        @else Modifica {{$dish->name}} @endif 
    </h1>

    {{-- banner validazione del form  --}}
    @include('admin.form.validation')

    {{--! Impostazioni del form --}}

    @if ($dish->exists)

        <form action="{{route('admin.dishes.update', $dish->id  ) }}" method="post" 
            class="flex container py-4 justify-content-center"
            id="form"
            enctype="multipart/form-data">

        @method('put')

    @else

        <form action="{{route('admin.dishes.store')}}" method="post" 
        class="flex container py-4 justify-content-center"
        id="form"
        enctype="multipart/form-data">

    @endif

    {{--! TOKEN csrf --}}
    @csrf

    <div class="row">

        {{-- INPUT GROUP DEL NAME --}}
        <div class=" mb-3 col-6">

            <label class="form-label label fw-bold" for="name">Titolo:</label>
           
            <input 
                type="text" 
                required 
                id="name" 
                name="name" 
                class="form-control @error('name') is-invalid @elseif(old('name')) is-valid @enderror" 
                value="{{old('name', $dish->name)}}" 
                placeholder="Inserisci titolo..."
            >
            @error('name')
            <div class="invalid-feedback">
                {{$message}}
            </div>   
            @else        
            <div class="valid-feedback">
                Campo corretto
            </div>      
            @enderror       
        </div>

        {{-- INPUT GROUP Availability --}}
        
        <div class="form-check form-switch d-flex align-center col-6">

            <label class="form-check-label" for="availability">Status articolo:</label>
            
            <div class="ms-5">
                
                <label class="form-check-label" id="availability-label" for="availability"></label>
                
                <input class="form-check-input" type="checkbox" role="switch" id="availability" 
                name="availability"
                {{-- {{$dish->availability == 1 ? 'checked' : ''}} --}}
                @if(old('availability', $dish->availability) == 1) checked @endif
                >

            </div>

        </div> 

        {{-- INPUT GROUP DIET --}}
        <div class=" mb-3 col-6">
            <select class="form-select" name="diet" id="diet">
                <?php
                $dietOptions = ['Vegetariano', 'Vegano', 'Gluten-free', 'Carne', 'Pesce', 'Calorico'];
                ?>
                <?php foreach ($dietOptions as $option): ?>
                <option value="{{$option}}" {{ old('diet', $dish->diet) === $option ? 'selected' : '' }}>{{$option}}</option>
                <?php endforeach; ?>
                <option 
                    value="" {{ old('diet', $dish->diet) ? '' : 'selected' }}>Scegli un'opzione (facoltativo)
                </option>
            </select>
            @error('diet')
            <div class="invalid-feedback">
                {{$message}}
            </div>   
            @else        
            <div class="valid-feedback">
                Campo corretto
            </div>      
            @enderror       
        </div>

        {{-- INPUT GROUP COURSE --}}
        <div class=" mb-3 col-6">
            <select class="form-select"  name="course" id="course" >
                
                {{-- creo la variabile $courseOptions--}}
                <?php
                    $courseOptions = ['Antipasto', 'Primo', 'Secondo', 'Dessert'];
                ?>
                {{-- stampo i campi option della select inserendo le opzioni --}}
                <?php foreach ($courseOptions as $option): ?>
                    <option value="{{$option}}" {{ old('course', $dish->course) === $option ? 'selected' : '' }}>{{$option}}</option>
                <?php endforeach; ?>
                <option 
                    value="" {{ old('course', $dish->course) ? '' : 'selected' }}>Scegli un'opzione (Obbligatorio)
                </option>
            </select>
            @error('course')
            <div class="invalid-feedback">
                {{$message}}
            </div>   
            @else        
            <div class="valid-feedback">
                Campo corretto
            </div>      
            @enderror       

        </div>

        {{-- INPUT GROUP INGREDIENTS --}}
        <div class="mb-3 col-12">
            

            <label class="form-label label fw-bold" for="ingredients">Ingredienti:</label>

            <div id="ingredient-inputs">

                @if (!$dish->ingredient)
                    @if (old('ingredients'))
                        @foreach (old('ingredients') as $ingredient)
                        <input type="text" name="ingredients[]" class="me-2 mb-2" value="{{ $ingredient }}" placeholder="Inserisci un ingrediente...">
                        @endforeach
                    @endif
                    {{-- Se sono in store e non ho ingredienti inizio con un campo vuoto --}}
                    <input 
                        type="text" 
                        id="ingredients" 
                        name="ingredients[]" 
                        class="me-2 mb-2" 
                        value="{{trim($dish->ingredient)}}" 
                        placeholder="Inserisci un ingrediente..."
                    >

                @else
                {{-- Se sono in edit stamperò ciascun ingrediente in un input field differente --}}
                    {{-- faccio diventare la stringa un'array --}}
                    @php
                    $ingredients = explode(', ', $dish->ingredient);
                    @endphp
                    {{-- stampo ciascun valore in un input field diverso --}}
                    @foreach ($ingredients as $ingredient)
                        <input type="text" name="ingredients[]" class="me-2 mb-2" value="{{ $ingredient }}" placeholder="Inserisci un ingrediente...">
                    @endforeach
                @endif
            </div>
            <button type="button" id="add-ingredient-btn" onclick="addIngredient()">Aggiungi Ingrediente</button>

        </div>

        {{-- INPUT GROUP PRICE --}}
        <div class=" mb-3 col-6">
            
            <span class="-text">€</span>
            <input type="number" name="price" id="price" step="0.01" class="form-control @error('price') is-invalid @elseif(old('price')) is-valid @enderror"
            value="{{old('price', $dish->price)}}">
            @error('price')
            <div class="invalid-feedback">
                {{$message}}
            </div>   
            @else        
            <div class="valid-feedback">
                Campo corretto
            </div>      
            @enderror     

        </div>

        {{-- INPUT IMMAGINE --}}
        <div class="d-flex  col-12 mb-3">
            <div class="w-75">
                <div class=" p-1 d-flex">
                    <label class="form-label label" for="image">Url Immagine</label>
                    <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @elseif(old('image')) is-valid @enderror">
                    @error('image')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>   
                    @else        
                    <div class="valid-feedback">
                        Campo corretto
                    </div>      
                    @enderror       
                </div>
            </div>
            {{-- CAMPO PREVIEW IMAGE --}}
            <div id="preview-section">
                <img id="preview" src="{{ old('image', $dish->image) && @getimagesize($dish->image)
                ? asset('storage/' . old('image', $dish->image)) 
                : asset('/images/default-dish.png')}}" 
                alt="{{$dish->slug}}">
            </div>
        </div>


    </div>

    <div class="w-100 pt-4">
        <button type="submit" class="btn btn-success me-3">Salva</button>
        <button type="reset" class="btn btn-danger">Svuota</button>
    </div>
    </form>
    
@endsection
    
@section('scripts')
<script>

    // Label di availability dinamico
    document.getElementById('availability').addEventListener('change', function() {;
        const label = document.getElementById('availability-label');
        const input = document.getElementById('availability');
        
        if (input.checked) {
            label.textContent = 'Articolo disponibile online';
        } else {
            label.textContent = 'Articolo disattivato';
        }
    });

    // setto la visualizzazione dinamica dell'immagine in pagina
    const imageField = document.getElementById('image');
    const previewField = document.getElementById('preview');

    let blobUrl;

    imageField.addEventListener('change', () => {

        // controllo se ho il file
        if (imageField.files && imageField.files[0]){
            
            //prendo il file
            const file = imageField.files[0];

            //preparo l'url
            const blobUrl = URL.createObjectURL(file);

            previewField.src = blobUrl;
        }
    })

    window.addEventListener('beforeunload', ()=> {
        if(blobUrl) URL.revokeObjectURL(blobUrl);
    })
        

    // Setto il field price in modo che abbia sempre 2 decimali quando submitto il form
    document.getElementById('form').addEventListener('submit', function() {;
        const priceInputField = document.getElementById('price');
        const priceValue = parseFloat(priceInputField.value).toFixed(2);
        priceInputField.value = priceValue;
    });

    // AGGIUNTA INPUT FIELD
    function addIngredient() {
        const inputs = document.getElementById('ingredient-inputs');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'ingredients[]';
        input.className = 'me-2 mb-2';
        inputs.appendChild(input);
    }
 
</script>

@endsection
