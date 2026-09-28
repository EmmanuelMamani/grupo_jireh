@props(['texto' => ''])
<button type="button" class="kpi-ayuda" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="top" data-bs-content="{{ $texto }}" aria-label="Qué significa este indicador"><x-icon name="help"/></button>
