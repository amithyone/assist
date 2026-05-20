@php
    $imgPlaceholder = asset('assist/assist-logo.png');
    $ogPreview = $sitePage->og_image && $media->fileExists($sitePage->og_image) ? $media->imageUrl($sitePage->og_image) : null;
    $twPreview = $sitePage->twitter_image && $media->fileExists($sitePage->twitter_image) ? $media->imageUrl($sitePage->twitter_image) : null;
@endphp
<div class="assist-admin-form-grid" style="max-width: 720px;">
    <label class="assist-checkbox-row">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $sitePage->is_published))> Published
    </label>

    <x-assist.input label="Meta title (~60 chars)" name="meta_title" :value="old('meta_title', $sitePage->meta_title)" />
    <div class="assist-field">
        <label for="meta_description">Meta description (~160 chars)</label>
        <textarea id="meta_description" name="meta_description" class="assist-input" rows="3">{{ old('meta_description', $sitePage->meta_description) }}</textarea>
    </div>
    <x-assist.input label="Meta keywords" name="meta_keywords" :value="old('meta_keywords', $sitePage->meta_keywords)" />
    <x-assist.input label="Canonical URL" name="canonical_url" type="url" :value="old('canonical_url', $sitePage->canonical_url)" />
    <x-assist.input label="Robots" name="robots" :value="old('robots', $sitePage->robots)" placeholder="index,follow" />

    <hr style="border-color: rgba(255,255,255,0.08); margin: 16px 0;">
    <p class="assist-text-muted" style="font-size: 13px;">Open Graph — use 1200×630 images for best social previews.</p>

    <x-assist.input label="OG title" name="og_title" :value="old('og_title', $sitePage->og_title)" />
    <div class="assist-field">
        <label for="og_description">OG description</label>
        <textarea id="og_description" name="og_description" class="assist-input" rows="3">{{ old('og_description', $sitePage->og_description) }}</textarea>
    </div>
    <x-assist.input label="OG type" name="og_type" :value="old('og_type', $sitePage->og_type)" />

    <div class="assist-upload-field" data-upload-preview>
        <label class="assist-label">OG image</label>
        <div class="assist-upload-preview-wrap">
            @if ($ogPreview)
                <img src="{{ $ogPreview }}" alt="OG preview" class="assist-upload-preview assist-upload-preview--saved" data-placeholder="{{ $imgPlaceholder }}" onerror="this.onerror=null;this.src=this.dataset.placeholder;">
                <label class="assist-checkbox-row"><input type="checkbox" name="remove_og_image" value="1"> Remove current OG image</label>
            @else
                <img src="" alt="" class="assist-upload-preview assist-upload-preview--empty" hidden>
            @endif
        </div>
        <input type="file" name="og_image" accept="image/jpeg,image/png,image/webp" class="assist-input" data-upload-input>
    </div>

    <x-assist.input label="Twitter card" name="twitter_card" :value="old('twitter_card', $sitePage->twitter_card)" />
    <x-assist.input label="Twitter title" name="twitter_title" :value="old('twitter_title', $sitePage->twitter_title)" />
    <div class="assist-field">
        <label for="twitter_description">Twitter description</label>
        <textarea id="twitter_description" name="twitter_description" class="assist-input" rows="3">{{ old('twitter_description', $sitePage->twitter_description) }}</textarea>
    </div>

    <div class="assist-upload-field" data-upload-preview>
        <label class="assist-label">Twitter image (optional — defaults to OG)</label>
        <div class="assist-upload-preview-wrap">
            @if ($twPreview)
                <img src="{{ $twPreview }}" alt="Twitter preview" class="assist-upload-preview assist-upload-preview--saved" data-placeholder="{{ $imgPlaceholder }}" onerror="this.onerror=null;this.src=this.dataset.placeholder;">
                <label class="assist-checkbox-row"><input type="checkbox" name="remove_twitter_image" value="1"> Remove Twitter image</label>
            @else
                <img src="" alt="" class="assist-upload-preview assist-upload-preview--empty" hidden>
            @endif
        </div>
        <input type="file" name="twitter_image" accept="image/jpeg,image/png,image/webp" class="assist-input" data-upload-input>
    </div>
</div>
