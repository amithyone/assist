@php
    $imgPlaceholder = asset('assist/assist-logo.png');
    $sectionLabels = [
        'hero' => 'Hero',
        'philosophy' => 'Philosophy',
        'editing_engine' => 'Editing engine',
        'features_intro' => 'Features intro',
        'feature_spotlight' => 'Music Video Cuts (featured)',
        'feature_reels' => 'Reels Cloner',
        'feature_beat' => 'Beat Edit',
        'feature_ai' => 'AI Editor',
        'feature_prepro' => 'Preproduction',
        'feature_transcription' => 'Transcription',
        'workspace' => 'Workspace',
        'interoperability' => 'Interoperability',
    ];
@endphp
<div class="assist-site-sections">
    @foreach ($sitePage->sections->sortBy('sort_order') as $section)
        @php
            $c = $section->content ?? [];
            $key = $section->section_key;
            $label = $sectionLabels[$key] ?? $key;
            $imgUrl = $section->image_path && $media->fileExists($section->image_path)
                ? $media->imageUrl($section->image_path)
                : null;
        @endphp
        <details class="assist-admin-card glass-panel" style="margin-bottom: 16px;">
            <summary style="cursor: pointer; font-weight: 600; padding: 4px 0;">{{ $label }}</summary>
            <div class="assist-admin-form-grid" style="margin-top: 16px; max-width: 640px;">
                @if ($key === 'hero')
                    <x-assist.input label="Badge" name="sections[hero][badge]" :value="$c['badge'] ?? ''" />
                    <x-assist.input label="Heading" name="sections[hero][heading]" :value="$c['heading'] ?? ''" />
                    <div class="assist-field">
                        <label>Lead</label>
                        <textarea name="sections[hero][lead]" class="assist-input" rows="4">{{ $c['lead'] ?? '' }}</textarea>
                    </div>
                    <x-assist.input label="Primary CTA label" name="sections[hero][cta_primary_label]" :value="$c['cta_primary_label'] ?? ''" />
                    <x-assist.input label="Secondary CTA label" name="sections[hero][cta_secondary_label]" :value="$c['cta_secondary_label'] ?? ''" />
                    <x-assist.input label="Secondary CTA URL" name="sections[hero][cta_secondary_url]" :value="$c['cta_secondary_url'] ?? ''" />
                @elseif ($key === 'philosophy')
                    <x-assist.input label="Eyebrow" name="sections[philosophy][eyebrow]" :value="$c['eyebrow'] ?? ''" />
                    <x-assist.input label="Heading" name="sections[philosophy][heading]" :value="$c['heading'] ?? ''" />
                    <div class="assist-field"><label>Lead</label><textarea name="sections[philosophy][lead]" class="assist-input" rows="3">{{ $c['lead'] ?? '' }}</textarea></div>
                    <div class="assist-field"><label>Pills (one per line)</label><textarea name="sections[philosophy][pills]" class="assist-input" rows="4">{{ implode("\n", $c['pills'] ?? []) }}</textarea></div>
                @elseif ($key === 'features_intro' || $key === 'workspace' || $key === 'editing_engine')
                    <x-assist.input label="Eyebrow" name="sections[{{ $key }}][eyebrow]" :value="$c['eyebrow'] ?? ''" />
                    <x-assist.input label="Heading" name="sections[{{ $key }}][heading]" :value="$c['heading'] ?? ''" />
                    <div class="assist-field"><label>Lead</label><textarea name="sections[{{ $key }}][lead]" class="assist-input" rows="3">{{ $c['lead'] ?? '' }}</textarea></div>
                    @if ($key === 'editing_engine')
                        <x-assist.input label="CTA label" name="sections[editing_engine][cta_label]" :value="$c['cta_label'] ?? ''" />
                    @endif
                @elseif (str_starts_with($key, 'feature_'))
                    <x-assist.input label="Icon (emoji)" name="sections[{{ $key }}][icon]" :value="$c['icon'] ?? ''" />
                    <x-assist.input label="Badge" name="sections[{{ $key }}][badge]" :value="$c['badge'] ?? ''" />
                    <x-assist.input label="Heading" name="sections[{{ $key }}][heading]" :value="$c['heading'] ?? ''" />
                    <div class="assist-field"><label>Body</label><textarea name="sections[{{ $key }}][body]" class="assist-input" rows="4">{{ $c['body'] ?? '' }}</textarea></div>
                    @if ($key === 'feature_spotlight')
                        <div class="assist-field"><label>Bullets (one per line)</label><textarea name="sections[feature_spotlight][bullets]" class="assist-input" rows="4">{{ implode("\n", $c['bullets'] ?? []) }}</textarea></div>
                    @endif
                @elseif ($key === 'interoperability')
                    <x-assist.input label="Eyebrow" name="sections[interoperability][eyebrow]" :value="$c['eyebrow'] ?? ''" />
                    <x-assist.input label="Heading" name="sections[interoperability][heading]" :value="$c['heading'] ?? ''" />
                    <div class="assist-field"><label>Lead</label><textarea name="sections[interoperability][lead]" class="assist-input" rows="3">{{ $c['lead'] ?? '' }}</textarea></div>
                    <x-assist.input label="Bridge title" name="sections[interoperability][bridge_title]" :value="$c['bridge_title'] ?? ''" />
                    <x-assist.input label="Bridge text" name="sections[interoperability][bridge_text]" :value="$c['bridge_text'] ?? ''" />
                @endif

                <div class="assist-upload-field" data-upload-preview>
                    <label class="assist-label">Featured image</label>
                    <div class="assist-upload-preview-wrap">
                        @if ($imgUrl)
                            <img src="{{ $imgUrl }}" alt="" class="assist-upload-preview assist-upload-preview--saved" data-placeholder="{{ $imgPlaceholder }}" onerror="this.onerror=null;this.src=this.dataset.placeholder;">
                            <label class="assist-checkbox-row"><input type="checkbox" name="remove_section_images[{{ $key }}]" value="1"> Remove image</label>
                        @else
                            <img src="" alt="" class="assist-upload-preview assist-upload-preview--empty" hidden>
                        @endif
                    </div>
                    <input type="file" name="section_images[{{ $key }}]" accept="image/jpeg,image/png,image/webp" class="assist-input" data-upload-input>
                    <x-assist.input label="Image alt text" name="section_image_alts[{{ $key }}]" :value="$section->image_alt ?? ''" />
                </div>
            </div>
        </details>
    @endforeach
</div>
