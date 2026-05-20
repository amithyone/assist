@extends('layouts.admin')

@section('title', 'Edit '.$sitePage->name)
@section('page_title', 'Edit: '.$sitePage->name)

@section('content')
<div class="assist-admin-card glass-panel mb-4" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
    <p class="assist-text-muted mb-0">Slug: <code>{{ $sitePage->slug }}</code></p>
    <a href="{{ $previewUrl }}" class="assist-btn assist-btn-outline" target="_blank" rel="noopener">Preview live page</a>
</div>

<form method="POST" action="{{ route('admin.assist.site-pages.update', $sitePage) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="assist-admin-tabs" style="display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap;">
        <a href="#tab-seo" class="assist-btn assist-btn-outline assist-btn-sm">SEO & social</a>
        <a href="#tab-content" class="assist-btn assist-btn-outline assist-btn-sm">Page content</a>
    </div>

    <div id="tab-seo" class="assist-admin-card glass-panel mb-6">
        <h2>SEO & social images</h2>
        @include('admin.assist-site-pages.partials.seo-fields')
    </div>

    <div id="tab-content" class="assist-admin-card glass-panel mb-6">
        <h2>Page content</h2>
        @if ($sitePage->slug !== 'home')
            @php $intro = $sitePage->intro ?? []; @endphp
            <div class="assist-admin-form-grid" style="max-width: 640px;">
                <x-assist.input label="Eyebrow" name="intro_eyebrow" :value="old('intro_eyebrow', $intro['eyebrow'] ?? '')" />
                <x-assist.input label="Heading" name="intro_heading" :value="old('intro_heading', $intro['heading'] ?? '')" />
                <x-assist.input label="Subheading" name="intro_subheading" :value="old('intro_subheading', $intro['subheading'] ?? '')" />
                @if ($sitePage->slug === 'docs')
                    <div class="assist-field">
                        <label for="intro_body_html">Intro HTML (optional)</label>
                        <textarea id="intro_body_html" name="intro_body_html" class="assist-input" rows="4">{{ old('intro_body_html', $intro['body_html'] ?? '') }}</textarea>
                    </div>
                @endif
            </div>
        @else
            @include('admin.assist-site-pages.partials.home-sections')
        @endif
    </div>

    <button type="submit" class="assist-btn assist-btn-primary">Save page</button>
</form>
@endsection

@push('scripts')
<script>
(function () {
  document.querySelectorAll('[data-upload-preview]').forEach(function (wrap) {
    var input = wrap.querySelector('[data-upload-input]');
    if (!input) return;
    var preview = wrap.querySelector('.assist-upload-preview');
    if (!preview) {
      preview = document.createElement('img');
      preview.className = 'assist-upload-preview';
      preview.alt = 'Upload preview';
      var box = wrap.querySelector('.assist-upload-preview-wrap');
      if (box) box.prepend(preview);
    }
    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file || !file.type.match(/^image\//)) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.hidden = false;
        preview.classList.remove('assist-upload-preview--empty');
      };
      reader.readAsDataURL(file);
    });
  });
})();
</script>
@endpush
