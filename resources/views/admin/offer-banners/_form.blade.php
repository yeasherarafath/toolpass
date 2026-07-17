<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label required">Title</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $banner->title ?? '') }}" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $banner->description ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Link (URL)</label>
            <input type="text" name="link" class="form-control" value="{{ old('link', $banner->link ?? '') }}" placeholder="https://...">
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label>
            @if (!empty($banner?->image_path))
                <div class="mb-2"><img src="{{ Storage::url($banner->image_path) }}" alt="" style="max-height:80px"></div>
            @endif
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Sort order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">Status</label>
                <select name="status" class="form-select">
                    @foreach (['active', 'inactive'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $banner->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('admin.offer-banners.index') }}" class="btn btn-link">Cancel</a>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</div>
