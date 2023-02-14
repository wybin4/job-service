<div class="flex flex-col items-center bg-gray-100">
    <div>
        {{ $logo ?? "" }}
    </div>

    <div class="flex mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>