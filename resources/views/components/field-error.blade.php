@props(['name'])
@error($name)
    <span class="text-[12.5px] font-semibold text-err-700">{{ $message }}</span>
@enderror
