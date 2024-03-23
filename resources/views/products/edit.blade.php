@extends('layouts.app')
@section('content')
<style>
    .image-container {
    display: flex;
    flex-wrap: wrap;
}

.image-wrapper {
    position: relative;
    margin-right: 10px;
    margin-bottom: 10px;
}

.image-wrapper img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: transparent;
    border: none;
    color: red;
    font-size: 16px;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    line-height: 1;
}

</style>
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Product') }}</div>

                <div class="card-body">
                <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-4 text-md-end">
                                <label for="category_id" class="col-form-label">{{ __('Category') }}</label>
                            </div>
                            <div class="col-md-6">
                                <select id="category_id" class="form-control @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('category_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 text-md-end">
                                <label for="name" class="col-form-label">{{ __('Product Name') }}</label>
                            </div>
                            <br/>
                            <div class="col-md-6">
                                <input id="name" type="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $product->name) }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 text-md-end">
                                <label for="name" class="col-form-label">{{ __('Product Images') }}</label>
                            </div>
                            <br/>
                            <div class="col-md-6">
                            <input id="images" type="file"
                             class="form-control @error('images') is-invalid @enderror" name="images[]"
                             multiple
                            accept="image/jpeg, image/png, image/gif">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                        <div class="col-md-4 text-md-end">
                            <label for="description" class="col-form-label">{{ __('Description') }}</label>
                        </div>
                        <div class="col-md-6">
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" required autocomplete="description">{{ old('description',$product->description) }}</textarea>

                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-md-end">
                            <label for="price" class="col-form-label">{{ __('Price') }}</label>
                        </div>
                        <div class="col-md-6">
                            <input id="price" type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price',$product->price) }}" required autocomplete="price">

                            @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <label for="images" class="col-form-label">{{ __('Images') }}</label>
                    <div class="image-container">
                        @foreach($product->images as $image)
                      
                        <div class="image-wrapper">
                            <img src="{{asset('/upload/product/'. $image->image)}}" alt="Image">
                            <button type="button" class="remove-image" data-id="{{$image->id}}">✖</button>
                        </div>
                        @endforeach
                    </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    $('.remove-image').on('click', function() {
        var delete_route =  '{{ route("product.image.remove") }}';
        var imageId = $(this).data('id');
        $.ajax({
            url: delete_route,
            type: 'POST',
            data:{id:imageId},
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.image-wrapper').remove();
                window.location.reload();
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error('AJAX request error:', status, error);
                 // Redirect to the same page or reload it
              window.location.reload();
            }
        });
    });
});
</script>
@endsection
