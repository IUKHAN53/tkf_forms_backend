{{--
    Key/value detail grid used inside expanded report records.
    Expects: $items — associative array of [label => value].
--}}
<div class="fsr-kv">
    @foreach ($items as $label => $value)
        <div class="fsr-kv-item">
            <span class="fsr-kv-label">{{ $label }}</span>
            <span class="fsr-kv-value">{{ (is_null($value) || $value === '') ? '—' : $value }}</span>
        </div>
    @endforeach
</div>
