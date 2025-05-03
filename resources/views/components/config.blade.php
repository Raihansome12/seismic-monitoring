@props(['isStreaming'])

<div class="flex items-center space-x-4">
    <span class="font-semibold text-gray-900"> IA.STMKG..SHZ </span>
    <span class="text-gray-500">|</span>
    <span class="text-gray-700">50 sps</span>
    <span class="text-gray-500">|</span>
    <span class="text-gray-900">Real<span class="text-red-600">Time</span></span>
    @if($isStreaming)
        <div id="streaming" class="flex items-center px-3 py-1 border-2 border-green-500 rounded-full">
            <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-blink mr-2"></span>
            <span class="text-green-500">streaming</span>
        </div>
    @else
        <div id="offline" class="px-3 py-1 border-2 border-red-500 rounded-full">
            <span class="text-red-500">offline</span>
        </div>
    @endif
</div>