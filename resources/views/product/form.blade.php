@csrf
<input type="hidden" name="product_id" value="{{ !empty($product->id) ? $product->id : '' }} " /> 
<div class="mb-3">
    <label for="name" class="form-label">Title<span class="text-danger">*</span></label>
    <input type="text" name="name" placeholder="Please enter product name" value="{{ !empty($product->name) ? $product->name : '' }}" class="form-control" aria-describedby="emailHelp">
</div>
<div class="mb-3">
    <label for="description" class="form-label">Description<span class="text-danger">*</span></label>
    <textarea class="form-control" name="description" id="" cols="30" rows="2" placeholder="Please enter description">{{ !empty($product->description) ? $product->description : '' }}</textarea>
</div>
<div class="mb-3">
    <label for="categories" class="form-label">Categories <span class="text-danger">*</span></label>
    <select class="form-select select2" id="category" name="categories[]"  multiple tyle="width: 100%;">
        @forelse ($categories as $category)
            <option value="{{ $category->id }}" 
                {{ (!empty($product_category) && in_array($category->id, array_column($product_category, 'category_id'))) ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @empty
            <option >No categories available</option>
        @endforelse
    </select>
</div>
<div class="mb-3">
    <label for="name" class="form-label">Price<span class="text-danger">*</span></label>
    <input type="text" name="price" placeholder="Please enter price" value="{{ !empty($product->price) ? $product->price : '' }}" class="form-control" aria-describedby="emailHelp">
</div>
<div class="mb-3">
    <label for="name" class="form-label">Qty<span class="text-danger">*</span></label>
    <input type="text" name="qty" placeholder="Please enter qty" value="{{ !empty($product->qty) ? $product->qty : '' }}" class="form-control" aria-describedby="emailHelp">
</div>
<div class="mb-3">
    <label for="category" class="form-label">Status <span class="text-danger">*</span></label>
    <select class="form-select" name="status">
        <option value="">Select</option`>
        <option value="active"  {{ ( !empty($product) && $product->status == 'active') ? 'selected' : '' }}>Active</option>
        <option value="in_active" {{ (!empty($product) && $product->status == 'in_active') ? 'selected' : '' }}>In-Active</option>
    </select>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Submit</button>
</div>