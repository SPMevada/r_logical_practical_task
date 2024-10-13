@csrf
<div class="mb-3">
    <label for="name" class="form-label">Title<span class="text-danger">*</span></label>
    <input type="text" name="name" placeholder="Please enter category name" value="{{ !empty($category->name) ? $category->name : '' }}" class="form-control" aria-describedby="emailHelp">
</div>
<div class="mb-3">
    <label for="description" class="form-label">Description<span class="text-danger">*</span></label>
    <textarea class="form-control" name="description" id="" cols="30" rows="2" placeholder="Please enter description">{{ !empty($category->description) ? $category->description : '' }}</textarea>
</div>
<div class="mb-3">
    <label for="image" class="form-label">Image <span class="text-danger">*</span></label>
    <div class="input-group">
        <input type="file" class="form-control" id="image" name="image" accept="image/*" aria-label="Upload">
    </div>
    <input type="hidden" name="check_image" value="{{ !empty($category->image) ? $category->image : '' }}">
</div>
{{-- <div class="mb-3">
    <label for="category" class="form-label">Status <span class="text-danger">*</span></label>
    <select class="form-select" name="status">
        <option value="">Select</option>
        <option value="active">Active</option>
        <option value="inactive">In-Active</option>
    </select>
</div> --}}
<input type="hidden" name="category_id" value="{{ !empty($category->id) ? $category->id : '' }}">
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Submit</button>
</div>