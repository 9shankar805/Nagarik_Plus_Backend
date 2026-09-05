<div class="min-h-screen bg-slate-50 text-slate-800 font-sans selection:bg-emerald-500 selection:text-white pb-20 relative overflow-hidden {{ auth()->check() ? '-mx-8 -mt-6' : '' }}">

    {{-- Background Decorative Patterns --}}
    <div class="absolute top-20 left-10 w-72 h-72 bg-emerald-300/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-40 right-10 w-80 h-80 bg-teal-300/10 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Sub-Navigation Tool Selector Bar --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2">
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs p-2 flex items-center justify-between flex-wrap gap-2">
            
            <div class="flex items-center gap-2.5 px-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                    🛠️
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black text-slate-900 leading-none">Nagarik+ Studio</span>
                    <span class="text-[10px] text-slate-500 font-medium mt-0.5">Free Image & PDF Utilities</span>
                </div>
            </div>

            {{-- Tool Tabs --}}
            <div class="flex items-center gap-1.5 overflow-x-auto py-1 scrollbar-none">
                <button wire:click="setTool('compress')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTool === 'compress' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Compress</span>
                </button>
                <button wire:click="setTool('resize')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTool === 'resize' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    <span>Resize</span>
                </button>
                <button wire:click="setTool('crop')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTool === 'crop' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>Crop</span>
                </button>
                <button wire:click="setTool('convert')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTool === 'convert' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Convert</span>
                </button>
                <button wire:click="setTool('editor')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTool === 'editor' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Photo Editor</span>
                </button>
                <button wire:click="setTool('all')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTool === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span>All PDF Tools</span>
                </button>
            </div>

        </div>
    </div>

    {{-- Main Workspace Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 relative z-10">

        {{-- Session Flash Notifications --}}
        @if (session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl mb-8 text-sm font-semibold shadow-sm flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </span>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-2xl mb-8 text-sm font-semibold shadow-sm flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ session('error') }}
                </span>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
            </div>
        @endif

        {{-- 1. COMPRESS IMAGE WORKSPACE --}}
        @if($activeTool === 'compress')
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">

            {{-- Left Split Information & Benefits --}}
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-white bg-emerald-600 px-4 py-1.5 rounded-full shadow-xs mb-6 uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>ULTRA FAST COMPRESSION ENGINE</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15] mb-4">
                    Compress Images<br>
                    <span class="text-emerald-600 font-black block mt-1">Without Losing Quality</span>
                </h1>

                <p class="text-slate-600 text-base sm:text-lg font-medium leading-relaxed mb-8">
                    Compress JPG, PNG, SVG, WebP or GIF files with industry-leading quality retention and lightning fast speed.
                </p>

                {{-- 2x2 Feature Cards Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                        <div class="w-11 h-11 bg-emerald-600 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-xs">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Ultra Fast</h4>
                            <p class="text-xs text-slate-500">Compress in seconds</p>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                        <div class="w-11 h-11 bg-teal-600 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-xs">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Secure</h4>
                            <p class="text-xs text-slate-500">Your files are safe</p>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                        <div class="w-11 h-11 bg-slate-900 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-xs">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 text-sm">High Quality</div>
                            <p class="text-xs text-slate-500">Best quality retention</p>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                        <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-xs">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Batch Support</h4>
                            <p class="text-xs text-slate-500">Multiple files at once</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Split Card (Dashed Upload Zone) --}}
            <div class="bg-white rounded-3xl border-2 border-dashed border-emerald-400 shadow-xl p-10 text-center relative hover:border-emerald-600 transition-all duration-300 group"
                 x-data="{ isDragging: false, uploading: false, progress: 0, previewUrl: null, fileName: '', fileSize: '' }"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false"
                 :class="{ 'bg-emerald-50/60 border-emerald-600': isDragging }"
                 x-on:livewire-upload-start="uploading = true; progress = 0"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">

                <div class="flex flex-col items-center justify-center py-6">

                    {{-- Icon Badge --}}
                    <div class="w-20 h-20 bg-emerald-600 rounded-3xl shadow-lg shadow-emerald-600/30 flex items-center justify-center mb-6 text-white group-hover:scale-105 transition-transform mx-auto">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-2">Drag & Drop your images here</h3>
                    <p class="text-xs text-slate-500 font-extrabold mb-6 uppercase tracking-wider">— OR —</p>

                    <label class="cursor-pointer bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-black text-base py-4 px-8 rounded-2xl shadow-lg shadow-emerald-600/30 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2.5 mx-auto">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-white font-extrabold">Select Images</span>
                        <input type="file" wire:model="compressorImage" accept="image/*" class="hidden"
                               @change="
                                   const f = $event.target.files[0];
                                   if(f) {
                                       fileName = f.name;
                                       fileSize = (f.size / 1024).toFixed(1) + ' KB';
                                       const r = new FileReader();
                                       r.onload = e => previewUrl = e.target.result;
                                       r.readAsDataURL(f);
                                   }
                               ">
                    </label>

                    <p class="text-xs text-slate-500 font-semibold mt-6">
                        JPG, PNG, SVG, WebP or GIF<br>
                        Max file size: 50MB
                    </p>

                    {{-- Live Upload Progress Bar --}}
                    <div x-show="uploading" x-cloak class="w-full max-w-md mt-6 bg-emerald-50 p-4 rounded-2xl border border-emerald-200">
                        <div class="flex justify-between text-xs font-bold text-emerald-700 mb-1.5">
                            <span>Uploading image...</span>
                            <span x-text="progress + '%'"></span>
                        </div>
                        <div class="w-full bg-emerald-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                        </div>
                    </div>

                    {{-- Image Instant Preview & Controls --}}
                    <template x-if="previewUrl">
                        <div class="w-full max-w-md mt-8 p-6 bg-slate-50 border border-slate-200 rounded-3xl text-left space-y-4 shadow-inner">
                            <div class="flex items-center gap-4">
                                <img :src="previewUrl" class="w-16 h-16 object-cover rounded-2xl border border-emerald-200">
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 truncate text-sm" x-text="fileName"></p>
                                    <p class="text-xs font-mono text-emerald-600 mt-1" x-text="'Original: ' + fileSize"></p>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs font-bold text-slate-700 mb-1.5">
                                    <span>Compression Quality</span>
                                    <span class="text-emerald-600 font-mono font-bold">{{ $compressorQuality }}%</span>
                                </div>
                                <input type="range" wire:model="compressorQuality" min="10" max="100" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-600">
                            </div>

                            <button wire:click="compressImage" wire:loading.attr="disabled"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-2xl font-bold text-base shadow-lg shadow-emerald-600/30">
                                <span wire:loading.remove wire:target="compressImage">Compress IMAGE Now</span>
                                <span wire:loading wire:target="compressImage">Compressing...</span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

        </div>
        @endif

        {{-- 2. RESIZE IMAGE WORKSPACE --}}
        @if($activeTool === 'resize')
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-3">Resize IMAGE</h1>
            <p class="text-gray-600 text-base max-w-2xl mx-auto mb-8 font-medium">
                Change width and height dimensions of JPG, PNG, WebP or GIF.
            </p>

            <div class="bg-white rounded-3xl p-10 border-2 border-dashed border-indigo-300 shadow-xl"
                 x-data="{ uploading: false, progress: 0, previewUrl: null, fileName: '' }">
                <div class="flex flex-col items-center justify-center py-6">
                    <label class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg shadow-indigo-500/30 flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Select Image to Resize</span>
                        <input type="file" wire:model="resizerImage" accept="image/*" class="hidden"
                               @change="
                                   const f = $event.target.files[0];
                                   if(f) {
                                       fileName = f.name;
                                       const r = new FileReader();
                                       r.onload = e => previewUrl = e.target.result;
                                       r.readAsDataURL(f);
                                   }
                               ">
                    </label>

                    <template x-if="previewUrl">
                        <div class="w-full max-w-md mt-8 p-6 bg-slate-50 border border-slate-200 rounded-3xl text-left space-y-4">
                            <img :src="previewUrl" class="w-20 h-20 object-cover rounded-2xl">
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-xs font-bold text-gray-700 mb-1">Width (px)</label><input type="number" wire:model="resizerWidth" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs"></div>
                                <div><label class="block text-xs font-bold text-gray-700 mb-1">Height (px)</label><input type="number" wire:model="resizerHeight" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs"></div>
                            </div>
                            <button wire:click="resizeImage" class="w-full bg-indigo-600 text-white py-3 rounded-2xl font-bold">Resize Image Now</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        @endif

        {{-- 3. CROP IMAGE WORKSPACE --}}
        @if($activeTool === 'crop')
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-3">Crop IMAGE</h1>
            <p class="text-gray-600 text-base max-w-2xl mx-auto mb-8 font-medium">Crop photo boundaries accurately.</p>

            <div class="bg-white rounded-3xl p-10 border-2 border-dashed border-purple-300 shadow-xl"
                 x-data="{ uploading: false, progress: 0, previewUrl: null }">
                <div class="flex flex-col items-center justify-center py-6">
                    <label class="cursor-pointer bg-purple-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg">
                        <span>Select Image to Crop</span>
                        <input type="file" wire:model="cropperImage" accept="image/*" class="hidden"
                               @change="const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => previewUrl = e.target.result; r.readAsDataURL(f); }">
                    </label>

                    <template x-if="previewUrl">
                        <div class="w-full max-w-md mt-8 p-6 bg-slate-50 border border-slate-200 rounded-3xl text-left space-y-4">
                            <img :src="previewUrl" class="w-full h-40 object-contain rounded-2xl">
                            <div class="grid grid-cols-2 gap-3">
                                <div><label class="block text-xs font-bold">X</label><input type="number" wire:model="cropX" class="w-full border rounded-xl px-3 py-2 text-xs"></div>
                                <div><label class="block text-xs font-bold">Y</label><input type="number" wire:model="cropY" class="w-full border rounded-xl px-3 py-2 text-xs"></div>
                                <div><label class="block text-xs font-bold">Width</label><input type="number" wire:model="cropWidth" class="w-full border rounded-xl px-3 py-2 text-xs"></div>
                                <div><label class="block text-xs font-bold">Height</label><input type="number" wire:model="cropHeight" class="w-full border rounded-xl px-3 py-2 text-xs"></div>
                            </div>
                            <button wire:click="cropImage" class="w-full bg-purple-600 text-white py-3 rounded-2xl font-bold">Crop Image Now</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        @endif

        {{-- 4. CONVERT WORKSPACE --}}
        @if($activeTool === 'convert')
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-3">Convert to JPG / WebP / PNG</h1>
            <p class="text-gray-600 text-base max-w-2xl mx-auto mb-8 font-medium">Convert photos into JPG format easily.</p>

            <div class="bg-white rounded-3xl p-10 border-2 border-dashed border-orange-300 shadow-xl"
                 x-data="{ uploading: false, progress: 0, previewUrls: [], fileNames: [] }"
                 x-on:livewire-upload-start="uploading = true; progress = 0"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                <div class="flex flex-col items-center justify-center py-6">
                    <label class="cursor-pointer bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Select Images to Convert</span>
                        <input type="file" wire:model="converterImages" accept="image/*" multiple class="hidden"
                               @change="
                                   previewUrls = [];
                                   fileNames = [];
                                   const files = Array.from($event.target.files);
                                   files.forEach(f => {
                                       fileNames.push(f.name);
                                       const r = new FileReader();
                                       r.onload = e => previewUrls.push(e.target.result);
                                       r.readAsDataURL(f);
                                   });
                               ">
                    </label>

                    <div x-show="uploading" x-cloak class="w-full max-w-md mt-6 bg-orange-50 p-4 rounded-2xl border border-orange-200">
                        <div class="flex justify-between text-xs font-bold text-orange-700 mb-1.5">
                            <span>Uploading image(s)...</span>
                            <span x-text="progress + '%'"></span>
                        </div>
                        <div class="w-full bg-orange-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-orange-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                        </div>
                    </div>

                    <template x-if="previewUrls.length > 0">
                        <div class="w-full max-w-md mt-8 p-6 bg-slate-50 border border-slate-200 rounded-3xl text-left space-y-4">
                            <div class="flex flex-wrap gap-2 mb-2">
                                <template x-for="url in previewUrls.slice(0, 5)">
                                    <img :src="url" class="w-16 h-16 object-cover rounded-2xl border border-gray-200">
                                </template>
                                <template x-if="previewUrls.length > 5">
                                    <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center text-sm font-bold text-gray-500">
                                        +<span x-text="previewUrls.length - 5"></span>
                                    </div>
                                </template>
                            </div>
                            <p class="text-sm font-semibold text-gray-600 mb-4">
                                <span x-text="previewUrls.length"></span> image(s) selected
                            </p>
                            <select wire:model="converterFormat" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold">
                                <option value="jpeg">JPG / JPEG</option>
                                <option value="webp">WebP</option>
                                <option value="png">PNG</option>
                            </select>
                            <button wire:click="convertImage" wire:loading.attr="disabled" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-2xl font-bold transition-all shadow-md">
                                <span wire:loading.remove wire:target="convertImage">Convert Now</span>
                                <span wire:loading wire:target="convertImage">Converting...</span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        @endif

        {{-- 5. PHOTO EDITOR WORKSPACE --}}
        @if($activeTool === 'editor')
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-3">Photo Editor & Filters</h1>
            <p class="text-gray-600 text-base max-w-2xl mx-auto mb-8 font-medium">Apply Grayscale, Sepia, Brightness filters.</p>

            <div class="bg-white rounded-3xl p-10 border-2 border-dashed border-rose-300 shadow-xl"
                 x-data="{ uploading: false, progress: 0, previewUrl: null }">
                <div class="flex flex-col items-center justify-center py-6">
                    <label class="cursor-pointer bg-rose-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg">
                        <span>Select Photo to Edit</span>
                        <input type="file" wire:model="filterImage" accept="image/*" class="hidden"
                               @change="const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => previewUrl = e.target.result; r.readAsDataURL(f); }">
                    </label>

                    <template x-if="previewUrl">
                        <div class="w-full max-w-md mt-8 p-6 bg-slate-50 border border-slate-200 rounded-3xl text-left space-y-4">
                            <img :src="previewUrl" class="w-full h-40 object-contain rounded-2xl">
                            <select wire:model="filterType" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold">
                                <option value="grayscale">Grayscale</option>
                                <option value="sepia">Sepia</option>
                                <option value="brightness">Brightness</option>
                                <option value="contrast">Contrast</option>
                            </select>
                            <button wire:click="applyFilter" class="w-full bg-rose-600 text-white py-3 rounded-2xl font-bold">Apply Filter</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        @endif
               {{-- Bottom Stats & Features Bar --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200/80 mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                {{-- Left Text + Illustration --}}
                <div class="lg:col-span-4 flex items-center gap-4">
                    <div class="w-16 h-16 bg-emerald-600 text-white rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base mb-1">Why Choose Nagarik+ Compressor?</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Our advanced compression technology ensures maximum file size reduction while maintaining original image clarity.
                        </p>
                    </div>
                </div>

                {{-- Right 4 Stat Metrics Grid --}}
                <div class="lg:col-span-8 grid grid-cols-2 md:grid-cols-4 gap-4 divide-y md:divide-y-0 md:divide-x divide-slate-200">

                    {{-- Metric 1 --}}
                    <div class="text-center pt-4 md:pt-0 md:px-4">
                        <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <p class="text-2xl font-black text-slate-900">98%</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">Quality Retention</p>
                        <p class="text-[10px] text-slate-500 font-bold">Best in class</p>
                    </div>

                    {{-- Metric 2 --}}
                    <div class="text-center pt-4 md:pt-0 md:px-4">
                        <div class="w-10 h-10 bg-teal-100 text-teal-700 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <p class="text-2xl font-black text-slate-900">90%</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">Size Reduction</p>
                        <p class="text-[10px] text-slate-500 font-bold">Average savings</p>
                    </div>

                    {{-- Metric 3 --}}
                    <div class="text-center pt-4 md:pt-0 md:px-4">
                        <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-2xl font-black text-slate-900">10M+</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">Images Compressed</p>
                        <p class="text-[10px] text-slate-500 font-bold">And counting</p>
                    </div>

                    {{-- Metric 4 --}}
                    <div class="text-center pt-4 md:pt-0 md:px-4">
                        <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <p class="text-2xl font-black text-slate-900">100%</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">Secure</p>
                        <p class="text-[10px] text-slate-500 font-bold">Files are protected</p>
                    </div>

                </div>

            </div>
        </div>

        {{-- Trust Footer Badge --}}
        <div class="flex items-center justify-center">
            <div class="bg-white/80 backdrop-blur-md rounded-full px-6 py-2.5 shadow-sm border border-gray-100 flex items-center gap-3 text-xs font-semibold text-gray-600">
                <span class="uppercase tracking-wider text-[11px] font-bold text-gray-500">TRUSTED BY THOUSANDS OF USERS WORLDWIDE</span>
                <div class="flex -space-x-2 overflow-hidden">
                    <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                    <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                    <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2.25&w=256&h=256&q=80" alt="">
                    <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                </div>
                <div class="flex items-center gap-1 text-amber-400 text-sm">
                    ★ ★ ★ ★ ★
                </div>
                <span class="text-xs font-bold text-gray-800">4.9/5 from 50K+ users</span>
            </div>
        </div>

        {{-- ALL / MORE TOOLS SUITE MATRIX --}}
        @if($activeTool === 'all')
        <div class="mt-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Complete Document & Image Suite</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Select any tool to process files instantly.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Tool Card 1: Compress --}}
                <div wire:click="setTool('compress')" class="bg-white p-7 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-purple-200 transition-all cursor-pointer group">
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg mb-1">Compress IMAGE</h3>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">Reduce filesize while maintaining quality.</p>
                </div>

                {{-- Tool Card 2: Resize --}}
                <div wire:click="setTool('resize')" class="bg-white p-7 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all cursor-pointer group">
                    <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg mb-1">Resize IMAGE</h3>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">Scale dimensions by exact pixel height and width.</p>
                </div>

                {{-- Tool Card 3: Crop --}}
                <div wire:click="setTool('crop')" class="bg-white p-7 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-purple-200 transition-all cursor-pointer group">
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg mb-1">Crop IMAGE</h3>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">Trim unwanted photo boundaries effortlessly.</p>
                </div>

                {{-- Tool Card 4: Convert --}}
                <div wire:click="setTool('convert')" class="bg-white p-7 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-orange-200 transition-all cursor-pointer group">
                    <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg mb-1">Convert to JPG</h3>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">Convert WebP, PNG, HEIC to JPG format.</p>
                </div>

                {{-- Tool Card 5: PDF Maker --}}
                <div class="bg-white p-7 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center text-teal-600 mb-5">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg mb-1">PDF Maker</h3>
                    <p class="text-xs text-gray-500 font-medium mb-4">Generate PDF documents from text.</p>
                    <input type="text" wire:model="pdfTitle" placeholder="Title" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl p-2.5 text-xs mb-3">
                    <button wire:click="generatePdf" class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-xl text-xs font-bold transition-all shadow-md">Create PDF</button>
                </div>

                {{-- Tool Card 6: PDF Merger --}}
                <div class="bg-white p-7 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="w-14 h-14 bg-sky-100 rounded-2xl flex items-center justify-center text-sky-600 mb-5">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg mb-1">PDF Merger</h3>
                    <p class="text-xs text-gray-500 font-medium mb-4">Combine PDF documents into one.</p>
                    <input type="file" wire:model="pdfMergerFiles" multiple accept="application/pdf" class="w-full text-xs text-gray-500 mb-3">
                    <button wire:click="mergePdfs" class="w-full bg-sky-600 hover:bg-sky-700 text-white py-2 rounded-xl text-xs font-bold transition-all shadow-md">Merge PDFs</button>
                </div>

                {{-- Tool Card 7: Images to PDF --}}
                <div class="bg-white p-7 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="w-14 h-14 bg-pink-100 rounded-2xl flex items-center justify-center text-pink-600 mb-5">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg mb-1">Images to PDF</h3>
                    <p class="text-xs text-gray-500 font-medium mb-4">Turn photo uploads directly into PDF.</p>
                    <input type="file" wire:model="imagesToPdfFiles" multiple accept="image/*" class="w-full text-xs text-gray-500 mb-3">
                    <button wire:click="imagesToPdf" class="w-full bg-pink-600 hover:bg-pink-700 text-white py-2 rounded-xl text-xs font-bold transition-all shadow-md">Convert to PDF</button>
                </div>

                {{-- Tool Card 8: GIF Maker --}}
                <div class="bg-white p-7 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="w-14 h-14 bg-violet-100 rounded-2xl flex items-center justify-center text-violet-600 mb-5">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg mb-1">GIF Maker</h3>
                    <p class="text-xs text-gray-500 font-medium mb-4">Create animated GIFs from photo frames.</p>
                    <input type="file" wire:model="gifImages" multiple accept="image/*" class="w-full text-xs text-gray-500 mb-3">
                    <button wire:click="makeGif" class="w-full bg-violet-600 hover:bg-violet-700 text-white py-2 rounded-xl text-xs font-bold transition-all shadow-md">Make GIF</button>
                </div>

            </div>
        </div>
        @endif

        {{-- Tool Processing History --}}
        @if(count(session('pdf_tools_history', [])) > 0)
        <div class="mt-16 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="font-black text-gray-900 text-base">Recent Tool Activity Log</h3>
                </div>
                <button wire:click="clearHistory" class="text-xs text-rose-600 hover:text-rose-700 font-bold">Clear Log</button>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach(session('pdf_tools_history', []) as $h)
                <div class="p-5 flex items-center justify-between text-sm">
                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-black rounded-lg font-mono">{{ $h['tool'] }}</span>
                        <span class="text-gray-700 font-medium">{{ $h['details'] }}</span>
                    </div>
                    <span class="text-xs text-gray-400 font-mono">{{ $h['time'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </main>
</div>
