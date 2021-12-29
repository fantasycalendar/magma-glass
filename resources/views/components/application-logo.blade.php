@if(strlen(app_logo()))
    <img class="h-18 mx-auto hidden dark:inline" src="{{ app_logo() }}" alt="Magma Glass">
    <img class="h-18 mx-auto dark:hidden" src="{{ app_logo_dark() }}" alt="Magma Glass">
@else
    <span class="h-18 text-2xl mx-auto text-center dark:text-gray-200">{{ config('app.name') }}</span>
@endif
