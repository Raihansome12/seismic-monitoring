<aside
    class="fixed left-0 top-60 bg-white shadow-lg p-6 rounded-r-lg w-48 z-10 
           transition-all duration-300 ease-in-out 
           max-[1600px]:-translate-x-45 
           max-[1600px]:hover:translate-x-0">
    <div class="flex flex-col space-y-8">
        <!-- Menu Real Time Data -->
        <a href="{{ route('data-view') }}"
           class="flex flex-col items-center text-center p-3 rounded-lg hover:bg-gray-100 cursor-pointer">
            <img src="img/seismograph.png" alt="Real Time Data" class="w-16 h-16 mb-2">
            <div class="text-sm font-bold">
                <span>Real Time</span>
                <span class="text-red-600">Data<span class="text-black">View</span></span>
            </div>
        </a>

        <div class="w-full border-t border-gray-300"></div>

        <!-- Menu Data Quality Assessment -->
        <a href="{{ route('quality') }}"
           class="flex flex-col items-center text-center p-3 rounded-lg hover:bg-gray-100 cursor-pointer">
            <img src="img/chart.png" alt="Data Quality"
                 class="p-1 w-16 h-16 mb-3 border-2 rounded border-black">
            <div class="text-sm font-bold">
                <span>Data <span class="text-red-600">Quality</span></span>
                <span>Assessment</span>
            </div>
        </a>
    </div>
</aside>
