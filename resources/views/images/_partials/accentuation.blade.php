<style>
    {{-- Form Inputs --}}
    input.accented:focus {
        border-color: rgb({{ $image['red'] }}, {{ $image['green'] }}, {{ $image['blue'] }}) !important;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba({{ $image['red'] }}, {{ $image['green'] }}, {{ $image['blue'] }}, 0.6) !important;
    }

    {{-- Upload Progress --}}
    .kv-upload-progress .progress-bar {
        background-color: rgb({{ $image['red'] }}, {{ $image['green'] }}, {{ $image['blue'] }}) !important;
    }
</style>