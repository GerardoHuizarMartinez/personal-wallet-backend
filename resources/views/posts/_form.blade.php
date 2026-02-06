@csrf

<label class="uppercase text-gray-700 text-xs"> Titulo</label>
<span class="text-xs text-red-600"> @error('name') {{ $message }} @enderror</span>
<input type="text" name="name" class="rounded border-gray-700 w-full mb-4" value="{{ old('name', $post->name) }}">

<label class="uppercase text-gray-700 text-xs"> Atributo</label>
<span class="text-xs text-red-600"> @error('atributo') {{ $message }} @enderror</span>
<input type="text" name="atributo" class="rounded border-gray-700 w-full mb-4" value="{{ old('atributo', $post->atributo) }}">

<label class="uppercase text-gray-700 text-xs"> Contenido </label>
<span class="text-xs text-red-600"> @error('body') {{ $message }} @enderror</span>
<textarea name="body" rows="10" class="rounded border-gray-700 w-full mb-4">{{ old('body', $post->body) }}</textarea>


<div class="flex justify-between">
    <a href=" {{ route('posts.index') }}" class="text-indigo-600"> Volver </a>

    <input type="submit" value="Enviar" class="bg-gray-800 text-white rounded px-4 py-2">
</div>